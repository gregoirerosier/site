# Beta hardening

- Central session cookie policy and response security headers.
- Shared CSRF tokens with protected state-changing endpoints.
- MIME- and image-validated healing uploads in private `var/` storage.
- Healing notes and ownership metadata persistence.
- SQLite/MySQL database explorer and guarded SQL presets.
- Fixed-registry admin app launcher and system troubleshooting overlays.
- Sunset default theme and unified Daily Studio tabs.
- Patch notes consolidated under `docs/patch-notes/`.

## Alpha gate

- Rotate all beta database, SMTP, payment, narration, and API credentials.
- Confirm public forms send `_csrf`.
- Set `BEYOND_VAR_PATH` outside the web root.
- Run PHP lint and integration tests on PHP 8.1+.
