# Beyond TV Complete Episode Guides — 2026-07-22

## Summary

Every episodic Beyond TV title page now uses one shared episode-library resolver. The page identifies the current episode, groups all listed episodes by season, highlights the selected episode, and provides previous/next navigation.

## Included

- Shared resolver for curated episode maps, Internet Archive collections, YouTube playlists, and catalogue-only shows.
- Complete season and episode lists for every show in `beyond-tv/data/catalog.json`.
- Current episode label with season/episode code.
- Previous and next episode controls.
- Device-local resume link for the last selected episode of each show.
- Direct per-episode playback where an approved media file is available.
- Protected metadata cache under the configured private `var/cache/beyond-tv/archive-episodes/` path.

## Deployment

Deploy the `site/` directory over the existing web root using merge/overwrite for application files. Do not delete or replace the production `var/` directory. The runtime creates its own disposable episode metadata cache under `var/cache/` when writable.

## Database

No database migration is required.
