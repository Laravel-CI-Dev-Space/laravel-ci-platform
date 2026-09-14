<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();

            // Type de média
            $table->enum('type', ['photo', 'video'])->default('photo');

            // Stockage R2
            $table->string('disk')->default('r2');
            $table->string('path');                        // chemin dans le bucket : events/{id}/photos/filename.jpg
            $table->string('original_name');               // nom de fichier original
            $table->string('mime_type')->nullable();       // image/jpeg, video/mp4…
            $table->unsignedBigInteger('file_size')->nullable(); // en octets

            // Thumbnail (photos uniquement — généré à l'upload)
            $table->string('thumbnail_path')->nullable();  // events/{id}/thumbs/filename.jpg

            // Métadonnées
            $table->string('caption', 255)->nullable();
            $table->unsignedSmallInteger('order')->default(0);

            $table->timestamps();

            $table->index(['event_id', 'type', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_media');
    }
};
