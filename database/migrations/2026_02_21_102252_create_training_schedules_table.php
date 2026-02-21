<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();

            // Which course this schedule belongs to
            $table->foreignId('course_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Which trainer is assigned to this schedule
            // Uses constrained('users') because trainer_id points to users table
            $table->foreignId('trainer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->time('time_start');
            $table->time('time_end');

            $table->string('location')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])
              ->default('upcoming');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_schedules');
    }
};