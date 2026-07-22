# Admin Class Access Plan

1. **Share guru class-detail UI**
   - Files: `resources/views/admin/classes/show.blade.php`, `resources/views/guru/classes/show.blade.php`, `resources/views/guru/classes/_tabs.blade.php`
   - Make admin render same teacher layout/tabs with admin route prefix.
   - Include: Detail, Materi, Pertemuan, Kuis, Nilai, tambah siswa.
   - Replace `is_active` UI gates with policy checks so Super Admin controls remain visible for inactive/owner-inactive classes.

2. **Add Super Admin teacher-feature routes**
   - File: `routes/web.php`
   - Add admin nested routes for meetings, attendance, quizzes/questions, grade components, scores, calculation, recap/export.
   - Reuse existing guru controllers/views where possible; role-aware route generation prevents redirects into `/guru`.

3. **Make teacher controllers route-prefix aware**
   - Files: guru material/meeting/attendance/quiz/question controllers; grade/recap controllers.
   - Return same teacher UI under `/admin`, routes generated from authenticated role.
   - Keep nested-resource class matching and policy authorization.

4. **Grant true all-class Super Admin access**
   - Files: `SchoolClassPolicy`, `MaterialPolicy`, `MeetingPolicy`, `QuizPolicy`, `QuizQuestionPolicy`, `GradeComponentPolicy`.
   - Super Admin bypasses class-active and assigned-guru-active guards.
   - Guru stays restricted to own active class with active owner.
   - Preserve quiz integrity locks: no question edits after publish/attempt; no delete published/attempted quiz.

5. **Tests + task record**
   - Files: existing feature tests for classes/materials/meetings/quizzes/grading; `todos.md`.
   - Cover Super Admin managing another guru’s active and inactive class across five areas.
   - Cover guru still blocked from another guru’s class; quiz locks still block Super Admin.
   - Tick/update deferred Super Admin CRUD task. Run targeted tests + `./vendor/bin/pint`.

## Risks

- Hard-coded `guru.*` redirects/views.
- Inactive-class UI hiding actions despite policy access.
