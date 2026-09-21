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

### Patterns

The plugin also registers block patterns (category "Plötner.dev") for the
site chrome and layout, so they travel with the plugin instead of a theme:

| Pattern | Purpose |
|---|---|
| `ploetner-dev/header` | Sticky header: site title, nav, contact CTA (template-part pattern) |
| `ploetner-dev/footer` | Footer: social links and copyright (template-part pattern) |
| `ploetner-dev/front-page` | The six section blocks, separated by rules |
| `ploetner-dev/separator` | Section separator |

Registered in `src/Patterns.php`. Build the header/footer template parts and the
front page from these in the Site Editor.

## Custom post types

`pd_expertise`, `pd_project`, `pd_talk`, `pd_community` — all non-public (no archive,
not publicly queryable, `show_ui` for editing). Per-type meta (card label, tech tags,
link, year, event) is edited via a classic “Details” meta box.

## Styling

The plugin ships its own stylesheet (`assets/blocks.css`), enqueued on the front
end and in the editor via `enqueue_block_assets` (see `src/Assets.php`). It styles
the plugin's own classes (`ploetner-card`, `ploetner-section-label`,
`ploetner-expertise-grid`, `ploetner-speaking-row`, `ploetner-card-desc`) and
depends only on the design-system tokens exposed by `theme.json` (colors `accent`
/ `surface` / `border` / `border-hover` / `muted` / `elevated`, spacing presets,
the `mono` font family).

The plugin is therefore theme-independent: drop it onto any theme that defines
those tokens (the `ploetner-base` family theme does) and it styles itself. It no
longer depends on the old `ploetner-dev-child` theme.

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
## Branching and releases

Development happens on `dev` (the default branch). Features land via pull
request into `dev`; CI (PHPCS, PHPStan, PHPUnit on PHP 8.1/8.2/8.3) runs on
every pull request and on pushes to `dev`.

On each push to `dev`, the **Build** workflow assembles the deployable plugin
(`composer install --no-dev`, filtered through `.distignore`) and force-pushes
it to `main`. So `main` always holds the installable build (source plus
`vendor/`, no dev tooling) and is never edited by hand.
