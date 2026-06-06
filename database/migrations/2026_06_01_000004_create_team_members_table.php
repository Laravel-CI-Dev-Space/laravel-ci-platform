<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('role', 150)->comment('e.g. Founder & Architect');
            $table->string('avatar')->nullable()
                ->comment('Path in public/assets/web/img/team/');
            $table->string('avatar_initials', 5)->nullable()
                ->comment('e.g. SB — fallback if no avatar');
            $table->string('avatar_color', 20)->default('av-1')
                ->comment('CSS class for avatar color e.g. av-1 to av-6');
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
