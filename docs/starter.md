# Starter template

This repository is a **template**: new projects start from it and are renamed to the project's own name. The flow is theme-only. WordPress core, the database and plugins are set up by hand afterwards.

## Starting a new project

1. Create an **empty** repository on GitHub for the project (no README, no license, no .gitignore).
2. Ask Claude Code for a new project, or run `/new-wp-project`. The skill asks for:
   - the **full project name** (used exactly as given; it becomes the slug, e.g. `cool-project-2026`);
   - the **URL of the empty repository**.
3. The skill (defined in `~/.claude/skills/new-wp-project/SKILL.md`, outside this repo):
   - clones this starter into `wp-content/themes/<slug>` without history,
   - runs `node bin/init.mjs <slug> --repo <url> --install`,
   - checks that `npm run build` compiles,
   - makes the first commit on `master` and pushes to the empty repository.
4. Do the manual steps listed in its final report: install WordPress, create the database, install plugins, activate the theme, check the local URLs ([Getting started](getting-started.md)).

**Without Claude**, the same result by hand:

```bash
git clone --depth 1 git@github.com:kvidek/altrd8-wp-starter.git wp-content/themes/<slug>
cd wp-content/themes/<slug>
rm -rf .git
node bin/init.mjs <slug> --repo git@github.com:<you>/<slug>.git --install
npm run build
git init && git add -A && git commit -m "Initial commit from altrd8-wp-starter"
git branch -M master
git remote add origin git@github.com:<you>/<slug>.git
git push -u origin master
```

### What `bin/init.mjs` does

`node bin/init.mjs <slug> [--repo <git-url>] [--install] [--keep-script]`

- `<slug>` must be lowercase letters, digits and single hyphens, starting with a letter.
- Reads the **current** slug from the `Text Domain` header in `style.css`, so it also works on a project that was itself created from the starter.
- Replaces three casings everywhere in the theme (skipping `node_modules/`, `vendor/`, `static/dist/`, binary files):

  | Casing | Example | Used for |
  |--------|---------|----------|
  | kebab | `cool-project-2026` | theme slug, text domain, package name, local URLs, `deploy.php`, docs |
  | snake | `cool_project_2026` | ACF group/field keys, PHP constants |
  | Pascal | `CoolProject2026` | PHP class names, e.g. `CoolProject2026Assets` |

- Renames files whose names contain those (the ACF JSON groups, the assets class).
- Replaces the starter's repository URL (`deploy.php`, `README.md`) with `--repo`.
- Generates a new random update-checker `Identifier` and replaces it in `style.css` and in `app/core/Core.php`, so the project doesn't share the starter's update feed.
- Deletes starter-only files (`BLOCK_BACKLOG.md`).
- **Checks its own work:** PHP lint of every file, valid ACF JSON, every referenced `group_`/`field_` key exists, and no trace of the old name remains. On a problem it exits non-zero and keeps itself so it can be re-run.
- `--install` runs `composer install` and `npm install`.
- Removes itself when it succeeds (unless `--keep-script`).

### What you still adapt in a new project

The starter is a working site, not a blank slate. After scaffolding, review:

- **Brand:** the palette in `static/scss/settings/_settings.color.scss` and `utilities/_utilities.color-scheme.scss`, fonts in `static/fonts/`, favicons in `static/ui/`, `screenshot.png`.
- **Example blocks:** `intro-block` and `media-with-content-block` (and `test-block`) are inherited examples. Keep, restyle or delete them (class, partial, ACF JSON, `Blocks::get_blocks()` entry, SCSS).
- **Navigation and footer** partials and their SCSS.
- **`README.md`:** the `{figma_link}` placeholder and the project info.
- **Local hosts:** `webpack.config.mjs` proxy, `wp-cli.yml`.
- **`deploy.php`:** hosts, if not Bornfight's servers ([Deployment](deployment.md)).
- **`style.css`** `Author` / `Description` headers.

## Improving the starter

New projects clone the starter's `master`, so `master` must always be scaffoldable.

1. **Work on a branch** in the starter repository (its own WordPress install is a good dev environment). Merge to `master` through a pull request.
2. **Test a scaffold before merging.** Copy the theme (without `node_modules`, `vendor`, `.git`, `static/dist`) to a scratch folder, run `node bin/init.mjs test-project`, and build it. If the script's sanity checks pass and `npm run build` compiles, the starter is still scaffoldable.
3. **Keep it generic.** Project branding and content belong in projects. Generic building blocks belong here. If a feature is obviously generic, build it in the starter first and scaffold projects from it. That is cheaper than porting later.
4. **Keep `bin/init.mjs` in step** with anything project-specific that isn't derived from the slug. Slug casings and the update `Identifier` are rewritten and checked automatically, but any other hard-coded ID, URL or name (like the update-service ID before it was fixed) is silently inherited by every project unless you teach the script about it.

### Bringing a feature from a project back into the starter

Projects have no git ancestry in common with the starter (history is stripped and everything is renamed), so you can't merge or cherry-pick. Port by copying, using the rename script in reverse:

1. Copy the project theme (without `node_modules`, `vendor`, `.git`, `static/dist`) to a scratch folder, and copy the starter's `bin/init.mjs` into it as `bin/init.mjs`.
2. In the scratch copy run `node bin/init.mjs altrd8-wp-starter --keep-script`. This renames every slug, key prefix and class back to the starter's names.
3. Diff the scratch copy against the starter checkout and copy over only the files that belong to the feature, plus what it depends on (a helper, a shared ACF group, SCSS tokens). Ignore the expected noise: `style.css` and `app/core/Core.php` differ by the regenerated update-checker `Identifier`, and `BLOCK_BACKLOG.md` is gone.
4. On a starter branch, generalize anything project-specific (copy, colors, hard-coded content), then test a scaffold as above.

Keeping a record of the starter commit each project came from (for example a small `.starter.json` written by the init script) would make syncing in both directions easier. It is not implemented yet.

## Making the repository a GitHub template

Optional, and it adds a "Use this template" button; the skill clones the repository directly and doesn't need it:

```bash
gh repo edit kvidek/altrd8-wp-starter --template
```
