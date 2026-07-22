# Implementation Plan — M7 Monitoring

> Milestone ID: M7 — Monitoring  
> Satisfies: `FR-SA-04`, `BR-06`, `NFR-02`  
> Source of truth: `PRD_PRO-BI_SMART_EN.md`, `todos.md`

---

## Requirements Summary

Super Admin must be able to monitor all user activity via a central view backed by `activity_logs`.

- View all activity logs (`login`, `logout`, `quiz_attempt`, `attendance`, `other`).
- Filter by: user, event type, date range.
- Paginate results.
- Server-side RBAC: only `super_admin` may access.

The required `activity_logs` table and event writes are already implemented in earlier milestones.

---

## Assumptions

- **No export feature** is required for M7. PRD only mentions monitoring, filtering, and pagination. Add export if explicitly requested.
- Logs are read-only; Super Admin cannot create, edit, or delete them.
- UI copy is in Bahasa Indonesia; code identifiers and comments are in English.

---

## Implementation Steps

### Step 1 — Create `ActivityLogPolicy`

**File:** `app/Policies/ActivityLogPolicy.php`

- Restrict `viewAny`/`view` to `super_admin` via `role:super_admin` check.
- Deny `create`, `update`, `delete`, `restore`, `forceDelete` always.
- Tag: `// FR-SA-04 / BR-06`

### Step 2 — Add `ActivityLogRepository`

**File:** `app/Repositories/ActivityLogRepository.php`

- Method `paginateForSuperAdmin(array $filters = []): LengthAwarePaginator`
- Query with optional filters:
  - `user_id` → exact match
  - `event_type` → exact match
  - `date_from` / `date_to` → `whereBetween` on `created_at`
- Eager-load `user()` relationship.
- Order by `created_at DESC`.
- Paginate default 25.
- Tag: `// FR-SA-04 / BR-06 / NFR-02`

### Step 3 — Add `MonitoringController`

**File:** `app/Http/Controllers/Admin/MonitoringController.php`

- Action `index(MonitoringFilterRequest $request)`
  - Call `authorize('viewAny', ActivityLog::class)`.
  - Pass validated filters to repository.
  - Return view with:
    - `logs` paginated
    - `eventTypes` array
    - `users` list for dropdown
    - current `filters`
- Tag: `// FR-SA-04 / BR-06 / NFR-02`

### Step 4 — Add `MonitoringFilterRequest`

**File:** `app/Http/Requests/Admin/MonitoringFilterRequest.php`

Rules:

```php
'user_id'    => ['nullable', 'exists:users,id'],
'event_type' => ['nullable', 'in:login,logout,quiz_attempt,attendance,other'],
'date_from'  => ['nullable', 'date'],
'date_to'    => ['nullable', 'date', 'after_or_equal:date_from'],
```

- Tag: `// BR-06 / §9`

### Step 5 — Register Route

**File:** `routes/web.php`

Inside the `admin` group (`prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin'])`):

```php
Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring');
```

- Tag comment: `// FR-SA-04`

### Step 6 — Build Super Admin Monitoring View

**File:** `resources/views/admin/monitoring/index.blade.php`

- Extend shared Metronic admin layout.
- Filter panel (GET form):
  - Select user (dropdown with name/email)
  - Select tipe event
  - Input date_from / date_to
- Logs table columns:
  - Waktu (`created_at`)
  - Pengguna (`user->name` or "System / Guest")
  - Tipe Event
  - Deskripsi
  - Alamat IP
  - Subjek (polymorphic type + id, if present)
- Pagination links below table.
- Preserve filters in pagination query string.
- Use Metronic table/card classes per demo reference.
- Tag: `// FR-SA-04 / NFR-02`

### Step 7 — Verify Existing Logging Sources

No changes expected; confirm current writes include:

| Event | Source | Subject |
|---|---|---|
| `login` | `AuthService::logActivity()` called by `LoginController` | none |
| `logout` | `AuthService::logActivity()` called by `LoginController` | none |
| `quiz_attempt` | `QuizAttemptService` | `QuizAttempt` |
| `attendance` | `AttendanceService` | `Attendance` |

If any event type is missing `subject_type`/`subject_id` or the correct `event_type`, fix the call site as part of M7.

### Step 8 — Feature Tests

**File:** `tests/Feature/Admin/MonitoringTest.php`

Happy path:
- `super_admin` can `GET /admin/monitoring` and sees paginated logs.
- Filter by `event_type=login` returns only login logs.
- Filter by user returns only that user's logs.
- Date range filter works.

Failure path:
- `guru` and `siswa` cannot access (403 or role-appropriate redirect).
- Guest is redirected to login.
- Paginate meta/links present in response.

Tag tests: `// FR-SA-04 / BR-06 / NFR-02`

### Step 9 — QA & Housekeeping

- Run `./vendor/bin/pint` and fix style.
- Update `todos.md` M7 checkboxes.
- Run feature tests.
- Update §Open Questions if any assumption changes.

---

## Files to Create or Modify

| File | Action |
|---|---|
| `app/Policies/ActivityLogPolicy.php` | Create |
| `app/Repositories/ActivityLogRepository.php` | Create |
| `app/Http/Controllers/Admin/MonitoringController.php` | Create |
| `app/Http/Requests/Admin/MonitoringFilterRequest.php` | Create |
| `resources/views/admin/monitoring/index.blade.php` | Create |
| `tests/Feature/Admin/MonitoringTest.php` | Create |
| `routes/web.php` | Modify |
| `todos.md` | Update checkboxes |

---

## Definition of Done

- [ ] `ActivityLogPolicy` denies writes and allows `super_admin` reads.
- [ ] `/admin/monitoring` renders paginated logs.
- [ ] Filters for user, event_type, date range apply correctly.
- [ ] Existing login, quiz_attempt, and attendance events are visible.
- [ ] Feature tests cover happy path and access-denied path; both green.
- [ ] `./vendor/bin/pint` clean.
- [ ] `todos.md` M7 checkboxes ticked.

---

## Open Questions

- Export feature intentionally deferred; confirm if required beyond PRD wording.
