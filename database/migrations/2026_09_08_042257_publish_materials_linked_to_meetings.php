<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DATA-04 / DATA-06 (legacy) — preserve prior student-visible material access.
        DB::table('materials')->update(['is_published' => false]);
        DB::table('materials')
            ->whereIn('id', DB::table('meeting_materials')->select('material_id'))
            ->update(['is_published' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible: previous publication state did not exist.
    }
};
