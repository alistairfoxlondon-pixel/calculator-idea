# Tool 6 — Schema Audit (`/schema-audit`)

Intent: "schema checker", "structured data test", "rich results checker".

Scan profile: extraction of JSON-LD, microdata, and RDFa across the rendered
sample; validation against a bundled, versioned copy of the Schema.org
vocabulary in the repository (no external validator endpoint, no model);
entity consistency checks against visible content; eligibility assessment per
page type.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Syntax validity | 25 |
| 2 | Type & property completeness | 30 |
| 3 | Rich-result eligibility | 20 |
| 4 | Entity consistency | 15 |
| 5 | Legacy & mixed formats | 10 |

## Checks

### 1. Syntax validity (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SCH-SYN-01 | All JSON-LD blocks parse as strict JSON | C | 30 | A | Strict parser with byte-offset evidence |
| SCH-SYN-02 | `@context` present and equals a valid schema.org URL | C | 15 | A | Parse tree |
| SCH-SYN-03 | `@type` values exist in bundled vocabulary (including grammar-aware: enum, class) | H | 20 | A | Vocabulary lookup |
| SCH-SYN-04 | Property values type-check (dates parse as dates, numbers as numbers, URLs resolve syntactically) | H | 20 | A | Type validator |
| SCH-SYN-05 | Microdata itemscope/itemtype well-formed; RDFa vocab/typeof well-formed | M | 10 | A | DOM parse |
| SCH-SYN-06 | No duplicate `@id` conflicts within a page | M | 5 | A | Graph lint |

### 2. Type & property completeness (30)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SCH-REQ-01 | Required properties present per detected type (per bundled type profile: Article, BlogPosting, Product, Organization, LocalBusiness, BreadcrumbList, FAQPage, HowTo, Recipe, Event, Person, WebSite, VideoObject) | H | 35 | A | Type profile table in rulepack |
| SCH-REQ-02 | Recommended properties present (penalized as warn, not fail) | M | 20 | A | Same |
| SCH-REQ-03 | Nested entities resolvable (author → Person, publisher → Organization with logo) | M | 15 | A | Graph walker |
| SCH-REQ-04 | Image/logo properties use absolute resolvable URLs | M | 10 | A | URL probes (HEAD) |
| SCH-REQ-05 | Date properties valid and ordered (datePublished ≤ dateModified) | M | 10 | A | Date parser |
| SCH-REQ-06 | BreadcrumbList matches visible breadcrumbs and URL hierarchy | M | 10 | A | DOM breadcrumb diff |

### 3. Rich-result eligibility (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SCH-RICH-01 | Per-page eligibility matrix computed (which bundled rich-result types the page qualifies for) | I | 0 | A | Eligibility evaluator over validation results |
| SCH-RICH-02 | Content pages carry an article type with complete required set | M | 35 | A | Validator |
| SCH-RICH-03 | Product pages (if present) carry offers with price, currency, availability | H | 25 | A | Validator + visible-content cross-check |
| SCH-RICH-04 | FAQPage/HowTo used only where visible matching content exists (hidden-markup rule) | H | 20 | A | Visible-content diff |
| SCH-RICH-05 | VideoObject matches a real embedded/linked video | M | 20 | A | Media census × markup |
| SCH-RICH-06 | Pages with no eligible type are explicitly stated (not failed) | I | 0 | A | Eligibility evaluator |

### 4. Entity consistency (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SCH-ENT-01 | Organization name/logo/url consistent sitewide | M | 20 | A | Entity merge across pages |
| SCH-ENT-02 | Author names match visible bylines | H | 20 | A | Byline × markup diff |
| SCH-ENT-03 | Product price/availability in markup matches rendered visible price | H | 20 | A | Rendered text extraction × markup |
| SCH-ENT-04 | Dates in markup match visible dates | M | 15 | A | Same |
| SCH-ENT-05 | sameAs targets resolve 200 | M | 15 | A | Link checker |
| SCH-ENT-06 | One canonical Organization node (no conflicting duplicates per @id/name) | M | 10 | A | Entity graph lint |

### 5. Legacy & mixed formats (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SCH-LEG-01 | No deprecated/dropped rich-result types relied on (per bundled deprecated-types list, e.g. FAQ/HowTo restrictions recorded with the rulepack's verification date) | M | 30 | A | Deprecated list (versioned, dated in rulepack meta) |
| SCH-LEG-02 | Microdata/RDFa not duplicating JSON-LD with conflicting values | M | 30 | A | Entity merge conflict detector |
| SCH-LEG-03 | No data-vocabulary.org (retired) markup | H | 25 | A | Context scan |
| SCH-LEG-04 | Single dominant format per page (mixing tolerated only when non-conflicting) | L | 15 | A | Format census |

## Hidden-markup rule

Markup describing content absent from the visible, rendered DOM (display:none
data, off-screen FAQ answers) is flagged `warn` with the exact diff, mapped to
the hidden-markup policy concern. Evidence: selector + visible/hidden text
pairs.

## Tier B panels for this tool

None required. (Google's Rich Results Test is not called; validation is
bundled and versioned. The methodology page states this explicitly.)

## Outputs

Per-URL markup inventory (all blocks with parse status) · type profile table ·
eligibility matrix · entity consistency report · deterministic corrected
JSON-LD snippets composed from the site's own real data by rule templates
(original compositions, never copied from any reference site) · deprecated
format report.
