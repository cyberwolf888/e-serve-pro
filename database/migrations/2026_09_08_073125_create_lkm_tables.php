<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // DATA-25..27 / FR-GR-15 / FR-SW-08 / BR-09 / M7.9
    public function up(): void
    {
        Schema::create('lkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('lkm_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lkm_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('instructions');
            $table->text('description');
            $table->json('sop_items');
            $table->timestamps();
            $table->unique(['lkm_id', 'name']);
        });

        Schema::create('lkm_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lkm_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lkm_role_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained('users')->restrictOnDelete();
            $table->string('proof_url', 1024)->nullable();
            $table->timestamp('proof_submitted_at')->nullable()->index();
            $table->json('sop_checks')->nullable();
            $table->timestamp('reflection_submitted_at')->nullable()->index();
            $table->timestamps();
            $table->unique(['lkm_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lkm_assignments');
        Schema::dropIfExists('lkm_roles');
        Schema::dropIfExists('lkms');
    }
};
