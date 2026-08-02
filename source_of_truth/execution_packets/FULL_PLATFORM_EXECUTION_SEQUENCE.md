# IProFixer Full Platform Execution Sequence

**Date:** 29 July 2026  
**Branch:** `agent/full-platform-implementation`  
**Authority:** Master Blueprint V2 → SRS → UI/UX Specification → Implementation Plan → approved visual reference → bounded execution packet.

## Purpose

This packet controls continuous implementation from the accepted R0 foundation through deployment readiness. Work proceeds release by release without treating individual CI corrections as product milestones. A release is merged only after its full gate passes.

## Non-negotiable laws

- Light mode only.
- English and Arabic are equal launch requirements with true RTL.
- No public pricing or exposed pricing logic.
- No fabricated clients, offices, certifications, metrics, capabilities, or compliance claims.
- Partner-delivered work is governed and represented truthfully.
- Smart defaults and deterministic automation precede AI.
- Modular Laravel monolith; no premature microservices.
- Public experience, Content & Growth Console, Commercial Workspace, Operations & Finance Workspace, and Client Portal remain bounded surfaces.
- Hostinger deployment occurs only after the deployability and release audit passes.

## Release sequence

### R1 — Product foundation and governance

Implement:

- Authentication, password reset, session security, and invitation boundaries.
- Roles, permissions, policies, audit events, and privileged-action controls.
- Countries, markets, regions, legal entities, currencies, tax profiles, contact routes, and activation governance.
- Global settings and feature/release controls.
- Queue, scheduler, mail, logs, error handling, backups, and restore procedures.
- Secure media metadata and upload validation boundary.

Exit proof:

- Identity and permission tests pass.
- Inactive markets and unapproved claims cannot publish.
- Audit records preserve actor, state change, reason, related record, and correlation identifier.
- Environment and operational runbooks are reproducible.

### R2 — Design system and public website

Implement:

- Approved navy/gold/off-white design tokens.
- Utility bar, responsive navigation, accessible mobile menu, locale-preserving language switch, footer, buttons, cards, badges, forms, and section shells.
- Homepage matching the approved visual hierarchy.
- Services index and approved service pages.
- Industries index and approved industry pages.
- Process, Proof & Results, About, Resources, FAQ, Contact/RFQ, Privacy, Cookies, and Terms.
- Progressive RFQ with secure uploads, non-sequential public reference, acknowledgement, internal routing, spam protection, and rate limiting.
- SEO metadata, canonical URLs, localized sitemap, robots controls, schema, redirects, and social previews.
- PWA manifest and installability baseline.

Exit proof:

- Desktop, tablet, mobile, English, Arabic, keyboard, and RTL acceptance passes.
- No unsupported public claim appears.
- A real buyer can discover the offer and submit a qualified enquiry.

### R3 — Content & Growth Console and commercial workspace

Implement:

- Governed page sections and design-system-controlled variants.
- Services, divisions, industries, locations, case studies, resources, FAQs, testimonials, claims, menus, redirects, and global settings.
- Translation completeness, draft/review/approved/published/archive workflow, revision history, and claim expiry.
- Media alt text, rights/source, focal points, crop variants, metadata, and usage references.
- Account groups, accounts, properties, contacts, stakeholder roles, leads, opportunities, activities, tasks, ownership, qualification, priorities, next actions, and loss reasons.
- RFQ-to-lead conversion without duplicate entry.
- Duplicate detection and controlled override.
- Versioned quotation/proposal workflow with taxes, validity, assumptions, exclusions, attachments, approval thresholds, and document generation.

Exit proof:

- One RFQ becomes an owned qualified opportunity and a versioned proposal with provenance and audit history.

### R4 — Operations and finance backbone

Implement:

- Contracts and purchase orders.
- Jobs, batches, intake, counts, condition records, custody, work stages, partner assignment, quality checks, defects, exceptions, corrective actions, packing, delivery, acceptance, and certificates.
- Count reconciliation at controlled checkpoints.
- Invoices, payments, allocations, balances, due dates, overdue status, credit notes, statements, expenses, partner costs, and accounting export boundary.
- Recurring asset-care programs, entitlements, frequencies, next due dates, reminders, missed-service handling, and renewals.
- Controlled lifecycle propagation from won opportunity to contract/order, job, and finance records without duplicate master data.

Exit proof:

- One engagement is traceable from opportunity through delivery, quality, invoice, payment, and recurring follow-up.

### R5 — Client portal

Implement:

- Invitation-only access.
- Group/account/property scoping and document permissions.
- Property switcher, active-job summary, simplified timeline, authorized documents, service history, invoices/statements where permitted, assigned contact, and notification preferences.
- Client-visible actions and support access are audited.

Exit proof:

- Cross-account and cross-property authorization tests pass.
- Approved clients see only explicitly authorized records.

### R6 — Deployment readiness and final release audit

Implement and prove:

- Hostinger-compatible production build and environment contract.
- Worker, scheduler, storage, private-file, mail, queue, cache, logging, TLS, DNS, backup, and restore plan.
- Database migration and rollback procedure.
- Production deployment script/runbook and smoke checks.
- Security, accessibility, bilingual/RTL, performance, document rendering, upload, notification-failure, and permission-denied tests.
- Blueprint/SRS/UI/UX/implementation traceability matrix.

Exit proof:

- No open critical or high-severity defect.
- Backup restoration is proven.
- Deployment is reproducible and rollback is documented.
- The implementation traceability matrix has no silent contradiction.

## Continuous merge discipline

For each release:

1. Implement on a bounded branch derived from the latest accepted base.
2. Add migrations, policies, tests, operational notes, and evidence in the same pull request.
3. Run the complete CI pipeline.
4. Correct only proven failures while preserving scope.
5. Update the pull-request evidence with exact commit and run references.
6. Merge only when the release exit gate passes.
7. Revalidate the receiving branch after merge.

## Final acceptance

The platform is not classified complete merely because screens exist. Completion requires correct business behavior, permissions, bilingual content and RTL, mobile behavior, empty/error/loading/permission-denied states, auditability, secure uploads, document rendering, integration failure handling, operational monitoring, backup restore proof, and deployment readiness.
