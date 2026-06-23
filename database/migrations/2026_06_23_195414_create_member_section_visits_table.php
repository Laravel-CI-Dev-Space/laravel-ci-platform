<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_section_visits', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->string('section', 32);
            $table->timestamp('visited_at');

            $table->primary(['user_id', 'section']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_section_visits');
    }
};
