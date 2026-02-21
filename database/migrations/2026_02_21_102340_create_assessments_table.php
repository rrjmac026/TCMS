<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            // Links to which enrollment (trainee + course combo) is being assessed
            $table->foreignId('enrollment_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Links to the trainer who conducted the assessment
            // This gets filled by auth()->id() in the controller
            $table->foreignId('trainer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->decimal('score', 5, 2)->nullable(); // e.g. 98.50
            $table->text('remarks')->nullable();

            // Only two possible values based on TESDA standards
            $table->enum('result', ['competent', 'not_yet_competent']);

            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};