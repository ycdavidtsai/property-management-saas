<?php
// Twilio webhook with proper broadcast_messages sync
header('Content-Type: application/json');
$log = __DIR__ . '/../storage/logs/twilio-simple.log';

function logMsg($msg) {
    global $log;
    file_put_contents($log, "[" . date('H:i:s') . "] " . $msg . "\n", FILE_APPEND);
}

try {
    // Load environment variables from .env
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                $value = trim($value, '"\'');
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    // Get database credentials
    $dbDriver = $_ENV['DB_CONNECTION'] ?? 'mysql';
    $dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $dbPort = $_ENV['DB_PORT'] ?? '3306';
    $dbName = $_ENV['DB_DATABASE'] ?? 'forge';
    $dbUser = $_ENV['DB_USERNAME'] ?? 'forge';
    $dbPass = $_ENV['DB_PASSWORD'] ?? '';

    // Create PDO connection
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
    } else {
        throw new Exception("Unsupported database driver: {$dbDriver}");
    }

    //logMsg("Database connected ({$dbDriver})");

    // Extract Twilio data
    $sid = $_POST['MessageSid'] ?? $_POST['SmsSid'] ?? null;
    $status = $_POST['MessageStatus'] ?? $_POST['SmsStatus'] ?? null;

    // Handle error notification format
    if (!$sid && isset($_POST['Payload'])) {
        $payload = json_decode($_POST['Payload'], true);
        if ($payload && isset($payload['webhook']['request']['parameters'])) {
            $params = $payload['webhook']['request']['parameters'];
            $sid = $params['MessageSid'] ?? $params['SmsSid'] ?? null;
            $status = $params['MessageStatus'] ?? $params['SmsStatus'] ?? null;
        }
    }

    if ($sid && $status) {
        //logMsg("SID={$sid} Status={$status}");

        // First, get the notification to check its current status and if it's part of a broadcast
        $stmt = $pdo->prepare('SELECT id, status, type, notifiable_type, notifiable_id FROM notifications WHERE provider_id = ? LIMIT 1');
        $stmt->execute([$sid]);
        $notification = $stmt->fetch();

        if (!$notification) {
            //logMsg("  → Notification not found");
            echo json_encode(['success' => false, 'error' => 'Notification not found']);
            exit;
        }

        $oldStatus = $notification['status'];

        // Map Twilio status
        $statusMap = [
            'queued' => 'sent',
            'sending' => 'sent',
            'sent' => 'sent',
            'delivered' => 'delivered',
            'undelivered' => 'failed',
            'failed' => 'failed',
        ];

        $newStatus = $statusMap[strtolower($status)] ?? 'sent';

        // Only update if status actually changed
        if ($oldStatus === $newStatus) {
            //logMsg("  → Status unchanged ({$newStatus})");
            echo json_encode(['success' => true, 'message' => 'Status unchanged']);
            exit;
        }

        // Update notification status
        $stmt = $pdo->prepare('UPDATE notifications SET status = ?, updated_at = ? WHERE provider_id = ?');
        $stmt->execute([$newStatus, date('Y-m-d H:i:s'), $sid]);

        //logMsg("  → Updated notification to {$newStatus}");

        // If delivered, set delivered_at
        if ($newStatus === 'delivered') {
            $stmt = $pdo->prepare('UPDATE notifications SET delivered_at = ? WHERE provider_id = ?');
            $stmt->execute([date('Y-m-d H:i:s'), $sid]);
            //logMsg("  → Set delivered_at timestamp");
        }

        // Update broadcast_messages if this notification is part of a broadcast
        if ($notification['notifiable_type'] === 'App\\Models\\BroadcastMessage' && $notification['notifiable_id']) {
            $broadcastId = $notification['notifiable_id'];
            $notificationType = $notification['type']; // 'email' or 'sms'

            //logMsg("  → Syncing broadcast #{$broadcastId}");

            // Handle status transitions for broadcast counters
            if ($oldStatus === 'sent' && $newStatus === 'delivered') {
                // Increment delivered counter
                if ($notificationType === 'sms') {
                    $stmt = $pdo->prepare('UPDATE broadcast_messages SET sms_delivered = sms_delivered + 1 WHERE id = ?');
                    $stmt->execute([$broadcastId]);
                    //logMsg("    → Incremented sms_delivered");
                } elseif ($notificationType === 'email') {
                    $stmt = $pdo->prepare('UPDATE broadcast_messages SET emails_delivered = emails_delivered + 1 WHERE id = ?');
                    $stmt->execute([$broadcastId]);
                    //logMsg("    → Incremented emails_delivered");
                }

            } elseif ($oldStatus === 'sent' && $newStatus === 'failed') {
                // Moving from sent to failed - decrement sent, increment failed
                if ($notificationType === 'sms') {
                    $stmt = $pdo->prepare('UPDATE broadcast_messages SET sms_sent = GREATEST(0, sms_sent - 1), sms_failed = sms_failed + 1 WHERE id = ?');
                    $stmt->execute([$broadcastId]);
                    //logMsg("    → Decremented sms_sent, incremented sms_failed");
                } elseif ($notificationType === 'email') {
                    $stmt = $pdo->prepare('UPDATE broadcast_messages SET emails_sent = GREATEST(0, emails_sent - 1), emails_failed = emails_failed + 1 WHERE id = ?');
                    $stmt->execute([$broadcastId]);
                    //logMsg("    → Decremented emails_sent, incremented emails_failed");
                }

            } elseif ($oldStatus === 'pending' && $newStatus === 'failed') {
                // Directly to failed (shouldn't happen often, but handle it)
                if ($notificationType === 'sms') {
                    $stmt = $pdo->prepare('UPDATE broadcast_messages SET sms_failed = sms_failed + 1 WHERE id = ?');
                    $stmt->execute([$broadcastId]);
                    //logMsg("    → Incremented sms_failed");
                } elseif ($notificationType === 'email') {
                    $stmt = $pdo->prepare('UPDATE broadcast_messages SET emails_failed = emails_failed + 1 WHERE id = ?');
                    $stmt->execute([$broadcastId]);
                    //logMsg("    → Incremented emails_failed");
                }
            }

            // Update broadcast overall status based on all its notifications
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

            // Determine broadcast status based on enum values:
            // 'draft', 'scheduled', 'sending', 'sent', 'failed'
            $broadcastStatus = 'sending';
            if ($stats['total'] > 0) {
                if ($stats['pending'] == $stats['total']) {
                    // All still pending - keep current status (likely 'scheduled' or 'draft')
                    $broadcastStatus = null; // Don't change
                } elseif ($stats['delivered'] + $stats['failed'] == $stats['total']) {
                    // All done (delivered or failed) - mark as 'sent'
                    $broadcastStatus = 'sent';
                } else {
                    // Some sent, some pending - mark as 'sending'
                    $broadcastStatus = 'sending';
                }
            }

            // Only update status if we determined a new one
            if ($broadcastStatus) {
                $stmt = $pdo->prepare('UPDATE broadcast_messages SET status = ? WHERE id = ?');
                $stmt->execute([$broadcastStatus, $broadcastId]);
                //logMsg("    → Set broadcast status to '{$broadcastStatus}' (D:{$stats['delivered']} F:{$stats['failed']} S:{$stats['sent']} P:{$stats['pending']})");
            } else {
                //logMsg("    → Broadcast status unchanged (D:{$stats['delivered']} F:{$stats['failed']} S:{$stats['sent']} P:{$stats['pending']})");
            }

            // If all messages are done, set completed_at timestamp
            if ($broadcastStatus === 'sent') {
                $stmt = $pdo->prepare('UPDATE broadcast_messages SET completed_at = ? WHERE id = ? AND completed_at IS NULL');
                $stmt->execute([date('Y-m-d H:i:s'), $broadcastId]);
                //logMsg("    → Set completed_at timestamp");
            }
        }

        echo json_encode([
            'success' => true,
            'sid' => $sid,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'broadcast_updated' => isset($broadcastId)
        ]);

    } else {
        //logMsg("Missing SID or Status");
        echo json_encode(['success' => false, 'error' => 'Missing MessageSid or MessageStatus']);
    }

} catch (\Exception $e) {
    //logMsg("ERROR: " . $e->getMessage() . " in " . basename($e->getFile()) . ":" . $e->getLine());

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
