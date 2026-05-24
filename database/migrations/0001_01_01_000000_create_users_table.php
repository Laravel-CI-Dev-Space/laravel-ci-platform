<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * is_active       → false = permanently banned, blocked at login
     * suspended_until → null = not suspended, future date = temporarily suspended
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Basic info pulled from GitHub OAuth
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();

            // GitHub OAuth — Socialite
            $table->string('github_id')->unique();
            $table->string('github_username')->unique();

            // Account status
            $table->boolean('is_active')->default(true);     // false = permanently banned
            $table->timestamp('suspended_until')->nullable(); // null = not suspended

            // Indexed for frequent status queries
            $table->index('is_active');
            $table->index('suspended_until');

            // Activity tracking
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps(); // created_at = registration date
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
