<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            // Unique course code e.g. "SMAW-NC-II"
            $table->string('code')->unique();

            $table->string('name');
            $table->text('description')->nullable();

            // Total training hours required to complete the course
            $table->unsignedInteger('duration_hours');

            // TESDA qualification levels
            $table->enum('level', ['NC I', 'NC II', 'NC III', 'NC IV', 'COC'])->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};