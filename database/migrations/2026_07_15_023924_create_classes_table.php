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
        // DATA-02 / FR-GR-02 / BR-01 / M3
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users')->restrictOnDelete();
            $table->string('name');
            $table->string('class_code', 16)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
