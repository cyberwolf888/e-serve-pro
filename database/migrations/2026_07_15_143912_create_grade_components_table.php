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
        // DATA-13 / FR-GR-12 / BR-03 / M6
        Schema::create('grade_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('weight', 5, 2);
            // ASSUMPTION: stakeholder-approved M6 link for quiz score auto-fill.
            $table->foreignId('quiz_id')->nullable()->unique()->constrained('quizzes')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_components');
    }
};
