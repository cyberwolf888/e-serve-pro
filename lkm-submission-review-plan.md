# LKM Submission Review and Inline Grading

## Summary

- Add `Kiriman & Penilaian` sub-page from teacher LKM detail.
- Show all submitted YouTube, Drive, and Docs proof links.
- Show full self-reflection checklist read-only.
- Save grade `0–100` individually per student.

## Public Interfaces

- Add `GET /{admin|guru}/classes/{class}/lkms/{lkm}/submissions`, named `*.classes.lkms.submissions.index`.
- Add `PATCH /{admin|guru}/classes/{class}/lkms/{lkm}/submissions/{assignment}/grade`, named `*.classes.lkms.submissions.grade`.
- Payload: required numeric `score`, range `0–100`.
- No schema, dependency, or JavaScript changes.

## Implementation

- Add detail-page button linking to submission sub-page.
- Query proof-submitted assignments only, newest first, 25 per page; eager-load student, role, LKM class, and teacher.
- Render Metronic responsive table: student, role, escaped external proof link, disabled reflection checklist, status, grade form, existing `Perbaiki` link.
- Include submissions awaiting reflection, but hide grading form until reflection completed. Inactive/unauthorized records remain visible but not editable.
- Add dedicated score Form Request using existing `correctSubmission` policy. Recheck completion under existing row lock.
- Adapt `LkmService::correctSubmission()` to accept score-only validated updates; retain timestamp preservation and automatic grade-component synchronization while preserving manual overrides.
- Return to same paginated page with success/error alert.
- Update PRD route documentation and checked M7.9 `todos.md` entry; tag changes with `FR-SA-08`, `FR-GR-11`, `FR-GR-15`, `DATA-27`, `NFR-08`.

## Test Plan

- Verify Guru and Super Admin can open page; foreign Guru and siswa cannot.
- Verify YouTube, Drive, and Docs submissions appear; assignments without proof do not.
- Verify selected/unselected reflection points render read-only; pending reflection shows status without grade form.
- Verify completed submission accepts boundary/decimal scores, persists assignment score, and synchronizes linked non-manual component score.
- Verify invalid score, incomplete reflection, inactive student/class ownership, and nested-resource tampering cause no mutation.
- Run `APP_ENV=testing php artisan test --compact` for LKM, grading, and admin-access suites; current LKM baseline: 21 passing.
- Run Pint, Blade compilation, frontend build, then desktop/mobile smoke test at `https://e-serve-pro.test`.

## Assumptions

- Existing proof-host validation remains unchanged.
- Existing correction page remains sole place for editing proof/reflection.
- Admin receives same page because current LKM grading controller, policy, and views are shared.
- No bulk grading, score clearing, rubric, feedback, or audit columns.
