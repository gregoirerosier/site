# Beyond OS 2.2.1 — Live Homepage Integration

## Included
- Preserves the existing Health / Education / Wallet / Entertainment hero and ecosystem orbit.
- Places an expanded Beyond TV player directly after the hero.
- Uses the existing eight-channel endpoints and embedded channel sources.
- Adds a direct `#guide` anchor to the existing Beyond TV guide section.
- Adds a prominent channel-art background that changes with the selected channel.
- Replaces large homepage ecosystem panels with live Daily Breath and Beyond French experiences.
- Daily Breath reads the currently published Verse of the Day when available and retains the bundled rotation as a safe fallback.
- Beyond French reads the lesson library and selects today’s lesson or a deterministic daily lesson.
- Adds compact App Store, Wallet, and Marketplace shortcuts.
- Mobile channel choices scroll horizontally so the player remains prominent.

## Deployment
Replace the public application files with this package using a merge deployment. Do not delete the server `var/` directory. The package does not require a database migration.

## Database
No schema, table, or data migration is included. Existing Daily Studio content is read through the current application interfaces.

## Rollback
Restore `docs/rollback/2.2.1-live-home/index.php` as the site-root `index.php`. No database rollback is required.
