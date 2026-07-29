# IProFixer Software Requirements Specification

## 1. Purpose

Define the functional, non-functional, data, security, integration, and acceptance requirements for the IProFixer Hospitality Asset Lifecycle Care platform.

## 2. Scope

The platform shall support five bounded product surfaces:

1. Public Experience
2. Content & Growth Console
3. Commercial Workspace
4. Operations & Finance Workspace
5. Client Portal

## 3. User groups

- anonymous visitor
- prospective client
- client contact
- procurement manager
- F&B/stewarding stakeholder
- content editor
- sales user
- operations coordinator
- quality user
- finance user
- administrator

## 4. Functional requirements

### FR-PUB — Public experience

- FR-PUB-001: The system shall provide English and Arabic public routes.
- FR-PUB-002: Arabic routes shall render with true RTL layout.
- FR-PUB-003: The homepage shall follow the approved section order.
- FR-PUB-004: Visitors shall be able to submit a consultation/RFQ request.
- FR-PUB-005: Submission shall capture consent, locale, source page, and campaign attribution where available.
- FR-PUB-006: Services, industries, proof/results, resources, about, contact, and legal content shall be CMS-controlled.
- FR-PUB-007: Public proof items shall support evidence status and approval status.
- FR-PUB-008: Unapproved proof shall not be publicly rendered.

### FR-CMS — Content and growth

- FR-CMS-001: Authorized editors shall create, edit, preview, schedule, publish, unpublish, and archive content.
- FR-CMS-002: The system shall maintain localized fields for English and Arabic.
- FR-CMS-003: Media shall support metadata, alternative text, ownership/source, usage approval, and focal point.
- FR-CMS-004: Content changes shall be auditable.
- FR-CMS-005: SEO metadata, canonical URL, sitemap inclusion, and structured-data fields shall be manageable.

### FR-CRM — Commercial workspace

- FR-CRM-001: New RFQ submissions shall create or associate an organization, contact, lead, and activity.
- FR-CRM-002: Users shall qualify leads and record property, service, quantity, urgency, and decision context.
- FR-CRM-003: Opportunities shall move through controlled sales stages.
- FR-CRM-004: Assessments/site visits shall record findings and attachments.
- FR-CRM-005: Quotations shall support versioning, line items, terms, validity, approval, PDF generation, and status history.
- FR-CRM-006: Loss reasons and next actions shall be mandatory at relevant stages.

### FR-OPS — Operations

- FR-OPS-001: Accepted commercial work shall create a job without destroying quotation history.
- FR-OPS-002: Jobs shall support batches, received counts, condition records, processing stages, quality checks, rework, and delivered counts.
- FR-OPS-003: Quantity discrepancies shall require explicit reconciliation.
- FR-OPS-004: Chain-of-custody events shall be auditable.
- FR-OPS-005: Quality acceptance shall be required before delivery completion.
- FR-OPS-006: Exceptions shall record owner, severity, action, and closure evidence.

### FR-FIN — Finance

- FR-FIN-001: The system shall support invoices, receipts, credit notes, payment status, and aging.
- FR-FIN-002: Financial records shall remain distinct from quotation, contract, and job records.
- FR-FIN-003: Tax and e-invoicing fields shall be configurable per legal entity and jurisdiction.
- FR-FIN-004: Regulatory compliance shall not be represented as complete until applicable production integration and verification are proven.

### FR-PORTAL — Client portal

- FR-PORTAL-001: Authorized client users shall access only permitted organizations and properties.
- FR-PORTAL-002: Clients shall view approved quotations, job progress, deliveries, certificates, invoices, and controlled downloads.
- FR-PORTAL-003: Portal access and downloads shall be auditable.

## 5. Roles and permissions

The authorization model shall be deny-by-default and support least privilege. Initial roles:

- super administrator
- administrator
- content editor
- sales
- operations
- quality
- finance
- client user

Sensitive actions shall be permission-based rather than role-name checks embedded in UI code.

## 6. Core data entities

- user
- role
- permission
- organization
- property
- contact
- lead
- opportunity
- assessment
- quotation
- quotation version
- quotation line
- contract/PO reference
- job
- batch
- item/count reconciliation
- quality check
- exception
- delivery
- invoice
- receipt
- credit note
- recurring care plan
- content page
- localized content
- media asset
- evidence/proof item
- form submission
- consent record
- activity
- task
- audit event

## 7. Technical architecture

- Laravel modular monolith.
- PostgreSQL as primary relational database.
- Redis-compatible cache and queue where infrastructure permits.
- Private object storage abstraction for sensitive files.
- Public media storage separated from private operational documents.
- Server-rendered public experience with progressive enhancement.
- Filament-based internal workspaces unless a later approved decision supersedes it.
- PWA baseline without pretending offline support for workflows that are not conflict-safe.

## 8. Non-functional requirements

### Performance

- NFR-PERF-001: Public pages shall target Core Web Vitals in the good range on representative production-like infrastructure.
- NFR-PERF-002: Hero and service media shall be responsive and optimized.
- NFR-PERF-003: Non-critical scripts shall not block first render.

### Security

- NFR-SEC-001: No secrets shall be committed.
- NFR-SEC-002: Authentication shall use secure session management and CSRF protection.
- NFR-SEC-003: Rate limiting shall protect authentication and public submission endpoints.
- NFR-SEC-004: Uploads shall be type-, size-, and authorization-controlled.
- NFR-SEC-005: Sensitive files shall not be exposed through guessable public URLs.
- NFR-SEC-006: Administrative actions shall be auditable.
- NFR-SEC-007: Dependencies shall be scanned in CI.

### Privacy

- NFR-PRV-001: Consent and legitimate purpose shall be captured for lead submissions.
- NFR-PRV-002: Retention rules shall be configurable by record type.
- NFR-PRV-003: Export and deletion workflows shall respect legal and audit constraints.

### Accessibility

- NFR-A11Y-001: Public experience shall target WCAG 2.2 AA.
- NFR-A11Y-002: Keyboard navigation, focus visibility, semantic structure, labels, and error feedback are mandatory.

### Reliability

- NFR-REL-001: The application shall expose health and readiness endpoints.
- NFR-REL-002: Queue failures shall be visible and retryable.
- NFR-REL-003: Backups and restore procedures shall be documented and tested before production acceptance.
- NFR-REL-004: Errors shall include correlation identifiers without leaking sensitive details.

### Localization

- NFR-L10N-001: UI strings shall not be hard-coded in one language.
- NFR-L10N-002: Dates, numbers, currencies, addresses, and phone formatting shall be locale-aware.
- NFR-L10N-003: Arabic layouts shall be independently acceptance-tested.

## 9. Integrations

Initial integrations shall be abstracted behind adapters:

- email delivery
- object storage
- analytics/consent
- anti-spam
- optional CRM or accounting export
- future e-invoicing provider
- future messaging channels

No integration shall be represented as live before production credentials and end-to-end proof exist.

## 10. Acceptance requirements

A release candidate shall not be accepted unless:

- requirements are traceable to implemented files and tests
- migrations pass from an empty database
- automated tests pass
- static analysis and formatting pass
- no critical dependency vulnerability remains unresolved
- responsive English and Arabic visual evidence is attached
- public claims are verified or withheld
- accessibility checks pass
- staging deployment and health proof pass
- rollback steps are documented
