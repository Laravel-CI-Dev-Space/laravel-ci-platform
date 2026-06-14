<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('recap_summary')->nullable()->after('cancellation_reason');
            $table->longText('recap_content')->nullable()->after('recap_summary');
            $table->string('recap_video_url_1')->nullable()->after('recap_content');
            $table->string('recap_video_url_2')->nullable()->after('recap_video_url_1');
            $table->string('recap_video_url_3')->nullable()->after('recap_video_url_2');
            $table->string('recap_document_path')->nullable()->after('recap_video_url_3');
            $table->string('recap_document_name')->nullable()->after('recap_document_path');
            $table->timestamp('recap_published_at')->nullable()->after('recap_document_name');
            $table->foreignId('recap_published_by')->nullable()->after('recap_published_at')
                ->constrained('users')->nullOnDelete();

            $table->index('recap_published_at');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recap_published_by');
            $table->dropIndex(['recap_published_at']);
            $table->dropColumn([
                'recap_summary',
                'recap_content',
                'recap_video_url_1',
                'recap_video_url_2',
                'recap_video_url_3',
                'recap_document_path',
                'recap_document_name',
                'recap_published_at',
            ]);
        });
    }
};
