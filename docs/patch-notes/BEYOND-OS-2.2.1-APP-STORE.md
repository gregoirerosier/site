# Beyond OS 2.2.1 — App Store, Wallet & Marketplace

## Applied
- Replaced the shared **Apps** dropdown with a direct **🛍 App Store** button.
- Removed the dropdown markup, menu CSS, and dropdown JavaScript from the shared navigation.
- App Store button routes to `/app-store/`.
- Added direct App Store entry points to `/beyond-finance/` and `/beyond-market/`.
- Updated visible shared-shell and App Store build labels to 2.2.1.
- Preserved every existing route and file supplied in the source build.
- No SQL or database migrations are included or required.
- No `var/` directory was added, removed, or modified. Production `var/` data remains merge-only during deployment.

## Deployment
Upload/merge the build over the existing web root. Do not delete or replace the production `var/` directory. The patch changes only shared navigation, App Store presentation, and version labels.

## Rollback
Restore these files from `docs/rollback/2.2.1/`:
- `includes/ecosystem.php`
- `includes/app-layout.php`
- `app-store/index.php`

Rollback requires no database action.
