# Tool 9 — Security Audit (`/security-audit`)

Intent: "security headers checker", "ssl test", "website security scan".

Scan profile: openssl TLS inspection · header grading on homepage + sample ·
cookie inventory across rendered sample · CORS probes with safe origins ·
non-destructive sensitive-file probes (HEAD/GET only, status/size evidence
only) · DNSSEC/CAA via our resolver · CT log subdomain exposure (Tier B).

Hard rules: non-destructive, rate-limited, GET/HEAD only; no auth attempts,
no injection, no fuzzing, no exploitation; file contents never exfiltrated —
existence proven via status/headers/size only. All documented on the
methodology page and in the ToS.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | TLS & HTTPS | 25 |
| 2 | Security headers | 25 |
| 3 | Exposure & sensitive files | 20 |
| 4 | Cookies, CORS, CSP correctness | 15 |
| 5 | DNS security | 10 |
| 6 | Misc hardening | 5 |

Cap: CAP-SEC-01 (no valid HTTPS → capped at 30).

## Checks

### 1. TLS & HTTPS (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEC-TLS-01 | Certificate valid, trusted chain, not expired (< 15 days remaining = warn), SAN matches host | C | 25 | A | openssl handshake + chain verify |
| SEC-TLS-02 | TLS 1.2 minimum; TLS 1.3 supported | H | 20 | A | Protocol probes per version |
| SEC-TLS-03 | SSLv3/TLS 1.0/1.1 rejected | H | 15 | A | Handshake attempts |
| SEC-TLS-04 | Strong cipher suites only (no RC4/3DES/NULL/EXPORT; AEAD suites) | M | 15 | A | Cipher enumeration |
| SEC-TLS-05 | Certificate chain complete (served intermediates; no missing chain causing mobile failures) | M | 10 | A | Chain build test |
| SEC-TLS-06 | OCSP stapling present | L | 5 | A | Handshake extension audit |
| SEC-TLS-07 | Key exchange strength ≥ 2048-bit RSA / ≥ 256-bit EC | L | 5 | A | Certificate + handshake params |
| SEC-TLS-08 | HTTP redirects to HTTPS same-host with 301/308 (not cross-host chains) | M | 5 | A | Redirect trace |

### 2. Security headers (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEC-HDR-01 | Content-Security-Policy present and not `unsafe-inline`/`unsafe-eval` script sources on HTML pages | H | 25 | A | Header parse + directive grader |
| SEC-HDR-02 | HSTS: max-age ≥ 15552000, ideally includeSubDomains (+preload noted) | H | 20 | A | Header inspection |
| SEC-HDR-03 | X-Content-Type-Options: nosniff | M | 10 | A | Header |
| SEC-HDR-04 | frame-ancestors 'none'/'self' (or X-Frame-Options DENY/SAMEORIGIN) | M | 10 | A | Header parse |
| SEC-HDR-05 | Referrer-Policy strict-origin-when-cross-origin or stricter | M | 10 | A | Header |
| SEC-HDR-06 | Permissions-Policy restricting sensitive features | L | 15 | A | Header parse |
| SEC-HDR-07 | COOP present (cross-origin-opener-policy) | L | 5 | A | Header |
| SEC-HDR-08 | No header conflicts (both XFO and frame-ancestors present is fine; contradictory values flagged) | L | 5 | A | Header diff |
| SEC-HDR-09 | CSP grader detail: default-src or script-src coverage, frame-ancestors within CSP, report-uri presence | I | 0 | A | Directive grader output |

### 3. Exposure & sensitive files (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEC-EXP-01 | `/.env` not accessible | C | 15 | A | HEAD/GET status+size only |
| SEC-EXP-02 | `/.git/config`, `/.git/HEAD` not accessible | C | 15 | A | Same |
| SEC-EXP-03 | `/phpinfo.php`, `/info.php`, `/server-status` style paths not accessible | H | 10 | A | Bounded probe list (≤ 12 paths) |
| SEC-EXP-04 | `/.DS_Store`, backup archives (`/backup.zip`, `/.bak`) not accessible | H | 10 | A | Bounded probe list |
| SEC-EXP-05 | Directory listing disabled on probed asset paths | M | 10 | A | Listing signature check |
| SEC-EXP-06 | No Server/X-Powered-By version banners | L | 10 | A | Header inspection |
| SEC-EXP-07 | Verbose error pages absent (stack traces on forced benign 404/500 paths — never induced; only observed errors in crawl) | M | 10 | A | Crawl-observed error bodies signature scan |
| SEC-EXP-08 | `/.well-known/security.txt` present and valid (positive signal) | L | 10 | A | Well-known fetch + RFC 9116 field lint |
| SEC-EXP-09 | Cookie/session identifiers not exposed in URLs | M | 10 | A | URL lint + crawler |

### 4. Cookies, CORS, CSP correctness (15)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEC-COK-01 | All cookies set with Secure | H | 20 | A | Set-Cookie census across render |
| SEC-COK-02 | Session-like cookies carry HttpOnly | H | 20 | A | Same |
| SEC-COK-03 | Cookies carry SameSite (Lax/Strict) | M | 20 | A | Same |
| SEC-COK-04 | No `__Host-` prefix violations | M | 10 | A | Name prefix rules check |
| SEC-COK-05 | CORS: no `Access-Control-Allow-Origin: *` combined with credentials; reflected-origin with credentials blocked | H | 20 | A | Probe with safe same/sister origins (never attacker-styled claims beyond evidence) |
| SEC-COK-06 | CSP actually restricts scripts (inline handlers counted; violation-free render) | M | 10 | A | Console + CSP header × inline handler census |

### 5. DNS security (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEC-DNS-01 | DNSSEC signed (AD bit / DS chain via our resolver) | M | 30 | A | DNS query with DO bit |
| SEC-DNS-02 | CAA record present permitting intended CAs | M | 30 | A | DNS query |
| SEC-DNS-03 | Authoritative NS diversity (≥ 2 distinct networks) | L | 20 | A | NS query + IP ASN grouping |
| SEC-DNS-04 | No dangling NS records (NS hostnames resolve) | L | 20 | A | NS + A/AAAA queries |

### 6. Misc hardening (5)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| SEC-MISC-01 | Mixed content absent on rendered pages | H | 30 | A | Network log |
| SEC-MISC-02 | Forms post over HTTPS only | H | 30 | A | Form action audit |
| SEC-MISC-03 | No client-side disclosed secrets patterns in fetched HTML (key-shaped strings: AKIA, ghp_, sk_live prefixes) | H | 30 | A | Pattern scan of owned-site HTML (bounded) |
| SEC-MISC-04 | Subdomain exposure from CT logs reported as inventory (info; unexpected subdomains listed for review) | I | 0 | B (fallback `not_measured`) | CT log query |
| SEC-MISC-05 | Cache-Control on sensitive-looking paths (account/login if discovered) prevents shared-cache storage | L | 10 | A | Header inspection when such paths are observed (never guessed/probed beyond robots-visible links) |

## Tier B panels for this tool

- **Certificate Transparency subdomain inventory** (SEC-MISC-04): labelled,
  timestamped, 7-day cache; unavailable → `not_measured` note.
- **Safe Browsing status flag** surfaced on the report header when enabled.

## Outputs

Header grade table with exact header values as evidence · TLS report
(protocols, ciphers, chain, expiry countdown) · cookie inventory with flags ·
probe results table (path, status, size-class, verdict) · DNS security panel ·
prioritized hardening list with config-level fixes.
