<?php
// Postmark webhook - tracks email delivery status with MessageID matching
header('Content-Type: application/json');
$log = __DIR__ . '/../storage/logs/postmark-webhook.log';

function logMsg($msg) {
    global $log;
    file_put_contents($log, "[" . date('H:i:s') . "] " . $msg . "\n", FILE_APPEND);
}

try {
    // Get raw POST data
    $rawPayload = file_get_contents('php://input');
    $payload = json_decode($rawPayload, true);

    if (!$payload) {
        logMsg("ERROR: Invalid JSON payload");
        echo json_encode(['success' => false, 'error' => 'Invalid payload']);
        exit;
    }

    logMsg("=== POSTMARK WEBHOOK RECEIVED ===");
    logMsg("Type: " . ($payload['RecordType'] ?? 'unknown'));

    // Load environment and create PDO connection
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value, '"\'');
            }
        }
    }

    $dbDriver = $_ENV['DB_CONNECTION'] ?? 'mysql';
    $dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $dbPort = $_ENV['DB_PORT'] ?? '3306';
    $dbName = $_ENV['DB_DATABASE'] ?? 'forge';
    $dbUser = $_ENV['DB_USERNAME'] ?? 'forge';
    $dbPass = $_ENV['DB_PASSWORD'] ?? '';

    if ($dbDriver === 'mysql') {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } elseif ($dbDriver === 'sqlite') {
        $dbPath = $dbName;
        if (!str_starts_with($dbPath, '/')) {
            $dbPath = __DIR__ . '/../database/' . $dbPath;
        }
        $pdo = new PDO("sqlite:{$dbPath}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    logMsg("Database connected ({$dbDriver})");

    // Extract Postmark data
    $recordType = $payload['RecordType'] ?? null; // Delivery, Bounce, SpamComplaint, etc.
    $messageId = $payload['MessageID'] ?? null; // Postmark's message ID
    $recipient = $payload['Recipient'] ?? $payload['Email'] ?? null;

    if (!$messageId) {
        logMsg("Missing MessageID");
        echo json_encode(['success' => false, 'error' => 'Missing MessageID']);
        exit;
    }

    logMsg("MessageID: {$messageId}, Recipient: {$recipient}, Type: {$recordType}");

    // Find notification by Postmark MessageID (exact match - just like Twilio!)
    $stmt = $pdo->prepare('SELECT id, status, type, notifiable_type, notifiable_id FROM notifications WHERE provider_id = ? LIMIT 1');
    $stmt->execute([$messageId]);
    $notification = $stmt->fetch();

    if (!$notification) {
        logMsg("  → Notification not found for MessageID: {$messageId}");
        echo json_encode(['success' => false, 'error' => 'Notification not found']);
        exit;
    }

    $notificationId = $notification['id'];
    $oldStatus = $notification['status'];

    logMsg("  → Found notification ID: {$notificationId}, Status: {$oldStatus}");

    // Map Postmark event types to our statuses
    $newStatus = null;
    $errorMessage = null;

    switch ($recordType) {
        case 'Delivery':
            $newStatus = 'delivered';
            logMsg("  → Email delivered successfully");
            break;

        case 'Bounce':
            $newStatus = 'failed';
            $bounceType = $payload['Type'] ?? 'Unknown';
            $errorMessage = $payload['Description'] ?? 'Email bounced';
            logMsg("  → Email bounced: {$bounceType} - {$errorMessage}");
            break;

        case 'SpamComplaint':
            $newStatus = 'failed';
            $errorMessage = 'Marked as spam by recipient';
            logMsg("  → Marked as spam");
            break;

        case 'Open':
            // Email was opened - don't change status, just log
            logMsg("  → Email opened (no status change)");
            echo json_encode(['success' => true, 'message' => 'Email opened']);
            exit;

        case 'Click':
            // Link was clicked - don't change status, just log
            logMsg("  → Link clicked (no status change)");
            echo json_encode(['success' => true, 'message' => 'Link clicked']);
            exit;

        default:
            logMsg("  → Unknown record type: {$recordType}");
            echo json_encode(['success' => true, 'message' => 'Event logged']);
            exit;
    }

    // Only update if status actually changed
    if ($newStatus && $oldStatus !== $newStatus) {
        // Update notification status
        $stmt = $pdo->prepare('UPDATE notifications SET status = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([$newStatus, date('Y-m-d H:i:s'), $notificationId]);

        logMsg("  → Updated notification to {$newStatus}");

        // If delivered, set delivered_at
        if ($newStatus === 'delivered') {
            $stmt = $pdo->prepare('UPDATE notifications SET delivered_at = ? WHERE id = ?');
            $stmt->execute([date('Y-m-d H:i:s'), $notificationId]);
            logMsg("  → Set delivered_at timestamp");
        }

        // If failed, store error message
        if ($newStatus === 'failed' && $errorMessage) {
            $stmt = $pdo->prepare('UPDATE notifications SET error_message = ? WHERE id = ?');
            $stmt->execute([$errorMessage, $notificationId]);
            logMsg("  → Stored error message: {$errorMessage}");
        }

        // Update broadcast_messages if this notification is part of a broadcast
        if ($notification['notifiable_type'] === 'App\\Models\\BroadcastMessage' && $notification['notifiable_id']) {
            $broadcastId = $notification['notifiable_id'];

            logMsg("  → Syncing broadcast #{$broadcastId}");

            // Handle status transitions for broadcast counters
            if ($oldStatus === 'sent' && $newStatus === 'delivered') {
                // Increment delivered counter
                $stmt = $pdo->prepare('UPDATE broadcast_messages SET emails_delivered = emails_delivered + 1 WHERE id = ?');
                $stmt->execute([$broadcastId]);
                logMsg("    → Incremented emails_delivered");

            } elseif ($oldStatus === 'sent' && $newStatus === 'failed') {
                // Moving from sent to failed - decrement sent, increment failed
                $stmt = $pdo->prepare('UPDATE broadcast_messages SET emails_sent = GREATEST(0, emails_sent - 1), emails_failed = emails_failed + 1 WHERE id = ?');
                $stmt->execute([$broadcastId]);
                logMsg("    → Decremented emails_sent, incremented emails_failed");

            } elseif ($oldStatus === 'pending' && $newStatus === 'failed') {
                // Directly to failed
                $stmt = $pdo->prepare('UPDATE broadcast_messages SET emails_failed = emails_failed + 1 WHERE id = ?');
                $stmt->execute([$broadcastId]);
                logMsg("    → Incremented emails_failed");
            }

            // Update broadcast overall status
            $stmt = $pdo->prepare("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                FROM notifications
                WHERE notifiable_type = 'App\\\\Models\\\\BroadcastMessage'
                AND notifiable_id = ?
            ");
            $stmt->execute([$broadcastId]);
            $stats = $stmt->fetch();

            // Determine broadcast status
            $broadcastStatus = 'sending';
            if ($stats['total'] > 0) {
                if ($stats['pending'] == $stats['total']) {
                    $broadcastStatus = null; // Don't change
                } elseif ($stats['delivered'] + $stats['failed'] == $stats['total']) {
                    $broadcastStatus = 'sent';
                } else {
                    $broadcastStatus = 'sending';
                }
            }

            if ($broadcastStatus) {
                $stmt = $pdo->prepare('UPDATE broadcast_messages SET status = ? WHERE id = ?');
                $stmt->execute([$broadcastStatus, $broadcastId]);
                logMsg("    → Set broadcast status to '{$broadcastStatus}' (D:{$stats['delivered']} F:{$stats['failed']} S:{$stats['sent']} P:{$stats['pending']})");
            }

            // If all done, set completed_at
            if ($broadcastStatus === 'sent') {
                $stmt = $pdo->prepare('UPDATE broadcast_messages SET completed_at = ? WHERE id = ? AND completed_at IS NULL');
                $stmt->execute([date('Y-m-d H:i:s'), $broadcastId]);
                logMsg("    → Set completed_at timestamp");
            }
        }

        echo json_encode([
            'success' => true,
            'notification_id' => $notificationId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'broadcast_updated' => isset($broadcastId)
        ]);

    } else {
        logMsg("  → Status unchanged ({$newStatus})");
        echo json_encode(['success' => true, 'message' => 'Status unchanged']);
    }

} catch (\Exception $e) {
    logMsg("ERROR: " . $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine());

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

logMsg("=== END WEBHOOK ===\n");
