<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()
                ->after('portfolio_url')
                ->comment('Opt-in/out par type: new_answer, article_published, event_reminder, job_alert');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('notification_preferences');
        });
    }
};
