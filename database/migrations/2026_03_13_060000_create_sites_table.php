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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name');
            $table->string('domain')->nullable()->index();

            // Public identifier expected from client payloads.
            $table->string('public_key')->unique();

            // TODO: enforce auth strategy in middleware (draft only for now).
            $table->string('auth_mode', 32)->default('none')->index();
            $table->string('captcha_provider', 32)->default('none');
            $table->string('captcha_site_key')->nullable();
            $table->text('captcha_secret')->nullable();

            $table->boolean('is_active')->default(true)->index();
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
        Schema::dropIfExists('sites');
    }
};
