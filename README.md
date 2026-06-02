# Ploetner Dev Blocks

WordPress 7.0 PHP-only (`autoRegister`) blocks and their backing custom post types
for the [Plötner Dev](https://ploetner.dev) site. Extracted from the
`ploetner-dev-child` theme so the content survives theme switches.

## Requirements

- WordPress **7.0+** (uses `supports.autoRegister` for PHP-only block registration)
- PHP **8.1+**

## What it provides

### Blocks (category “Ploetner.dev”)

| Block | Content source |
|---|---|
| `ploetner-dev/hero` | Block attributes (single instance) |
| `ploetner-dev/cta-banner` | Block attributes (single instance) |
| `ploetner-dev/expertise` | `pd_expertise` posts |
| `ploetner-dev/open-source` | `pd_project` posts |
| `ploetner-dev/speaking` | `pd_talk` posts |
| `ploetner-dev/community` | `pd_community` posts |

All blocks render server-side via `do_blocks()` of canonical block markup, and the
WP 7.0 editor auto-generates Inspector Controls from each block's declared
attributes. No build step / JavaScript.

### Custom post types

`pd_expertise`, `pd_project`, `pd_talk`, `pd_community` — all non-public (no archive,
not publicly queryable, `show_ui` for editing). Per-type meta (card label, tech tags,
link, year, event) is edited via a classic “Details” meta box.

## Styling dependency

The blocks output the site's design-system CSS classes (`ploetner-card`,
`ploetner-section-label`, `ploetner-expertise-grid`, `ploetner-speaking-row`,
`ploetner-card-desc`) and rely on `theme.json` presets (colors `accent` /
`base-card` / `border` / `text-muted`, spacing presets, the `mono` font family).
Those live in the **`ploetner-dev-child` theme**. The plugin supplies structure and
data; the theme supplies presentation. To use these blocks in another theme, provide
equivalent presets and class styles.

## Seeding sample content

The sample content is **seeded automatically on plugin activation** — there is
nothing to run by hand for a fresh install. Seeding is idempotent (items whose
slug already exists are skipped) and version-aware: a `pd_blocks_seed_version`
option records what has been written, and a future plugin version that adds new
sample items seeds them on the next admin load. The data lives in one place,
`src/Seeder.php`.

The WP-CLI seeders remain available for re-seeding or CI. Each is a thin wrapper
over `Seeder` and runs only its own post type:

```bash
wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-expertise.php
wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-open-source.php
wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-speaking.php
wp eval-file wp-content/plugins/ploetner-dev-blocks/tools/seed-community.php
```