<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table): void {
            $table->string('ticket_number', 30)->nullable()->unique()->after('payment_status');
            $table->string('ticket_qr_token', 64)->nullable()->unique()->after('ticket_number');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table): void {
            $table->dropColumn(['ticket_number', 'ticket_qr_token']);
        });
    }
};
