<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_origin_sections', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow', 100)->nullable()
                ->comment('Small label above title e.g. Notre naissance');
            $table->string('title', 200);
            $table->longText('content')->comment('Rich text / HTML content');
            $table->enum('media_type', ['image', 'video', 'youtube', 'none'])
                ->default('none');
            $table->string('media_path')->nullable()
                ->comment('Image path or video path in public/assets/');
            $table->string('youtube_url')->nullable()
                ->comment('YouTube embed URL if media_type is youtube');
            $table->string('media_position', 10)->default('right')
                ->comment('left or right — image/video position relative to text');
            $table->string('caption')->nullable()
                ->comment('Optional caption under media');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_origin_sections');
    }
};
