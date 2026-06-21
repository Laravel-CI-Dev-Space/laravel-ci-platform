<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analytics_page_views', function (Blueprint $table): void {
            $table->string('method', 10)->default('GET')->after('browser');
            $table->string('route_name', 200)->nullable()->after('method');
            $table->unsignedSmallInteger('status_code')->default(200)->after('route_name');
            $table->unsignedInteger('duration_ms')->nullable()->after('status_code');

            $table->index('duration_ms');
            $table->index(['route_name', 'duration_ms']);
        });
    }

    public function down(): void
    {
        Schema::table('analytics_page_views', function (Blueprint $table): void {
            $table->dropIndex(['duration_ms']);
            $table->dropIndex(['route_name', 'duration_ms']);
            $table->dropColumn(['method', 'route_name', 'status_code', 'duration_ms']);
        });
    }
};
