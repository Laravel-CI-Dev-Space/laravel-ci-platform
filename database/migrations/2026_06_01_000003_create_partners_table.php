<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('logo')->nullable()
                ->comment('Path in public/assets/web/img/partners/');
            $table->string('icon', 100)->nullable()
                ->comment('Font Awesome class fallback if no logo');
            $table->string('url')->nullable();
            $table->string('type', 50)->default('community')
                ->comment('community/sponsor/institutional');
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
