# Visitor Traffic Analytics

This build adds first-party visitor traffic reporting at `/beyond-id/admin/analytics.php`.

## Deployment

- The `visitor_traffic` table is created by the existing Beyond ID automatic migration runner.
- MySQL and SQLite migrations are included.
- The tracker is loaded across the main Beyond OS public pages and apps while excluding admin, API, asset, and tool routes.
- `/server/admin/analytics.php` redirects to the new Beyond ID analytics dashboard.

## Protected `var/` association

The tracker creates one random hashing key at `var/analytics/visitor-hash.key` using the configured `BEYOND_VAR_PATH` or the existing protected private root. This file must remain outside the public web root.

The build does **not** include or overwrite `var/config/live.php`, databases, secrets, or production state. Treat `var/` as merge-only during deployment.

## Privacy

Raw IP addresses, full user-agent strings, URL query parameters, and referrer query parameters are not stored. Visitor and session identifiers are one-way hashed. Browser Do Not Track is respected.
