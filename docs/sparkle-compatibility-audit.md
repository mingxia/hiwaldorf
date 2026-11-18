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

## Full-tree audit status (2026-11-18)

- Repository tree verified: **349 files** total in the parent and child, including **151 PHP**, **26 JavaScript**, **29 CSS**, **14 LESS**, and binary images/fonts/archives.
- GitHub Actions inventory scanner enumerates every file and emits a searchable audit report as the `sparkle-full-file-audit` artifact. Text files receive heuristic pattern scans. Binary files receive an inventory entry but no source-code inspection.
- PHP 8.3 and 8.4 syntax lint currently passes for all PHP files; JavaScript syntax check passes for all JS files.
- PHPCompatibility sniff v9.3.5 ran with requested version 8.3 and reported no additional findings. **It is not full coverage of PHP 8.3 deprecations**, particularly dynamic property creation and framework runtime behavior.
- Scanner initially reported ~132 dynamic-property assignment candidates and ~96 direct request-data references (many could be legitimate). They require comparison against class declarations, authorization checks, and escaping in call sites, not bulk regex rewriting.
- High-risk `create_function` scanner matches were two commented-out lines, not live code, after the four active occurrences were replaced.
- Code fixes added this round: two optional-before-required parameter declarations in `framework/admin/includes/yt-options-settings.api.php` and guarded global `$post` in the child header.
- **Unfinished:** manual, semantic code review of every executable file and runtime testing in a staging WordPress install. The inventory is comprehensive, but it must not be described as a completed line-by-line manual audit.

### Production safety
No changes were deployed to the server. Keep the PR Draft, validate the code on a staging copy of hiwaldorf.com, and do not migrate theme options or alter the `_post_views` data before backups and equal-results tests.
