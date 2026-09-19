# Blocks

Pages are built by inserting **ACF Gutenberg blocks** into the WordPress block editor. Page templates render that content with `get_content()`; there is no flexible-content "page builder" field. The editor is where a page gets assembled.

A block is always three things that match by slug:

| Piece | Location | Example (`intro-block`) |
|-------|----------|-------------------------|
| PHP class | `app/blocks/<PascalSlug>.php` | `app/blocks/IntroBlock.php` |
| Partial (markup) | `partials/blocks/<slug>.php` | `partials/blocks/intro-block.php` |
| ACF field group | `acf-json/group_<prefix>_block_<slug_underscored>.json` | `group_<prefix>_block_intro_block.json` |

Registered blocks appear in the editor as `acf/<slug>`, in the "Custom Blocks" category.

## How a block renders

```
editor / front end
   -> ACF calls the block's render_callback  (IntroBlock::get_view)
      -> reads fields with get_field(), normalizes them (helpers, BlockSettings)
      -> get_partial('blocks/intro-block', $data)   // markup only
```

The class reads and prepares data. The partial echoes markup and holds no logic.

### The block class

Implements `App\interfaces\BlockInterface`: `get_settings()` returns the ACF block config and `get_view(array $block): void` renders.

```php
class IntroBlock implements BlockInterface {
    use BlockSettings;                       // shared Settings-tab readers

    public function get_settings(): array {
        return array(
            'name'            => 'intro-block',
            'title'           => 'Intro Block',
            'category'        => 'custom-blocks',
            'mode'            => 'preview',          // live editor preview (ACF PRO)
            'post_types'      => array( 'post', 'page' ),
            'render_callback' => array( $this, 'get_view' ),
            'templates'       => array( '*' ),       // page templates it may be used on
        );
    }

    public function get_view( array $block ): void {
        get_partial( 'blocks/intro-block', array_merge(
            array( 'title' => get_field( 'title' ) ?: '' /* ... */ ),
            $this->get_section_wrapper(),
            $this->get_section_id(),
            $this->get_color_scheme(),
            $this->get_section_borders()
        ) );
    }
}
```

- **`'mode' => 'preview'`** on every block: ACF PRO re-renders the block over AJAX on each field change, so no save/reload and no custom JS is needed.
- **`'templates'`** is not a native ACF key. `Blocks::filter_allowed_blocked_types()` reads it to restrict which page templates (`get_page_template_slug()`) a block can be inserted on. `['*']` (the default) means everywhere.

### Registration

Add the slug to `Blocks::get_blocks()` in `app/blocks/Blocks.php`:

```php
return array( 'test-block', 'intro-block', 'media-with-content-block' );
```

The class name is derived from the slug (`media-with-content-block` → `MediaWithContentBlock`) in the `App\blocks\` namespace. `BaseBlocks` (from `wp-helpers-bf`) does the actual `acf_register_block_type()` call.

`Blocks::get_default_blocks()` lists the **core** blocks that stay allowed next to the ACF ones: heading, paragraph, list, image, quote, buttons, button, and reusable blocks (`core/block`). Anything not listed is hidden from the inserter.

## Shared settings (the Settings tab)

Every block's field group has two tabs, **Content** and **Settings**. The Settings tab is built from reusable ACF **clone groups**, and the `BlockSettings` trait (`app/blocks/BlockSettings.php`) reads them:

| Trait method | Clone group | Fields / output |
|--------------|-------------|-----------------|
| `get_section_wrapper()` | `spacing` | `padding_top`, `padding_bottom` (`none`/`small`/`medium`/`large`) → `u-pt-*` / `u-pb-*` |
| `get_color_scheme()` | `color_scheme` | `light`, `off-light`, `dark`, `off-dark` → `u-color-scheme-*` |
| `get_section_borders()` | `section_borders` | `border_top`, `border_bottom` → `u-border-top/bottom` |
| `get_section_id()` | `section_id` | sanitized anchor `id` for the section (for scroll-to links) |
| `get_alignment()` | `alignment` | `left` / `right` for two-column layouts |
| `get_bg_gradient()` | `component_bg_gradient` | soft background blob; pass to `components/bg-gradient` |

Every block clones **spacing**. Use the others only when the block needs them. Add **section_id** to every block that renders as its own page section.

## Reusable content components

| Helper / partial | Use |
|------------------|-----|
| `App\helpers\CtaHelper::normalize_rows()` | turns rows of the cloned CTA group (`component_cta`: link, button style, open-modal / scroll-to / download actions) into args for `components/button` |
| `App\helpers\VideoHelper::normalize()` | turns the cloned video group (`component_video`: QHD/FHD/HD/SD file or URL + poster) into args for `get_responsive_video()` |
| `get_responsive_image()` | responsive images, see [Front-end](frontend.md#responsive-images-and-video) |

## Core blocks in page content

Core blocks dropped straight into a page (heading, paragraph, list, image, quote, buttons) are wrapped in the theme's `o-section` / `o-container` markup by `app/blocks/wrap-core-blocks.php`, so the `u-content-editor` rhythm applies. They get spacing controls mapped to the same four-step scale as ACF blocks. Blocks nested inside another block are not wrapped again, and ACF blocks bring their own section wrapper.

## Adding a block

**With Claude Code (recommended):** run `/create-block <description of the block>`. The command in `.claude/commands/create-block.md` scaffolds the class, partial, ACF JSON and registration following all the conventions here. `/create-block-backend` builds the PHP class and JSON from a partial that already exists.

**By hand:**
1. Copy `app/blocks/TestBlock.php` to `app/blocks/<PascalSlug>.php` and adjust `get_settings()` / `get_view()`.
2. Create `partials/blocks/<slug>.php` (doc-comment header with `@var`s, then escaped markup).
3. Create the ACF group JSON (see [ACF conventions](acf-conventions.md)): Content tab, Settings tab with a cloned Spacing group, location rule `block == acf/<slug>`.
4. Add the slug to `Blocks::get_blocks()`.
5. Add SCSS only if the block needs custom presentation: a `c-<slug>` component under `static/scss/components/blocks/` forwarded from its `_components.index.scss`.
6. Check it in the editor: insert the block, change fields (preview should update live), check it on the front end and on mobile.

Markup conventions for a block partial (see `partials/blocks/intro-block.php`): a `<section>` with `c-<slug> o-section u-color-scheme-* u-pt-* u-pb-*`, an optional `id` from the section-id setting, and an inner `o-container`. Escape output (`esc_html`, `esc_attr`, `wp_kses_post`), and return early if the block has no content.

## Flexible content modules

A secondary system, used only when a *block's own fields* need a repeatable, structured sub-layout (for example a layout-variant chooser inside one block). It is not for composing a page.

- `ACFConfig::get_modules()` lists module slugs available as flexible-content layouts.
- `get_modules_partial($rows)` loops the ACF rows and asks `App\modules\Modules::get_module($row)` for the class matching the row's `acf_fc_layout`.
- Each module class implements `ControllerInterface` (`get_view(): string`) and renders `get_partial('modules/<slug>', ..., true)`.
- Start from `app/modules/TestModule.php` and `partials/modules/test.php`.
