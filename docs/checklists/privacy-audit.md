# Tool 14 — Privacy Audit (`/privacy-audit`)

Intent: "gdpr checker", "cookie consent audit", "privacy policy checker".

Profile: renderer session with a cold profile; passive observation first
(pre-consent network/cookie/localStorage capture with zero interaction), then
scripted consent interactions (accept path, reject path) with network capture
after each; certified-CMP detection against a bundled, versioned signature
list in config (`config/cmp-signatures.php`) with source URLs — no API calls.

Scope statement (visible on page and report): a technical-signal audit, not
legal advice; qualified counsel is required for legal interpretation.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | Policy pages presence & linkage | 20 |
| 2 | CMP & TCF signals | 20 |
| 3 | Consent Mode v2 signals | 15 |
| 4 | Pre-consent tracker leakage | 15 |
| 5 | Disclosure accuracy vs observed | 10 |
| 6 | Forms & notices | 10 |
| 7 | CCPA/GPC & children's disclosures | 10 |

Cap: CAP-PRIV-01 (third-party trackers fire before consent with no banner
present → capped at 40).

## Checks

### 1. Policy pages presence & linkage (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| PRV-POL-01 | Privacy Policy present, substantive, linked site-wide | C | 30 | A | Discovery + content checks + link census |
| PRV-POL-02 | Cookie Policy (or cookie section in privacy policy) present when cookies observed | M | 20 | A | Cookie census × page discovery |
| PRV-POL-03 | Terms present where transactions/UGC exist | M | 15 | A | Discovery |
| PRV-POL-04 | Refund/cancellation policy present where purchases possible | M | 10 | A | Commerce-signal detection × discovery |
| PRV-POL-05 | Policy pages reachable within 1 click from sampled pages | M | 15 | A | Link census |
| PRV-POL-06 | Policy pages indexable and not accidentally noindexed | L | 10 | A | Directive check |

### 2. CMP & TCF signals (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| PRV-CMP-01 | Consent banner/dialog detected on first render (heuristics: fixed overlay with choice controls, common CMP DOM signatures) | H | 15 | A | Overlay detector + signature match |
| PRV-CMP-02 | Certified CMP identified by script/DOM signature from the bundled versioned list (name + version shown; else "unrecognized consent tooling") | M | 15 | A | Signature matcher (`config/cmp-signatures.php`) |
| PRV-CMP-03 | IAB TCF present: `__tcfapi` functional, TC string retrievable and parses, vendor-list version referenced | M | 20 | A | API probe in renderer + TC string parser |
| PRV-CMP-04 | Banner presented before content interaction (not scroll-baited-only) | M | 10 | A | Render timing probe |
| PRV-CMP-05 | Rejecting is as easy as accepting: reject button present, equal visual weight, same interaction depth (no reject buried in second layer while accept is first-layer) | H | 25 | A | Scripted both-path interaction + geometry/style comparison |
| PRV-CMP-06 | Consent state persists and is re-presented appropriately (link to re-open preferences exists) | L | 15 | A | Storage probe + preferences-link census |

### 3. Consent Mode v2 signals (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| PRV-CMV2-01 | `gtag`/GTM present and Consent Mode API usage detected (consent default set before/with tag load) | M | 25 | A | Script census + `gtag('consent'...)` call detection in captured calls |
| PRV-CMV2-02 | Consent default denies non-essential categories by default (ads/analytics personalization) in EEA-facing configuration | H | 30 | A | Behavior probe: network requests to ad/analytics hosts before consent interaction |
| PRV-CMV2-03 | Consent update fires on interaction (accept and reject paths produce distinct, persisted states) | M | 25 | A | Scripted interaction + storage/network diff |
| PRV-CMV2-04 | `ads_data_redaction` and `url_passthrough` settings observed where ad personalization is used | L | 20 | A | Config-signal scan in captured gtag calls |

### 4. Pre-consent tracker leakage (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| PRV-LEAK-01 | Third-party requests fired before any consent interaction (host census with categories: advertising, analytics, personalization) | C | 35 | A | Cold-profile network log (no interaction) |
| PRV-LEAK-02 | Cookies/localStorage written before consent (identifier-like names; category classification via versioned pattern table) | C | 30 | A | Storage diff before interaction |
| PRV-LEAK-03 | After "reject", no residual ad/analytics requests continue on subsequent navigation within the session | H | 25 | A | Post-reject navigation capture |
| PRV-LEAK-04 | After "accept", only consented categories observed active | M | 10 | A | Post-accept capture |

### 5. Disclosure accuracy vs observed (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| PRV-DIS-01 | Cookie policy names the observed first-party cookies (string overlap census; unnamed observed cookies listed) | M | 40 | A | Policy text extraction × observed cookie names |
| PRV-DIS-02 | Third-party vendors disclosed match observed third-party hosts (named-vendor census) | M | 35 | A | Vendor-name extraction × host census |
| PRV-DIS-03 | Policy states retention or retention-pointer for major categories | L | 25 | A | Pattern check (retention/duration language census) |

### 6. Forms & notices (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| PRV-FRM-01 | Data-collecting forms (contact, newsletter, comments) carry a privacy notice/link adjacent to the form | M | 35 | A | Form census × notice detection |
| PRV-FRM-02 | Consent checkboxes unticked by default (no pre-ticked marketing consent) | H | 30 | A | Form state probe |
| PRV-FRM-03 | Newsletter/marketing forms state purpose and identity | L | 20 | A | Text census |
| PRV-FRM-04 | Double opt-in signal present for newsletters where observable | L | 15 | A | Static signals (N/A when unobservable) |

### 7. CCPA/GPC & children's disclosures (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| PRV-CCPA-01 | "Do Not Sell or Share My Personal Information" link present when site targets California (self-declared or privacy-policy jurisdiction statements) | M | 30 | A | Footer/policy census |
| PRV-CCPA-02 | Global Privacy Control honored: banner/API reacts to GPC-enabled profile (renderer launched with GPC signal) | M | 30 | A | GPC-emulated session comparison |
| PRV-CHI-01 | Children's data disclosures present when content targets children (age-screening signals, parental-consent language) | M | 25 | A | Content classification (pattern census) × policy text |
| PRV-ACC-01 | Consent interface keyboard-operable and labeled (shares Tool 8 form/banner accessibility evidence) | M | 15 | A | Shared a11y measurements |

## Interaction rules

Probes interact only with consent UI (accept, reject, save, preferences).
They never create accounts, submit contact forms, or purchase anything. Every
capture stores host, cookie names, storage keys, and timestamps — evidence,
not conclusions. No personal data is harvested from target sites.

## Tier B panels for this tool

None required. All signals are measured directly. (Certified-CMP signature
list is bundled and versioned, not an API.)

## Outputs

Pre-consent leakage inventory with hosts/cookies · both-path consent
comparison (accept vs reject network diffs, side by side) · TCF/Consent Mode
signal report · disclosure-accuracy diff table · policy-page audit ·
prioritized fixes with config-level guidance (CMP settings, tag ordering).
