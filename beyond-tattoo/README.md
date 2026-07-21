# Beyond Tattoo operations

## Daily stencil publication

The checked-in source definition is `config/stencil-day.php`. It is the publishing input and lists every asset that belongs in the downloadable package. The live runtime manifest is stored in the protected Beyond `var/data` directory so all storefront and download views read the same release.

1. Add the preview, editable master, transfer PNG/SVG/PDF, placement guide, and metadata file under `assets/stencils/`.
2. Update `config/stencil-day.php` with the title, release date, URLs, placement, and `package_files` map.
3. Run `php tools/publish-beyond-tattoo-stencil.php` from the site root.
4. Confirm the command reports the package and manifest paths.
5. Smoke-test `stencil-of-day.php` and every option under `api/stencil-download.php?type=`: `package`, `preview`, `png`, `pdf`, `editable`, `placement`, and `ig`.

The publisher validates every required field and source file, rebuilds a private ZIP package, and atomically updates the protected live manifest. Schedule the same command once daily after the day’s reviewed assets are ready; a failed validation exits non-zero and leaves the previous live manifest intact.

## Legacy JSON import

Tattoo profiles, tattoos, healing metadata, beta signups, studio opportunities, and invitations now use the shared Beyond ID database. The previous repository contains no legacy users or tattoos and one beta signup. After deployment migrations run, execute `php tools/migrate-beyond-tattoo-json.php` once to copy that signup. The command stops for manual identity review if legacy users or tattoos are ever present.

