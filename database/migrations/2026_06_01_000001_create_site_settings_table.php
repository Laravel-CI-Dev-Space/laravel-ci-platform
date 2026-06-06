<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group', 50)->default('general')
                ->comment('general/home/about/seo/social');
            $table->longText('value')->nullable();
            $table->string('type', 20)->default('text')
                ->comment('text/textarea/image/boolean/number/color/url/video');
            $table->string('label', 150)->comment('Label displayed in Filament');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
