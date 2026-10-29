# Deployment guide (staging)

## Environment
- Hostinger shared hosting, LiteSpeed, PHP 8.3. Staging: `domains/green-gorilla-647751.hostingersite.com/public_html`.
- SSH on port 65002 with a dedicated key. Hostinger SSH keys are account-wide: delete the key `esnad-staging-claude` when the work is finished.
- WP-CLI: `php -d disable_functions= /usr/local/bin/wp`.
- Table prefix `cpeup_`. The `esnad-staging` mu-plugin is active only on `*hostingersite.com` (noindex, mail blocked).

## Code deploy
`powershell -File tools\deploy-staging.ps1` zips `themes/esnad-next`, `plugins/esnad-core` and `mu-plugins` (forward-slash entries), copies the zip once with scp, unzips over the staging `wp-content`, purges LiteSpeed. It never touches `uploads` or the database.
If the host answers "Connection closed", wait a few minutes: Hostinger rate-limits repeated SSH connections. Do not loop faster than every ~2 minutes.

## Content migration (database)
Content was converted from Elementor JSON to block content by `tools/migrate-elementor.php`, then `tools/create-form-pages.php`, `tools/replace-forminator.php`, and the careers fix, orchestrated by `tools/migrate-all.sh` (run with `ESNAD_WRITE=1`). They update `post_content` directly with `$wpdb->update` (WPML save hooks fatal in CLI) and are idempotent. Run on staging only.
Before any content run: `wp db export` to a file outside the web root and keep it outside Git.

## Settings the company edits (no code)
Settings > General, section "إسناد - Esnad": form recipient e-mail and the Google Analytics 4 ID. With no GA ID there is no analytics and no consent bar. Legal pages are created by `tools/create-legal-pages.php` (`wp eval-file`, idempotent); the text is a draft until the company approves it.

## Optional layers (guarded includes in `functions.php`)
`inc/landing.php` (landing, investment and thank-you pages), `inc/article.php` (articles), `inc/orgchart.php` (organisation chart, page IDs 16852 and 29468), `inc/settings.php` (settings and consent bar). Each is wrapped in a try/catch include, so a fault falls back to the original content instead of breaking the site.

## Rollback
- Code: redeploy the previous commit (`git checkout <sha>` then run the deploy script).
- Content: `wp db import` the pre-run export; post revisions also exist for each page.

## Production release (not authorised yet)
1. Full backup of files and database.
2. Deploy theme and plugin; import migrated content or rerun migration on a production copy first.
3. Remove staging mu-plugin effects (they are host-name gated), set the real form recipient, verify Rank Math head output (the theme prints a fallback only when Rank Math does not).
4. Redirect check for changed URLs, then disable old builder plugins only after replacements are tested.
