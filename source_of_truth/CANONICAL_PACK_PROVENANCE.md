# Canonical IProFixer Execution Pack Provenance

**Canonical pack date:** 29 July 2026  
**Project:** IProFixer  
**Domain:** `iprofixer.com`

The approved project pack used to govern implementation contains the following exact artifacts. Their SHA-256 digests are recorded so repository copies and future deployment bundles can be checked for silent alteration.

| Artifact | SHA-256 |
|---|---|
| `00_IPROFIXER_MASTER_BLUEPRINT_V2.md` | `a484e88310971e2512930a350aa5c500e58ca9fe8ee15e034c78df8b0abf4709` |
| `01_IMPLEMENTATION_PLAN.md` | `461ab033dbf924e3fcf297d86bed598298811cbe14d5a3a3a20705e8a9477926` |
| `02_UI_UX_SPECIFICATION.md` | `2c605bb96f69ee5e042713f3fbfe32c908942fab336ede4d8b30257c74ed8f8d` |
| `03_SRS.md` | `93c5dc93267adf82d448eb226525d2f61e1e4d59f16df179d87b6aa7ff7b839c` |
| `04_DELIVERY_SKILLS_INDEX.md` | `1c7f00b9a7eb08183a19357c426d0ebd79612a375bf26fe6f2edbdcc7e542b40` |
| `APPROVED_HOME_PAGE_VISUAL_REFERENCE.png` | `40b4bf81515d5e81985b6087174dcce525431c18e60be898f9a27462d03f6d3b` |

## Authority order

1. Master Blueprint V2.
2. SRS for software behavior.
3. UI/UX Specification for presentation and interaction.
4. Implementation Plan for sequencing and release boundaries.
5. Approved visual reference for homepage fidelity.
6. Bounded execution packets.
7. Delivery skills for execution discipline and release acceptance.

No lower artifact may silently overturn a higher authority. Any contradiction discovered during implementation must be resolved in the canonical source before code acceptance.

## Repository defect noted at R0 closure

At R0 closure, `source_of_truth/README.md` referenced `MASTER_BLUEPRINT_V2.md` as the highest authority while the file was absent from `main`. This branch records the provenance and establishes a controlled restoration requirement before later releases are accepted. The absence must not be treated as permission to reinterpret the product.
