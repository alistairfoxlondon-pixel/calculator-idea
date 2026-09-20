# Tool 11 — Email Audit (`/email-audit`)

Intent: "spf dkim dmarc checker", "email deliverability test", "dns audit".

Scan profile: entirely Tier A — our own DNS resolver and HTTP client. No keys,
no cost, no third-party API. DKIM selector discovery runs a bounded,
versioned candidate list plus sitemap-of-convention selectors from the
rulepack.

## Scoring categories

| # | Category | Weight |
| --- | --- | --- |
| 1 | SPF | 25 |
| 2 | DKIM | 20 |
| 3 | DMARC | 25 |
| 4 | MX & PTR | 10 |
| 5 | Blocklists | 10 |
| 6 | MTA-STS, TLS-RPT, BIMI, DNSSEC | 10 |

Cap: CAP-EMAIL-01 (no MX records found → capped at 50, with the implicit-A
delivery note).

## Checks

### 1. SPF (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| EM-SPF-01 | TXT SPF record present at apex (exactly one; multiples fail) | C | 20 | A | DNS TXT query |
| EM-SPF-02 | Syntax valid per RFC 7208 grammar (parser with error positions) | H | 20 | A | Own parser |
| EM-SPF-03 | DNS lookup count ≤ 10 (mechanism expansion counted per RFC; include/redirect/a/mx/ptr) | H | 20 | A | Recursive expansion counter |
| EM-SPF-04 | Void-lookup count ≤ 2 | M | 10 | A | Expansion trace |
| EM-SPF-05 | All-mechanism strength graded: `-all` best; `~all` warn; `?all`/`+all` fail | H | 25 | A | Parser |
| EM-SPF-06 | Sending infra covered (MX/known ESP includes present; unmatched ESP detection via MX/hostname fingerprint table, informational) | M | 5 | A | Expansion result × ESP fingerprint table |

### 2. DKIM (20)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| EM-DKIM-01 | At least one publishable selector discovered | M | 25 | A | Candidate selector queries (versioned list: google, default, k1–k3, s1–s3, s2000–s2025 style, mail, dkim, mte1/mte2, zoho, protonmail, sele etc.) |
| EM-DKIM-02 | Key syntax valid (v=DKIM1, k=rsa/ed25519, p= base64 or empty-revoked) | H | 25 | A | Record parser + key header parse |
| EM-DKIM-03 | Key strength ≥ 1024-bit RSA (2048 recommended); ed25519 accepted | M | 20 | A | Key size extraction |
| EM-DKIM-04 | h= hash algorithms sane (sha256 preferred; sha1 flagged) | L | 15 | A | Parser |
| EM-DKIM-05 | Multiple selectors published (rotation-friendly; single selector = note) | L | 15 | A | Discovery census |

### 3. DMARC (25)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| EM-DMARC-01 | `_dmarc` record present and syntactically valid (org-domain mapping for subdomains via PSL) | C | 25 | A | DNS query + PSL-bundled suffix logic |
| EM-DMARC-02 | Policy `p=` graded: `reject` best, `quarantine` warn, `none` fail (upgrade path shown) | H | 25 | A | Parser |
| EM-DMARC-03 | `rua` aggregate reporting present with valid mailto (and size limit sanity) | M | 15 | A | Parser + URI validation |
| EM-DMARC-04 | `ruf` forensic where present valid; `pct` sanity (100 unless staged rollout) | L | 10 | A | Parser |
| EM-DMARC-05 | Subdomain policy `sp=` set when subdomains send mail | L | 10 | A | Parser + subdomain MX probe (sample) |
| EM-DMARC-06 | Alignment achievable: SPF/DKIM domains align with From domain (relaxed/strict noted) — inferred from published records, stated as inference | M | 15 | A | Record cross-comparison |

### 4. MX & PTR (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| EM-MX-01 | MX records present for the domain | C | 35 | A | DNS query |
| EM-MX-02 | Priorities sane (no duplicate priorities on different hosts; at least one non-zero priority; RFC-single-fallback pattern noted) | M | 20 | A | Record audit |
| EM-MX-03 | All MX hostnames resolve (A/AAAA present, not dangling) | H | 20 | A | DNS queries |
| EM-MX-04 | PTR records exist for MX IPs and forward-confirm (PTR → A → PTR match) | M | 25 | A | Reverse DNS + forward confirmation |
| EM-MX-05 | SMTP never probed: all mail evidence is DNS/HTTP-level (scope rule; informational) | I | 0 | A | Scope note; no SMTP connection is ever attempted |

Scope rule: we never open SMTP connections. All evidence is DNS/HTTP-level.

### 5. Blocklists (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| EM-BL-01 | Primary MX IPs not listed on queried IP-based DNSBL zones (zone list versioned with sources) | M | 60 | A | DNSBL queries |
| EM-BL-02 | Domain not listed on domain-level blocklist zones | M | 40 | A | DNSBL queries |
| EM-BL-03 | Zone reachability verified with a control query (else `not_measured` instead of "clean") | I | 0 | A | Control-record query |

### 6. MTA-STS, TLS-RPT, BIMI, DNSSEC (10)

| ID | Check | Sev | W | Tier | Data path |
| --- | --- | --- | --- | --- | --- |
| EM-SEC-01 | `_mta-sts` TXT present with sane policy id | M | 30 | A | DNS query |
| EM-SEC-02 | `https://mta-sts.{domain}/.well-known/mta-sts.txt` reachable, valid, mode consistent with TXT | M | 30 | A | Well-known fetch + parser |
| EM-SEC-03 | `_smtp-tls-rpt` record present with valid URI | M | 25 | A | DNS query |
| EM-SEC-04 | BIMI record presence noted (VMC/VM absence stated as informational — no purchase advice) | I | 0 | A | DNS query |
| EM-SEC-05 | DNSSEC signed zone (shared measurement with Tool 9) | M | 15 | A | DNS DO-bit query |

## Outputs

Verdict table per protocol with the exact DNS records as evidence ·
copy-paste-ready corrected record set generated from measured values by
deterministic templates (SPF with counted lookups, DMARC upgrade path, TLS-RPT
and MTA-STS snippets), each with an explanation and a rollback note · lookup
budget visualization for SPF.
