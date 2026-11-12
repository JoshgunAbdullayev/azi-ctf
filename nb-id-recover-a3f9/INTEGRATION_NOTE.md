# ACT II — Account Takeover Chain (ADNSU31–ADNSU35)

Self-contained add-on. Owns its own SQLite (`nb-id-recover-a3f9/act2.sqlite`, auto-created
and auto-seeded on first request) and its own key material. Does **not** touch `serve.php`,
`includes/*`, `database.sqlite`, the `users` table, or any existing file. The orchestrator
must only (1) add a few `serve.php` blocking rules, (2) add robots.txt lines, and
(3) wire one breadcrumb. Nothing else.

New folders created:
- `nb-id-recover-a3f9/` (primary — identity recovery; ADNSU31 + ADNSU32; shared bootstrap + SQLite)
- `nb-mfa-gw-7c21/`   (MFA step-up gateway; ADNSU33)
- `nb-cors-edge-5b8e/` (internal edge API; ADNSU34)
- `nb-jwks-vault-9d4c/` (JWKS admin console; ADNSU35)

---

## (a) New routes / URLs

| Method | URL | Purpose |
|---|---|---|
| GET/POST | `/nb-id-recover-a3f9/forgot.php` | "Forgot password". POST `email=`. Builds the reset link from the **Host header** (vuln) and writes it to the mail relay log. |
| GET | `/nb-id-recover-a3f9/mail-log.php` | Mock outbound mail relay log (QA view). Shows queued reset mails. Entries built from a **non-canonical Host** are flagged as poisoned and reveal ADNSU31 + the victim's valid reset token. |
| GET/POST | `/nb-id-recover-a3f9/reset.php?token=` | Consume a reset token, set a new password for the victim, issue a session cookie `act2_sid`. |
| GET | `/nb-id-recover-a3f9/dash.php` | Victim dashboard (needs `act2_sid`). Shows ADNSU32 + the MFA enrollment reference `mfa_ref`. |
| GET/POST | `/nb-mfa-gw-7c21/verify.php` | MFA step-up. POST `{mfa_ref, otp}`. Trusts the proxy-only header `X-MFA-Verified` from the client (vuln). On pass → ADNSU33 + mints internal `api_token` + marks session `mfa_passed`. |
| GET/POST/OPTIONS | `/nb-cors-edge-5b8e/data.php` | Internal edge API. Reflects any `Origin` with `Access-Control-Allow-Credentials: true` (vuln). Needs session + `mfa_passed` + `X-Api-Token`. Returns a signed HS256 `session_token` (header carries `kid`), and reveals ADNSU34 only when an attacker-controlled cross-origin `Origin` is reflected. |
| GET/POST | `/nb-jwks-vault-9d4c/admin.php` | JWKS admin console. Verifies a Bearer JWT; loads the HMAC key from a file named by the **`kid` header with no sanitisation** (vuln). A forged `role:admin` token signed with a predictable readable key file → ADNSU35. |
| GET | `/nb-jwks-vault-9d4c/static/cdn-cache.txt` | **Must stay web-readable.** Predictable file whose bytes the attacker uses as the forged-JWT key (kid target). |

Internal (do NOT expose): `_act2_boot.php`, `act2.sqlite*`, `nb-jwks-vault-9d4c/keys/hs_main.key`.

---

## (b) robots.txt — lines to ADD (discovery breadcrumbs, same style as existing)

```
Disallow: /nb-id-recover-a3f9/
Disallow: /nb-mfa-gw-7c21/
Disallow: /nb-cors-edge-5b8e/
Disallow: /nb-jwks-vault-9d4c/
```

---

## (c) serve.php — blocking rules to ADD (exact regex patterns)

Add these to the `$blocked` array (they protect state/secret files; they do NOT block the
`.php` endpoints, which the built-in server executes, nor `static/cdn-cache.txt`).
Note `#\.sqlite3?$#i` already blocks `act2.sqlite`, but NOT its WAL/SHM sidecars — so:

```php
    '#^/nb-id-recover-a3f9/_act2_boot\.php$#i',   // shared include (no direct access)
    '#^/nb-id-recover-a3f9/act2\.sqlite#i',       // db + -wal/-shm sidecars
    '#^/nb-jwks-vault-9d4c/keys/#i',              // real HS256 signing key must stay secret
```

(Do NOT add any rule that blocks `/nb-jwks-vault-9d4c/static/` — ADNSU35 depends on that file being readable.)

---

## (d) The 5 flags (exact strings)

```
ADNSU31{host_header_reset_poisoning_a3f9}
ADNSU32{reset_token_account_takeover_b7e4}
ADNSU33{mfa_client_trust_header_bypass_7c21}
ADNSU34{cors_credentialed_origin_reflection_5b8e}
ADNSU35{jwt_kid_path_traversal_key_confusion_9d4c}
```

Format matches the CTF convention `ADNSU{N}{snake_case_descriptor_hex}`.

---

## (e) Intended solve path (step by step)

Target victim account (seeded): `deniz.aydin` / `deniz.aydin@novabank.com.tr`.

1. **ADNSU31 — Host Header / Reset Poisoning.** Discover `/nb-id-recover-a3f9/` (robots).
   POST `email=deniz.aydin@novabank.com.tr` to `forgot.php` **with a forged Host header**
   (e.g. `Host: evil.attacker.test`). Open `mail-log.php`: the poisoned entry shows
   **ADNSU31** and the victim's valid `reset_token`.
2. **ADNSU32 — Account Takeover.** GET `reset.php?token=<reset_token>`, POST a new password.
   Server resets the victim's password and sets cookie `act2_sid`. GET `dash.php` →
   **ADNSU32** + MFA enrollment reference `mfa_ref = mfaenr-7c21-deniz`.
3. **ADNSU33 — MFA Bypass.** GET `/nb-mfa-gw-7c21/verify.php` → doc names the trusted
   proxy header `X-MFA-Verified`. POST (with session cookie) `{"mfa_ref":"mfaenr-7c21-deniz","otp":"0"}`
   plus header `X-MFA-Verified: 1` → OTP check skipped → **ADNSU33** + `api_token` + internal
   endpoint `/nb-cors-edge-5b8e/data.php`. (Session is now marked `mfa_passed`.)
4. **ADNSU34 — CORS.** GET `data.php` with the session cookie + `X-Api-Token: <api_token>`
   **and** `Origin: https://evil.attacker.test`. The server reflects the Origin with
   `Access-Control-Allow-Credentials: true` → **ADNSU34** + a signed `session_token`
   (JWT whose header is `{"alg":"HS256","kid":"keys/hs_main.key"}`, `role:user`).
5. **ADNSU35 — JWT kid Injection / Key Confusion.** The JWT's `kid` is a file path. The real
   key `keys/hs_main.key` is web-blocked, so flipping `role` and re-signing is impossible.
   Instead repoint `kid` to a **readable** file: GET `/nb-jwks-vault-9d4c/static/cdn-cache.txt`
   (known bytes), forge a JWT `{"alg":"HS256","kid":"static/cdn-cache.txt"}` / `{"role":"admin"}`
   HMAC-signed with those bytes, send as `Authorization: Bearer` to `admin.php` → **ADNSU35**.

Each step's output is literally required by the next: poisoned token → reset/session cookie →
mfa_ref + cookie → api_token + mfa'd session → JWT structure to tamper.

---

## (f) Proposed breadcrumb (describe only — orchestrator wires it; do NOT implement)

Add ONE line to the existing debug log `/logs/app.log` (same leak-via-log style as FLAG 2/7),
pointing solvers from the main app into this chain, e.g.:

```
auth.recovery: identity-recovery service re-enabled (/nb-id-recover-a3f9/forgot.php); mail relay QA log active [host-header validation: TODO]
```

The `[host-header validation: TODO]` aside nudges toward the Host-header vuln without naming it.
(Alternatively, a commented hint on `login.php` near the "Forgot password?" link.) Leaving it
in `app.log` keeps the breadcrumb consistent with how the rest of the CTF seeds discovery.
