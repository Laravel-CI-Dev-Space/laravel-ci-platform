// database/migrations/2026_05_28_000007_create_articles_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->comment('Admin who validated');
            $table->string('title', 300);
            $table->string('slug', 350)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->longText('body_html')->nullable()->comment('Compiled Markdown HTML cached');
            $table->string('cover_image')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->boolean('newsletter_sent')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('level');
            $table->index('published_at');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
