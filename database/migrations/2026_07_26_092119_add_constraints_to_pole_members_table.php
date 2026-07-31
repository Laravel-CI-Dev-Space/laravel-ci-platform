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
        // Un email ne peut apparaître qu'une seule fois dans pole_members (1 personne = 1 pôle)
        Schema::table('pole_members', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('pole_members', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }
};
