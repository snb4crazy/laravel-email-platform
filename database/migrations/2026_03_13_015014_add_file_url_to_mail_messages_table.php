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
        if (! Schema::hasTable('mail_messages') || Schema::hasColumn('mail_messages', 'file_url')) {
            return;
        }

        Schema::table('mail_messages', function (Blueprint $table) {
            $table->string('file_url', 2048)
                ->nullable()
                ->after('body_html');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('mail_messages') || ! Schema::hasColumn('mail_messages', 'file_url')) {
            return;
        }

        Schema::table('mail_messages', function (Blueprint $table) {
            $table->dropColumn('file_url');
        });
    }
};

