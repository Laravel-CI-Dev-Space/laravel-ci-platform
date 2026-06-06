<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_stats', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 100)->comment('Font Awesome class e.g. fa-solid fa-users');
            $table->string('label', 100)->comment('e.g. Members, Questions');
            $table->unsignedInteger('value')->default(0)
                ->comment('Manual override value');
            $table->string('suffix', 10)->default('+')
                ->comment('e.g. + or empty');
            $table->boolean('auto_count')->default(false)
                ->comment('If true, count from DB instead of manual value');
            $table->string('model', 100)->nullable()
                ->comment('Model class name for auto count e.g. App\Models\User');
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_stats');
    }
};
