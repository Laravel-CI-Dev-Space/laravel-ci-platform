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
        Schema::table('company_registration_requests', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('website')->comment('Logo de la company uploadé lors de la demande');
        });
    }

    public function down(): void
    {
        Schema::table('company_registration_requests', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
    }
};
