# Scoreboard integration note

- Main URL: `http://<host>:<port>/scoreboard/` (challenges); `/scoreboard/scoreboard.php` (live board); `/scoreboard/team.php` (my team); `/scoreboard/admin.php` (admin).
- Admin password: constant `SB_ADMIN_PASSWORD` in `scoreboard/config.php` (default `<SB_ADMIN_PASSWORD env>`) - CHANGE IT before the event.
- Admin can: list teams/submissions, reset or delete a team, close/re-open (freeze) the competition.
- DB: `scoreboard/scoreboard.sqlite`, auto-created on first request. Delete it to wipe all scoreboard state. Main `database.sqlite` is untouched.
- Scoring: Easy 50 / Medium 150 / Hard 300 / Expert 700 (`SB_TIER_POINTS` in config.php). Wrong-submission rate limit: 8 per 60s per team.

## flags.json
`scoreboard/flags.json` -> `{"challenges":[ {number,title,category,tier,flag}, ... ]}`. Read on every request, so edits are live.
Optional per-entry keys: `points` (override tier points), `hidden: true` (not listed/submittable). Entries with a missing number/flag/tier are skipped.
Challenge 8 flag is still `ADNSU8{PLACEHOLDER_FILL_LATER}` - fill it in.

### Adding flags 31-35
Append objects before the closing `]` (mind the commas), e.g.
`{"number": 31, "title": "New Chal", "category": "Web", "tier": "Hard", "flag": "ADNSU31{...}"}`.
No code change needed; there is no dependency on an on-site challenge existing.
Changing a flag's points after solves exist does not alter already-awarded points (stored per solve).

## REQUIRED change by orchestrator (serve.php)
`serve.php` does NOT block `/scoreboard/flags.json` (verified HTTP 200) - anyone could download every flag.
Add to the `$blocked` array in serve.php:

    '#^/scoreboard/(flags\.json|inc/)#i',

(`scoreboard.sqlite` is already blocked by the existing `\.sqlite3?$` rule - verified 403.)
Pages, CSS and sessions work under the unmodified router.

## robots.txt
Optional: add `Disallow: /scoreboard/admin.php`. Not required (note this also advertises the path).
