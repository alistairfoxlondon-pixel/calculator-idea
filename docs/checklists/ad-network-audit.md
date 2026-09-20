# Tool 13 — Ad Network Audit (`/ad-network-audit`)

Intent: "ad network eligibility", "mediavine requirements", "ezoic
requirements".

Profile-driven design: every network's published requirements live in the
versioned `network_requirement_profiles` table (network, requirement code,
threshold, comparator, source_url, last_verified_at, notes, change history).
Thresholds are never hardcoded in code or templates. Phase 8 seeds and
verifies every row against each network's own published pages, recording the
source URL and verification date.

Traffic truth rules: we never estimate a site's traffic from third-party
sources. Traffic-dependent requirements show one of: `Met`/`Not met` from the
owner's connected GA4/Search Console (free OAuth, owner consented), or
`Self-reported` when the owner enters figures (labelled), or `Unknown — check
your analytics` with a short guide. Integrity rules: no acceptance promises,
no artificial-traffic or incentivized-click advice, no policy-violating
placement tricks, no revenue estimates.

## Our measurements (shared with other tools)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| NET-DEPTH-01 | Content depth gate (median words, substantial-page count) | H | 15 | A | Shared content measurements |
| NET-QUAL-01 | Content quality gate (originality, duplication, boilerplate) | H | 12 | A | Shared content measurements |
| NET-LEGAL-01 | Required pages gate (privacy, about, contact; advertising disclosure where required) | H | 12 | A | Shared trust-page discovery |
| NET-POL-01 | Policy-risk scan (shared lexicon engine with Tool 12; per-network restricted categories applied from profiles) | C | 12 | A | Lexicon engine |
| NET-ADS-01 | ads.txt presence and validity | M | 5 | A | Fetch + parser |
| NET-CMP-01 | CMP/consent readiness (TCF signals, Consent Mode v2) — required by many networks for EEA traffic | H | 12 | A | Shared privacy measurements |
| NET-CWV-01 | Core Web Vitals lab assessment (LCP, INP proxy, CLS) | M | 12 | A | Shared speed measurements |
| NET-MOB-01 | Mobile usability | M | 5 | A | Shared mobile measurements |
| NET-IDX-01 | Indexability and HTTPS | M | 5 | A | Shared technical measurements |
| NET-EXP-01 | Ad-experience patterns (no pop-unders, auto-refresh, deceptive placement) | H | 5 | A | Behavior probes + script signatures |
| NET-TRAFFIC-01 | Traffic-gated requirements: sessions/pageviews per profile threshold | — | 5 | A+B | GA4 OAuth (Tier B, consented) or owner entry (self-reported) or `Unknown` (default) |

W note: NET-TRAFFIC-01 carries 5 units of the profile score; when traffic is
`Unknown`, those units are excluded from the applicable denominator and the
row shows Unknown — the score never guesses.

## Eligibility matrix logic

For each network profile row:

- Comparator semantics: `min_sessions_30d`, `min_pageviews_30d`,
  `min_substantial_pages`, `required_pages:[codes]`, `restricted_categories`,
  `cmp_required`, `cwv_thresholds`, `geo_availability` (owner-declared audience
  region, self-reported), `platform_constraints`.
- Each requirement renders `Met` / `Not met` / `Unknown` with its evidence and
  provenance badge (Our measurement / Your analytics / Self-reported / Not
  applicable in your region — as declared).
- Gap text: the exact numeric or structural gap and the fastest legitimate
  path to meeting it, from the profile's documented requirement — no invented
  advice.
- "Best current fit" ranking: networks whose Met-requirement coverage is 100%
  of applicable rows, ordered by fewest gaps, then fewest Unknowns.

## Profiles maintained at launch (Phase 8, each with sourced verification)

Google AdSense · Ezoic · Mediavine · Raptive · Monumetric · Journey (by
Mediavine) · Sovrn · Taboola · MGID · PropellerAds. Each row records
`source_url` + `last_verified_at`; `docs/BACKLOG.md` B-02 tracks the
verification work. A scheduled job flags rows older than the review window
(90 days) for re-verification.

## Outputs

Side-by-side eligibility matrix across all profiled networks · per-network gap
list with provenance badges · ranked best-current-fit · shared underlying
measurements with links back to the deep tools · explicit Unknowns with the
"check your analytics" guide.
