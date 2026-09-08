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
        // DATA-04 / FR-SW-04
        Schema::table('materials', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->boolean('is_published')->default(false)->index()->after('file_size_kb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['is_published']);
            $table->dropColumn(['description', 'is_published']);
        });
    }
};
