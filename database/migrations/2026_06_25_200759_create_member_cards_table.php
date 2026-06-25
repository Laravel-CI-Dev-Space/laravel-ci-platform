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
        Schema::create('member_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('level')->default(1)->comment('1=Initié, 2=Bâtisseur, 3=Maître Artisan');
            $table->string('card_avatar')->nullable()->comment('Avatar redéfini spécifiquement pour la carte');
            $table->string('poste', 120)->nullable()->comment('Titre / poste affiché sur la carte');
            $table->text('qr_code_svg')->nullable()->comment('SVG du QR code généré une fois, réutilisé');
            $table->boolean('is_active')->default(false);
            $table->boolean('forced_by_admin')->default(false)->comment('Activée manuellement sans seuil de réputation');
            $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_cards');
    }
};
