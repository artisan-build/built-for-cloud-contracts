# Workflow — built-for-cloud-contracts

Canonical profile for `artisan-build/built-for-cloud-contracts`. Written by the brain orchestrator on 2026-09-26 for
scalpels #251. The coordinator commits it VERBATIM as `.solo/workflow.md` in the repo's initial scaffold commit.

The package holds the pure Scalpels ↔ Built for Cloud protocol contract: constants, value vocabularies (enums) and pure
validators. It is required by `artisan-build/built-for-cloud` and by `artisan-build/scalpels.app`. It is a **library**:
no app shell, routes, migrations, service provider surfaces, models, or runtime services.

## Phase & mode
- phase: pre-launch (new library; first release v0.1.0).
- default mode: A-autonomous: merge each PR on green CI; no human PR code review unless asked.
  This mirrors built-for-cloud, whose contract it carries.
- merge method: `gh pr merge --squash --delete-branch`, then confirm on GitHub that it merged.
- Bootstrap exception: the FIRST commit (scaffold: composer.json, LICENSE, README, pint.json, phpstan.neon, pest setup,
  `.github/workflows/tests.yml`, this file) goes straight to `main`, because there is no base to open a PR against.
  Every commit after that is a PR gated on CI.

## Who runs which tests
- Implementers run FOCUSED tests plus `composer stan` and `composer lint:test` once before handover. The coordinator
  runs the full `composer test` once per candidate. (The suite is small, and that distinction is kept only for consistency.)

## Hard gate (green before review; verify on committed SHA, clean tree)
- `composer stan` (phpstan level max or matching built-for-cloud's level, memory 512M), `composer lint:test` (pint --test), and `composer test` (pest).
- No `composer ready`. Add `composer audit` to CI. It is cheap and the fleet lacks it.

## CI (merge gate for Mode A)
- `.github/workflows/tests.yml`: PHP 8.5, with a `prefer-lowest` lane (the "tests (lowest deps)" lane, per
  brain playbooks/lowest-deps-lane-red.md) running pest, plus phpstan, pint --test, and composer audit.
- Branch protection on `main`: require the CI checks (use the exact matrix context names, e.g. `tests (8.5)`), after the first green run.

## Dependencies
- PHP ^8.4. Prefer ZERO runtime dependencies. If a Laravel component is truly needed, require the narrowest
  `illuminate/*` piece and justify it in the PR body.
- Dev: pestphp/pest, larastan/phpstan, laravel/pint.
- Install: `composer install --no-interaction`.

## Namespace
- `ArtisanBuild\BuiltForCloudContracts\` → `src/`. Never `ArtisanBuild\BuiltForCloud\` (built-for-cloud owns that prefix).

## Release
- Manual tag `vX.Y.Z` on merged, green `main`. Packagist auto-updates after Ed registers the package once.
- A MINOR/MAJOR release needs Ed's per-version OK; patches are pre-authorized; never re-tag.
- A tag triggers the fleet bump (brain's job): built-for-cloud and scalpels.app move to the new tag.
- Any change to a contract VALUE is a protocol change: both consumers must move together. Say so in the PR body.

## Toolchain conformance
- The standing `composer lint` ride-along applies to every PR as its own commit (see built-for-cloud's profile).

## Harness map
- Resolve every role through `~/Herd/brain/playbooks/resolve-agent-role.md` against `~/Herd/brain/agents.json`.
