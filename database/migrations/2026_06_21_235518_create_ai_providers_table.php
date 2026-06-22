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
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();               // claude | openai | grok
            $table->string('display_name');
            $table->string('base_url');                     // endpoint API
            $table->text('api_key')->nullable();            // chiffré via cast encrypted
            $table->boolean('is_active')->default(false);
            $table->unsignedTinyInteger('priority')->default(1); // ordre de fallback
            $table->json('extra_config')->nullable();       // org_id, headers custom, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
