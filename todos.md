# TODOs — PRO-BI SMART

> **Execution checklist for the AI coding agent.** Work top to bottom. Do not start a milestone until the previous milestone's **Gate** is checked. Every task cites the PRD ID it satisfies — put that ID in the related migration/controller/test.
> Source of truth: `PRD_PRO-BI_SMART_EN.md`. If a task conflicts with the PRD, the PRD wins (rule priority: `BR > DATA > FR > UI`).

**Legend:** `[ ]` todo · `[x]` done · `[~]` in progress · `[!]` blocked (note why)

---

## Global Conventions (apply to every task)

- [ ] Code, identifiers, comments = English. UI copy = Bahasa Indonesia.
- [ ] PSR-12 formatting; run `./vendor/bin/pint` before each commit.
- [ ] Every model/migration/controller/test comments the PRD ID it implements (e.g. `// FR-GR-05 / BR-04`).
- [ ] RBAC enforced server-side only — use Spatie role/permission middleware for role gates and `authorize()`/Policies for record-level checks.
- [ ] Add a feature test with each feature; happy-path + failure-path both required (Definition of Done).
- [ ] Commit per task or logical group; message references the PRD ID.
- [ ] Flag any guess with `// ASSUMPTION:` and log it in §Open Questions below.

---

## M0 — Scaffold

- [x] Create new Laravel 13.x app (`composer create-project`), confirm PHP 8.3+. `[§4]`
- [x] Configure MariaDB connection in `.env`; charset `utf8mb4`, collation `utf8mb4_unicode_ci`. `[§4]`
- [x] Install & configure Tailwind CSS via Vite. `[§4]`
- [x] Integrate Metronic 9.5.0 (Tailwind edition): assets, base layout, sidebar/topbar shell. `[NFR-08]`
- [x] Build a shared authenticated layout using Metronic components (no ad-hoc styling). `[NFR-08]`
- [x] Verify `npm run build` / `vite` compiles cleanly. `[§4]`
- [x] Set up testing (PHPUnit/Pest), CI-friendly `phpunit.xml`, a `smoke` test that boots the app.
- [x] **Do NOT enable Octane yet** (deferred to M8). `[§12]`
- [x] **Gate M0:** app boots, base Metronic layout renders, DB migrates, smoke test green.

---

## M1 — Auth & RBAC

- [x] Migration `users` with all columns incl. `is_active`, `created_by` (no custom `role` column). `[DATA-01]`
- [x] Install `spatie/laravel-permission`, publish config/migrations. `[§3.1, §4]`
- [x] Run Spatie permission migrations: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`. `[DATA-18..22]`
- [x] Migration `password_reset_tokens` (standard). `[DATA-17]`
- [x] Migration `activity_logs`. `[DATA-16]`
- [x] Add `HasRoles` to `User` and use the `web` guard for role assignment/checks. `[§3.1]`
- [x] Seed fixed roles `super_admin`, `guru`, `siswa`; seed baseline permissions mapped from §3.2. `[§3.1, §3.2]`
- [x] Login (email+password) for all roles + logout. `[FR-AUTH-01, FR-AUTH-05]`
- [x] Siswa self-registration (assign role `siswa`, is_active=1). `[FR-AUTH-02]`
- [x] Block guru/admin self-registration at the public route. `[FR-AUTH-03]`
- [x] Enforce unique email across all users. `[FR-AUTH-06]`
- [x] Forgot/reset password via emailed token, queued mail, SMTP configured. `[FR-AUTH-04, BR-02]`
- [x] Passwords hashed (bcrypt/argon2). `[FR-AUTH-05, NFR-03]`
- [x] Spatie `role` middleware (`role:super_admin|guru|siswa`) + route groups. `[§3.2]`
- [x] Scaffold one Policy per model for `OWN` and read-only checks (empty methods now, filled per milestone). `[§3.2]`
- [x] Write login/logout events to `activity_logs`. `[BR-06]`
- [x] Role-based post-login redirect to each dashboard.
- [x] **Gate M1:** siswa self-register + login; guru self-register rejected; super_admin creates+logs-in guru; password reset email works; login events logged. `[§11 Auth scenarios]`

---

## M2 — Users Admin & Read-only Guard

- [x] Super Admin user CRUD (create guru, edit, delete, deactivate). `[FR-SA-02]`
- [x] Guru creation sets `created_by = auth id`, assigns role `guru`. `[DATA-01, FR-AUTH-03]`
- [x] Deactivate = set `is_active=0`, never hard-delete. `[BR-05]`
- [x] Shared **read-only guard** (middleware/policy) rejecting (403) writes on records owned by inactive users. `[BR-05, NFR-09]`
- [x] UserPolicy: only super_admin manages users. `[§3.2]`
- [x] Validation for admin user store (name/email unique/role in [guru,siswa]/password min:8). `[§9]`
- [x] **Gate M2:** deactivating a guru retains data, blocks writes (403), deletes nothing. `[§11 Deactivation scenario]`

---

## M3 — Classes & Membership

- [ ] Migration `classes` (+ unique `class_code`, `guru_id`, `is_active`). `[DATA-02]`
- [ ] Migration `class_members` with UNIQUE(class_id, student_id). `[DATA-03]`
- [ ] Guru class CRUD (owned only). `[FR-GR-02]`
- [ ] Auto-generate unique 6–8 char `class_code` on class create. `[§9, BR-01]`
- [ ] Guru adds students to owned classes. `[FR-GR-03]`
- [ ] Siswa join-by-code, **no approval**, reject duplicate join. `[FR-SW-03, BR-01, §9]`
- [ ] No caps on students/class or classes/guru. `[BR-07]`
- [ ] ClassPolicy: guru OWN, super_admin all, siswa join only. `[§3.2]`
- [ ] Siswa "my classes" list. `[FR-SW-04]`
- [ ] **Gate M3:** join-by-code works with no approval; duplicate join rejected; unlimited members/classes. `[§11 Classes scenarios]`

---

## M4 — Materials, Meetings & Attendance

- [ ] Migration `materials` (type figma|file, figma_url, file_path, file_size_kb). `[DATA-04]`
- [ ] Migration `meetings`. `[DATA-05]`
- [ ] Migration `meeting_materials` UNIQUE(meeting_id, material_id). `[DATA-06]`
- [ ] Migration `attendances` UNIQUE(meeting_id, student_id), status enum. `[DATA-07]`
- [ ] Material via Figma link. `[FR-GR-04]`
- [ ] Material via PDF upload — validate `mimes:pdf`, `max:20480` KB, store size. `[FR-GR-05, BR-04, §9]`
- [ ] Meeting CRUD per class (title, scheduled_at). `[FR-GR-06]`
- [ ] Record attendance per meeting (hadir/izin/sakit/alfa). `[FR-GR-07]`
- [ ] Share materials to a meeting. `[FR-GR-08]`
- [ ] Siswa views meetings + accesses shared materials of joined classes only. `[FR-SW-04, §3.2]`
- [ ] Log attendance events. `[BR-06]`
- [ ] **Gate M4:** non-PDF/oversized upload rejected; valid PDF saved; attendance recorded & logged. `[§11 Materials scenarios]`

---

## M5 — Quizzes

- [ ] Migrations: `quizzes`, `quiz_questions`, `quiz_options`, `quiz_attempts`, `quiz_answers`. `[DATA-08..12]`
- [ ] Guru quiz builder: questions + options; enforce exactly one correct option/question. `[FR-GR-09, DATA-10]`
- [ ] Publish/unpublish + optional opens_at/closes_at window. `[DATA-08]`
- [ ] Siswa takes quiz (only joined class, within window, single attempt default). `[FR-SW-05, §9]`
- [ ] Auto-score on submit → store 0–100 on attempt; mark per-answer correctness. `[FR-SW-05, §13 scoring assumption]`
- [ ] Log quiz-attempt events. `[BR-06]`
- [ ] QuizPolicy (guru OWN class, siswa take-only). `[§3.2]`
- [ ] **Gate M5:** submit auto-scores correctly; attempt logged. `[§11 Quizzes scenario]`

---

## M6 — Grading & Recap

- [ ] Migrations: `grade_components`, `component_scores`, `final_grades`. `[DATA-13,15,14]`
- [ ] Guru defines grade components + manual weights per class; warn (not block) if total ≠ 100. `[FR-GR-12, BR-03, §9]`
- [ ] Enter/record `component_scores` per student. `[DATA-15]`
- [ ] Final-grade calculation = weighted sum → store `final_grades`. `[FR-GR-11]`
- [ ] Guru recap of all students in owned classes. `[FR-GR-10]`
- [ ] Siswa dashboard shows OWN grades only. `[FR-SW-06, §3.2]`
- [ ] Super Admin recap across ALL classes. `[FR-SA-05]`
- [ ] Super Admin export recap (default XLSX — confirm, see Open Q3). `[FR-SA-05, §13]`
- [ ] **Gate M6:** weighted final grade computed & stored; role-scoped recap views correct. `[§11 Grading scenario]`

---

## M7 — Monitoring

- [ ] Super Admin monitoring UI over `activity_logs`. `[FR-SA-04, BR-06]`
- [ ] Filters: by user, event_type, date range. `[BR-06]`
- [ ] Confirm login, quiz_attempt, attendance events all recorded from M1/M5/M4. `[BR-06]`
- [ ] Paginate logs. `[NFR-02]`
- [ ] **Gate M7:** all event types visible & filterable by super_admin. `[§11 Monitoring scenario]`

---

## M8 — Hardening & Octane

- [x] Enable Laravel Octane (FrankenPHP or Swoole driver). `[§4]`
- [ ] Audit for shared-state leaks (no request state in singletons/statics; rebind stateful services). `[§4]`
- [ ] Performance pass — page load < 2 s, TTFB target < 300 ms. `[NFR-01]`
- [ ] Security review — HTTPS, CSRF/XSS/SQLi defaults, RBAC coverage on every route. `[NFR-03]`
- [ ] Confirm PII access restricted by role. `[BR-08, NFR-04]`
- [ ] Responsive check on Chrome/Firefox/Edge/Safari, desktop + mobile. `[NFR-07]`
- [ ] Full test suite green; review all `// ASSUMPTION:` flags. `[§11]`
- [ ] Confirm no artificial caps anywhere. `[BR-07]`
- [ ] **Gate M8:** all §11 scenarios pass under Octane; perf + security review complete.

---

## Cross-Cutting Checklist (verify before final handover)

- [ ] All 3 roles enforced on every route (matrix §3.2 fully covered).
- [ ] `BR-01` … `BR-08` each have a passing test.
- [ ] Inactive-user data is read-only everywhere, never hard-deleted. `[BR-05]`
- [ ] Every uploaded file is PDF ≤ 20 MB. `[BR-04]`
- [ ] Activity logs cover login, quiz attempts, attendance. `[BR-06]`
- [ ] UI uses Metronic 9.5.0 components consistently. `[NFR-08]`
- [ ] Seeder creates an initial Super Admin account.

---

## Open Questions (resolve by M5–M6; do not block M0–M3)

- [ ] **Q1** Quiz retakes allowed? *(default: no — single attempt)* `[§13]`
- [ ] **Q2** Do quiz scores auto-populate a matching `grade_component`, or are all component scores entered manually? `[§13]`
- [ ] **Q3** Recap export format — CSV / XLSX / PDF? *(default: XLSX)* `[§13]`
- [ ] **Q4** Extra required `siswa` fields (e.g. NIS / student ID)? `[§13]`

---

*Keep this file updated as work progresses — it is the live execution state of the project.*
