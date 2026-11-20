# HiWaldorf Steiner Quotes

WordPress plugin using the existing `{prefix}steiner_quotes` MySQL table. Does not copy, seed, drop, or alter legacy data.

## Install

1. Check `$table_prefix` in `wp-config.php`. If the prefix is `wp_`, the expected table is `wp_steiner_quotes`.
2. Back up the WordPress database and theme before deployment.
3. Copy this directory to `wp-content/plugins/hiwaldorf-steiner-quotes/` and activate **HiWaldorf Steiner Quotes** in WordPress Plugins.
4. Make sure WordPress permalinks are enabled; activation flushes rewrite rules, creating `/quotes/` and `/quotes/{code}/`.
5. Check several historical `code` values, `endif` translation flags, and `gaid` samples in DMS. Only rows with `endif=1` and nonempty `content_cn` are public.
6. WordPress Admin -> **施泰纳语录** allows searching and editing/addition. No delete control is deliberately offered.
7. Add `[steiner_quote_random]` to a homepage widget or content area to show one random quote. The card's link points to its permanent URL.

## Behavior

- `/quotes/` starts with a random completed Chinese translation; blank-space click, **换一条**, or space key fetches another quote via the public read-only REST endpoint.
- `/quotes/{code}/` always opens a specific quote, for copyable links. After switching, the browser URL updates to the new quote.
- Original text is labeled **原文** because the historical `content` column is not guaranteed to be English or German.
- `gaid` and `note` are shown as existing text only; no invented work titles.
- The plugin uses current site's WordPress header/footer, independent of theme-specific hooks, and exposes a native WordPress sitemap for publicly available quote slugs.
- Existing `views` increments when a permanent link is loaded; random API changes are not counted as legacy views.

## Deployment smoke checks

- Enable plugin on a staging site with a backed-up copy of the table.
- Open `/quotes/`, press space, click empty space, and test the **换一条** button.
- Test a permanent URL from the real `code` field: refresh must not change the displayed quote.
- Use **查看原文** and **分享语录**; test both desktop and mobile.
- Confirm `[steiner_quote_random]` embeds neatly into the homepage and doesn't affect other pages.
- Edit a single staged row in wp-admin and confirm its ID, original creation time, slug, and other fields are preserved.
- Confirm an untranslated row is omitted from public page and sitemap, while still editable in admin.
- Confirm `wp-sitemap.xml` exposes the `steiner-quotes` provider and correct links.
- Confirm current theme navigation/footer still render correctly on mobile and desktop.

## Constraints

No public list/archive or public search interface by design. No auto-translation. No automatic migration/renaming of tables. If an SEO plugin already emits canonical URLs or sitemap entries for these routes, coordinate duplicate metadata during deployment.
