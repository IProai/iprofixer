# R0-01 — Application Foundation

## Classification

`APPROVED_FOR_IMPLEMENTATION`

## Objective

Create the first executable IProFixer application foundation without implementing business modules or drifting from the canonical blueprint, SRS, UI/UX specification, or approved homepage reference.

## Branch Boundary

- Working branch: `kickoff/foundation-r0-r1`
- Base branch: `main`
- Direct feature implementation on `main`: prohibited
- Protected source-of-truth documents: may only change through an explicit governance correction

## In Scope

1. Complete Laravel application skeleton.
2. PostgreSQL-first configuration.
3. Vite and Tailwind asset pipeline.
4. English and Arabic locale foundations.
5. True RTL document-direction support.
6. Health and readiness endpoints.
7. Baseline authentication architecture.
8. Role and permission package installation.
9. Filament operator-console foundation.
10. Database-backed sessions, cache, and queues.
11. CI for dependency validation, formatting, build, migration, and automated tests.
12. Initial automated tests for application boot, health, locale, and RTL behavior.

## Explicitly Out of Scope

- Final homepage styling or imagery.
- CMS resources and editorial workflows.
- CRM entities and sales automation.
- Job, batch, quality, finance, or portal workflows.
- AI, chatbot, voice processing, or recommendations.
- Production deployment.
- Public claims, client logos, performance metrics, or certifications.

## Required Routes

| Method | Route | Purpose |
|---|---|---|
| GET | `/` | Temporary governed foundation page until R1 homepage implementation |
| GET | `/health` | Process liveness response |
| GET | `/ready` | Application and required dependency readiness response |
| GET | `/locale/{locale}` | Supported locale selection for `en` and `ar` only |

## Required Architectural Boundaries

- `App/Foundation` owns cross-cutting application capabilities.
- Future business modules must not be placed directly in controllers or Filament resources without an owned module boundary.
- Public web, operator console, client portal, and APIs remain separate presentation surfaces.
- PostgreSQL is the canonical relational database.
- Queued work must be idempotent or explicitly documented as non-idempotent.
- Public and private file storage must remain separate.

## Required Quality Gates

- Composer manifest validates.
- Dependency lockfiles are committed.
- Application boots from a clean checkout.
- Database migrations run against PostgreSQL.
- Frontend assets build successfully.
- PHP style check passes.
- Frontend format check passes.
- Automated tests pass.
- `/health` does not depend on the database.
- `/ready` fails safely when a required dependency is unavailable.
- Arabic renders with `lang="ar"` and `dir="rtl"`.
- English renders with `lang="en"` and `dir="ltr"`.
- No secrets are committed.

## Evidence Required for Acceptance

1. Commit SHA and exact changed-file inventory.
2. CI run showing all mandatory jobs passing.
3. Test output.
4. Migration output against PostgreSQL.
5. Desktop and mobile screenshots of English and Arabic foundation pages.
6. Health and readiness response samples.
7. Confirmation that `main` remains unchanged until review and merge.

## Stop Conditions

Stop and classify the packet as blocked when:

- dependency versions cannot resolve cleanly;
- the runtime requires a departure from the approved architecture;
- authentication or permission implementation requires an unapproved product decision;
- CI cannot reproduce the clean-checkout build;
- secrets or production credentials are required;
- source-of-truth conflicts are discovered.

## Completion Classification

Use only one:

- `R0_01_ACCEPTED`
- `R0_01_CORRECTION_REQUIRED`
- `R0_01_BLOCKED`
