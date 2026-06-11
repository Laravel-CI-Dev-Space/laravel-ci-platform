<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->unsignedInteger('min_points')->default(0);
            $table->string('color', 20)->default('#6B7280');
            $table->string('icon', 60)->default('fa-solid fa-seedling');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index('order');
            $table->index('min_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
