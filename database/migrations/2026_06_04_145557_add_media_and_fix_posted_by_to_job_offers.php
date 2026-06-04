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
        Schema::table('job_offers', function (Blueprint $table) {
            // posted_by devient nullable : les entreprises peuvent créer sans user lié
            $table->foreignId('posted_by')->nullable()->change();

            // Image de couverture et document joint
            $table->string('cover_image')->nullable()->after('is_urgent')->comment("Image de couverture de l'offre");
            $table->string('attachment_path')->nullable()->after('cover_image')->comment('Document joint (PDF/DOC) avec détails de l\'offre');
            $table->string('attachment_name')->nullable()->after('attachment_path')->comment('Nom original du document');
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->foreignId('posted_by')->nullable(false)->change();
            $table->dropColumn(['cover_image', 'attachment_path', 'attachment_name']);
        });
    }
};
