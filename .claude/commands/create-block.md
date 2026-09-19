# Create ACF Block On The Fly

Create a complete ACF-backed block in this project — an ACF **Gutenberg block** (`App\blocks`, the default — pages are built from blocks inserted directly into the block editor) or, less commonly, an ACF **flexible-content module** (`App\modules`, for a structured sub-layout nested inside a block's own fields) — end to end: PHP class, partial, ACF field group JSON, and registration.

## Input

$ARGUMENTS

## Goals

- Scaffold a new block/module from scratch following this theme's existing conventions (see `.claude/rules/wordpress.md` and `.claude/rules/code-style.md`).
- Generate all required pieces: PHP controller class, PHP partial, ACF JSON field group, registration entry.
- Add SCSS/JS scaffolding only if the content actually needs custom presentation/behavior.
- Never introduce Blade, Vite, View Composers, or any Sage/Acorn pattern — this theme uses plain PHP partials rendered via `get_partial()`.

## Project context

- **Theme directory:** `wp-content/themes/altrd8-wp-starter/` (plain PHP + `bornfight/wp-helpers-bf`, webpack, SCSS, BEM/ITCSS)
- **Namespace:** `App\`, PSR-4 from `app/`
- **ACF JSON:** `acf-json/` (currently only `.gitkeep` — first block/module in this project creates the first exported group)
- **ACF field/group key prefix:** `altrd8_wp_starter`
- **Text domain / theme slug:** `altrd8-wp-starter`
- **ACF Extended (ACFE) is installed.** Use its field-type/UI enhancements where they clearly help (e.g. `acfe_group_modal_*` on a `group`-type field so its settings open in a modal instead of inline — see `group_altrd8_wp_starter_component_cta.json`'s `settings` field). Do **not** use ACFE's *dynamic* block/post-type/taxonomy registration or its multi-language support — `App\optimization\WPPlugins::init()` deliberately disables those (`remove_acfe_dynamic_block_types`, etc.) because this project registers blocks/CPTs through PHP classes (`App\blocks`, `App\postTypes`), not ACFE's dynamic-engine equivalents.
- **Every field group JSON MUST set `"acfe": {"autosync": ["json"]}` at the top level.** This is not optional and not cosmetic: ACFE's `ACFE_AutoSync_Json::pre_update_field_group()` (`wp-content/plugins/acf-extended/includes/modules/autosync-json.php`) hooks `acf/update_field_group` — which fires on **any** wp-admin save of that field group, including clicking "Sync" to import a JSON-only group into the DB for the first time — and calls `unlink()` on the JSON file whenever `acfe.autosync` doesn't include `"json"`. Confirmed by direct incident in this project: two field groups created without this key were silently deleted from disk the next time anything touched them in wp-admin. Add it to every new group (block groups and shared clone groups alike) the moment you create the file — don't wait to add it later.
- **Every block's ACF field group MUST be split into two top-level tabs**, in this order: `Content` (all editable content fields) and `Settings` (presentation/configuration — always starts with a cloned `Spacing` group; other clones added as needed). See step 6 below.

### First-block infrastructure

A few shared pieces are part of the target architecture but won't exist until the first block that needs them is scaffolded. Create any that are missing **before** the block-specific files, then reuse them for every later block:

| File | Purpose |
|------|---------|
| `app/blocks/BlockSettings.php` | PHP trait read by block classes to pull Settings-tab clone values (`get_field()` wrapper) — this project's equivalent of Villa Argentina's `BlockComposer` base, adapted for plain block classes (no View Composer layer here) |
| `acf-json/group_altrd8_wp_starter_spacing.json` | Shared "Spacing" clone group — every block's Settings tab clones this |
| `acf-json/group_altrd8_wp_starter_color_scheme.json` | Shared "Color Scheme" clone group — only when a block needs it |
| `acf-json/group_altrd8_wp_starter_section_borders.json` | Shared "Section Borders" clone group — only when a block needs it |
| `acf-json/group_altrd8_wp_starter_section_id.json` | Shared "Section ID" clone group — add on every block that renders as its own page section (has an anchor-able wrapper) |
| `acf-json/group_altrd8_wp_starter_alignment.json` | Shared "Alignment" clone group — only when a block needs left/right column alignment |
| `app/helpers/CtaHelper.php` | PHP normalizer for CTA repeater rows — turns cloned `component_cta` rows into args ready for `get_partial('components/button', ...)` |
| `acf-json/group_altrd8_wp_starter_component_cta.json` | Shared "CTA" clone group (link + button style + open-modal/scroll-to/download settings, the settings sub-fields open in an ACFE modal) — clone (seamless) into a repeater sub-field wherever a block needs one or more CTAs |
| `app/helpers/VideoHelper.php` | PHP normalizer for the video component — turns cloned `component_video` data into args ready for `get_responsive_video()` |
| `acf-json/group_altrd8_wp_starter_component_video.json` | Shared "Video" clone group (QHD/FHD/HD/SD file-or-URL sources + poster) — clone into a media-type toggle field wherever a block needs an image-or-video choice |
| `acf-json/group_altrd8_wp_starter_component_bg_gradient.json` | Shared "BG Gradient" clone group (enable toggle, `off-light`/`tint`/`dark` color, horizontal/vertical alignment, size) — clone into a block's Settings tab wherever a decorative background blob is wanted; `BlockSettings::get_bg_gradient()` reads it, `get_partial('components/bg-gradient', $bg_gradient)` renders it (see below). Each color maps to its own `*-alt` palette token (`--off-light-alt`, `--tint-alt`, `--dark-alt` in `_settings.color.scss`) — a separate, more vivid sub-palette for these blurred blobs, distinct from the base tokens the color-scheme component uses |

These are ports of Villa Argentina's equivalent shared groups (`themes/villa-argentina-web-20226/acf-json/group_villa_argentina_{spacing,color_scheme,section_borders,section_id,alignment,component_cta,component_video}.json` and `App\Helpers\{CtaNormalizer,VideoFieldNormalizer}`) — same field shapes and choices (CTA's `button_style` choices adapted to this project's actual button partial styles: `primary`/`secondary`/`tertiary`, not Villa Argentina's `primary`/`secondary`/`bordered`/`link`; video posters resolved via this project's named image-size registry instead of Villa Argentina's on-the-fly `ImageResizer`), renamed to this project's `altrd8_wp_starter` key prefix. `component_bg_gradient` has no Villa Argentina equivalent — it's specific to this project's own brand palette (see `.claude/rules/wordpress.md` and `static/scss/settings/_settings.color.scss` for the `light`/`off-light`/`tint`/`accent`/`dark`/`off-dark` tokens, extracted from Figma). Shared-group JSON for the spacing/color-scheme/etc. family is given in step 6c below; create only the ones an actual block needs (Spacing is the only one every block needs), not all of them preemptively.

If this project later adds WPML/ACFML, run the `acfml-translation-prefs` skill afterward to set per-field translation preferences — don't hand-add `wpml_cf_preferences` keys into these field JSONs.

### Content-tab CTA repeaters

When a block needs one or more CTAs (not a Settings-tab concern — this lives in the Content tab, inside a `repeater`):

1. Add a `repeater` content field (e.g. `ctas`) whose single sub-field is a **seamless** clone of `group_altrd8_wp_starter_component_cta`:
   ```json
   {
       "key": "field_altrd8_wp_starter_{slug_underscored}_cta",
       "label": "CTA",
       "name": "cta",
       "type": "clone",
       "instructions": "",
       "required": 0,
       "conditional_logic": 0,
       "wrapper": { "width": "", "class": "", "id": "" },
       "clone": [ "group_altrd8_wp_starter_component_cta" ],
       "display": "seamless",
       "layout": "block",
       "prefix_label": 0,
       "prefix_name": 0,
       "parent_repeater": "field_altrd8_wp_starter_{slug_underscored}_ctas"
   }
   ```
   `display: "seamless"` and `prefix_name: 0` here (unlike the Settings-tab clones in step 6b) — this is a repeater sub-field, not a page-level Settings group, so there's no cross-block collision to guard against.
2. In the block class, normalize the raw repeater value: `\App\helpers\CtaHelper::normalize_rows( get_field( 'ctas' ) )`.
3. In the partial, loop the normalized rows and render each with the existing button component: `foreach ( $ctas as $cta ) { get_partial( 'components/button', $cta ); }` — `CtaHelper` already shapes each row's keys (`label`, `url`, `type`, `style`, `new_tab`, `download`, `modal_id`, `id`) to match `partials/components/button.php`'s own `$data` contract directly, no remapping needed.

### Settings-tab BG Gradient

When a block wants an optional decorative background-gradient blob (a Settings-tab concern, like spacing/color scheme — not Content):

1. Add a clone field in the Settings tab, same shape as the spacing/color-scheme clones in step 6b, `name: "bg_gradient"`, cloning `group_altrd8_wp_starter_component_bg_gradient`.
2. In the block class, merge `$this->get_bg_gradient()` into the partial's data — it returns a single nested `['bg_gradient' => ['enabled' => bool, 'color' => ..., 'horizontal_alignment' => ..., 'vertical_alignment' => ..., 'size' => ...]]`, not flattened, so its generic sub-keys can't collide with an unrelated field.
3. In the partial, render it right after the opening `<section ...>` tag (so it sits behind the content in stacking order) via `get_partial( 'components/bg-gradient', $bg_gradient )` — the sub-partial handles the enabled-check and class-building itself, nothing else to do. The block's own section wrapper needs `position: relative` (every `.o-section` already has it) for the blob's `position: absolute` to anchor correctly; add `overflow: hidden` on the section only if the block wants the blob clipped instead of bleeding past the edge.

### 0) Decide: Gutenberg block vs. flexible-content module

Ask (or infer from `$ARGUMENTS`) which system this belongs to:

- **ACF Gutenberg block** (`App\blocks`) — a block an editor inserts directly into a page's block-editor content, with live preview in the admin. This is the default/most common case: **the theme is built page-section-by-page-section from blocks in post content**, not from a flexible-content field. Almost anything described as a page section, hero, cards grid, etc. is a block.
- **Flexible-content module** (`App\modules`) — only for a *structured sub-layout inside a block's own fields* (e.g. a repeatable list of variant rows nested inside one block), not for composing the page itself.

If ambiguous, default to an ACF Gutenberg block.

## Steps — ACF Gutenberg block (default)

### 1) Analyze requirements

From `$ARGUMENTS`, derive: block slug (kebab-case, e.g. `article-cards`), human title, content fields needed (text, wysiwyg, repeater, image, link, true/false), which page templates it should be insertable on (`['*']` unless the user names specific ones), which Settings-tab clones it needs beyond Spacing (Color Scheme? Section Borders? Section ID — yes if it's its own page section? Alignment?), whether custom SCSS/JS is needed.

### 2) Inspect existing patterns first

Read before generating anything:

- `app/blocks/TestBlock.php` and `app/blocks/Blocks.php` (registration + per-template scoping)
- `app/blocks/BlockSettings.php` if it already exists (Settings-tab clone reader) — otherwise you're creating it now, see "First-block infrastructure" above
- `partials/blocks/test.php` (partial doc-comment header convention)
- Any real block ACF JSON already in `acf-json/` for precedent; otherwise the field defs in step 4 below are canonical

### 3) Create `app/blocks/BlockSettings.php` if it doesn't exist yet

A trait with one low-level reader plus one convenience method per Settings-tab clone, read via `get_field()` against the prefixed clone `name` (matches `prefix_name: 1` in the clone fields, see step 4b):

```php
<?php

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait BlockSettings {
	/**
	 * Read a sub-field from a prefixed Settings-tab clone group.
	 */
	private function get_cloned_setting( string $group_name, string $sub_field, mixed $default = null ): mixed {
		$group = get_field( $group_name );

		if ( is_array( $group ) && array_key_exists( $sub_field, $group ) && $group[ $sub_field ] !== null && $group[ $sub_field ] !== '' ) {
			return $group[ $sub_field ];
		}

		return $default;
	}

	/** Cloned `group_altrd8_wp_starter_spacing`. Every block clones this. */
	private function get_section_wrapper( string $default_top = 'medium', string $default_bottom = 'medium' ): array {
		return array(
			'padding_top'    => $this->get_cloned_setting( 'spacing', 'padding_top', $default_top ),
			'padding_bottom' => $this->get_cloned_setting( 'spacing', 'padding_bottom', $default_bottom ),
		);
	}

	/** Cloned `group_altrd8_wp_starter_color_scheme`, when a block uses it. */
	private function get_color_scheme( string $default = 'light' ): array {
		return array(
			'color_scheme' => $this->get_cloned_setting( 'color_scheme', 'color_scheme', $default ),
		);
	}

	/** Cloned `group_altrd8_wp_starter_section_borders`, when a block uses it. */
	private function get_section_borders(): array {
		return array(
			'border_top'    => filter_var( $this->get_cloned_setting( 'section_borders', 'border_top', false ), FILTER_VALIDATE_BOOLEAN ),
			'border_bottom' => filter_var( $this->get_cloned_setting( 'section_borders', 'border_bottom', false ), FILTER_VALIDATE_BOOLEAN ),
		);
	}

	/** Cloned `group_altrd8_wp_starter_section_id`, on every block that is its own page section. */
	private function get_section_id(): array {
		$id = $this->get_cloned_setting( 'section_id', 'section_id', '' );

		return array(
			'section_id' => is_string( $id ) && $id !== '' ? sanitize_title( $id ) : '',
		);
	}

	/** Cloned `group_altrd8_wp_starter_alignment`, when a block uses it. */
	private function get_alignment( string $default = 'right' ): array {
		$alignment = $this->get_cloned_setting( 'alignment', 'alignment', $default );

		return array(
			'alignment' => in_array( $alignment, array( 'left', 'right' ), true ) ? $alignment : $default,
		);
	}
}
```

Every method returns a flat associative array (not a bare scalar) so they compose cleanly with `array_merge()` in `get_view()` below — this is what `App\blocks\IntroBlock` (the first real block built from this pattern) actually does.

### 4) Create the block class

Create `app/blocks/{PascalCase}.php` implementing `App\interfaces\BlockInterface` and `use BlockSettings;`:

```php
<?php

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use App\interfaces\BlockInterface;

class {PascalCase} implements BlockInterface {
	use BlockSettings;

	public function get_settings(): array {
		return array(
			'name'            => '{slug}',
			'title'           => '{Human Readable Title}',
			'description'     => '{Brief description}.',
			'category'        => 'custom-blocks',
			'icon'            => null,
			// Live editor preview — ACF PRO AJAX-refreshes render_callback on every field change.
			'mode'            => 'preview',
			'keywords'        => array(),
			'post_types'      => array( 'post', 'page' ),
			'render_callback' => array( $this, 'get_view' ),
			// Page-template slugs this block may be inserted on; ['*'] = every template.
			// Read by App\blocks\Blocks::filter_allowed_blocked_types().
			'templates'       => array( '*' ),
		);
	}

	public function get_view( array $block ): void {
		get_partial( 'blocks/{slug}', array_merge(
			array(
				// ...content fields via get_field()...
			),
			$this->get_section_wrapper(),
			$this->get_section_id(),
			// $this->get_color_scheme(),   // only if this block clones Color Scheme
			// $this->get_section_borders(), // only if this block clones Section Borders
			// $this->get_alignment(),       // only if this block clones Alignment
		) );
	}
}
```

`get_section_wrapper()`/`get_section_id()` return flat arrays (`['padding_top' => ..., 'padding_bottom' => ...]`, `['section_id' => ...]`) so they merge straight into the partial's `$data` array alongside content fields.

### 5) Create the partial

Create `partials/blocks/{slug}.php` — plain PHP, follow the doc-comment header + `@var` block convention from `partials/components/*.php`. BEM class naming: `.c-{slug}` (see README's naming convention). Escape output (`esc_html`, `esc_attr`, `esc_url`); use raw `echo` only for trusted WYSIWYG. Consume `$padding_top`/`$padding_bottom` as spacing utility classes (`u-pt-{value}`/`u-pb-{value}` — add these utilities in `static/scss/utilities/` on first use if they don't exist yet) and `$section_id` as the section wrapper's `id` attribute (only rendered when non-empty).

### 6) Create the ACF field group JSON

Create `acf-json/group_altrd8_wp_starter_block_{slug_underscored}.json` following the canonical Content/Settings structure below.

- `title`: `Block - {Human Readable Title}` (per README's ACF field-naming convention)
- Location rule: `"param": "block", "operator": "==", "value": "acf/{slug}"`
- Set `"modified"` to the current Unix timestamp (`date +%s`)

#### 6a) Top-level tab structure

Every block field group's `fields` array starts with these two tabs, content fields between them, Settings clones after the second:

```json
{
    "key": "field_altrd8_wp_starter_{slug_underscored}_tab_content",
    "label": "Content",
    "name": "",
    "type": "tab",
    "instructions": "",
    "required": 0,
    "conditional_logic": 0,
    "wrapper": { "width": "", "class": "", "id": "" },
    "placement": "top",
    "endpoint": 0,
    "selected": 1
},

/* ...block-specific content fields (text, wysiwyg, repeater, image, link, true_false)... */

{
    "key": "field_altrd8_wp_starter_{slug_underscored}_tab_settings",
    "label": "Settings",
    "name": "",
    "type": "tab",
    "instructions": "",
    "required": 0,
    "conditional_logic": 0,
    "wrapper": { "width": "", "class": "", "id": "" },
    "placement": "top",
    "endpoint": 0,
    "selected": 0
},

/* Spacing clone (6b, always) — optional Color Scheme / Section Borders / Alignment clones — Section ID clone (6b, on every block that is its own page section) */
```

`Content`'s `selected: 1` makes it the default open tab. `Settings` uses `selected: 0`/`endpoint: 0` so it continues the same tab strip. Both use `placement: "top"`.

#### 6b) Settings-tab clone fields

Each Settings-tab clone follows this shape (shown for Spacing — the only one every block needs; swap `spacing`/`Spacing` for `color_scheme`/`Color Scheme`, `section_borders`/`Section borders`, `section_id`/`Section ID`, `alignment`/`Alignment` for the others):

```json
{
    "key": "field_altrd8_wp_starter_{slug_underscored}_spacing",
    "label": "Spacing",
    "name": "spacing",
    "type": "clone",
    "instructions": "",
    "required": 0,
    "conditional_logic": 0,
    "wrapper": { "width": "", "class": "", "id": "" },
    "clone": [ "group_altrd8_wp_starter_spacing" ],
    "display": "group",
    "layout": "block",
    "prefix_label": 0,
    "prefix_name": 1
}
```

`prefix_name: 1` is required on every Settings clone — without it, cloned sub-fields share the same field key across every block on a page and values reset in the editor (an ACF clone-field limitation). The clone's `name` (`spacing`, `color_scheme`, `section_borders`, `section_id`, `alignment`) is what `BlockSettings::get_cloned_setting()` reads via `get_field($name)`.

#### 6c) Shared clone group JSON (create only the ones this block needs, reused by every later block)

`acf-json/group_altrd8_wp_starter_spacing.json` — ported from Villa Argentina's `group_villa_argentina_spacing.json`:

```json
{
    "key": "group_altrd8_wp_starter_spacing",
    "title": "Component - Spacing",
    "fields": [
        {
            "key": "field_altrd8_wp_starter_sw_padding_top",
            "label": "Padding Top",
            "name": "padding_top",
            "type": "button_group",
            "instructions": "",
            "required": 0,
            "conditional_logic": 0,
            "wrapper": { "width": "", "class": "", "id": "" },
            "choices": { "xl": "XL", "large": "Large", "medium": "Medium", "small": "Small", "none": "None" },
            "default_value": "medium",
            "return_format": "value",
            "allow_null": 0,
            "layout": "horizontal"
        },
        {
            "key": "field_altrd8_wp_starter_sw_padding_bottom",
            "label": "Padding Bottom",
            "name": "padding_bottom",
            "type": "button_group",
            "instructions": "",
            "required": 0,
            "conditional_logic": 0,
            "wrapper": { "width": "", "class": "", "id": "" },
            "choices": { "xl": "XL", "large": "Large", "medium": "Medium", "small": "Small", "none": "None" },
            "default_value": "medium",
            "return_format": "value",
            "allow_null": 0,
            "layout": "horizontal"
        }
    ],
    "location": [ [ { "param": "post_type", "operator": "==", "value": "post" } ] ],
    "menu_order": 0,
    "position": "normal",
    "style": "default",
    "label_placement": "top",
    "instruction_placement": "label",
    "hide_on_screen": "",
    "active": false,
    "description": "Reusable spacing settings. Cloned into block field groups; not assigned to any post type directly.",
    "show_in_rest": 0,
    "modified": 1700000000
}
```

`"active": false` on every shared clone group — they're cloned only, never assigned directly to a post type. Set `"modified"` to `date +%s` when actually creating the file.

The other four shared groups follow the same shape, ported from their Villa Argentina counterparts:

- `group_altrd8_wp_starter_color_scheme.json` — one `button_group` field `color_scheme`, choices `light`/`off-light`/`dark`/`off-dark`, default `light` (source: `group_villa_argentina_color_scheme.json`)
- `group_altrd8_wp_starter_section_borders.json` — two `true_false` fields `border_top`/`border_bottom`, `"ui": 1`, default `0` (source: `group_villa_argentina_section_borders.json`)
- `group_altrd8_wp_starter_section_id.json` — one `text` field `section_id`, `"prepend": "#"`, instructions noting the editor is responsible for page-wide uniqueness (source: `group_villa_argentina_section_id.json`)
- `group_altrd8_wp_starter_alignment.json` — one `button_group` field `alignment`, choices `left`/`right`, default `right` (source: `group_villa_argentina_alignment.json`)

If the Villa Argentina project checkout is available locally (commonly `~/work/bwp_projects/villa-argentina-web-2026/themes/villa-argentina-web-20226/`), read the exact source file at `acf-json/group_villa_argentina_{name}.json` there before creating one of these, to copy field choices/defaults exactly — only renaming the `group`/`field` key prefix from `villa_argentina` to `altrd8_wp_starter` and dropping any `wpml_cf_preferences` keys (see "First-block infrastructure" above). If that checkout isn't available, the field shapes summarized above (choices, defaults, field types) are sufficient to recreate them.

### 7) Register the block

Add `'{slug}'` to `App\blocks\Blocks::get_blocks()`.

## Steps — Flexible-content module (only if step 0 determined this is a module)

### 1–2) Same analysis/inspection as above, but read `app/modules/TestModule.php`, `app/modules/Modules.php`, `partials/modules/test.php`, and `app/config/ACFConfig.php` instead.

### 3) Create the module controller class

Create `app/modules/{PascalCase}.php`:

```php
<?php

namespace App\modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use App\interfaces\ControllerInterface;

class {PascalCase} implements ControllerInterface {
	private bool $show;
	// ...typed properties for each ACF field...

	public function __construct( array $module ) {
		$this->show = ! empty( $module['show'] );
		// ...hydrate properties from $module, matching ACF flexible-content sub-field names...
	}

	public function get_view(): string {
		if ( empty( $this->show ) ) {
			return '';
		}

		return get_partial( 'modules/{slug}', array(
			// ...pass through to the partial...
		), true );
	}
}
```

`Modules::get_module()` resolves `acf_fc_layout` (the flexible-content layout name, snake_case) to this class name — the layout name in ACF JSON **must** match `str_replace('_', '', ucwords($layout_name, '_'))` producing this class name.

### 4) Create the partial

Create `partials/modules/{slug}.php` — same conventions as block partials. BEM class naming: `.c-{slug}-module`.

### 5) Create the ACF field group JSON

Create `acf-json/group_altrd8_wp_starter_module_{slug}.json`.

- `title`: `Module - {Human Readable Title}`
- Location: assigned as a layout inside the parent block's flexible-content field (check that field's `key` in the parent block's ACF JSON before wiring the layout in)
- Set `"modified"` to the current Unix timestamp (`date +%s`)

### 6) Register the module

Add the slug to `App\config\ACFConfig::get_modules()`.

## Optional: SCSS / JS

- SCSS: `static/scss/components/modules/_components.{slug}-module.scss` (or `components/blocks/...` for a block), forwarded from the matching `_components.index.scss`. Use design tokens from `static/scss/settings/`, the `fluidValue`/`mq` mixins — never hardcode spacing/color. See `.claude/rules/code-style.md`.
- JS: `static/js/components/common/{PascalCase}.js` following the `TemplateComponent.js` pattern, imported and `init()`'d in `static/js/index.js`. See `.claude/rules/frontend-animations.md` for what's already available (no animation library — build with native CSS/IntersectionObserver if needed).

## Validate

- `vendor/bin/phpmd app text phpmd.xml` (if installed) for new PHP
- `npm run lint:scss` for new SCSS; `npx eslint static/js` for new JS
- `npm run dev` should already be running locally for a live rebuild — don't run `npm run build` unless verifying a production bundle

## Output summary

- Block or module, slug, and title
- Files created (controller class, partial, ACF JSON, registration entry)
- Any first-block infrastructure created (`BlockSettings.php`, and which shared Settings-tab clone groups)
- ACF fields created, including which Settings-tab clones this block uses
- Whether SCSS/JS scaffolding was added
- Any assumptions made from ambiguous input
