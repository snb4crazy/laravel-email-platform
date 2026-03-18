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
        Schema::create('site_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('credential_type', 32)->default('api_key')->index();

            // External key identifier used by clients (safe to expose).
            $table->string('key_id')->unique();

            // TODO: store only hashed secret in production.
            $table->string('secret_hash')->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();

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
        Schema::dropIfExists('site_credentials');
    }
};

