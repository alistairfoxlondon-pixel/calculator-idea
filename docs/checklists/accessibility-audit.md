# Tool 8 — Accessibility Audit (`/accessibility-audit`)

Intent: "accessibility checker", "wcag checker", "a11y audit".

Scan profile: self-hosted axe-core injected across the rendered sample plus
our own heuristics in the renderer (focus, contrast on rendered pixels, zoom,
text spacing, target size, reduced motion). Mandatory honesty statement in UI
and report: automated testing detects only a subset of WCAG issues; this
report never certifies legal compliance; manual and assistive-technology
testing is required.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Automated violations (axe-core) | 60 |
| 2 | Keyboard & focus heuristics | 15 |
| 3 | Contrast & visual | 10 |
| 4 | Document & landmark structure | 8 |
| 5 | Forms & error handling | 7 |

## Checks

### 1. Automated violations (60)

axe-core (pinned version) runs on the rendered sample. Violations are grouped
by rule with impact, node counts, selectors, and fix guidance. Scoring uses an
impact-weighted violation density per page, then aggregates.

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| A11Y-AXE-01 | No critical violations (axe impact=critical) on any sampled page | C | 30 | A | axe-core results |
| A11Y-AXE-02 | No serious violations | H | 25 | A | axe-core results |
| A11Y-AXE-03 | Moderate violations below density threshold per page | M | 15 | A | axe-core results |
| A11Y-AXE-04 | Images: alt present, decorative marked, name not duplicated (axe rules grouped) | H | 10 | A | axe rules: image-alt, alt-space, duplicate-img-label |
| A11Y-AXE-05 | ARIA used per spec (roles valid, required owned elements, no aria-hidden on focusable) | H | 10 | A | axe rules: aria-* family |
| A11Y-AXE-06 | Language and titles: `lang` attribute present/valid, page titles unique and descriptive | M | 5 | A | axe + DOM audit |
| A11Y-AXE-07 | Tables: headers/th association correct | M | 5 | A | axe rules: table-* family |

### 2. Keyboard & focus heuristics (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| A11Y-KB-01 | Visible focus indicator present (focus-visible styles non-zero) on interactive elements | H | 20 | A | Renderer focus probe + computed styles |
| A11Y-KB-02 | Tab order follows reading order (no jumps to hidden/off-screen elements) | M | 15 | A | Tab-walk probe |
| A11Y-KB-03 | No keyboard traps (focus can always leave modals/menus with Escape/Tab) | C | 20 | A | Renderer interaction probe |
| A11Y-KB-04 | Skip-to-content link present and functional | M | 10 | A | DOM + activation probe |
| A11Y-KB-05 | Modals trap focus while open and restore on close | M | 15 | A | Modal probe (when modals exist; else N/A) |
| A11Y-KB-06 | Custom widgets (menus, tabs, accordions, carousels) keyboard-operable with correct ARIA patterns | M | 20 | A | Pattern detector + interaction probe |

### 3. Contrast & visual (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| A11Y-CON-01 | Text contrast ≥ 4.5:1 (≥ 3:1 large text) measured on rendered pixels (backgrounds incl. images/gradients sampled) | H | 40 | A | Rendered pixel contrast measurement (§3.9) |
| A11Y-CON-02 | UI component boundaries and state indicators ≥ 3:1 against adjacent colors | M | 20 | A | Pixel measurement on controls |
| A11Y-CON-03 | Page usable at 200% zoom (no overlap/clip) | M | 20 | A | Zoom probe (200%) |
| A11Y-CON-04 | Text-spacing override (line-height/letter/word/paragraph increases) causes no clipping or overlap | M | 10 | A | Text-spacing probe (WCAG 1.4.12 values) |
| A11Y-CON-05 | Target size ≥ 24 × 24 px (WCAG 2.2 SC 2.5.8) | M | 10 | A | Geometry probe (shared with Tool 4) |

### 4. Document & landmark structure (8)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| A11Y-DOC-01 | One main landmark; header/nav/footer/contentinfo landmarks present | M | 25 | A | Landmark census |
| A11Y-DOC-02 | Heading order logical (no skips; H1 present) | M | 25 | A | Heading tree |
| A11Y-DOC-03 | Lists marked up as lists; no fake lists via `<br>` | L | 15 | A | Structure census |
| A11Y-DOC-04 | `prefers-reduced-motion` respected (auto-playing motion pauses; no essential motion-only cues) | M | 20 | A | Emulated reduced-motion render probe |
| A11Y-DOC-05 | Dragging movements have non-drag alternatives (WCAG 2.2 SC 2.5.7) where drag interactions exist | L | 15 | A | Drag-dependence probe (N/A when none) |

### 5. Forms & error handling (7)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| A11Y-FRM-01 | Every input has a programmatic label (label/aria-label/title rules per axe) | C | 30 | A | axe + DOM audit |
| A11Y-FRM-02 | Required fields and constraints indicated in text (not color/asterisk alone) | M | 20 | A | Form audit |
| A11Y-FRM-03 | Errors identified in text and associated to fields (aria-describedby/descendant messaging) | H | 25 | A | Scripted invalid-submit probe |
| A11Y-FRM-04 | Fieldsets/legends group related controls; selects have meaningful options | L | 10 | A | DOM audit |
| A11Y-FRM-05 | Redundant entry avoided where flows testable without submission (autosave/prefill signals) | L | 15 | A | Static audit; `not_measured` without a testable flow |

WCAG 2.2 additions scoped in: 2.4.11 focus not obscured (focus probe records
occlusion), 2.5.8 target size, 3.3.7 redundant entry, 3.3.8 accessible
authentication (static check for captcha-only paths — flagged for review).

## Tier B panels for this tool

None. All measurement is self-hosted (axe-core + our renderer heuristics).

## Mandatory honesty surfaces

- UI banner and report section: "Automated testing detects only a subset of
  WCAG success criteria. This report is not a legal compliance certificate.
  Manual testing with assistive technologies is required."
- Each axe rule links to its rule documentation; each heuristic links to the
  methodology entry with the probe definition.

## Outputs

Violation table grouped by rule with impact, count, selectors, and fixes ·
keyboard/focus probe results · contrast report with sampled pairs · structure
report · forms report · per-page score breakdown with sample sizes and
confidence.
