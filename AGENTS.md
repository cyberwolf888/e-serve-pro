# PRO-BI SMART

Web-based Indonesian-language learning platform aligned with *Kurikulum Merdeka*. Three roles: **Super Admin (Peneliti)**, **Guru (Teacher)**, **Siswa (Student)**. Built in Laravel. This file is the entry point for coding agents; the full spec and task list live in the referenced docs below.

## Tech Stack (fixed — never substitute)

- PHP **8.3+**, **Laravel 13.x** (`^13.0`)
- **MariaDB** (InnoDB, `utf8mb4_unicode_ci`)
- **Laravel Octane** (FrankenPHP or Swoole) — enable only at milestone M8
- **Tailwind CSS** + **Metronic 9.5.0 (Tailwind edition)** for all UI
- Session-based auth, Laravel Queue + SMTP mailer

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

## Metronic Reference (before building views)

Before creating any Blade components or views, inspect the Metronic demo HTML at `/Users/master/Projects/HTML-Templates/metronic-tailwind-html-demos/dist/html/demo1/`. Browse the relevant page or partial in the demo to match Metronic's exact markup, component classes, and layout structure — then replicate it in Blade. Do not guess Metronic HTML patterns; use the demo as the source of truth.

## KTUI Reference (before building interactive components)

Before using any interactive JS component (modal, dropdown, tabs, select, collapsible, etc.), inspect its KTUI docs at `https://ktui.io/docs/<component-name>` (e.g. `https://ktui.io/docs/modal`). Match KTUI's exact data-attribute API (`data-kt-*`), semantic component classes (`kt-*`), and initialization method. For declarative usage, prefer the zero-JS `data-kt-*` markup approach wherever possible. For programmatic usage, import selectively from `@keenthemes/ktui/components/<name>`. Do not guess KTUI APIs; use the docs as the source of truth.

## Local Development URL

Open this project using the URL defined in the `.env` file (e.g. `APP_URL`). Do **not** use `localhost`; this project is proxied via Laravel Herd.

## Definition of Done (per requirement)

1. Code implemented and tagged with its PRD ID.
2. Validation enforced per PRD §9.
3. Feature test covers happy path **and** failure path — both green.
4. `./vendor/bin/pint` clean; the matching `@todos.md` checkbox is ticked.
