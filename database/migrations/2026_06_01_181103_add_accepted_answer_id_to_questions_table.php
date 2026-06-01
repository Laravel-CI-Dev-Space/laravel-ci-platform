<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Ajouté après answers pour éviter la contrainte circulaire
            $table->foreignId('accepted_answer_id')
                ->nullable()
                ->after('is_pinned')
                ->constrained('answers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['accepted_answer_id']);
            $table->dropColumn('accepted_answer_id');
        });
    }
};
