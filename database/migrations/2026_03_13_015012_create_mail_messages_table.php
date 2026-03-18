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
        Schema::create('mail_messages', function (Blueprint $table) {
            $table->id();

            // Tenant (nullable until multi-tenancy is implemented)
            $table->foreignId('tenant_id')->nullable()->constrained('users')->nullOnDelete();

            // Source of the message
            $table->enum('source', ['web', 'webhook'])->index();

            // Envelope
            $table->string('from_name')->nullable();
            $table->string('from_email');
            $table->string('to_name')->nullable();
            $table->string('to_email');
            $table->string('reply_to')->nullable();
            $table->string('subject')->nullable();

            // Body
            $table->text('body_text')->nullable();
            $table->mediumText('body_html')->nullable();

            // Status / lifecycle
            $table->enum('status', [
                'received',   // ingested but not yet queued
                'queued',     // dispatched to queue
                'sending',    // picked up by worker
                'sent',       // accepted by mail provider
                'delivered',  // confirmed delivery by provider webhook
                'failed',     // permanent failure
                'cancelled',  // manually cancelled
            ])->default('received')->index();

            // Provider info (populated once a mailer is wired)
            $table->string('mailer')->nullable();          // smtp, ses, postmark, resend...
            $table->string('provider_message_id')->nullable()->index(); // ID returned by provider

            // Spam / abuse
            $table->boolean('is_spam')->default(false)->index();
            $table->timestamp('spam_reported_at')->nullable();

            // Request metadata (for debugging / audit)
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Catch-all for extra provider/app data
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_messages');
    }
};
