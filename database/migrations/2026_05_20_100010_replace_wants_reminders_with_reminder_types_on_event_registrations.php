<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->json('reminder_types')->nullable()->after('status');
        });

        if (Schema::hasColumn('event_registrations', 'wants_reminders')) {
            DB::table('event_registrations')
                ->where('wants_reminders', true)
                ->update(['reminder_types' => json_encode(['J-7', 'J-1', 'H-1'])]);

            DB::table('event_registrations')
                ->where('wants_reminders', false)
                ->update(['reminder_types' => json_encode([])]);

            Schema::table('event_registrations', function (Blueprint $table) {
                $table->dropColumn('wants_reminders');
            });
        }
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->boolean('wants_reminders')->default(false)->after('status');
        });

        DB::table('event_registrations')
            ->whereNotNull('reminder_types')
            ->where('reminder_types', '!=', '[]')
            ->update(['wants_reminders' => true]);

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn('reminder_types');
        });
    }
};
