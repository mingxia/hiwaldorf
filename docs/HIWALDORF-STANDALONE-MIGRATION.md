# HiWaldorf Magazine standalone migration

## Decision

- **Official WordPress theme directory remains `sparkle-childtheme/`.**
- Visible theme name: **HiWaldorf Magazine**; maintained-fork version **3.0.0**.
- The historical `yeahthemes-sparkle/` source is retained in this repository for review/rollback, but is **not required** in the production WordPress themes directory once the standalone build is fully validated.
- `sparkle-childtheme/style.css` no longer declares `Template:` and no longer imports `../yeahthemes-sparkle/style.css`.
- `sparkle-childtheme/functions.php` is the former parent's bootstrap; the existing upstream include list already loads `custom-functions.php`, preserving historical dashboard counts.
- All parent files missing in the child were copied into the child; where both contained a file with the same relative path, the child-specific file was retained.
- Original copyright and GPL notices are preserved in vendored files and `license.txt`.

## Why preserve the directory name?

The original framework uses `get_stylesheet()` to derive the legacy
`yeahthemes_theme_options_sparkle_childtheme` option key. WordPress also
uses the theme directory slug in `theme_mods_sparkle-childtheme`, widget
arrangements and theme identification. Keeping the slug avoids unnecessary
renaming/migration of these long-lived options.

**Warning:** The theme directory is stable but the actual site settings,
third-party plugin behavior and historical database content are not in CI.
Always verify on a staging copy before production replacement.

## Staging checklist (required)

1. Create independent staging copy, private and excluded from search indexing.
   Backup SQL database, uploads, the active parent and child directories and
   server PHP configuration. Confirm rollback.
2. Copy **only** the new `sparkle-childtheme/` from PR #4 into staging
   WordPress `wp-content/themes/` using its *unchanged* directory name.
   Remove/hide the former parent on staging **after** validating that the new
   child has become a valid standalone theme.
3. Confirm `wp_get_theme('sparkle-childtheme')->parent() === false` and
   `get_template() === get_stylesheet() === 'sparkle-childtheme'`.
4. Check existing design options, CSS colors, menus, widget areas, custom
   logos, typography, post meta, headers/footers, and custom advertisement
   placements match the original site.
5. Confirm custom dashboard: `all_view()`, `view_5000()` and `_post_views`
   values. Never reset or rewrite post meta during migration.
6. Validate gallery, multilingual switcher (if installed), video, WPBakery/
   Visual Composer shortcodes and any other plugins used on the real site.
7. Visit category, search, post, page, 404 and archive routes, plus responsive
   mobile widths and custom layouts. Check console for missing scripts/styles.
8. Log PHP 8.3 errors with `WP_DEBUG_LOG` or PHP-FPM error logging; assess
   Fatal, Warning, Deprecated and Notice messages during tests.
9. Only after staging sign-off, replace `sparkle-childtheme/` with the
   validated build **as an atomic, backed-up deployment**. Recheck routes
   and retain the old files for quick rollback.

## Automated baseline

GitHub Actions `HiWaldorf Magazine standalone WordPress test` installs
WordPress and **only** `sparkle-childtheme`; it does not copy the upstream
`yeahthemes-sparkle` directory. It checks the theme metadata, lack of parent,
CSS header, core PHP syntax, custom statistics hooks, dashboard bootstrap and
HTTP page responses on PHP 8.3.

It does **not** emulate the site's entire production plugin stack or
logged-in Customizer live preview, so it is not deployment approval by itself.

## Branching

- PR #3 (`fix/php83-wordpress-compat`): base PHP/WordPress modernization.
- PR #4 (`refactor/standalone-hiwaldorf-magazine`): this standalone theme
  migration, stacked on PR #3.

Neither PR is merged without explicit approval. The old parent directory in
GitHub remains for comparison and to preserve upstream source.
