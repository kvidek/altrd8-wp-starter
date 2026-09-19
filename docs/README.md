# altrd8-wp-starter — documentation

A plain-PHP WordPress theme starter: ACF Gutenberg blocks for page building, webpack + SCSS + ES6 for assets, no framework and no templating engine.

| Document | Read it when you want to… |
|----------|---------------------------|
| [Getting started](getting-started.md) | set up a project locally, run the build, fix a broken setup |
| [Architecture](architecture.md) | understand how the theme boots and where code lives |
| [Blocks](blocks.md) | build pages from ACF blocks, or add a new block |
| [ACF conventions](acf-conventions.md) | name field groups/keys, keep `acf-json` in sync, reuse shared groups |
| [Front-end](frontend.md) | write SCSS/JS, use images/video/icons, understand the build |
| [Workflow](workflow.md) | branch, format, lint, commit, and work with Claude Code in this repo |
| [Deployment](deployment.md) | ship to staging/production with Deployer |
| [Starter template](starter.md) | start a new project from this repo, or feed improvements back |

## Stack at a glance

- **PHP 8.0+**, PSR-4 classes in `app/` (namespace `App\`), plain PHP partials in `partials/`
- **Private Composer package** `bornfight/wp-helpers-bf` supplies the base classes (blocks, post types, image provider, partial finder, asset bundle, optimization helpers)
- **Plugins the code assumes:** ACF PRO (blocks, `mode: preview`) and ACF Extended (JSON auto-sync). BF Advanced Images is referenced as a dependency for image sizes. Contact Form 7 and WPML have optional support (hooks and vendor SCSS)
- **Front end:** webpack 5, SCSS (ITCSS + BEM), hand-rolled ES6 classes, `vanilla-lazyload`, `instant.page`, `tua-body-scroll-lock`. No jQuery, no animation library
- **Node >= 25.9 / npm >= 11.13** (`.nvmrc` says `v25`)

## Folder map

```
acf-json/          ACF field groups (one JSON per group), synced by ACF Extended
app/               all PHP classes (namespace App\)
  blocks/          ACF Gutenberg blocks + shared BlockSettings trait
  bundles/         asset enqueueing (reads static/dist/manifest.json)
  config/          static config: image sizes, menus, REST namespace
  core/            bootstrap (Core.php) and theme update checker
  helpers/         Cta / Video / Image / Menu / Pagination / Archive helpers
  optimization/    opt-in WordPress hardening/performance toggles
  postTypes/       CPTs and taxonomies (auto-discovered)
  ...              adminMenus, rest, cli, options, modules, interfaces, exception
bin/               init.mjs — renames the starter into a new project (removes itself)
docs/              this documentation
page-templates/    WordPress page templates
partials/          presentational PHP (blocks/, components/, layout/, modules/)
static/            js/, scss/, fonts/, icons/, images/, ui/ — and dist/ (build output, git-ignored)
```

Root-level `header.php`, `footer.php`, `page.php`, `single.php`, `archive.php`, `search.php`, `404.php`, `front-page.php` are the WordPress template files.

## Where the rules live

- **`.claude/rules/*.md`** are short, always-loaded rules for Claude Code (WordPress, code style, responsive media, animations). They are accurate and worth reading as a human too.
- **`README.md`** (repo root) is the older, longer HTML/SCSS/JS style guide. Parts of it are out of date (it mentions gulp, `build:vendor`, PHP 7.4, node 14). When it disagrees with these docs or with `package.json`, trust the docs and the code.
