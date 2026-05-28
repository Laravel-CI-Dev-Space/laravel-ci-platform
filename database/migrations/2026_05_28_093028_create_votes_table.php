// database/migrations/2026_05_28_000005_create_votes_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->morphs('votable'); // votable_id + votable_type + index
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('value')->comment('1 = upvote, -1 = downvote');
            $table->timestamps();

            // Un seul vote par user par entité
            $table->unique(['votable_id', 'votable_type', 'user_id'], 'votes_unique');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
