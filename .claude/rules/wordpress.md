# WordPress & Theme Rules

## Repo layout

This is a **full WordPress install checked into git** (`wp-admin/`, `wp-includes/`, etc. all present at repo root) — not a theme-only repo. The theme lives at `wp-content/themes/altrd8-wp-starter`. Work happens almost exclusively inside the theme directory.

- No Bedrock, no Sage, no Acorn, no Blade, no Composer-managed WordPress core
- The theme depends on the private Composer package `bornfight/wp-helpers-bf` (installed via VCS repository in `composer.json`), which supplies most base classes (`BasePostType`, `BaseTaxonomy`, `BaseBlocks`, `Service`, `ImageProvider`, `PartialFinder`, `BaseWPDefaultsOptimization`, ...). Theme classes extend/implement these rather than reimplementing plumbing
- Theme `Text Domain` / slug: `altrd8-wp-starter` (see `style.css` header)

## Theme bootstrap

- `functions.php` checks for `vendor/autoload.php`, requires it, requires `app/global-theme-functions.php`, then instantiates `App\core\Core` and calls `init()`
- `App\core\Core::init()` ([app/core/Core.php](app/core/Core.php)) wires everything: theme update checker + admin menus (admin only), CPTs, taxonomies, WP/CF7 hooks, role management, REST routes, `App\blocks\Blocks` (ACF Gutenberg block registration), and optimization classes (`WPDefaults`, `WPPlugins`)
- `App\core\ThemeUpdateChecker` polls `services.bfs.wtf` for theme updates using the `Identifier` value from the `style.css` header — this is Bornfight's private theme-update service, not WordPress.org

## App structure (`app/`, namespace `App\`, PSR-4 from `app/`)

- `core/` — bootstrap (`Core.php`), theme self-update checker
- `config/` — plain static-method config classes: `Config` (REST namespace, image sizes map used by `ImageHelper`, date format), `ACFConfig` (list of module slugs), `MenuConfig`
- `postTypes/types/` — CPT classes extending `BasePostType` + `CustomPostTypeInterface`; auto-discovered by `App\postTypes\CustomPostTypes` (a `Service` whose `get_pattern()` points at that folder — **no manual registration array**, dropping a class in the folder is enough)
- `postTypes/taxonomies/` — same pattern via `App\postTypes\CustomTaxonomies` and `BaseTaxonomy`
- `modules/` — ACF **flexible content** module classes (see below), factory in `Modules.php`
- `blocks/` — ACF **Gutenberg block** classes (see below), registry in `Blocks.php`
- `helpers/` — `ImageHelper` (extends `ImageProvider`), `MenuHelper`, `ArchiveHelper`, `PaginationHelper`
- `optimization/` — `WPDefaults` (opt-in performance/hardening toggles, all commented out by default — enable per project need) and `WPPlugins`
- `acf/` — ACF setup/defaults (`ACFDefaults`, `ACFWoocommerce`)
- `adminMenus/`, `rest/`, `options/`, `cli/`, `interfaces/`, `exception/`, `bundles/` — admin UI, REST route registration, WP/CF7 hook classes, `wp-cli` commands, shared interfaces (`ControllerInterface`, `BlockInterface`)

Follow the existing `TestPostType` / `TestTaxonomy` / `TestModule` / `TestBlock` files as the literal template for any new class of that kind, then delete the test file once real content replaces it (they're marked `// DELETE to register` / `// PLEASE DELETE ME`).

## Rendering: partials, not templates

There is **no templating engine**. Everything is plain PHP included via the theme's `get_partial()` helper (backed by `PartialFinder` from `wp-helpers-bf`), defined in [app/global-theme-functions.php](app/global-theme-functions.php):

```php
get_partial('layout/footer');
get_partial('components/button', ['label' => 'Click me']);
$html = get_partial('components/tag', ['label' => 'New'], true); // $return = true
```

- Partials live in `partials/`, organized by type: `partials/components/`, `partials/layout/`, `partials/modules/`, `partials/blocks/`, plus any page-specific folders as needed
- A partial is a plain `.php` file that reads variables out of the `$data` array passed to `get_partial()` (see any file under `partials/components/` for the doc-comment-header convention — list expected vars in a leading comment block, then a `/** @var */` block, then markup)
- `get_slice_partial()` is the same lookup but rooted at `slice/partials/` — used for static, backend-less markup that hasn't been wired up yet (see `docs`/README "Slice folder" convention: static markup lives there until a WordPress developer connects it to real data)
- `bu('images/foo.jpg')` returns a URL under the theme's `static/` folder; `au(...)` returns the equivalent filesystem path
- `get_icon('arrow')` renders `static/icons/icon-arrow` (SVG icon partial) via `get_partial(..., 'static')`

## ACF Gutenberg blocks (`App\blocks`) — the primary way pages are built

Pages are built by inserting ACF blocks directly into the WordPress block editor's post content — **not** via a flexible-content field. Page templates (`page.php`, `front-page.php`, `page-templates/*.php`) all render that content through `get_content()`/`the_content` (see "Page templates" below); the block editor is where an editor actually assembles a page.

- `App\blocks\Blocks extends BaseBlocks` lists registered block slugs in `get_blocks()` and default allowed core blocks (`core/paragraph`, `core/heading`, ...) in `get_default_blocks()`. Registration itself (`acf_register_block_type()` per listed slug) happens in the parent `BaseBlocks::register_blocks()` — no need to touch that
- Each block class implements `App\interfaces\BlockInterface`: `get_settings()` returns the ACF block config array and `get_view(array $block): void` echoes/`get_partial()`s the markup
- **Live preview:** every block's `get_settings()` should set `'mode' => 'preview'`. This is a built-in ACF PRO behavior — the editor re-renders the block's PHP `render_callback` output over AJAX whenever a field changes, no page save/reload and no custom JS required
- **Per-template scoping:** a block's `get_settings()` may include an optional `'templates' => [...]` key (page-template slugs; `['*']` — the default when omitted — means every template). `App\blocks\Blocks::filter_allowed_blocked_types()` (overriding the base class) reads this per block, compares against `get_page_template_slug()` for the post being edited, and restricts the inserter accordingly
- Block partials live in `partials/blocks/`
- Copy `TestBlock.php` / `partials/blocks/test.php` as the starting point for a new block — `TestBlock.php` demonstrates the `mode: preview` + `templates` pattern

## Flexible content modules (`App\modules`) — for structured sub-sections, not page layout

A secondary system, used when a *block's own fields* need a repeatable/structured sub-layout (e.g. a "layout variant" chooser inside one block), not for composing the page itself:

- `get_modules_partial($acf_flexible_field_value)` / `get_static_modules_partial(...)` loop the ACF rows and call `App\modules\Modules::get_module($row)`, which maps the row's `acf_fc_layout` (snake_case) to a `App\modules\{PascalCase}` class
- Each module class implements `App\interfaces\ControllerInterface` (`get_view(): string`), reads its constructor args from the `$module` array, and renders via `get_partial('modules/{slug}', [...], true)`
- `ACFConfig::get_modules()` lists the module slugs that should be available as ACF flexible-content layout choices
- Copy `TestModule.php` / `partials/modules/test.php` as the starting point for a new module

## Responsive media

See [responsive-image.md](responsive-image.md) for `get_responsive_image()` / `get_responsive_video()`.

## Page templates

`page-templates/` holds WordPress page templates (`Template Name: ...` doc comment), named after the page they represent, e.g. `page-templates/home.php`, plus `page.php` (default), `single.php`, and `front-page.php` (static homepage) at the theme root. All of them follow the same shape: `get_header()`, `get_partial('layout/loader')`, `get_partial('layout/navigation')`, a page wrapper around `echo get_content();` (renders the post's block-editor content — this is what makes the page "built from blocks"), `get_partial('layout/footer')`, `get_footer()`. A template only needs custom logic beyond that if a page genuinely needs structurally different markup around the content (a different wrapper id/class, extra partials outside the content area, etc.) — it should still render `get_content()` for the actual page body, not a hardcoded partial.

## Deploy

Deployed with [Deployer](https://deployer.org) via `deploy.php` — `staging` and `production` hosts both point at `services.bfs.wtf` (Bornfight infra), deploying under `~/bwp_projects[-staging]/altrd8-wp-starter`. `static/dist` is uploaded as a build artifact (`deploy:upload_dist` task) — it is **not** built on the server, so run `npm run build` locally/in CI before deploying. There is no Drone/GitHub Actions pipeline checked into this repo.

## Security & performance

- Escape on output (`esc_attr`, `esc_url`, `wp_kses_post`), sanitize on input — standard WordPress practice, not separately enforced by a theme class
- `App\optimization\WPDefaults::init()` is where opt-in hardening/perf toggles live (disable Gutenberg script bloat, drop jQuery migrate, disable REST user enumeration, etc.) — all commented out by default; enable only what a given project actually needs, don't uncomment speculatively
