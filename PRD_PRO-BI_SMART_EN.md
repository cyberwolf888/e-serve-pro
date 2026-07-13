# PRD — PRO-BI SMART

> **Product Requirements Document, optimized for AI coding agents.**
> This document is the single source of truth for building PRO-BI SMART. It is written to be parsed and executed by an AI agent. Every requirement has a stable ID. Do not invent features outside this document. When a detail is ambiguous, follow the `BR-*` (Business Rules) and `DATA-*` (Schema) sections first, then ask.

---

## 0. Agent Instructions (read first)

- **Stack is fixed.** Do not substitute frameworks, databases, or the UI template. See §4.
- **Traceability.** Every model, migration, controller, route, and test must map to an ID in this doc (`FR-*`, `BR-*`, `DATA-*`). Reference the ID in code comments.
- **Build order.** Follow the milestone sequence in §12. Do not build later milestones before earlier ones pass their acceptance criteria.
- **Definition of Done** for any requirement = code implemented + validation enforced + happy-path and failure-path tests green (§11).
- **RBAC is enforced server-side** via **spatie/laravel-permission** plus Laravel Policies/Gates for record ownership rules. Never trust the client for authorization. See §3.2.
- **Architecture pattern.** Implement application logic using the **repository + services** pattern: controllers stay thin, services hold business logic, repositories encapsulate data access.
- **Language:** UI copy is in **Bahasa Indonesia**; code, identifiers, comments, and commit messages are in **English**.
- **When unsure:** prefer the most literal reading of the business rule. Flag assumptions in code comments prefixed `// ASSUMPTION:`.

---

## 1. Product Summary

PRO-BI SMART is a **web-based Indonesian-language learning platform** aligned with the *Kurikulum Merdeka*. It unifies visual learning material, class sessions, attendance, multiple-choice quizzes, grading, and progress recap into one system. There are three roles: **Super Admin (Peneliti)**, **Guru (Teacher)**, and **Siswa (Student)**.

**Core capabilities**
1. Role-based user & access management.
2. Class, material, and meeting (session) management.
3. Material delivery via Figma design links and PDF uploads.
4. Attendance tracking per meeting.
5. Multiple-choice quizzes with auto-scoring.
6. Manual weighted final-grade calculation and recap.
7. Full activity monitoring & audit logs for Super Admin.

**Out of scope (this release):** native mobile apps, payments, AI-adaptive learning, non-multiple-choice question types.

---

## 2. Actors

| ID | Actor | Description | Registration |
|----|-------|-------------|--------------|
| ACT-SA | Super Admin (Peneliti) | Highest authority. Full control over all data, users, and configuration. | Seeded / created by system |
| ACT-GR | Guru (Teacher) | Manages own classes, materials, meetings, quizzes, grading. | Created **only** by Super Admin |
| ACT-SW | Siswa (Student) | Joins classes, attends meetings, takes quizzes, views own grades. | **Self-registration** |

---

## 3. Roles & Access Control

### 3.1 Roles

Roles are fixed names: `super_admin`, `guru`, `siswa`. Implement RBAC with **spatie/laravel-permission** using the package `roles` / `permissions` tables and `model_has_roles` assignments. Do not build custom RBAC tables beyond the package schema. Keep Laravel Policies for `OWN` record checks and inactive-user write blocking.

### 3.2 Permission Matrix (authoritative)

`✅ = allowed`, `❌ = denied`, `OWN = only records the user owns`, `— = not applicable`

| Capability | super_admin | guru | siswa |
|---|:---:|:---:|:---:|
| Login | ✅ | ✅ | ✅ |
| Self-register | ❌ | ❌ | ✅ |
| Password reset via email | ✅ | ✅ | ✅ |
| Create/edit/delete/deactivate any user | ✅ | ❌ | ❌ |
| Manage system config & reference data | ✅ | ❌ | ❌ |
| Create/manage classes | ✅ | OWN | ❌ |
| Add students to a class | ✅ | OWN | — |
| Join class via class code | — | — | ✅ |
| Create/manage materials | ✅ | OWN | ❌ |
| View materials | ✅ | ✅ | OWN classes |
| Create meetings & record attendance | ✅ | OWN | ❌ |
| Create/manage quizzes | ✅ | OWN | ❌ |
| Take quizzes | ❌ | ❌ | ✅ |
| Define grade weights (manual) | ✅ | OWN | ❌ |
| Calculate final grades | ✅ | OWN | ❌ |
| View recap of ALL classes | ✅ | ❌ | ❌ |
| View recap of OWN classes | — | ✅ | ❌ |
| View OWN grades only | — | — | ✅ |
| Monitoring & activity logs (all users) | ✅ | ❌ | ❌ |
| Download recap/export | ✅ (all) | OWN | ❌ |

> **Agent note:** Protect role-based route groups with Spatie `role` middleware. Implement one Policy per model for record-level checks, and every controller action must call `authorize()` against the matching Policy method. `OWN` checks compare `record.user_id`/`class.guru_id` to `auth()->id()`.

---

## 4. Technology Stack (fixed)

| Layer | Technology | Version / Constraint | Notes |
|---|---|---|---|
| Language | PHP | **8.3+** | Required minimum for Laravel 13. |
| Framework | **Laravel** | **13.x** (`^13.0`) | Latest major (released ~Mar 2026). |
| App server | **Laravel Octane** | latest compatible | Persistent worker runtime. Use **FrankenPHP** or **Swoole** driver. Beware shared-state leaks between requests. |
| Database | **MariaDB** | 10.11+ / 11.x | Relational. Use InnoDB, `utf8mb4`. |
| ORM | Eloquent | bundled | |
| Templating | Blade | bundled | |
| CSS | **Tailwind CSS** | v4.x (as shipped with Metronic 9.5.0) | Utility-first. |
| UI Template | **Metronic** | **9.5.0 (Tailwind edition)** | Use its layouts, components, and design tokens consistently. Do not hand-roll components that Metronic already provides. |
| Build tool | Vite | bundled with Laravel | |
| Auth | Laravel built-in (Breeze/Fortify-style) | — | Session-based web auth + email password reset. |
| RBAC | **spatie/laravel-permission** | latest compatible | Use package roles/permissions; keep Policies for `OWN` checks and read-only guards. |
| Queue | Laravel Queue | — | For email (password reset) and async logging. Driver: database or Redis. |
| Mail | Laravel Mailer (SMTP) | — | Required for `BR-02`. |

**Octane cautions for the agent:**
- Do not store request-specific state in singletons or static properties.
- Reset/rebind stateful services and repositories per request where needed.
- Validate that Metronic asset pipeline (Vite + Tailwind) builds cleanly before wiring Octane.

**Version caveats to confirm at project setup (flag if mismatched):**
- Confirm Laravel Octane officially supports the chosen Laravel 13.x patch.
- Confirm Metronic 9.5.0 Tailwind version matches the Tailwind major you install.

---

## 5. Functional Requirements

IDs: `FR-<MODULE>-<NN>`. Priority: `MUST` for this release.

### 5.1 Auth & Account (`FR-AUTH-*`)

| ID | Requirement |
|---|---|
| FR-AUTH-01 | Provide login for all roles (super_admin, guru, siswa) via email + password. |
| FR-AUTH-02 | Students can self-register (name, email, password). Account is `siswa` by default. |
| FR-AUTH-03 | Guru accounts can ONLY be created by Super Admin. No self-registration for guru. |
| FR-AUTH-04 | Provide "forgot password" reset via email token for every role. |
| FR-AUTH-05 | Secure session management + logout. Passwords hashed (bcrypt/argon2). |
| FR-AUTH-06 | Email must be unique across all users. |

### 5.2 Super Admin (`FR-SA-*`)

| ID | Requirement |
|---|---|
| FR-SA-01 | Login with a Super Admin account. |
| FR-SA-02 | Create, edit, delete, and deactivate any user (guru & siswa). |
| FR-SA-03 | Manage all reference data & config (classes, materials, learning results). |
| FR-SA-04 | Monitor all user activity via a monitoring view backed by activity logs (`BR-06`). |
| FR-SA-05 | View and download (export) the recap of ALL registered classes. |

### 5.3 Guru (`FR-GR-*`)

| ID | Requirement |
|---|---|
| FR-GR-01 | Login with an account created by Super Admin. |
| FR-GR-02 | Create and manage classes, including setting the class name. |
| FR-GR-03 | Add students to owned classes. |
| FR-GR-04 | Create/manage materials via a Figma design link. |
| FR-GR-05 | Upload material files (PDF only, ≤ 20 MB — see `BR-04`). |
| FR-GR-06 | Create meeting sessions (schedule) for a class. |
| FR-GR-07 | Record student attendance per meeting. |
| FR-GR-08 | Share materials on each meeting. |
| FR-GR-09 | Create/manage multiple-choice quizzes per class. |
| FR-GR-10 | View and manage the grade recap of all students in owned classes. |
| FR-GR-11 | Calculate students' final grades from quiz results and other components. |
| FR-GR-12 | Define final-grade component format and weights manually per class (`BR-03`). |

### 5.4 Siswa (`FR-SW-*`)

| ID | Requirement |
|---|---|
| FR-SW-01 | Self-register an account. |
| FR-SW-02 | Login with the registered account. |
| FR-SW-03 | Join a class using a class code — no teacher approval (`BR-01`). |
| FR-SW-04 | Attend meetings and access shared materials of joined classes. |
| FR-SW-05 | Take multiple-choice quizzes in joined classes. |
| FR-SW-06 | View own grades via a student dashboard. |

---

## 6. Business Rules (`BR-*`) — hard constraints

| ID | Rule | Implementation hint |
|---|---|---|
| BR-01 | Students join a class directly with a **class code**; NO teacher approval. | `classes.class_code` unique; join endpoint validates code, inserts `class_members`. |
| BR-02 | Password reset is done **via email** for every role. | Laravel password broker + SMTP mailer + queued mail. |
| BR-03 | Final-grade components & weights are **entered manually by the Guru** per class. | `grade_components(class_id, name, weight)`; weights should sum to 100 (validate, warn if not). |
| BR-04 | Uploaded material files are **PDF only, max 20 MB per file**. | Validation: `mimes:pdf`, `max:20480` (KB). Reject otherwise with clear error. |
| BR-05 | Deactivating a guru/siswa **does not hard-delete**; all related data (classes, grades, meeting history) becomes **read-only**. | Use `users.is_active=false` (or soft delete) + a global read-only guard/policy that blocks writes on records linked to inactive users. Do NOT cascade-delete. |
| BR-06 | Super Admin monitoring shows **all activity logs**: login, quiz attempts, attendance. | Central `activity_logs` table written on those events. |
| BR-07 | **No limit** on students per class or classes per guru. | Do not add artificial caps in validation or schema. |
| BR-08 | User data privacy handled per applicable **personal-data-protection regulation**. | Hash passwords, encrypt sensitive fields if added, restrict PII access by role, log access. |

> **Conflict resolution priority:** `BR-*` > `DATA-*` > `FR-*` > UI copy. If two rules appear to conflict, the higher-priority section wins; flag it.

---

## 7. Data Model (`DATA-*`)

**Implementation structure:** use Eloquent models for persistence, repositories for data-access queries/commands, and services for business workflows that coordinate validation, authorization-adjacent domain checks, logging, and persistence.

Conventions: all tables InnoDB / `utf8mb4_unicode_ci`; PK `id` BIGINT UNSIGNED auto-increment unless noted; timestamps `created_at`/`updated_at` on every table; FKs indexed. Money/weights use `DECIMAL`. Booleans TINYINT(1).

### DATA-01 `users`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| name | varchar(255) | not null |
| email | varchar(255) | not null, unique |
| email_verified_at | timestamp | nullable |
| password | varchar(255) | not null (hashed) |
| is_active | tinyint(1) | not null, default 1 |
| created_by | bigint unsigned | nullable, FK→users.id (which admin created a guru) |
| remember_token | varchar(100) | nullable |

### DATA-02 `classes`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| guru_id | bigint unsigned | not null, FK→users.id (owner teacher) |
| name | varchar(255) | not null |
| class_code | varchar(16) | not null, unique |
| description | text | nullable |
| is_active | tinyint(1) | not null, default 1 |

### DATA-03 `class_members` (student ↔ class, N:N)
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| class_id | bigint unsigned | not null, FK→classes.id |
| student_id | bigint unsigned | not null, FK→users.id |
| joined_at | timestamp | not null |
| — | — | UNIQUE(class_id, student_id) |

### DATA-04 `materials`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| class_id | bigint unsigned | not null, FK→classes.id |
| title | varchar(255) | not null |
| type | enum('figma','file') | not null |
| figma_url | varchar(1024) | nullable (required if type='figma') |
| file_path | varchar(1024) | nullable (required if type='file') |
| file_size_kb | int unsigned | nullable (≤ 20480) |

### DATA-05 `meetings`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| class_id | bigint unsigned | not null, FK→classes.id |
| title | varchar(255) | not null |
| scheduled_at | datetime | not null |
| notes | text | nullable |

### DATA-06 `meeting_materials` (meeting ↔ material shared, N:N)
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| meeting_id | bigint unsigned | not null, FK→meetings.id |
| material_id | bigint unsigned | not null, FK→materials.id |
| — | — | UNIQUE(meeting_id, material_id) |

### DATA-07 `attendances`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| meeting_id | bigint unsigned | not null, FK→meetings.id |
| student_id | bigint unsigned | not null, FK→users.id |
| status | enum('hadir','izin','sakit','alfa') | not null |
| recorded_at | timestamp | not null |
| — | — | UNIQUE(meeting_id, student_id) |

### DATA-08 `quizzes`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| class_id | bigint unsigned | not null, FK→classes.id |
| title | varchar(255) | not null |
| description | text | nullable |
| is_published | tinyint(1) | not null, default 0 |
| opens_at | datetime | nullable |
| closes_at | datetime | nullable |

### DATA-09 `quiz_questions`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| quiz_id | bigint unsigned | not null, FK→quizzes.id |
| question_text | text | not null |
| order | int unsigned | not null, default 0 |

### DATA-10 `quiz_options`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| question_id | bigint unsigned | not null, FK→quiz_questions.id |
| option_text | varchar(1024) | not null |
| is_correct | tinyint(1) | not null, default 0 |
| label | char(1) | not null (e.g. 'A','B','C','D') |

> **Rule:** exactly one option per question has `is_correct=1` (validate on save).

### DATA-11 `quiz_attempts`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| quiz_id | bigint unsigned | not null, FK→quizzes.id |
| student_id | bigint unsigned | not null, FK→users.id |
| score | decimal(5,2) | nullable (0–100, set on submit) |
| started_at | timestamp | not null |
| submitted_at | timestamp | nullable |
| — | — | UNIQUE(quiz_id, student_id) unless retakes allowed (default: single attempt) |

### DATA-12 `quiz_answers`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| attempt_id | bigint unsigned | not null, FK→quiz_attempts.id |
| question_id | bigint unsigned | not null, FK→quiz_questions.id |
| selected_option_id | bigint unsigned | nullable, FK→quiz_options.id |
| is_correct | tinyint(1) | not null, default 0 |

### DATA-13 `grade_components` (manual weights — `BR-03`)
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| class_id | bigint unsigned | not null, FK→classes.id |
| name | varchar(255) | not null (e.g. 'Kuis 1', 'Tugas', 'UAS') |
| weight | decimal(5,2) | not null (percentage; components per class should sum to 100) |

### DATA-14 `final_grades`
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| class_id | bigint unsigned | not null, FK→classes.id |
| student_id | bigint unsigned | not null, FK→users.id |
| final_score | decimal(5,2) | not null (weighted result) |
| calculated_at | timestamp | not null |
| — | — | UNIQUE(class_id, student_id) |

### DATA-15 `component_scores` (per-student score for each grade component)
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| grade_component_id | bigint unsigned | not null, FK→grade_components.id |
| student_id | bigint unsigned | not null, FK→users.id |
| score | decimal(5,2) | not null (0–100) |
| — | — | UNIQUE(grade_component_id, student_id) |

### DATA-16 `activity_logs` (`BR-06`)
| Column | Type | Constraints |
|---|---|---|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | nullable, FK→users.id |
| event_type | enum('login','logout','quiz_attempt','attendance','other') | not null |
| description | varchar(1024) | nullable |
| ip_address | varchar(45) | nullable |
| subject_type | varchar(255) | nullable (polymorphic target) |
| subject_id | bigint unsigned | nullable |
| created_at | timestamp | not null |

### DATA-17 `password_reset_tokens`
Standard Laravel table (`email`, `token`, `created_at`).

### DATA-18 `roles` (Spatie package)
Package-default roles table. Seed fixed roles: `super_admin`, `guru`, `siswa`. Use `guard_name='web'`.

### DATA-19 `permissions` (Spatie package)
Package-default permissions table. Seed permissions that map to the capability matrix in §3.2.

### DATA-20 `model_has_roles` (Spatie package)
Package-default polymorphic pivot assigning roles to models; used to attach fixed roles to `users`.

### DATA-21 `model_has_permissions` (Spatie package)
Package-default polymorphic pivot for direct model permissions; avoid direct user-level grants unless there is a concrete need.

### DATA-22 `role_has_permissions` (Spatie package)
Package-default pivot connecting roles to permissions.

### Entity Relationship (text)
```
users(1)───<(N)classes            [guru_id]
users(N)>──<(N)classes            via class_members [student_id]
classes(1)──<(N)materials
classes(1)──<(N)meetings
meetings(N)>──<(N)materials        via meeting_materials
meetings(1)──<(N)attendances──>(1)users
classes(1)──<(N)quizzes──<(N)quiz_questions──<(N)quiz_options
quizzes(1)──<(N)quiz_attempts──<(N)quiz_answers
classes(1)──<(N)grade_components──<(N)component_scores──>(1)users
classes(1)──<(N)final_grades──>(1)users
users(1)──<(N)activity_logs
```

---

## 8. Route Map (web, session-auth)

Group by Spatie `role` middleware. Use resourceful controllers where sensible. `{class}` etc. are route-model-bound.

```
# Public / Guest
GET   /login                        auth.login.show
POST  /login                        auth.login
GET   /register                     auth.register.show        (siswa only)
POST  /register                     auth.register             FR-AUTH-02
POST  /logout                       auth.logout
GET   /forgot-password              auth.forgot.show
POST  /forgot-password              auth.forgot.email         FR-AUTH-04 / BR-02
GET   /reset-password/{token}       auth.reset.show
POST  /reset-password               auth.reset

# Super Admin  (middleware: role:super_admin)
GET   /admin/dashboard              admin.dashboard
resource /admin/users               admin.users               FR-SA-02
GET   /admin/monitoring             admin.monitoring          FR-SA-04 / BR-06
GET   /admin/classes                admin.classes.index       FR-SA-03
GET   /admin/recap                  admin.recap.index         FR-SA-05
GET   /admin/recap/export           admin.recap.export        FR-SA-05

# Guru  (middleware: role:guru)
GET   /guru/dashboard               guru.dashboard
resource /guru/classes              guru.classes              FR-GR-02
POST  /guru/classes/{class}/students  guru.classes.addStudent FR-GR-03
resource /guru/classes/{class}/materials  guru.materials      FR-GR-04/05
resource /guru/classes/{class}/meetings   guru.meetings       FR-GR-06
POST  /guru/meetings/{meeting}/attendance guru.attendance.store FR-GR-07
POST  /guru/meetings/{meeting}/materials   guru.meetings.share  FR-GR-08
resource /guru/classes/{class}/quizzes     guru.quizzes        FR-GR-09
resource /guru/classes/{class}/grade-components guru.gradeComponents FR-GR-12/BR-03
POST  /guru/classes/{class}/grades/calculate    guru.grades.calculate FR-GR-11
GET   /guru/classes/{class}/recap                guru.recap    FR-GR-10

# Siswa  (middleware: role:siswa)
GET   /siswa/dashboard              siswa.dashboard           FR-SW-06
POST  /siswa/classes/join           siswa.classes.join        FR-SW-03 / BR-01
GET   /siswa/classes                siswa.classes.index
GET   /siswa/classes/{class}        siswa.classes.show        FR-SW-04
GET   /siswa/quizzes/{quiz}         siswa.quizzes.show        FR-SW-05
POST  /siswa/quizzes/{quiz}/submit  siswa.quizzes.submit      FR-SW-05
GET   /siswa/grades                 siswa.grades.index        FR-SW-06
```

---

## 9. Validation Rules (per key endpoint)

| Endpoint | Rules |
|---|---|
| register (siswa) | name: required|string|max:255; email: required|email|unique:users; password: required|min:8|confirmed |
| admin.users.store (guru) | name required; email required|email|unique:users; password required|min:8; assign role `guru` server-side |
| classes.store | name: required|string|max:255; class_code auto-generated unique 6–8 chars |
| materials.store | title required; type in [figma,file]; figma_url required_if type=figma|url; file required_if type=file|**mimes:pdf|max:20480** (BR-04) |
| meetings.store | title required; scheduled_at required|date |
| attendance.store | per student: status in [hadir,izin,sakit,alfa] |
| quizzes.store | title required; questions[].question_text required; each question exactly 1 correct option |
| classes.join (siswa) | class_code: required|exists:classes,class_code; reject if already a member (BR-01, no approval) |
| quizzes.submit | attempt open (opens_at/closes_at window); one attempt per student; answers map to valid options |
| grade-components.store | name required; weight: required|numeric|0–100; warn if class total ≠ 100 (BR-03) |

**Read-only guard (BR-05):** any write targeting a record owned by an inactive user must be rejected (403) by a shared policy/middleware.

---

## 10. Non-Functional Requirements (`NFR-*`)

| ID | Aspect | Requirement |
|---|---|---|
| NFR-01 | Performance | Served via Laravel Octane; typical page TTFB target < 300 ms, full load < 2 s on normal network. |
| NFR-02 | Scalability | No structural caps on students/classes (BR-07). Paginate long lists. |
| NFR-03 | Security | HTTPS; hashed passwords; CSRF, XSS, SQLi protections (framework defaults); `spatie/laravel-permission` RBAC enforced server-side. |
| NFR-04 | Privacy | PII access restricted by role; comply with applicable data-protection regulation (BR-08). |
| NFR-05 | Availability | Target ≥ 99% during operational hours. |
| NFR-06 | Auditability | Login, quiz attempts, attendance logged and reviewable by Super Admin (BR-06). |
| NFR-07 | Compatibility | Responsive; works on current Chrome, Firefox, Edge, Safari (desktop + mobile web). |
| NFR-08 | UX consistency | Use Metronic 9.5.0 components/tokens throughout; no ad-hoc component styling. |
| NFR-09 | Data integrity | Inactive-user data is read-only, never hard-deleted (BR-05). |
| NFR-10 | Maintainability | Follow Laravel conventions; PSR-12; feature-tested. |

---

## 11. Acceptance Criteria (Gherkin — testable)

The system is accepted when all scenarios pass automated tests.

```gherkin
Feature: Authentication & roles
  Scenario: Student self-registers
    Given a guest on the register page
    When they submit a valid name, unique email, and matching password
    Then a user with is_active=1 is created and assigned the `siswa` role
    And they can log in

  Scenario: Guru cannot self-register
    Given a guest
    When they attempt to create a guru account via public registration
    Then the request is rejected

  Scenario: Super Admin creates a guru
    Given an authenticated super_admin
    When they create a user and assign the `guru` role
    Then the guru can log in with the given credentials

  Scenario: Password reset via email
    Given any existing user
    When they request a password reset
    Then a reset email is sent and a valid token lets them set a new password

Feature: Classes and joining (BR-01)
  Scenario: Student joins by class code without approval
    Given a class with a valid class_code
    And an authenticated siswa not yet a member
    When they submit the class_code
    Then they become a member immediately with no teacher approval

  Scenario: Duplicate join prevented
    Given a siswa already in a class
    When they submit the same class_code
    Then the request is rejected as already joined

Feature: Materials upload (BR-04)
  Scenario: Reject non-PDF or oversized file
    Given an authenticated guru on their class
    When they upload a non-PDF OR a PDF larger than 20 MB
    Then the upload is rejected with a clear validation error

  Scenario: Accept valid PDF
    When they upload a PDF ≤ 20 MB
    Then the material is saved with type "file"

Feature: Quizzes
  Scenario: Auto-score on submit
    Given a published quiz with correct options defined
    And an authenticated siswa in the class
    When they submit answers
    Then each answer is graded and a 0–100 score is stored on the attempt
    And a "quiz_attempt" activity log entry is written (BR-06)

Feature: Grading (BR-03)
  Scenario: Manual weighted final grade
    Given a class with grade_components summing to 100
    And component_scores recorded per student
    When the guru triggers "calculate"
    Then final_grades are computed as the weighted sum and stored

Feature: Deactivation (BR-05)
  Scenario: Deactivated data becomes read-only
    Given a guru is deactivated by super_admin
    Then their classes/grades/meetings are retained
    And any write to those records is rejected (403)
    And nothing is hard-deleted

Feature: Monitoring (BR-06)
  Scenario: Super Admin sees all logs
    Given login, quiz, and attendance events have occurred
    When the super_admin opens monitoring
    Then all corresponding activity_logs are visible and filterable

Feature: Limits (BR-07)
  Scenario: No caps
    Then a class accepts unlimited students and a guru owns unlimited classes
```

---

## 12. Build Sequence (milestones for the agent)

Build in order. Each milestone must pass its acceptance criteria (§11) before the next.

- **M0 — Scaffold.** New Laravel 13 app, MariaDB connection (`utf8mb4`), Tailwind + Metronic 9.5.0 integrated via Vite, base layout renders. Do NOT enable Octane yet.
- **M1 — Auth & RBAC.** `users`, Spatie permission tables, login/logout, siswa self-register (`FR-AUTH-02/03`), password reset via email (`BR-02`), fixed role/permission seeding, Spatie role middleware + Policies scaffolded, `activity_logs` + login/logout logging.
- **M2 — Users admin.** Super Admin user CRUD + deactivate; read-only guard for inactive users (`BR-05`).
- **M3 — Classes & membership.** `classes` (+ unique `class_code`), guru class CRUD, siswa join-by-code (`BR-01`, `FR-SW-03`), add-student by guru.
- **M4 — Materials & meetings.** Figma-link + PDF upload (`BR-04`), meetings, attendance (`FR-GR-06/07`), share materials to meetings.
- **M5 — Quizzes.** Quiz builder (questions + options, one correct), publish, siswa take + auto-score, quiz-attempt logging.
- **M6 — Grading & recap.** `grade_components` manual weights (`BR-03`), `component_scores`, final-grade calculation, guru recap, siswa grade dashboard, Super Admin all-class recap + export (`FR-SA-05`).
- **M7 — Monitoring.** Super Admin monitoring UI over `activity_logs` (`BR-06`) with filters.
- **M8 — Hardening & Octane.** Enable Octane (FrankenPHP/Swoole), audit for shared-state leaks, perf pass (`NFR-01`), security review (`NFR-03`), full test suite green.

---

## 13. Assumptions & Open Questions

**Assumptions** (flagged `// ASSUMPTION:` in code):
- Quiz scoring is `(correct / total) * 100`; single attempt per student unless retakes are later specified.
- Attendance statuses use Indonesian enum values (`hadir/izin/sakit/alfa`).
- Grade weights are percentages that should total 100 (warn, don't hard-block, per manual-entry intent of `BR-03`).
- `component_scores` capture non-quiz components; quiz-derived components may be auto-filled from `quiz_attempts` (confirm mapping with stakeholder).

**Open questions for the human (do not block M0–M3):**
1. Are quiz **retakes** allowed? Default: no.
2. Should quiz-derived scores auto-populate a matching `grade_component`, or are all component scores entered manually?
3. Export format for recap — CSV, XLSX, or PDF? Default: XLSX.
4. Any required fields for `siswa` beyond name/email (e.g. student ID/NIS)?

---

## 14. Traceability Note

Every `FR-*` and `BR-*` maps directly to the agreed *Lampiran Spesifikasi dan Proses Bisnis Sistem PRO-BI SMART*. Keep IDs stable; if the business spec changes, update this PRD's IDs rather than diverging silently.

---

*— End of Document —*
