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
        // DATA-27 / FR-GR-11 / FR-GR-12 / FR-GR-15
        Schema::table('lkm_assignments', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->nullable()->after('reflection_submitted_at');
        });

        Schema::table('grade_components', function (Blueprint $table) {
            $table->foreignId('lkm_id')->nullable()->unique()->after('quiz_id')->constrained('lkms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_components', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lkm_id');
        });

        Schema::table('lkm_assignments', function (Blueprint $table) {
            $table->dropColumn('score');
        });
    }
};
