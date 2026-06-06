<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_page_views', function (Blueprint $table): void {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path', 500);
            $table->string('query_string', 500)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
            $table->string('browser', 100)->nullable();
            $table->timestamp('created_at')->index();

            $table->index(['created_at', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_page_views');
    }
};
