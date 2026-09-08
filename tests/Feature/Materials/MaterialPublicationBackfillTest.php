<?php

// DATA-04 / DATA-06 (legacy) / FR-SW-04

namespace Tests\Feature\Materials;

use App\Models\Material;
use App\Models\Meeting;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaterialPublicationBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_materials_linked_to_legacy_meetings_are_published(): void
    {
        $guru = User::factory()->create();
        $class = SchoolClass::create([
            'guru_id' => $guru->id,
            'name' => 'Bahasa Indonesia',
            'class_code' => 'KELAS001',
            'is_active' => true,
        ]);
        $linked = Material::create([
            'class_id' => $class->id,
            'title' => 'Materi Lama Terhubung',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/linked',
        ]);
        $unlinked = Material::create([
            'class_id' => $class->id,
            'title' => 'Materi Lama Lepas',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/unlinked',
            'is_published' => true,
        ]);
        $meeting = Meeting::create([
            'class_id' => $class->id,
            'title' => 'Pertemuan Lama',
            'scheduled_at' => now(),
        ]);
        $meeting->materials()->attach($linked);

        $migration = require database_path('migrations/2026_09_08_042257_publish_materials_linked_to_meetings.php');
        $migration->up();

        $this->assertTrue($linked->fresh()->is_published);
        $this->assertFalse($unlinked->fresh()->is_published);
    }
}
