# PRO-BI SMART

Web-based Indonesian-language learning platform aligned with *Kurikulum Merdeka*. Three roles: **Super Admin (Peneliti)**, **Guru (Teacher)**, **Siswa (Student)**. Built in Laravel. This file is the entry point for coding agents; the full spec and task list live in the referenced docs below.

## External File Loading (read these)

CRITICAL: opencode does not auto-parse file references. When a task relates to a reference below, use your **Read tool** to load it on a need-to-know basis.

- `@PRD_PRO-BI_SMART_EN.md` — **authoritative spec**: requirements (`FR-*`), business rules (`BR-*`), data model (`DATA-*`), routes, validation, acceptance criteria. Read before implementing any feature.
- `@todos.md` — **execution checklist**: milestones M0–M8 with per-task PRD IDs and gates. Read to know what to build next; update it as you complete tasks.

Rule priority when anything conflicts: **BR > DATA > FR > UI**. If two rules still conflict, stop and flag it — do not guess.

## Tech Stack (fixed — never substitute)

- PHP **8.3+**, **Laravel 13.x** (`^13.0`)
- **MariaDB** (InnoDB, `utf8mb4_unicode_ci`)
- **Laravel Octane** (FrankenPHP or Swoole) — enable only at milestone M8
- **Tailwind CSS** + **Metronic 9.5.0 (Tailwind edition)** for all UI
- Session-based auth, Laravel Queue + SMTP mailer

## Project Structure (standard Laravel)

- `app/Models/` — Eloquent models (one per `DATA-*` table)
- `app/Http/Controllers/` — controllers, grouped by role where useful
- `app/Repositories/` — repositories encapsulating data access and query logic
- `app/Services/` — services orchestrating business rules and workflows
- `app/Policies/` — one policy per model; RBAC lives here
- `app/Http/Requests/` — form-request validation (see PRD §9)
- `database/migrations/` — one migration per `DATA-*` table
- `database/seeders/` — must seed an initial Super Admin
- `routes/web.php` — routes per PRD §8, grouped by `role:` middleware
- `resources/views/` — Blade + Metronic components
- `tests/Feature/` — one feature test per `FR-*`/`BR-*`

## Commands

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
php artisan serve            # dev before Octane (M0–M7)
npm install && npm run dev   # Vite + Tailwind/Metronic
php artisan test             # run test suite
./vendor/bin/pint            # format (run before every commit)
php artisan octane:start     # M8 only
```

## Agent Mode

- **Caveman mode always on.** All responses must be terse, ultra-compressed, token-efficient. No preamble, no summary, no filler.
- **Ponytail (YAGNI) for every code change.** Climb the ladder: delete > stdlib > native > already-installed dep > one-liner > minimal code. No speculative abstractions, no scaffolding "for later," no interface with one impl. Shortest diff that works.

## Code Standards

- Code, identifiers, comments, commits = **English**. UI copy = **Bahasa Indonesia**.
- PSR-12; run `./vendor/bin/pint` before committing.
- Follow Laravel conventions (Eloquent, form requests, resourceful controllers) while using the **repository + services** pattern.
- Keep controllers thin: HTTP in controllers, business logic in services, persistence/query logic in repositories.
- Tag every model/migration/controller/test with the PRD ID it implements, e.g. `// FR-GR-05 / BR-04`.
- Prefer framework features over custom code (validation rules, policies, password broker).
- Flag any assumption with `// ASSUMPTION:` and log it in `todos.md` Open Questions.

## Critical Rules (never violate)

- **RBAC is server-side only.** Every controller action calls `authorize()` against a Policy. Never trust the client. Enforce the permission matrix in PRD §3.2.
- **BR-04:** uploaded files are PDF only, ≤ 20 MB (`mimes:pdf|max:20480`). Reject anything else.
- **BR-05:** deactivating a user is `is_active=0`, **never** a hard delete. Related data becomes read-only; block writes with a shared guard (403).
- **BR-01:** students join classes by class code with **no** teacher approval.
- **BR-06:** log login, quiz attempts, and attendance to `activity_logs`.
- **BR-07:** no caps on students-per-class or classes-per-guru — never add artificial limits.
- Passwords hashed; HTTPS; keep framework CSRF/XSS/SQLi defaults on.

## Workflow

- Work through `@todos.md` in milestone order (M0→M8). Do not start a milestone until the previous one's **Gate** passes.
- Octane stays **off** until M8 to avoid shared-state debugging noise. When enabling it, audit for request state in singletons/statics.
- Never build features not in the PRD. If something's missing, ask.

## Metronic Reference (before building views)

Before creating any Blade components or views, inspect the Metronic demo HTML at `/Users/master/Projects/HTML-Templates/metronic-tailwind-html-demos/dist/html/demo1/`. Browse the relevant page or partial in the demo to match Metronic's exact markup, component classes, and layout structure — then replicate it in Blade. Do not guess Metronic HTML patterns; use the demo as the source of truth.

## KTUI Reference (before building interactive components)

Before using any interactive JS component (modal, dropdown, tabs, select, collapsible, etc.), inspect its KTUI docs at `https://ktui.io/docs/<component-name>` (e.g. `https://ktui.io/docs/modal`). Match KTUI's exact data-attribute API (`data-kt-*`), semantic component classes (`kt-*`), and initialization method. For declarative usage, prefer the zero-JS `data-kt-*` markup approach wherever possible. For programmatic usage, import selectively from `@keenthemes/ktui/components/<name>`. Do not guess KTUI APIs; use the docs as the source of truth.

## Definition of Done (per requirement)

1. Code implemented and tagged with its PRD ID.
2. Validation enforced per PRD §9.
3. Feature test covers happy path **and** failure path — both green.
4. `./vendor/bin/pint` clean; the matching `@todos.md` checkbox is ticked.

## Commit Convention

`type(scope): summary [PRD-ID]` — e.g. `feat(quiz): auto-score on submit [FR-SW-05]`.
Types: `feat`, `fix`, `test`, `refactor`, `chore`, `docs`.
