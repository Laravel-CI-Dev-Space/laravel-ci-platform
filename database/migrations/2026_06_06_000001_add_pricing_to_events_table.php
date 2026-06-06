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
            $table->boolean('is_paid')->default(false)->after('waitlist_enabled');
            $table->decimal('price', 10, 2)->nullable()->after('is_paid')->comment('Base price, null if free');
            $table->char('currency', 3)->nullable()->default('XOF')->after('price')->comment('ISO 4217 currency code');
            $table->string('promo_code', 50)->nullable()->after('currency');
            $table->enum('promo_discount_type', ['percent', 'fixed'])->nullable()->after('promo_code');
            $table->decimal('promo_discount_value', 10, 2)->nullable()->after('promo_discount_type');
            $table->dateTime('promo_expires_at')->nullable()->after('promo_discount_value');
            $table->unsignedInteger('promo_max_uses')->nullable()->after('promo_expires_at')->comment('null = unlimited');
            $table->unsignedInteger('promo_uses_count')->default(0)->after('promo_max_uses');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn([
                'is_paid', 'price', 'currency',
                'promo_code', 'promo_discount_type', 'promo_discount_value',
                'promo_expires_at', 'promo_max_uses', 'promo_uses_count',
            ]);
        });
    }
};
