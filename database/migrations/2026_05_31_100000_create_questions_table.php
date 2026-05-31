<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->boolean('pinned')->default(false);
            $table->boolean('closed')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->softDeletes();

            $table->timestamps();

            $table->index(['pinned', 'created_at']);
            $table->index(['pinned', 'views']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
