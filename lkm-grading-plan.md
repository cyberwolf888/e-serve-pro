# LKM Grading + Weight Integration

## Summary

- Let owning Guru and Super Admin grade completed LKM assignments.
- Link one LKM to one weighted grade component.
- Reuse existing component-score and final-grade calculation flow.

## Implementation

- Add nullable `decimal(5,2)` `score` to `lkm_assignments`.
- Add nullable, unique `lkm_id` foreign key to `grade_components`; retain manual and quiz sources.
- Extend existing submission update endpoint with optional `score`.
- Accept `0–100`; reject grading before proof and reflection completion. Blank input preserves existing score.
- Add score input to submission review page; show current score in LKM student table.
- Extend grade-component UI with optional same-class LKM selector. Reject quiz and LKM together, foreign-class LKM, or duplicate LKM linkage.
- Backfill component scores when linking an already-graded LKM.
- Sync later LKM score changes into non-overridden component scores. Preserve manual overrides.
- Keep weighted formula unchanged: linked LKM becomes normal component score; missing score counts as `0`; weights remain normalized.
- Reuse existing policies, routes, services, repositories, Metronic markup, and KTUI select API. No new endpoint or dependency.
- Tag changes and checked `todos.md` item with `[DATA-27, FR-GR-11, FR-GR-12, FR-GR-15]`.

## Interfaces

- Existing `PUT /{admin|guru}/classes/{class}/lkms/{lkm}/submissions/{assignment}` accepts nullable numeric `score`.
- Grade-component store/update accepts nullable `lkm_id`; `quiz_id` and `lkm_id` are mutually exclusive.
- New model relationships: grade component to LKM; assignment `score` cast to `decimal:2`.

## Tests

- Guru and Super Admin grade completed assignment successfully.
- Foreign Guru, inactive ownership/student, incomplete assignment, and scores outside `0–100` are rejected without mutation.
- Same-class LKM linkage succeeds; cross-class, duplicate, and quiz-plus-LKM linkage are rejected.
- Existing LKM grades backfill component scores.
- Later score changes synchronize unless a manual override exists.
- Mixed manual/quiz/LKM weights calculate expected final score; ungraded LKM contributes `0`.
- Run focused Grading/LKM suites, Pint, Blade compilation, then desktop/mobile Herd smoke checks.
- Current prerequisite: MariaDB at `127.0.0.1:3307`; baseline focused suite presently fails before assertions with connection refused.

## Assumptions

- Per-LKM component model; score scale `0–100`, two decimals.
- Grading available only after proof and reflection completion.
- No rubric, feedback text, grader audit columns, or automatic final-grade recalculation.
- Existing deployed migrations remain untouched; one reversible additive migration.
