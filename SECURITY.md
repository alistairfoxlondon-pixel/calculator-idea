# Security

## SSRF — core risk (we fetch user URLs)

- Allow only http/https
- Resolve DNS locally, validate every A/AAAA
- Block 127/8, 10/8, 172.16/12, 192.168/16, 169.254/16, 100.64/10, 0.0.0.0/8, ::1, fc00::/7, fe80::/10, IPv4-mapped
- Pin resolver (CURLOPT_RESOLVE mapping), re-validate on every redirect hop
- Ports 80/443 only, 5 MB per HTML cap, 15s timeout, no XXE
- Deny scanning own host; egress cannot hit DB/admin
- Unit tests prove: cloud metadata blocked, 127 blocked, decimal/octal/hex blocked, IPv6 loopback blocked, redirect-to-internal blocked, rebinding blocked, non-HTTP blocked, oversized blocked

## App

- Strict CSP with nonces, HSTS, X-Content-Type-Options nosniff, frame-ancestors none, secure cookies, CSRF on mutations, Argon2id, login throttling, rate limits (anon 5/hr, 20/day), parameterized queries, escaped output. Crawled HTML is hostile — never render; show escaped snippets only.

## Ethics

- Honest UA, respect robots.txt + Crawl-delay, never bypass bot protection/paywalls, never auth, never attack, non-destructive, terms require own/authorised target, stop-on-request page.

