<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->boolean('ticketing_enabled')->default(false)->after('promo_uses_count');
            $table->string('ticket_prefix', 10)->nullable()->after('ticketing_enabled')
                ->comment('Prefix for ticket numbers, e.g. LCI');
            $table->boolean('guest_registration_enabled')->default(false)->after('ticket_prefix')
                ->comment('Allow non-member public registrations');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn(['ticketing_enabled', 'ticket_prefix', 'guest_registration_enabled']);
        });
    }
};
