// database/migrations/2026_05_28_000009_create_resources_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->enum('type', ['boilerplate', 'cheatsheet', 'guide', 'pdf', 'other']);
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->comment('Size in bytes');
            $table->string('mime_type', 100);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->boolean('is_public')->default(true)->comment('False = members only');
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
