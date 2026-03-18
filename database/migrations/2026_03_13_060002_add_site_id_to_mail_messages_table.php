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
        if (! Schema::hasTable('mail_messages') || Schema::hasColumn('mail_messages', 'site_id')) {
            return;
        }

        Schema::table('mail_messages', function (Blueprint $table) {
            $table->foreignId('site_id')
                ->nullable()
                ->after('tenant_id')
                ->constrained('sites')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('mail_messages') || ! Schema::hasColumn('mail_messages', 'site_id')) {
            return;
        }

        Schema::table('mail_messages', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
        });
    }
};

