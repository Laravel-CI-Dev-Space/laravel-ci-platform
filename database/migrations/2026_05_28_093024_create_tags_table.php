// database/migrations/2026_05_28_000001_create_tags_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->enum('scope', ['forum', 'blog', 'both'])->default('both');
            $table->string('color', 7)->nullable()->comment('Hex color code e.g. #FF6600');
            $table->unsignedInteger('usage_count')->default(0)->comment('Cached count for performance');
            $table->timestamps();

            $table->index('scope');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
