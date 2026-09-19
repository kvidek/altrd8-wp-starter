# Architecture

## Boot sequence

1. WordPress loads `functions.php`, which
   - defines `INCLUDE_PATH`, `TEMPLATE_PATH`, `INCLUDE_URL` and `FS_METHOD`;
   - stops with an admin notice if `vendor/autoload.php` is missing;
   - requires `vendor/autoload.php`, `app/global-theme-functions.php` and `app/blocks/wrap-core-blocks.php`;
   - runs `(new App\core\Core())->init()`.
2. `Core::init()` (`app/core/Core.php`) wires everything:
   - **admin only:** the theme update checker, `WordpressHooksAdmin`, `CustomAdminMenus`, and the block-editor stylesheet injection;
   - **always:** WP-CLI commands (only under WP-CLI), custom post types, taxonomies, `WordpressHooks`, `CF7Hooks`, `RoleManagement`, REST routes, `Blocks`, `WPDefaults`, `WPPlugins`.

Keep new logic in classes under `app/` and register it from `Core`. Don't add code to `functions.php` ("Please don't paste code here!!").

## Code layout (`app/`, PSR-4 `App\` → `app/`)

Class names are `CamelCase` (the autoloader needs it), folders are `camelCase` and match their namespace segment (`postTypes`, `adminMenus`), functions and variables are `snake_case`.

| Folder | Purpose |
|--------|---------|
| `core/` | `Core` (bootstrap), `ThemeUpdateChecker` / `ThemeUpdate` |
| `config/` | static config: `Config` (REST namespace `bornfight/v1`, image sizes, date format), `MenuConfig` (menu locations), `ACFConfig` (flexible-content module slugs) |
| `blocks/` | ACF Gutenberg blocks, the `Blocks` registry, the `BlockSettings` trait, `wrap-core-blocks.php` — see [Blocks](blocks.md) |
| `modules/` | ACF flexible-content modules (secondary system, see [Blocks](blocks.md#flexible-content-modules)) |
| `postTypes/` | `types/` and `taxonomies/`: drop a class in the folder and it is registered automatically |
| `helpers/` | `CtaHelper`, `VideoHelper`, `ImageHelper`, `MenuHelper`, `PaginationHelper`, `ArchiveHelper` |
| `bundles/` | `<Project>Assets`: enqueues the webpack output, see [Front-end](frontend.md#asset-loading) |
| `optimization/` | `WPDefaults` and `WPPlugins`: opt-in performance/hardening toggles |
| `options/` | hook classes: `WordpressHooks`, `WordpressHooksAdmin`, `CF7Hooks`, `RoleManagement` |
| `rest/` | `CustomRoutes` (auto-registers everything in `routes/`), plus `callback/` |
| `cli/` | `CustomCli` (auto-registers classes in `commands/` that carry an `#[Attr]` attribute, e.g. `wp bwp example`) |
| `adminMenus/` | admin pages, e.g. the "Theme Options" page |
| `acf/` | ACF defaults |
| `interfaces/` | `BlockInterface`, `ControllerInterface` |
| `exception/` | `ValidationException`, `SendMailException` |

### Auto-discovered classes

`CustomPostTypes`, `CustomTaxonomies`, `CustomRoutes` and `CustomCli` are `Service`s whose `get_pattern()` points at a folder (`app/postTypes/types`, `.../taxonomies`, `app/rest/routes`, `app/cli/commands`). New classes there are picked up with no registration list. The `Test*` classes (`TestPostType`, `TestTaxonomy`, `TestModule`, `TestBlock`, `TestRoute`, `TestCallback`) are working templates: copy one, then delete the test file once real content replaces it.

### Opt-in toggles

`App\optimization\WPDefaults::init()` lists hardening/performance switches (disable Gutenberg scripts, comments, embeds, emoji, jQuery migrate, REST user enumeration, ...). They are all **commented out**. Enable only what a project needs.
`WPPlugins` already disables the ACF Extended features this theme doesn't use (dynamic post types/taxonomies/blocks/options pages, multi-language support, ...).

## Rendering: partials, not templates

There is no templating engine, only plain PHP. Templates and blocks call `get_partial()`; partials echo markup.

```php
get_partial( 'layout/footer' );
get_partial( 'components/button', array( 'label' => 'Click me' ) );
$html = get_partial( 'components/tag', array( 'label' => 'New' ), true ); // return instead of echo
```

Partials live in `partials/`, grouped by type:

| Folder | Content |
|--------|---------|
| `components/` | discrete UI pieces (button, accordion, badge, breadcrumbs, responsive image/video/iframe, sliders parts, loaders, ...) |
| `layout/` | site-wide pieces (loader, navigation, mobile navigation, footer, pagination) |
| `blocks/` | one partial per ACF block |
| `modules/` | flexible-content module partials |

A partial:
- has a doc-comment header listing the variables it expects (`@var`), then the markup;
- reads variables from the `$data` array passed to `get_partial()` (they are extracted into local variables);
- stays presentational: no queries and no business logic. Compute in a class, pass the result in.

### Global helper functions (`app/global-theme-functions.php`)

Keep this list short; wrap new reusable logic in a class instead.

| Function | Purpose |
|----------|---------|
| `get_partial($partial, $data, $return)` | include a partial from `partials/` |
| `get_slice_partial($partial, $data, $return)` | same, rooted at `slice/partials/` (static, backend-less markup not yet wired to data) |
| `get_content($id)` | the block-editor content of a post (used by every page template) |
| `get_responsive_image($args)` / `get_responsive_video($args)` | responsive media, see [Front-end](frontend.md#responsive-images-and-video) |
| `get_icon($name)` | renders the SVG partial `static/icons/icon-<name>` |
| `bu($path)` / `au($path)` | URL / filesystem path under the theme's `static/` folder |
| `get_module`, `get_modules_partial`, `get_static_modules_partial` | render ACF flexible-content modules |
| `is_frontend_request()` / `is_rest_request()` | request-type checks |

## Page templates

`page.php`, `single.php`, `front-page.php`, `archive.php`, `search.php`, `404.php` and everything in `page-templates/` share one shape:

```php
get_header();
get_partial( 'layout/loader' );
get_partial( 'layout/navigation' );
// <div class="o-page o-page--{name}"> ... echo get_content(); ... footer partial </div>
get_footer();
```

`get_content()` outputs the block-editor content, which is what makes pages "built from blocks". A template needs its own logic only when a page needs structurally different markup around the content; it should still render `get_content()` for the body. A new page template is a file in `page-templates/` starting with `/** Template Name: ... */`.

## Theme update checker

`Core::init_theme_update_checker()` (admin only) polls `services.bfs.wtf` for theme updates using the `Identifier` from the `style.css` header (and the same value in the URL inside `Core.php`). This is Bornfight's private update service, not WordPress.org. A new project gets a fresh identifier from the init script; see [Starter template](starter.md).

## Editor styles

The block editor gets the compiled `editor.css` inlined into its canvas iframe (`block_editor_settings_all` filter in `Core`). It is deliberately not enqueued in wp-admin, because it resets fonts and spacing and would clobber core admin UI. Details in [Front-end](frontend.md#asset-loading).
