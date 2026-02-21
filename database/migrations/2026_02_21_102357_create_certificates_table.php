<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            // Links to the enrollment that earned this certificate
            $table->foreignId('enrollment_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Unique certificate number e.g. "TESDA-2026-00001"
            $table->string('certificate_number')->unique();

            $table->date('issued_at');
            $table->date('expires_at')->nullable(); // some certs don't expire

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};