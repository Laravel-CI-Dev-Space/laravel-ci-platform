<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255);
            $table->string('whatsapp', 30)->nullable();
            $table->string('photo')->nullable()->comment('Path relative to public/assets/web/img/guests/');
            $table->enum('status', ['confirmed', 'waitlisted', 'cancelled'])->default('confirmed');
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('promo_code_used', 50)->nullable();
            $table->decimal('discount_applied', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'free'])->default('free');
            $table->string('ticket_number', 30)->nullable()->unique();
            $table->string('ticket_qr_token', 64)->nullable()->unique();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('event_id');
            $table->index('status');
            $table->index('email');
            $table->unique(['event_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_registrations');
    }
};
