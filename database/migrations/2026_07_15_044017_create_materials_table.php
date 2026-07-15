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
        // DATA-04 / FR-GR-04 / FR-GR-05 / BR-04 / M4
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['figma', 'file']);
            $table->string('figma_url', 1024)->nullable();
            $table->string('file_path', 1024)->nullable();
            $table->unsignedInteger('file_size_kb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
