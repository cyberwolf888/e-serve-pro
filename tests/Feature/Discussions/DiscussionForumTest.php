<?php

// DATA-23 / DATA-24 / FR-SA-07 / FR-GR-14 / FR-SW-07 / BR-05 / M7.8

namespace Tests\Feature\Discussions;

use App\Models\ClassMember;
use App\Models\DiscussionComment;
use App\Models\DiscussionTopic;
use App\Models\Material;
use App\Models\SchoolClass;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscussionForumTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RoleSeeder::class;

    private function user(string $role, bool $active = true): User
    {
        $user = User::factory()->create(['is_active' => $active]);
        $user->assignRole($role);

        return $user;
    }

    private function schoolClass(User $guru, array $data = []): SchoolClass
    {
        return SchoolClass::create($data + [
            'guru_id' => $guru->id,
            'name' => 'Bahasa Indonesia',
            'class_code' => fake()->unique()->bothify('KLS#####'),
            'is_active' => true,
        ]);
    }

    private function join(SchoolClass $class, User $student): void
    {
        ClassMember::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'joined_at' => now(),
        ]);
    }

    private function material(SchoolClass $class, array $data = []): Material
    {
        return Material::create($data + [
            'class_id' => $class->id,
            'title' => 'Materi Teks Eksposisi',
            'type' => 'figma',
            'figma_url' => 'https://figma.com/file/exposition',
            'is_published' => true,
        ]);
    }

    private function topic(SchoolClass $class, User $guru, array $data = []): DiscussionTopic
    {
        return DiscussionTopic::create($data + [
            'class_id' => $class->id,
            'author_id' => $guru->id,
            'title' => 'Topik Teks Eksposisi',
            'body' => 'Apa ciri utama teks eksposisi?',
        ]);
    }

    public function test_guru_creates_topic_for_owned_class(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $material = $this->material($class);

        $response = $this->actingAs($guru)->post(route('guru.classes.materials.discussions.store', [$class, $material]), [
            'title' => 'Struktur Teks Eksposisi',
            'body' => 'Jelaskan tesis, argumentasi, dan penegasan ulang.',
        ]);

        $discussion = DiscussionTopic::sole();
        $response->assertRedirect(route('guru.classes.discussions.show', [$class, $discussion]));
        $this->assertDatabaseHas('discussion_topics', [
            'class_id' => $class->id,
            'material_id' => $material->id,
            'author_id' => $guru->id,
            'title' => 'Struktur Teks Eksposisi',
        ]);
    }

    public function test_enrolled_student_creates_topic_for_published_material(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $material = $this->material($class);
        $this->join($class, $student);

        $this->actingAs($student)->post(route('siswa.classes.materials.discussions.store', [$class, $material]), [
            'title' => 'Pertanyaan Siswa',
            'body' => 'Bagaimana membedakan fakta dan opini?',
        ])->assertRedirect();

        $this->assertDatabaseHas('discussion_topics', [
            'class_id' => $class->id,
            'material_id' => $material->id,
            'author_id' => $student->id,
            'title' => 'Pertanyaan Siswa',
        ]);
    }

    public function test_topic_validation_rejects_empty_and_oversized_content(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $material = $this->material($class);

        $this->actingAs($guru)
            ->from(route('guru.classes.materials.discussions.create', [$class, $material]))
            ->post(route('guru.classes.materials.discussions.store', [$class, $material]), [
                'title' => '',
                'body' => str_repeat('a', 10001),
            ])
            ->assertRedirect(route('guru.classes.materials.discussions.create', [$class, $material]))
            ->assertSessionHasErrors(['title', 'body']);

        $this->actingAs($guru)
            ->post(route('guru.classes.materials.discussions.store', [$class, $material]), [
                'title' => str_repeat('a', 256),
                'body' => '',
            ])
            ->assertSessionHasErrors(['title', 'body']);

        $this->assertDatabaseEmpty('discussion_topics');
    }

    public function test_joined_student_views_topic_and_posts_comment(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $discussion = $this->topic($class, $guru);

        $this->actingAs($student)
            ->get(route('siswa.classes.discussions.show', [$class, $discussion]))
            ->assertOk()
            ->assertSee($discussion->title);

        $this->actingAs($student)
            ->post(route('siswa.classes.discussions.comments.store', [$class, $discussion]), [
                'body' => 'Tesis menyatakan pendapat awal penulis.',
            ])
            ->assertRedirect(route('siswa.classes.discussions.show', [$class, $discussion]));

        $this->assertDatabaseHas('discussion_comments', [
            'discussion_topic_id' => $discussion->id,
            'author_id' => $student->id,
            'body' => 'Tesis menyatakan pendapat awal penulis.',
        ]);
    }

    public function test_guru_comments_and_comments_are_shown_oldest_first(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $discussion = $this->topic($class, $guru);
        DiscussionComment::create([
            'discussion_topic_id' => $discussion->id,
            'author_id' => $student->id,
            'body' => 'Komentar lebih awal',
            'created_at' => now()->subMinute(),
        ]);

        $this->actingAs($guru)->post(route('guru.classes.discussions.comments.store', [$class, $discussion]), [
            'body' => 'Komentar lebih akhir',
        ])->assertRedirect(route('guru.classes.discussions.show', [$class, $discussion]));

        $this->actingAs($guru)
            ->get(route('guru.classes.discussions.show', [$class, $discussion]))
            ->assertOk()
            ->assertSeeInOrder(['Komentar lebih awal', 'Komentar lebih akhir']);
    }

    public function test_non_members_and_other_gurus_cannot_access_or_write(): void
    {
        $owner = $this->user('guru');
        $otherGuru = $this->user('guru');
        $outsider = $this->user('siswa');
        $class = $this->schoolClass($owner);
        $material = $this->material($class);
        $discussion = $this->topic($class, $owner);

        $this->actingAs($outsider)
            ->get(route('siswa.classes.discussions.index', $class))
            ->assertForbidden();
        $this->actingAs($outsider)
            ->post(route('siswa.classes.discussions.comments.store', [$class, $discussion]), ['body' => 'Tidak boleh'])
            ->assertForbidden();
        $this->actingAs($outsider)
            ->post(route('siswa.classes.materials.discussions.store', [$class, $material]), ['title' => 'Tidak boleh', 'body' => 'Tidak boleh'])
            ->assertForbidden();
        $this->actingAs($otherGuru)
            ->post(route('guru.classes.materials.discussions.store', [$class, $material]), ['title' => 'Tidak boleh', 'body' => 'Tidak boleh'])
            ->assertForbidden();
    }

    public function test_scoped_bindings_reject_cross_class_topic_and_comment_ids(): void
    {
        $guru = $this->user('guru');
        $firstClass = $this->schoolClass($guru);
        $secondClass = $this->schoolClass($guru);
        $secondMaterial = $this->material($secondClass);
        $firstTopic = $this->topic($firstClass, $guru);
        $secondTopic = $this->topic($secondClass, $guru, ['title' => 'Topik Kedua']);
        $comment = DiscussionComment::create([
            'discussion_topic_id' => $secondTopic->id,
            'author_id' => $guru->id,
            'body' => 'Komentar topik kedua',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.classes.discussions.show', [$firstClass, $secondTopic]))
            ->assertNotFound();
        $this->actingAs($guru)
            ->delete(route('guru.classes.discussions.comments.destroy', [$firstClass, $firstTopic, $comment]))
            ->assertNotFound();
        $this->actingAs($guru)
            ->post(route('guru.classes.materials.discussions.store', [$firstClass, $secondMaterial]), [
                'title' => 'Lintas kelas',
                'body' => 'Tidak boleh dibuat.',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('discussion_comments', ['id' => $comment->id]);
    }

    public function test_guru_and_super_admin_moderate_comments_but_student_cannot(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $admin = $this->user('super_admin');
        $class = $this->schoolClass($guru);
        $this->join($class, $student);
        $discussion = $this->topic($class, $guru);
        $studentComment = DiscussionComment::create([
            'discussion_topic_id' => $discussion->id,
            'author_id' => $student->id,
            'body' => 'Komentar siswa',
        ]);

        $this->actingAs($guru)
            ->get(route('guru.classes.discussions.show', [$class, $discussion]))
            ->assertOk()
            ->assertSee('data-kt-modal-toggle="#confirm_status_modal"', false)
            ->assertDontSee('return confirm(', false);

        $this->actingAs($student)
            ->delete(route('guru.classes.discussions.comments.destroy', [$class, $discussion, $studentComment]))
            ->assertForbidden();

        $this->actingAs($guru)
            ->delete(route('guru.classes.discussions.comments.destroy', [$class, $discussion, $studentComment]))
            ->assertRedirect(route('guru.classes.discussions.show', [$class, $discussion]));
        $this->assertDatabaseMissing('discussion_comments', ['id' => $studentComment->id]);

        $guruComment = DiscussionComment::create([
            'discussion_topic_id' => $discussion->id,
            'author_id' => $guru->id,
            'body' => 'Komentar guru',
        ]);
        $this->actingAs($admin)
            ->get(route('admin.classes.discussions.show', [$class, $discussion]))
            ->assertOk()
            ->assertSee('Komentar guru');
        $this->actingAs($admin)
            ->delete(route('admin.classes.discussions.comments.destroy', [$class, $discussion, $guruComment]))
            ->assertRedirect(route('admin.classes.discussions.show', [$class, $discussion]));
        $this->assertDatabaseMissing('discussion_comments', ['id' => $guruComment->id]);
    }

    public function test_inactive_classes_and_users_are_read_only(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $material = $this->material($class);
        $this->join($class, $student);
        $discussion = $this->topic($class, $guru);
        $class->update(['is_active' => false]);

        $this->actingAs($student)
            ->get(route('siswa.classes.discussions.show', [$class, $discussion]))
            ->assertOk();
        $this->actingAs($student)
            ->post(route('siswa.classes.discussions.comments.store', [$class, $discussion]), ['body' => 'Tidak boleh'])
            ->assertForbidden();
        $this->actingAs($guru)
            ->post(route('guru.classes.materials.discussions.store', [$class, $material]), ['title' => 'Tidak boleh', 'body' => 'Tidak boleh'])
            ->assertForbidden();

        $class->update(['is_active' => true]);
        $student->update(['is_active' => false]);
        $this->actingAs($student)
            ->post(route('siswa.classes.materials.discussions.store', [$class, $material]), ['title' => 'Tetap tidak boleh', 'body' => 'Tetap tidak boleh'])
            ->assertForbidden();
        $this->actingAs($student)
            ->post(route('siswa.classes.discussions.comments.store', [$class, $discussion]), ['body' => 'Tetap tidak boleh'])
            ->assertForbidden();

        $this->assertDatabaseEmpty('discussion_comments');
    }

    public function test_draft_material_blocks_new_topics_for_guru_and_student(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $material = $this->material($class, ['is_published' => false]);
        $this->join($class, $student);

        foreach ([[$guru, 'guru'], [$student, 'siswa']] as [$user, $prefix]) {
            $this->actingAs($user)
                ->post(route($prefix.'.classes.materials.discussions.store', [$class, $material]), [
                    'title' => 'Topik Draf',
                    'body' => 'Tidak boleh dibuat.',
                ])
                ->assertForbidden();
        }

        $this->assertDatabaseEmpty('discussion_topics');
    }

    public function test_unpublished_linked_topic_is_hidden_and_blocked_from_student_only(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $admin = $this->user('super_admin');
        $class = $this->schoolClass($guru);
        $material = $this->material($class);
        $this->join($class, $student);
        $discussion = $this->topic($class, $guru, [
            'material_id' => $material->id,
            'title' => 'Topik Materi Rahasia',
        ]);

        $this->actingAs($student)->get(route('siswa.classes.show', $class))
            ->assertOk()
            ->assertSee('Topik Materi Rahasia');

        $material->update(['is_published' => false]);

        $this->actingAs($student)->get(route('siswa.classes.show', $class))
            ->assertOk()
            ->assertDontSee('Topik Materi Rahasia');
        $this->actingAs($student)
            ->get(route('siswa.classes.materials.discussions.index', [$class, $material]))
            ->assertForbidden();
        $this->actingAs($student)
            ->get(route('siswa.classes.discussions.show', [$class, $discussion]))
            ->assertForbidden();
        $this->actingAs($student)
            ->post(route('siswa.classes.discussions.comments.store', [$class, $discussion]), ['body' => 'Tidak boleh'])
            ->assertForbidden();

        $this->actingAs($guru)
            ->get(route('guru.classes.materials.discussions.index', [$class, $material]))
            ->assertOk()
            ->assertSee('Topik Materi Rahasia');
        $this->actingAs($admin)
            ->get(route('admin.classes.materials.discussions.index', [$class, $material]))
            ->assertOk()
            ->assertSee('Topik Materi Rahasia')
            ->assertDontSee('Mulai Diskusi');
        $this->actingAs($admin)
            ->get(route('admin.classes.discussions.show', [$class, $discussion]))
            ->assertOk()
            ->assertSee('Topik Materi Rahasia');
    }

    public function test_material_preview_shows_latest_three_and_nested_index_is_paginated(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);
        $material = $this->material($class, ['title' => 'Materi Pertama']);
        $otherMaterial = $this->material($class, ['title' => 'Materi Kedua']);

        foreach (range(1, 12) as $number) {
            $this->topic($class, $guru, [
                'material_id' => $material->id,
                'title' => 'Topik Nomor '.$number,
                'created_at' => now()->addMinutes($number),
            ]);
        }
        $this->topic($class, $guru, [
            'material_id' => $otherMaterial->id,
            'title' => 'Topik Materi Kedua',
        ]);

        $this->actingAs($guru)->get(route('guru.classes.materials.index', $class))
            ->assertOk()
            ->assertSeeInOrder(['Topik Nomor 12', 'Topik Nomor 11', 'Topik Nomor 10'])
            ->assertDontSee('Topik Nomor 9')
            ->assertSee('Topik Materi Kedua');

        $this->actingAs($guru)
            ->get(route('guru.classes.materials.discussions.index', [$class, $material]))
            ->assertOk()
            ->assertSee('Topik Nomor 12')
            ->assertSee('Topik Nomor 3')
            ->assertDontSee('Topik Nomor 2')
            ->assertSee('Menampilkan 1–10 dari 12 topik');
    }

    public function test_deleted_material_topics_and_comments_become_general_discussions(): void
    {
        $guru = $this->user('guru');
        $student = $this->user('siswa');
        $class = $this->schoolClass($guru);
        $material = $this->material($class);
        $this->join($class, $student);
        $discussion = $this->topic($class, $guru, [
            'material_id' => $material->id,
            'title' => 'Topik Materi Terhapus',
        ]);
        $comment = DiscussionComment::create([
            'discussion_topic_id' => $discussion->id,
            'author_id' => $student->id,
            'body' => 'Komentar tetap tersimpan.',
        ]);

        $this->actingAs($guru)
            ->delete(route('guru.classes.materials.destroy', [$class, $material]))
            ->assertRedirect(route('guru.classes.materials.index', $class));

        $this->assertNull($discussion->fresh()->material_id);
        $this->assertModelExists($comment);
        $this->actingAs($student)
            ->get(route('siswa.classes.discussions.index', $class))
            ->assertOk()
            ->assertSee('Topik Materi Terhapus')
            ->assertSee('Diskusi Umum');
        $this->actingAs($student)
            ->get(route('siswa.classes.discussions.show', [$class, $discussion]))
            ->assertOk()
            ->assertSee('Komentar tetap tersimpan.');
    }
}
