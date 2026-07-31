<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pole_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pole_id')->constrained()->cascadeOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150);
            $table->string('poste', 150)->comment('Rôle dans le pôle, ex: Responsable, Membre');
            $table->enum('status', ['actif', 'inactif'])->default('actif');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pole_members');
    }
};
