# Rollback — Beyond TV Complete Episode Guides

Restore these files from the previous Beyond OS build:

- `beyond-tv/title.php`
- `beyond-tv/assets/css/app.css`
- `beyond-tv/data/catalog.json`

Then remove:

- `beyond-tv/includes/episode-library.php`

The optional runtime cache at `var/cache/beyond-tv/archive-episodes/` may be removed safely. Do not alter any other production `var/` content.
