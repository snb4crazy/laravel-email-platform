<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each site has a fixed inbox that receives all contact-form submissions.
     * The form submitter's address is stored as reply_to so the site owner
     * can reply directly — but the platform never delivers to an arbitrary
     * address supplied by the caller.
     */
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('notification_email')->nullable()->after('domain')
                ->comment('Locked delivery address for all contact submissions from this site.');
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('notification_email');
        });
    }
};
