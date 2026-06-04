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
        Schema::create('company_registration_requests', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('company_name', 200);
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->string('position', 100)->comment('Poste du responsable');
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('business_domain', 150)->comment("Domaine d'activité");
            $table->string('website')->nullable();
            $table->text('motivation')->nullable()->comment('Message de présentation');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_registration_requests');
    }
};
