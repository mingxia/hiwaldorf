# Sparkle compatibility upgrade: baseline audit

## Target and scope
- Parent: `yeahthemes-sparkle/`; child: `sparkle-childtheme/`
- Target PHP: 8.3 (also syntax-check 8.4)
- Target WordPress: currently deployed stable release; confirm exact production version before staging testing
- Preserve original theme layouts, menus, widget areas, URLs, post metadata and options.
- Never auto-deploy to production; confirm backup and validate on a staging copy first.

## Confirmed findings (initial source inspection)
1. Parent `includes/theme-config.php` used PHP `create_function()` three times. Removed in PHP 8.0. Replace with closures without changing hook behavior.
2. Parent `includes/theme-interface.php` used `create_function()` for the hero-cache filter. Replace with a closure.
3. Child `custom-functions.php` passed undefined `$ramattr` to its Youku filter (actual parameter `$rawattr`). Correct the variable while preserving filter arguments.
4. Child `custom-functions.php` defines dashboard statistics `all_view()`, `view_5000()`, `custom_dashboard_help()`. **Do not remove or reset** existing `_post_views` data. `all_view()` currently fetches all matching postmeta rows into PHP memory: needs an independently verified SQL optimization and equivalent behavior checks.
5. Child still embeds Youku content over HTTP and uses outdated URL matching. Diagnose against actual legacy embedded links and current HTTPS behavior before changing.
6. Parent theme's `functions.php` has `THEME_VERSION = 2.0`; child stylesheet says `Version: 2.3`. Confirm real parent version and its use in asset cache busting.
7. Parent includes a custom framework, widget classes, and editor integrations. Those require runtime WordPress smoke tests; syntax lint alone is not sufficient.
8. Repository has additional historical plugin folders at root. Excluded from this theme modernization PR; audit independently before enabling.

## Acceptance tests before activation
- PHP 8.3 lints all PHP files; record PHP 8.4 failures separately.
- WordPress loads theme without fatal errors; inspect web PHP-FPM version, not CLI only.
- Visit home, category/archive, post, page, search, mobile navigation, and 404.
- Test admin customizer, theme options save, widgets, menu positions, images, galleries, video embedding, and post pagination.
- Verify child dashboard widget count agrees with legacy `_post_views` data; no change to data storage.
- Test multilingual content, theme shortcodes, embedded content, and plugin integrations in use.
- Ensure URLs, canonical tags, permalinks, and ad placements stay unchanged.
- Record PHP error logs after simulated requests.
- Back up WordPress database, uploads, parent/child themes and options; maintain rollback path.
- Do not merge until staged runtime validation.
