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
        Schema::table('discussion_topics', function (Blueprint $table) {
            // DATA-23 / FR-GR-14 / FR-SW-07 / M7.8
            $table->foreignId('material_id')->nullable()->after('class_id')->constrained()->nullOnDelete();
            $table->index(['material_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussion_topics', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropIndex(['material_id', 'created_at']);
            $table->dropColumn('material_id');
        });
    }
};
