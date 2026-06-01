// database/migrations/2026_05_28_000010_create_events_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('slug', 250)->unique();
            $table->text('description');
            $table->longText('program')->nullable()->comment('Detailed program / agenda');
            $table->enum('type', ['meetup', 'webinar', 'hackathon', 'conference', 'workshop']);
            $table->string('location')->nullable()->comment('Physical address');
            $table->string('online_url')->nullable()->comment('Zoom/Meet link');
            $table->string('cover_image')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedSmallInteger('capacity')->nullable()->comment('Max attendees, null = unlimited');
            $table->unsignedSmallInteger('registrations_count')->default(0)->comment('Cached count');
            $table->boolean('waitlist_enabled')->default(false);
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->text('cancellation_reason')->nullable();
            $table->boolean('reminder_7d_sent')->default(false);
            $table->boolean('reminder_1d_sent')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
            $table->index('starts_at');
            $table->index(['status', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
