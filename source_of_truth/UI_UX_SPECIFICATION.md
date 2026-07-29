# IProFixer UI/UX Specification

## 1. Visual authority

The approved homepage concept is the visual source of truth for R1. Implementation must preserve its light, premium hospitality character rather than reinterpret it as a generic SaaS website.

## 2. Non-negotiable rules

- Light mode only.
- Spacious white and warm-neutral surfaces.
- Deep navy for primary typography and structural contrast.
- Restrained gold accent for primary CTAs, emphasis, and selected details.
- No gradients, neon effects, glassmorphism, heavy shadows, or dark sections that resemble dark mode.
- Hospitality-led photography with polished silverware, table settings, controlled workshop detail, and property environments.
- No unverified logos, statistics, certifications, or client claims.
- English and Arabic must have equal visual quality.

## 3. Page structure

Homepage order:
1. Utility bar
2. Main navigation
3. Hero
4. Trust/evidence strip
5. Services
6. Results / before-and-after
7. Operational proof metrics
8. Industries
9. Final consultation CTA
10. Structured footer

## 4. Layout system

- Desktop content container: approximately 1200–1320 px.
- Main page side padding: 24 px minimum, increasing on large screens.
- Section spacing: generous and consistent; target 80–120 px desktop and 48–72 px mobile.
- Grid: 12-column desktop, 8-column tablet, 4-column mobile.
- Corners: restrained; avoid excessively rounded SaaS cards.
- Borders: subtle warm-gray or navy-tinted dividers.
- Shadows: minimal and used only for functional elevation.

## 5. Typography

- Display/headings: refined editorial serif suitable for premium hospitality.
- UI/body: highly readable sans serif.
- Headings use navy, strong hierarchy, and controlled line lengths.
- Gold may emphasize one phrase, never entire paragraphs.
- Body copy should generally remain between 16–18 px on desktop.
- Arabic typography must be selected and tested independently; it must not be treated as an afterthought or mechanical font substitution.

## 6. Core components

Required reusable components:
- utility bar
- desktop and mobile navigation
- language switcher
- client portal link
- primary and secondary CTA buttons
- evidence badge
- service card
- industry card
- logo/evidence strip
- before/after comparison module
- benefit checklist
- statistic block
- CTA band
- office/contact footer group
- RFQ/consultation form

## 7. Hero behavior

- Two-column desktop composition with message and actions on the left and premium silverware/table-setting imagery on the right.
- Main message hierarchy: category label, headline, outcome-led body, evidence points, primary and secondary CTA.
- Hero image must remain prominent and may crop responsively without hiding the key silverware subject.
- Mobile stacks content before image unless usability testing supports an alternative.

## 8. Responsive behavior

- Navigation collapses to a clear mobile menu.
- CTA remains visible without crowding the header.
- Four-column services become two-column tablet and one-column mobile.
- Statistics wrap cleanly with labels retained.
- Before/after module must remain understandable and operable by touch and keyboard.
- No horizontal page overflow at 320 px viewport width.

## 9. Arabic and RTL

- Set document direction at locale boundary.
- Mirror directional layout and icons where semantics require it.
- Do not mirror logos, photographs, phone numbers, email addresses, or non-directional symbols.
- Arabic navigation and CTA copy must be professional GCC business Arabic.
- Re-test line lengths, heading wraps, card heights, and mobile navigation independently in Arabic.

## 10. Accessibility

- Keyboard-operable navigation and interactive elements.
- Visible focus states.
- Semantic headings and landmarks.
- Accessible form labels and error summaries.
- Contrast at WCAG AA minimum.
- Alternative text for meaningful images.
- Reduced-motion support.

## 11. Visual acceptance

A page cannot be accepted based only on functional completion. R1 requires screenshot comparison at representative desktop, tablet, and mobile widths for English and Arabic.

Reject implementation when it:
- introduces dark mode or dark visual dominance
- uses generic templates instead of the approved composition
- changes the section hierarchy without source-of-truth approval
- replaces hospitality imagery with abstract technology artwork
- fabricates proof or uses placeholder proof as though it were real
- produces materially weaker Arabic layouts
