# Getting started

## Requirements

| Tool | Version | Notes |
|------|---------|-------|
| PHP | 8.0+ | code uses union types and `str_ends_with` |
| Composer | 2 | needs GitHub access to the private `bornfight/wp-helpers-bf` package |
| Node / npm | node >= 25.9, npm >= 11.13 | `nvm use` reads `.nvmrc` |
| WordPress | recent | plus ACF PRO and ACF Extended installed and activated |
| Database | MariaDB 10.5+ / MySQL | |

## New project vs. working on an existing one

- **Starting a new project from this starter:** see [Starter template](starter.md). In short: create an empty GitHub repo, then tell Claude "new project" (the `new-wp-project` skill), or clone the starter and run `node bin/init.mjs <slug> --repo <git-url> --install`.
- **Working on an existing project:** clone it into `wp-content/themes/<slug>` of a local WordPress install and follow the steps below.

The scaffolding covers the theme only. WordPress itself, the database and plugins are set up by hand.

## Local setup

1. **WordPress:** download and install WordPress locally, with the site reachable on a local domain (this repo assumes `http://www.private.loc/<slug>/`).
2. **Plugins:** install and activate ACF PRO and ACF Extended, plus [BF Advanced Images](https://wordpress.org/plugins/bf-advanced-images/) (the theme registers its image sizes through it when present, and the REST/image code lists it as a dependency). Add Contact Form 7 / WPML only if the project needs them.
3. **Theme:** the repo root *is* the theme, so it lives at `wp-content/themes/<slug>`.
4. **PHP dependencies** (inside the theme folder):
   ```bash
   composer install
   ```
   Without `vendor/autoload.php` the theme shows an admin notice ("Missing vendor/autoloader.php") and does nothing else. If autoloading breaks after adding or renaming classes, run `composer dump-autoload`.
5. **JS dependencies and first build:**
   ```bash
   npm install
   npm run build
   ```
6. **Activate the theme** in *Appearance → Themes*.
7. **Menus:** assign menus to the two registered locations, `header-menu` and `footer-menu` (`App\config\MenuConfig`).
8. **ACF field groups:** open *Custom Fields* in wp-admin; groups from `acf-json/` should appear as "Sync available" if they are not already loaded. Sync them (see [ACF conventions](acf-conventions.md)).
9. **Start developing:**
   ```bash
   npm run dev
   ```

## Scripts (`package.json`)

| Script | What it does |
|--------|--------------|
| `npm run dev` | webpack in development mode with watch; BrowserSync proxies your local site and reloads on JS/CSS/PHP changes |
| `npm run build` | Prettier over `static/**/*.{scss,js}`, then a production webpack build (content-hashed files, `console.log` stripped) |
| `npm run format` | Prettier over `static/**/*.{scss,js}` |
| `npm run lint:scss` | Stylelint over `static/**/*.scss` |

There are no scripts for ESLint or PHPMD yet; run them directly:

```bash
npx eslint static/js
vendor/bin/phpmd app text phpmd.xml   # add phpmd/phpmd as a dev dependency first if missing
```

`npm run dev` is normally left running; you don't need `npm run build` until you want to verify a production bundle or deploy.

## Local URL configuration

Two files hold host-specific values and usually need adjusting per machine:

- **`webpack.config.mjs`**: `const proxy = "http://www.private.loc/<slug>/"` is the site BrowserSync proxies. Change it if your local domain differs.
- **`wp-cli.yml`**: `url`, `user` and especially `path` (`../../../../bwp/wp`, a Bornfight folder layout). Point `path` at your WordPress root, or run `wp` from the WordPress root with `--path`/without this file.

## Troubleshooting

| Symptom | Cause / fix |
|---------|-------------|
| Admin notice "Missing vendor/autoloader.php" | run `composer install` in the theme folder |
| Site loads but has no CSS/JS | `static/dist/` is empty. Run `npm run build` (or leave `npm run dev` running) |
| Blocks are missing from the editor | ACF PRO not active, or the field groups are not synced yet |
| Block fields don't show on a block | the group's JSON isn't synced, or its location rule doesn't match the block name (`acf/<slug>`) |
| `wp` says "not a WordPress installation" | see `wp-cli.yml` `path` above |
| `npm install` reports "`.git can't be found`" | the `prepare` script runs `husky` two levels up (`wp-content/`). That folder isn't a git repo here, so husky installs nothing and no pre-commit hook exists. Harmless; format manually with `npm run format` |
| Wrong node version errors | `nvm use` (see `.nvmrc`) |

## Things that are *not* in the theme

- The **WordPress core install**, `wp-config.php` and the database. The theme repo contains only the theme.
- **Plugins.** They are installed per project.
- **`static/dist/`** and `vendor/` are git-ignored build/dependency output (see [Deployment](deployment.md) for how they reach a server).
