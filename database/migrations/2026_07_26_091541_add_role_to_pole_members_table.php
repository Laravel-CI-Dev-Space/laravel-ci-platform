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
        Schema::table('pole_members', function (Blueprint $table) {
            $table->enum('role', ['responsable', 'adjoint', 'membre'])->default('membre')->after('poste');
        });
    }

    public function down(): void
    {
        Schema::table('pole_members', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
