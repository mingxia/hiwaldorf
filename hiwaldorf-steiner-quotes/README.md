# HiWaldorf Steiner Quotes — 1.0.7

A standalone WordPress plugin that reads the existing `${wpdb->prefix}steiner_quotes` MySQL table. No data migration, no theme dependency, no external assets.

## Deploy

1. Copy this directory to `wp-content/plugins/hiwaldorf-steiner-quotes/`.
2. Confirm WordPress `$table_prefix` (usually `wp_`), and confirm table `wp_steiner_quotes` exists in that same database.
3. Activate **HiWaldorf Steiner Quotes** in WP Admin > Plugins; activation flushes rewrite rules. If `/quotes/` returns 404, re-save **Settings > Permalinks**.
4. Visit `/quotes/`. Each visit presents a random, completed Chinese translation. Clicking empty areas / pressing Space fetches another quote without altering its shareable permalink.
5. Visit `/quotes/{code}/` to share a specific quote. Untranslated records are deliberately not public. Shortcode `[steiner_quote_random]` inserts a single quote card in articles, homepage modules, and widgets accepting shortcodes.
6. WP Admin > **施泰纳语录** to search, add and edit records. No deletions in v1. Keep the original export as a backup.

## Schema contract

`id`, `code` (unique slug), `content`, `content_cn`, `views`, `created_at`, `updated_at`, `gaid`, `note`, `endif`. Public random selection requires `endif = 1` and a non-empty `content_cn`. `content` is labeled “原文” rather than guessing its language; `gaid` is displayed as a bare GA number until work titles are linked. Existing `code` is preserved; admins should not change established slugs unless redirects are configured separately.

## SEO, performance & privacy

- `/quotes/` is a random reader (`noindex,follow`). Shareable `/quotes/{code}/` has canonical, basic Open Graph and description metadata. Do not create ordinary WordPress posts for each quote.
- WordPress native sitemap provider `steiner-quotes` surfaces translated quote permalink URLs (if WP sitemap is enabled), in batches of 1000; check your SEO plugin for sitemap duplication.
- Random reads use `ORDER BY RAND()` which is acceptable for this initial ~1.5k-row dataset; if the dataset grows substantially, optimize sampling.
- `views` counts quote displays: initial random reader, direct quote URL, successful next-quote AJAX response, and shortcode rendering. It is not a unique visitor count; full-page caches may skip PHP counting on cached responses.
- Text is escaped; public AJAX returns only publishable translations; administrator actions require `manage_options` and a nonce.

## Smoke checklist

- Plugin activates without PHP fatal; `/quotes/` and `/quotes/a-real-code/` render with current theme header/footer.
- An `endif=0` or blank translated quote is excluded from random and direct permalink display.
- Touch/click empty area & Space change cards; text selection, language toggle, copy/share link still work.
- Admin edit preserves `created_at`; new entries have 0 views; duplicate slugs fail.
- Site homepage shortcode only where chosen; plugin active does not modify existing homepage automatically.
- Test mobile and desktop layouts, caching (exclude `/quotes/` and its Ajax endpoint from full-page caching if random per visit is important), non-Latin text, source formatting, WP core sitemap, and existing SEO plugin metadata.

## Deployment note

The `wp_` prefix is NOT hardcoded: `$wpdb->prefix` is used. No direct production database credentials are needed.

## Version 1.0.2
- Suppress homepage-only featured hero and H1 ad hooks on `/quotes/` and `/quotes/{code}/`, while keeping the shared theme header and footer.
- Hide the theme news ticker on these reader routes only. The regular homepage remains unchanged.

## Version 1.0.2 — integrated full-bleed reader
- On quote routes only, remove the 1200px colored-card boundary; fill the surrounding site canvas with the current quote tone.
- Match the existing footer background and its text/link contrast to the immersive reading space. The shared masthead remains unchanged.
- Random quote switches update the entire page's background to the new quote tone.
- Embedded `[steiner_quote_random]` remains a compact card and does not recolor ordinary pages.
- No database migration, theme modification or permalink flush required.

## Version 1.0.3 — distraction-free quote reader
- Hide the regular site masthead and multi-column footer only on `/quotes/` and individual quote URLs. This is a presentation change; the original theme hooks still execute.
- Add a quiet `← HiWaldorf` link at the top of the reading space, centered section label and random-reading link.
- Fill the viewport with the current quote tone, while keeping the WordPress admin bar (if logged in).
- All normal WordPress pages and shortcode cards retain their original header/footer styling.

## Version 1.0.4 — GA label polish
- Normalize legacy `gaid` values (`293`, `GA293`, `GA 293`) to a single `GA 293` display on both first page render and AJAX switches. Do not alter MySQL records.
- Keep existing distraction-free design unchanged.

## Version 1.0.5
- Fix browser title on quote pages for HiWaldorf's legacy `wp_title()` theme header while retaining the modern document-title filter.
- Random reader title: `施泰纳语录 · 随机阅读 - HiWaldorf`.
- Fixed quote title: excerpt of the Chinese quote plus `施泰纳语录 - HiWaldorf`.
- Align individual quote Open Graph title with browser title. No database or theme changes.

## Version 1.0.6
- Increment the existing `views` whenever a quote is selected for display on the reader, a permanent URL, the random AJAX endpoint, or an embedded shortcode.
- Preserve historical timestamps for stats-only updates. No new tables, cookies, or visitor identifiers.
- Accuracy caveat: a cached HTML page may bypass PHP; successful AJAX generation can increment even if the browser subsequently fails to render.

## Version 1.0.7 — one shareable image template

- “分享图片” opens a local preview and downloads a 1080×1350 PNG. On supporting mobile browsers, “发送图片” invokes the native file share sheet. Existing “分享” and permanent links remain.
- Prefer server-side GD + an installed Chinese font (Noto CJK/WenQuanYi/AR PL) for a consistent first render. Cache each generated image in `wp-content/uploads/hw-steiner-cards/` and reuse the static PNG. No new tables and no external service. The filename includes a content fingerprint to avoid serving stale cards after edits. An old fingerprint file remains until an administrator cleans up old cards.
- If GD/fonts/upload writes are unavailable, the browser transparently renders using Canvas and downloads PNG with no persistence. This also handles server rendering failures. Server support should be checked after deployment with a test image; no assumption that PHP GD or font files exist.
- The generator does **not** increment `views`. Existing quote-display counting continues unchanged. Very long quotes which cannot fit completely are rejected with a helpful message rather than silently truncated.
- No QR code, watermark art, extra templates, remote fonts, image library dependencies or runtime subprocesses.
- Smoke tests: server GD/fonts present and absent, generated PNG output, repeated cache hit, translated text and GA number, refresh after edit, AJAX switching then share, desktop download, Android/iOS file share, pages embedded via shortcode, large quote handling, no `views` increment from image generation.