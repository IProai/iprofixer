# IProFixer Implementation Plan

## 1. Delivery objective

Build IProFixer as a bilingual Hospitality Asset Lifecycle Care platform for UAE and KSA, beginning with the approved light-mode public experience and the commercial entry point of silverware, flatware, and hollowware restoration.

## 2. Product surfaces

1. Public Experience
2. Content & Growth Console
3. Commercial Workspace
4. Operations & Finance Workspace
5. Client Portal

These surfaces may share one modular Laravel application, but each requires its own requirements, permissions, routes, data ownership, and acceptance gates.

## 3. Release sequence

### R0 — Business readiness and technical foundation

Deliver:
- repository governance and source-of-truth structure
- Laravel application foundation
- environment configuration pattern
- PostgreSQL baseline
- queue/cache/mail/storage abstractions
- authentication foundation
- role and permission baseline
- CI quality gates
- logging, health checks, and error handling
- content-truth register and launch assumptions

Exit gate:
- clean build and tests
- no committed secrets
- documented local setup
- health endpoint proven
- architecture boundaries established

### R1 — Approved design system and homepage shell

Deliver:
- exact light-mode design tokens derived from the approved visual
- responsive header, navigation, mobile menu, footer
- homepage sections in approved order
- English content architecture
- Arabic translation structure and true RTL layout
- reusable buttons, cards, badges, statistic blocks, service cards, proof modules, CTA bands
- RFQ/consultation lead capture
- CMS-manageable homepage content boundaries

Exit gate:
- desktop visual fidelity
- mobile responsive proof
- Arabic RTL proof
- accessibility and keyboard proof
- no fabricated client logos, metrics, certifications, or claims

### R2 — Bilingual public website launch

Deliver:
- services and service detail pages
- industries and industry detail pages
- process page
- proof/results case-study architecture
- about page
- resources architecture
- contact and consultation flows
- legal pages
- SEO, sitemap, structured data, analytics consent
- PWA baseline

### R3 — CMS and commercial workspace

Deliver:
- content workflow and publishing
- media library
- leads, contacts, organizations, opportunities
- assessment/site-visit records
- quotations and proposal documents
- activity history and follow-up tasks

### R4 — Operations and finance backbone

Deliver:
- contracts/PO records
- jobs, batches, counts, chain of custody
- quality checks, exceptions, rework, delivery
- invoices, receipts, credit notes, aging
- recurring care plans

### R5 — Client portal

Deliver:
- secure client access
- property and contact scope
- quotation, job, delivery, certificate, and invoice visibility
- controlled downloads and messages

### R6 — Evidence-led automation and AI

Deliver only validated use cases with human control, auditability, and measurable operational benefit.

## 4. Initial execution packet

The first implementation packet is bounded to R0 and R1 foundations:

1. Scaffold the Laravel modular monolith.
2. Configure PostgreSQL, Redis-compatible queue/cache, mail, and object storage through environment variables.
3. Add health, readiness, logging, and exception conventions.
4. Add authentication and initial roles.
5. Implement design tokens and approved public shell.
6. Build the homepage section sequence.
7. Add consultation/RFQ submission and admin review path.
8. Add Arabic locale and RTL foundations.
9. Add automated tests and CI.
10. Produce visual and functional acceptance evidence.

## 5. Definition of done

A packet is complete only when:
- code is committed on a bounded branch
- tests and static checks pass
- migrations are reversible and repeatable
- English and Arabic behavior is verified where applicable
- visual implementation is compared against the approved reference
- security and accessibility checks pass
- no unsupported public claims are introduced
- documentation is updated in the same change
