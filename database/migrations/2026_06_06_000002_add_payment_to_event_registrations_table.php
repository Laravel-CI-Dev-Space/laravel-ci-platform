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
            $table->decimal('amount_paid', 10, 2)->nullable()->after('status')->comment('Amount charged after discount, null = free');
            $table->string('promo_code_used', 50)->nullable()->after('amount_paid');
            $table->decimal('discount_applied', 10, 2)->nullable()->after('promo_code_used');
            $table->enum('payment_status', ['pending', 'paid', 'free', 'refunded'])->default('free')->after('discount_applied');
        });
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table): void {
            $table->dropColumn(['amount_paid', 'promo_code_used', 'discount_applied', 'payment_status']);
        });
    }
};
