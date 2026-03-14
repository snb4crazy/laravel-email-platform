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
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();

            // null = global default; set to a user/tenant id for tenant-specific override
            $table->foreignId('tenant_id')->nullable()->constrained('users')->nullOnDelete();

            // Which event/route this template handles
            // e.g. 'contact_form', 'webhook_contact', 'password_reset', etc.
            $table->string('event_type')->index();

            // Human-readable label for admin UI
            $table->string('name');

            // Subject line: supports {name}, {subject}, {app_name} placeholders
            $table->string('subject_template')->nullable();

            // Body: supports same {placeholder} syntax (NOT Blade, safe for DB storage)
            $table->longText('body_html')->nullable();
            $table->text('body_text')->nullable();

            // Only one template per (tenant, event_type) can be the default.
            // Enforced at service layer, not DB constraint.
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
