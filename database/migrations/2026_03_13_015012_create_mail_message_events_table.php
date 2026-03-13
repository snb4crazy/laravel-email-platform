<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mail_message_events', function (Blueprint $table) {
            $table->id();

            // Keep column/index here; add FK in a later migration to avoid order issues.
            $table->foreignId('mail_message_id')->index();

            // What happened
            $table->enum('type', [
                'received',        // message first ingested
                'queued',          // job dispatched to queue
                'sending',         // worker picked up job
                'sent',            // provider accepted the message
                'delivered',       // provider confirmed delivery
                'bounced',         // hard or soft bounce
                'complained',      // spam complaint from recipient
                'opened',          // recipient opened the email (pixel tracking)
                'clicked',         // recipient clicked a link
                'unsubscribed',    // recipient unsubscribed
                'failed',          // sending failed (worker error / provider rejection)
                'retried',         // job was retried after failure
                'cancelled',       // message was manually cancelled
            ])->index();

            // Extra context per event type:
            // - bounced: { "bounce_type": "hard", "bounce_code": "550", "description": "..." }
            // - clicked:  { "url": "https://...", "user_agent": "..." }
            // - failed:   { "error": "...", "exception": "..." }
            // - opened:   { "ip": "...", "user_agent": "..." }
            $table->json('payload')->nullable();

            // For tracking events: IP + user agent of the recipient
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Provider-side event ID (for deduplication)
            $table->string('provider_event_id')->nullable()->unique();

            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_message_events');
    }
};
