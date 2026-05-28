// database/migrations/2026_05_28_000002_create_questions_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('title', 300);
        $table->string('slug', 350)->unique();
        $table->longText('body');
        $table->longText('body_html')->nullable()->comment('Compiled Markdown HTML cached');
        $table->enum('status', ['published', 'hidden', 'closed', 'deleted'])->default('published');
        $table->boolean('is_pinned')->default(false);
        // accepted_answer_id ajouté APRÈS answers dans add_accepted_answer_id migration
        $table->unsignedInteger('views_count')->default(0);
        $table->integer('votes_score')->default(0)->comment('Sum of upvotes minus downvotes');
        $table->unsignedInteger('answers_count')->default(0)->comment('Cached count');
        $table->unsignedInteger('comments_count')->default(0)->comment('Cached count');
        $table->timestamp('last_activity_at')->nullable()->comment('Last answer or comment date');
        $table->timestamps();
        $table->softDeletes();

        $table->index('status');
        $table->index('is_pinned');
        $table->index('votes_score');
        $table->index('last_activity_at');
        $table->index(['user_id', 'status']);
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
