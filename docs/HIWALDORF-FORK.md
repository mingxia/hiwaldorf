# HiWaldorf Sparkle — maintained fork

This repository contains HiWaldorf's maintained adaptation of the former
**Yeahthemes / wpthms Sparkle** magazine theme (upstream last verified
in the supplied files: **2.3, November 30, 2018**) and the heavily customized
HiWaldorf child theme.

## Distribution names and versions

| Component | WordPress-visible name | Directory slug | HiWaldorf release |
|---|---|---|---|
| Parent | HiWaldorf Sparkle Core | `yeahthemes-sparkle` | 3.0.0 |
| Child | HiWaldorf Magazine | `sparkle-childtheme` | 3.0.0 |

**Do not rename the existing directories, change the child theme's `Template:`
header, or change the `wpthms` text domain in this release.** WordPress stores
theme settings, widgets, menu locations, CSS references and other integrations
by directory slug or historical option keys. A rename could reset theme-specific
options or break deployed assets.

Version 3.0.0 means **HiWaldorf's maintained fork release series**; it does
not imply Yeahthemes released version 3.0.0. Retain the original 2.3 changelog.

## Copyright, attribution, and licenses

- The original upstream theme credits, authorship and copyrights remain those
  of **wpthms / Yeahthemes** and other applicable contributors.
- HiWaldorf is the maintainer of this fork and may claim copyright only to
  original HiWaldorf-authored additions, customizations, and modifications.
- Existing upstream notices, third-party copyright notices, and applicable
  GPL license terms must remain intact. A paid purchase and lack of upstream
  maintenance do **not** assign the original author's copyright to HiWaldorf.
- The parent theme's original `license.txt` (GPL v2) is retained. Embedded
  dependencies may have their own notices; confirm them before distributing
  the full package. This document does not grant extra rights in third-party assets.
- The WordPress *Author* field now displays HiWaldorf as the **fork maintainer**,
  with upstream authors credited in the field and description.

## Compatibility goals

- PHP 8.3 and supported current WordPress versions.
- No Fatal/Warning/Deprecated errors on supported pages under normal usage.
- Preserve historical site options, widgets, menus, URLs, layout, shortcodes,
  and the `_post_views` dataset and dashboard statistics.
- Maintain the visual behavior of custom CSS and all child overrides.
- No silent disablement of features. Optional integrations should fail
  gracefully when the relevant plugin is not installed.

## Release safety

- Upgrade the parent **and** the child together in staging.
- Backup database, `wp-content/uploads`, both original theme directories,
  and theme options before deploying.
- Do not run this GitHub branch directly on the production server.
- Keep PR #3 Draft until full source audit, isolated compatibility tests, and
  production-like staging visual/functional regression tests pass.

See `docs/sparkle-compatibility-audit.md` for findings and runtime test evidence.
