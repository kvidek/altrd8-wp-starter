# Create ACF Block/Module Backend From Existing Partial

Wire up the **backend** (controller class, ACF fields, registration) for a partial that already exists under `partials/modules/` or `partials/blocks/` but has no PHP controller class or ACF field group behind it yet. This is the second half of the pipeline described in `.claude/commands/create-block.md` — use it when the markup was built first (e.g. from a static comp/slice) and the ACF wiring was left for later.

## Input

$ARGUMENTS

Accepts one or more partial slugs (e.g. `article-cards team`), or `all`/empty to audit every partial under `partials/modules/` and `partials/blocks/`.

## Goals

- **Every module/block partial should end up with a matching controller class, ACF JSON field group, and registration entry.** Treat these as three independently-checkable artifacts — a partial can have a controller class and a `register.php`-equivalent entry but still be missing its ACF JSON (e.g. because a field group was only ever created through the wp-admin field group editor, which writes to the database, and never exported to `acf-json/`).
- Treat the existing partial as the **source of truth** for what data it needs — never edit its markup, its SCSS, or its JS while doing backend wiring.
- Reverse-engineer the variables the partial reads (from its doc-comment header / `@var` block, and from what it echoes) into an ACF field contract.
- Be gap-aware and idempotent: if some backend artifacts already exist for a slug, only create what's missing and flag drift (a controller property with no matching partial var, or vice versa) rather than silently overwriting.
- Never modify `partials/**`, `static/scss/**`, or `static/js/**`.

## Project context

Same conventions as `create-block.md` — read that file first (module vs. block distinction, namespace/PSR-4, `acf-json/` naming, `ACFConfig`/`Blocks` registration points).

## Steps

### 1) Resolve target partials

- List `partials/modules/*.php` and `partials/blocks/*.php`.
- List `app/modules/*.php` and `app/blocks/*.php`.
- List `acf-json/*.json`.
- For each target partial, check the three artifacts independently:
  1. A matching controller class (`App\modules\{PascalCase}` implementing `ControllerInterface`, or `App\blocks\{PascalCase}` implementing `BlockInterface`)
  2. A registration entry (`ACFConfig::get_modules()` for modules, `Blocks::get_blocks()` for blocks)
  3. A matching `acf-json/group_altrd8_wp_starter_{module|block}_{slug}.json` file that **exists on disk right now** (an actual directory listing check, not "I created it earlier in this run")
- If `$ARGUMENTS` names specific slugs, use those (error if the partial doesn't exist under `partials/modules/` or `partials/blocks/`). If `all`/empty, audit every partial and target any with at least one gap.
- Report the resolved target list and, per partial, which of the three artifacts are missing before doing anything else.

### 2) Read the partial as a contract, not as markup to copy

For each target, read the full partial file:

- Its leading doc-comment block (lists expected vars, e.g. `- $title: {String}`) and the `@var` block below it — these are the field contract
- Any nested `get_partial()` calls it makes (e.g. to a card component) — read that partial too, since repeater sub-fields come from there
- Infer ACF field types from usage:
  - a plain string echoed via `esc_html()` → `text` (or `textarea` if usage suggests multi-line copy)
  - output via raw `echo`/no escaping (trusted WYSIWYG) → `wysiwyg`
  - an array looped with `foreach` → `repeater`, sub-fields derived from what each row's keys are used for
  - a var shaped like `['url' => ..., 'title' => ..., 'target' => ...]` → `link` (ACF return format `array`)
  - an image URL/ID pair → `image` field (return format matching how the partial consumes it — check whether it expects a raw URL, in which case the controller resolves it via `get_responsive_image()`/`ImageHelper` before passing it down, per `.claude/rules/responsive-image.md`)
  - a boolean gate (e.g. `if (empty($show))`) → `true_false`
- List the derived field contract back to the user before generating files, flagging ambiguous inferences.

### 3) Create the controller class

Per `create-block.md` steps 3 (module) or 3 (block): `App\modules\{PascalCase}` implementing `ControllerInterface` with a constructor that hydrates typed properties from the ACF-shaped `$module` array, and `get_view(): string` that calls `get_partial('modules/{slug}', [...], true)` — or the block equivalent implementing `BlockInterface`.

### 4) Create the ACF field group JSON

Per `create-block.md`'s ACF JSON steps — `acf-json/group_altrd8_wp_starter_{module|block}_{slug}.json`, `"modified"` set via `date +%s`.

### 5) Verify the write actually persisted

Don't trust a "file created successfully" result on its own. Immediately after writing each `acf-json/*.json` file (and again at the end of the run), re-check the directory listing (`ls acf-json/` or `git status --short acf-json/`) to confirm the file is still there and is valid JSON (e.g. `php -r "json_decode(file_get_contents('...'), flags: JSON_THROW_ON_ERROR);"`).

**Confirmed root cause of files vanishing in this project:** ACF Extended's `ACFE_AutoSync_Json::pre_update_field_group()` (`wp-content/plugins/acf-extended/includes/modules/autosync-json.php`) hooks `acf/update_field_group` — firing on any wp-admin save of that field group, including clicking "Sync" to import a JSON-only group into the DB — and calls `unlink()` on the JSON file whenever that group's top-level `"acfe": {"autosync": ["json"]}` key is missing. This is why every `acf-json/*.json` file in this project **must** set that key (see `create-block.md`'s Project context section) — it isn't optional. If a file you just wrote is missing or reverted when re-checked, first check whether it's missing `acfe.autosync` and add it; re-write, re-verify, and if it disappears again with autosync already set, stop and tell the user explicitly rather than silently retrying.

### 6) Register

Add the slug to `App\config\ACFConfig::get_modules()` (module) or `App\blocks\Blocks::get_blocks()` (block).

### 7) Gap-check before writing

Before creating each artifact, check whether it already exists. If it exists and matches the derived contract, leave it alone and report "already present." If it exists but conflicts (e.g. a controller property with no matching field, or vice versa), stop and report the discrepancy instead of overwriting — ask the user how to reconcile.

### 8) Validate

- `vendor/bin/phpmd app text phpmd.xml` (if installed) on new/changed PHP
- Confirm every new/changed `acf-json/*.json` file still exists on disk and is valid JSON (per step 5), as the very last check
- Don't run `npm run build` or touch `static/js`/`static/scss` — no frontend assets change here

### 9) Output summary

Per partial, report:

- Derived field contract (var → ACF field type → required?), with assumptions called out
- Files created vs. already present (untouched) vs. conflicts flagged for user decision
- Confirmation that every `acf-json/*.json` file listed as "created" was re-verified present on disk at the end of the run
- The registration entry added
- Reminder that the partial itself, its SCSS, and its JS were not touched

## Important constraints

- Never edit `partials/**`, `static/scss/**`, or `static/js/**`.
- Never invent content fields that aren't actually read by the partial — the partial is the contract, not a guess from the slug name.
- Follow `.claude/rules/code-style.md` and `.claude/rules/wordpress.md`.
