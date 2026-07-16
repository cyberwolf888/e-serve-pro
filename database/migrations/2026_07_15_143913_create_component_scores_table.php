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
        // DATA-15 / FR-GR-11 / M6
        Schema::create('component_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_component_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
            $table->decimal('score', 5, 2);
            // ASSUMPTION: preserve a teacher's manual quiz-score override.
            $table->boolean('is_manual_override')->default(false);
            $table->timestamps();

            $table->unique(['grade_component_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_scores');
    }
};
