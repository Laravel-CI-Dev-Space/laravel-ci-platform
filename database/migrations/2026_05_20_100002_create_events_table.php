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
            $table->string('title');
            $table->longText('description');
            $table->foreignId('type_id')->constrained('event_types')->cascadeOnDelete();
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->unsignedInteger('capacity')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->index('status');
            $table->index('start_date');
            $table->index(['status', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
