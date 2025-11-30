<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds SMS segment tracking to broadcast_messages for billing purposes.
     * - sms_segments_total: Total segments used for this broadcast (null for email-only)
     * - sms_segments_per_message: Segments per recipient (based on message length)
     */
    public function up(): void
    {
        Schema::table('broadcast_messages', function (Blueprint $table) {
            // Segments per individual message (calculated from message length)
            // 1 segment = 160 chars, 2 segments = 161-306 chars, etc.
            $table->unsignedSmallInteger('sms_segments_per_message')
                  ->nullable()
                  ->after('sms_failed')
                  ->comment('Number of SMS segments per recipient based on message length');

            // Total segments for entire broadcast (segments_per_message * sms_sent)
            // This is what gets billed
            $table->unsignedInteger('sms_segments_total')
                  ->nullable()
                  ->after('sms_segments_per_message')
                  ->comment('Total SMS segments used for billing (null for email-only broadcasts)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcast_messages', function (Blueprint $table) {
            $table->dropColumn(['sms_segments_per_message', 'sms_segments_total']);
        });
    }
};
