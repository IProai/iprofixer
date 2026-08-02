# R1-01 — Platform Governance Foundation

## Purpose

Establish the durable application authority required before public acquisition, CMS, commercial, operations, finance, or portal workflows are expanded.

## Governing requirements

- SRS sections 5–8.
- FR-CMS-002 through FR-CMS-005.
- FR-PUB-004 through FR-PUB-008.
- NFR-SEC-001 through NFR-SEC-007.
- NFR-PRV-001 through NFR-PRV-003.
- NFR-L10N-001 through NFR-L10N-003.
- UI/UX sections 2, 9, and 10.

## Scope

1. Identity and secure-session persistence.
2. Deny-by-default role and permission foundation.
3. Immutable audit-event persistence.
4. Locale-aware content and translation persistence.
5. Public/private media metadata and approval state.
6. Evidence/proof approval state; unpublished proof is never public authority.
7. Consent-backed RFQ/form intake records.
8. Market, legal-entity, currency, tax-profile, and system-setting authority.
9. Correlation identifiers and protected public submission boundaries.
10. Automated migration and contract tests.

## Security laws

- Authorization decisions are permission-based and server-side.
- Authentication, invitation, and public form endpoints are rate-limited.
- Consent records are separate auditable records.
- Sensitive documents are private by default.
- Media usage approval is explicit.
- Audit records are append-only at application level.
- User-facing errors do not disclose internals.
- Public evidence requires both evidence verification and publication approval.

## Exit gates

- Empty PostgreSQL database migrates successfully.
- Required authority tables and constraints exist.
- Duplicate permission names, content slugs, consent identifiers, and setting keys are rejected within their authority boundary.
- Public content cannot resolve unpublished or unapproved proof.
- Audit, consent, locale, and source-attribution fields are covered by automated tests.
- CI formatting, build, migrations, tests, and dependency checks pass.

## Explicit exclusions

- No public pricing.
- No unsupported certification or compliance claim.
- No simulated production integration.
- No client portal access before organization/property scope enforcement exists.
- No complete visual acceptance claim until English and Arabic screenshots are reviewed at desktop, tablet, and mobile widths.
