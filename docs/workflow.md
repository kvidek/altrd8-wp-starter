# Workflow

## Branching and commits

- Don't work on `master` directly. Create `feature/<name>` or `fix/<name>` branches and merge through a pull request.
- Before pushing: `npm run format`, `npm run lint:scss`, and `npm run build` if you changed `static/`.
- Commit messages and PR descriptions are **English**, imperative, subject <= 72 characters (`Add gallery module`).
- Never commit `static/dist/`, `vendor/`, `node_modules/`, `.env` or `wp-config.php` (all except `wp-config.php`, which is outside the theme, are git-ignored).
- The old README mentions a protected `master`, mandatory review and a `CODEOWNERS` file. That is the Bornfight team process; adopt it if the project has a team, and configure branch protection on the new GitHub repo yourself.

## Code style in short

| Area | Rule |
|------|------|
| PHP | logic in classes under `app/`; partials only present; WordPress coding standards, except `CamelCase` class names (autoloader); `snake_case` functions/variables |
| PHP lint | `vendor/bin/phpmd app text phpmd.xml` (Bornfight ruleset in `phpmd.xml`) |
| SCSS | ITCSS + BEM, tokens only, `fluidValue` for sizing, no hex / `!important`; `npm run lint:scss` |
| JS | ES6 classes, `.js-*` selectors only, registered in `index.js`; `npx eslint static/js` |
| Formatting | Prettier for `static/**/*.{scss,js}` via `npm run format` |
| Security | escape on output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`), sanitize on input |

Details: [Architecture](architecture.md), [Front-end](frontend.md), [Blocks](blocks.md), `.claude/rules/code-style.md`.

**Pre-commit hook:** `package.json` has a `prepare` script that runs husky two levels above the theme (`wp-content/`). In the current layout, where the theme is its own repository, no hook gets installed. Format before committing yourself.

## Working with Claude Code in this repo

Project rules and commands are checked in under `.claude/`:

| File | Role |
|------|------|
| `.claude/rules/wordpress.md` | repo layout, bootstrap, blocks, partials, deploy |
| `.claude/rules/code-style.md` | PHP / SCSS / JS / build / git conventions |
| `.claude/rules/responsive-image.md` | `get_responsive_image()` / `get_responsive_video()` reference |
| `.claude/rules/frontend-animations.md` | motion, reduced motion, performance limits |
| `.claude/commands/create-block.md` | `/create-block`: scaffold a block or module end to end |
| `.claude/commands/create-block-backend.md` | `/create-block-backend`: PHP class + ACF JSON from an existing partial |
| `.claude/commands/check-other-project.md` | `/check-other-project`: inspect another local project folder |

The rules load automatically and steer Claude toward the conventions in these docs. If you change a convention, update the matching rule file too, so the two don't drift.
`.claude/settings.local.json` is personal: don't commit it. The theme's own `.gitignore` doesn't list it (it is only excluded by a global gitignore on the original author's machine), so add it to `.gitignore` if you work from another machine.

## Common tasks

| Task | How |
|------|-----|
| New block | `/create-block ...`, or see [Blocks](blocks.md#adding-a-block) |
| New CPT / taxonomy | copy `TestPostType` / `TestTaxonomy` into `app/postTypes/types/` or `taxonomies/` (auto-registered) |
| New REST route | copy `TestRoute` / `TestCallback` into `app/rest/routes/` and `app/rest/callback/`; anything in `routes/` is auto-registered |
| New WP-CLI command | add a class under `app/cli/commands/` with an `#[Attr('command', 'description')]` attribute; it is auto-registered (see `BwpCommand`: `wp bwp example`) |
| New page template | a file in `page-templates/` with `/** Template Name: ... */`; render `get_content()` |
| New JS component | copy `TemplateComponent.js`, register in `index.js` |
| New image size | add it to `Config::get_image_sizes()` |
| Enable a WP hardening toggle | uncomment it in `WPDefaults::init()` |
