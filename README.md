# Ploetner Dev Blocks

WordPress 7.0 PHP-only (`autoRegister`) blocks and their backing custom post types
for the [Ploetner Dev](https://ploetner.dev) site. Extracted from the
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
| `ploetner-dev/language-switcher` | Sites of the network via the Multisite Language Switcher |

All blocks render server-side via `do_blocks()` of canonical block markup, and the
WP 7.0 editor auto-generates Inspector Controls from each block's declared
attributes. No build step / JavaScript.

### Patterns

The plugin also registers block patterns (category "ploetner.dev") for the
site chrome and layout, so they travel with the plugin instead of a theme:

| Pattern | Purpose |
|---|---|
| `ploetner-dev/header` | Sticky header: site title, nav, contact CTA (template-part pattern) |
| `ploetner-dev/footer` | Footer: social links and copyright (template-part pattern) |
| `ploetner-dev/front-page` | The six section blocks, separated by rules |
| `ploetner-dev/separator` | Section separator |

Registered in `src/Patterns.php`. Build the header/footer template parts and the
front page from these in the Site Editor.

### Language switcher

`ploetner-dev/language-switcher` shows a minimal globe in the header. A click
opens the list of languages, one per network site known to the
[Multisite Language Switcher](https://wordpress.org/plugins/multisite-language-switcher/)
(MLS), in the order configured there. Each link points to the translation of the
current content when MLS has one, otherwise to that site's home page. The current
language is marked with `aria-current`.

- Built on `<details>`/`<summary>`: works without JavaScript. A small deferred
  script (`assets/language-switcher.js`) only closes it on outside click or Escape.
- Renders nothing when MLS is inactive or fewer than two languages exist.
- The header pattern includes it between navigation and CTA. Headers already
  saved in the Site Editor need the block inserted once by hand.
- MLS is an optional runtime dependency. PHPStan reads its API from
  `tests/phpstan/msls-stubs.php`.

### Accessibility and front-page meta

- Links that open a new tab carry a visually hidden hint, "(opens in a new tab)"
  (`.ploetner-sr-only`).
- The accent "▸" in Expertise headings is `aria-hidden`.
- Footer social links wrap on narrow screens; the header CTA stays on one line.
- The front page gets `<meta name="description">` from the site tagline
  (Settings > General), per language site. Skipped when Yoast, Rank Math,
  AIOSEO, SEOPress or The SEO Framework is active.

### Contact, legal notice and privacy policy

Provider details live in one place, `src/Contact.php` (name, address, email,
VAT number, hosting provider, log retention). They feed:

- the footer pattern: copyright with VAT number, `mailto:` link and a link to
  the legal page (`/legal/`, translated slug, `/rechtliches/` on the German site);
- the `ploetner-dev/legal` pattern (pages only): legal notice (Art. 7 D.Lgs.
  70/2003) and privacy policy (Art. 13 GDPR) describing the actual setup: server
  logs, Cloudflare, technically necessary cookies only, local fonts, no tracking.

Create a page per site with the translated slug and insert the pattern. Update
`Contact.php` and the texts when the setup changes (e.g. analytics).

## Custom post types

`pd_expertise`, `pd_project`, `pd_talk`, `pd_community` — all non-public (no archive,
not publicly queryable, `show_ui` for editing). Per-type meta is edited via a
classic “Details” meta box:

| Post type | Meta |
|---|---|
| `pd_project` | tech tags, link URL, link text |
| `pd_talk` | year, event, link URL (links the talk title) |
| `pd_community` | card label, link URL, link text |

Links open in a new tab. Without a link text, cards fall back to a default
label ("View project ↗" / "Learn more ↗").

The admin list tables sort like the blocks (`menu_order`, then date) unless a
column header is clicked, and show a sortable “Order” column. Talks also show
their year and event.

## Languages / Multisite

All user-facing strings use the `ploetner-dev-blocks` text domain. The plugin
ships German translations (`languages/*-de_DE.{po,mo,l10n.php}`). The main
plugin file calls `load_plugin_textdomain()` to register the `languages/` path.
WordPress 6.8+ derives it from the `Domain Path` header on its own, but only for
plugins activated per site. When the plugin is **network-activated**, core
skips that step and the translations are never found. The call only records the
path; strings are still loaded just in time. Keep all `__()` calls on or after
`init`, otherwise core triggers a "called too early" notice.

What follows the **site language** of each network site (e.g. `ploetner.dev/de/`
with `de_DE`):

- Block attribute defaults (Hero, section labels/headings/intros, CTA banner).
  Blocks inserted without custom attributes render in the site language.
- Patterns: header navigation labels and CTA, footer copyright line. The
  section anchors (`#expertise`, `#open-source`, `#speaking`, `#community`) stay
  identical in every language.
- Sample content from the seeder. Seeding switches to the site locale, so a
  German site gets German titles and texts even when the admin user runs
  WordPress in English. Slugs derive from the translated titles; items seeded
  earlier in another language are not replaced.

Header, footer and front page live in the Site Editor per site. On a new
language site, build them from the plugin patterns there once the site
language is set.

Update translations after changing strings:

```bash
vendor/bin/wp i18n make-pot . languages/ploetner-dev-blocks.pot --exclude=vendor,tests,tools,bin --skip-js
vendor/bin/wp i18n update-po languages/ploetner-dev-blocks.pot languages/
vendor/bin/wp i18n make-mo languages/
vendor/bin/wp i18n make-php languages/
```

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

The sample content is **seeded automatically on the first admin request of each
site** (`admin_init`), which right after activation is the redirect to
`plugins.php`. There is nothing to run by hand for a fresh install or a new
network site. It deliberately does not run on the activation hook: in that
request the plugin is not active yet, so its post types are unregistered and
its translations are not found.

Seeding is idempotent (items whose slug already exists, in any status except
trash, are skipped) and version-aware: a per-site `pd_blocks_seed_version`
option records what has been written. Bump `Seeder::SEED_VERSION` when the
sample data changes; every site picks up missing items on its next admin load.
The data lives in one place, `src/Seeder.php`.

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
