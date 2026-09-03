<?php

// DATA-23 / DATA-24 / FR-SA-07 / FR-GR-14 / FR-SW-07 / BR-05 / M7.8

namespace Tests\Feature\Discussions;

use App\Models\ClassMember;
use App\Models\DiscussionComment;
use App\Models\DiscussionTopic;
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

        $response = $this->actingAs($guru)->post(route('guru.classes.discussions.store', $class), [
            'title' => 'Struktur Teks Eksposisi',
            'body' => 'Jelaskan tesis, argumentasi, dan penegasan ulang.',
        ]);

        $discussion = DiscussionTopic::sole();
        $response->assertRedirect(route('guru.classes.discussions.show', [$class, $discussion]));
        $this->assertDatabaseHas('discussion_topics', [
            'class_id' => $class->id,
            'author_id' => $guru->id,
            'title' => 'Struktur Teks Eksposisi',
        ]);
    }

    public function test_topic_validation_rejects_empty_and_oversized_content(): void
    {
        $guru = $this->user('guru');
        $class = $this->schoolClass($guru);

        $this->actingAs($guru)
            ->from(route('guru.classes.discussions.create', $class))
            ->post(route('guru.classes.discussions.store', $class), [
                'title' => '',
                'body' => str_repeat('a', 10001),
            ])
            ->assertRedirect(route('guru.classes.discussions.create', $class))
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
        $discussion = $this->topic($class, $owner);

        $this->actingAs($outsider)
            ->get(route('siswa.classes.discussions.index', $class))
            ->assertForbidden();
        $this->actingAs($outsider)
            ->post(route('siswa.classes.discussions.comments.store', [$class, $discussion]), ['body' => 'Tidak boleh'])
            ->assertForbidden();
        $this->actingAs($otherGuru)
            ->post(route('guru.classes.discussions.store', $class), ['title' => 'Tidak boleh', 'body' => 'Tidak boleh'])
            ->assertForbidden();
    }

    public function test_scoped_bindings_reject_cross_class_topic_and_comment_ids(): void
    {
        $guru = $this->user('guru');
        $firstClass = $this->schoolClass($guru);
        $secondClass = $this->schoolClass($guru);
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
            ->post(route('guru.classes.discussions.store', $class), ['title' => 'Tidak boleh', 'body' => 'Tidak boleh'])
            ->assertForbidden();

        $class->update(['is_active' => true]);
        $student->update(['is_active' => false]);
        $this->actingAs($student)
            ->post(route('siswa.classes.discussions.comments.store', [$class, $discussion]), ['body' => 'Tetap tidak boleh'])
            ->assertForbidden();

        $this->assertDatabaseEmpty('discussion_comments');
    }
}
