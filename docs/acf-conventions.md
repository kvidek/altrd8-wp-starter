# ACF conventions

Field definitions live as JSON in `acf-json/` and are committed to git. ACF PRO reads them; ACF Extended keeps them in sync.

## Naming

**Field group titles** describe what the group belongs to:

| Group for | Title pattern | Example |
|-----------|---------------|---------|
| Options page | `Options - {name}` | Options - Theme |
| CPT archive | `Template - {CPT}` | Template - News |
| CPT single | `Template - {CPT} Single` | Template - News Single |
| Page template | `Template - {template}` | Template - Home Page |
| Taxonomy | `Taxonomy - {name}` | Taxonomy - Product Category |
| Gutenberg block | `Block - {name}` | Block - Intro Block |
| Flexible module | `Module - {name}` | Module - Big Image |
| Shared component | `Component - {name}` | Component - CTA |

**Keys and files** carry the project's snake-case prefix (the theme slug with hyphens turned into underscores; `altrd8_wp_starter` in the starter itself, `cool_project_2026` in a project called `cool-project-2026`):

| Thing | Pattern |
|-------|---------|
| Group key | `group_<prefix>_block_<slug_underscored>` (also `_module_`, `_component_`, or a bare name for shared settings) |
| Field key | `field_<prefix>_<slug_underscored>_<field>` |
| File | `acf-json/<group key>.json` |
| Block location rule | `block == acf/<slug>` |

Never hand-edit a key after content exists; keys are how stored values find their fields. The `bin/init.mjs` script rewrites the prefix consistently when a project is created (see [Starter template](starter.md)).

## Required settings in every group

- **`"acfe": { "autosync": ["json"] }`** at the top level, on every group (blocks and shared clone groups alike), from the moment the file is created. Without it, ACF Extended **deletes the JSON file from disk** the next time the group is saved in wp-admin, including when you click "Sync" to import a JSON-only group. This has silently removed groups in this project before.
- **`modified`** updated when you change the file (a Unix timestamp, `date +%s`); ACF uses it to detect groups that need syncing.
- **Two top-level tabs on every block group**, in this order: `Content` (all editable content) and `Settings` (presentation). The Settings tab starts with a cloned Spacing group.

## Shared clone groups

Reusable groups that are cloned into block groups and are not assigned to any post type:

| Group | Purpose |
|-------|---------|
| `spacing` | top/bottom padding (every block) |
| `color_scheme` | section color scheme |
| `section_borders` | top/bottom border toggles |
| `section_id` | anchor id for scroll-to / deep links |
| `alignment` | left/right media alignment |
| `component_cta` | CTA link + button style + action settings |
| `component_video` | video sources and poster |
| `component_bg_gradient` | background gradient blob |
| `menu_item_spacer` | a field on nav menu items (used by the footer menu) |

Clone conventions:
- Clone fields on the Settings tab use `"display": "group"` and **`"prefix_name": 1`**. Without the prefix, cloned sub-fields share names across blocks on the same page and values reset in the editor.
- A repeater of CTAs uses a *seamless* clone of `component_cta` as its single sub-field.
- The `BlockSettings` trait reads the cloned groups by their field name (`spacing`, `color_scheme`, `section_borders`, `section_id`, `alignment`, `bg_gradient`), so keep those field names.

## Working with the JSON

- **Edit in wp-admin or in the JSON.** With ACFE auto-sync, saving in wp-admin rewrites the JSON file. Commit the resulting diff.
- **After pulling changes** that touch `acf-json/`, open *Custom Fields*: groups with a "Sync available" badge need syncing (or use bulk actions).
- **Don't mix** formats or key prefixes. If a group refers to another (clone, conditional logic, `collapsed`), the referenced key must exist in `acf-json/`.
- The init script validates that every `group_/field_` key that the theme references is defined, so a rename mistake is caught at scaffold time.

## Fields in PHP

- Read block fields with `get_field('name')` inside the block class (ACF gives the block context during `render_callback`).
- Read options-page fields with `get_field('name', 'option')`.
- Normalize and default values in the class or a helper, never in the partial.
- ACF Extended features that are enabled here are the UI/field-type enhancements (for example `acfe_group_modal_*` so a group's settings open in a modal). The dynamic post type / taxonomy / block registration and multi-language modules are turned off in `WPPlugins`; don't rely on them.
