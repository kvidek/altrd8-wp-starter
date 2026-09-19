# Code Style Rules

Theme lives at `wp-content/themes/altrd8-wp-starter`.

## PHP

- **Namespace:** `App\` for all theme code, PSR-4 autoloaded from `app/` (see `composer.json`)
- **Naming:** classes `CamelCase` (autoloader requirement), functions and variables `snake_case`, folders `camelCase` (`postTypes`, `adminMenus`) matching their namespace segment
- **OOP only:** every piece of logic belongs in a class under `app/`; avoid adding functions to `functions.php` or `app/global-theme-functions.php` beyond the small set of global helpers already there (`get_partial`, `bu`, `au`, `get_icon`, `get_responsive_image`, ...) — wrap new reusable logic in a class instead
- Partials (`partials/**/*.php`) stay presentational: read from the `$data`/extracted vars, echo markup, no business logic
- **Lint:** `phpmd.xml` defines the project's PHPMD ruleset (Bornfight preset). There is no Composer `lint` script configured — run PHPMD directly against `app/`, e.g. `vendor/bin/phpmd app text phpmd.xml` (install `phpmd/phpmd` as a dev dependency first if `vendor/bin/phpmd` isn't present)
- Follow WordPress coding standards for everything not covered by the class-naming exception above (see `phpmd.xml`'s excluded `Camel*` rules — those exist specifically because class names must be CamelCase for the autoloader)

## CSS / SCSS

- **Preprocessor:** SCSS via `sass`/`sass-loader` (webpack). No PostCSS pipeline, no Tailwind, no Bootstrap
- **Architecture:** ITCSS-derived, based on Bornfight's `b-creative` conventions (itself based on `inuitcss`) — `tools/ → settings/ → generics/ → elements/ → objects/ → components/ → utilities/ → vendors/` under `static/scss/`, entry point `static/scss/style.scss`
- **Methodology:** BEM — `.c-component-name {}` / `&__element` / `&--modifier`; objects `.o-*`, utilities `.u-*`, page-specific `.p-*`, JS hooks `.js-*` (never styled). Enforced by `stylelint-config-standard-scss` + `@namics/stylelint-bem` (`patternPrefixes: c, o, u` in `.stylelintrc.json`)
- **File naming:** `_{layer}.{name}.scss`, e.g. `_components.button.scss`, `_objects.header.scss`, `_utilities.typography.scss`; module-style components get a `-module` suffix, e.g. `_components.hero-module.scss`. Every subdirectory has its own `{layer}.index.scss` forwarding/importing its files (e.g. `components/_components.index.scss`) — never bloat `style.scss` with per-file imports
- **Responsiveness:** use the `fluidValue` mixin for spacing/sizing (`@include fluidValue("margin-bottom", "s-32");` from the spacing map, or `@include fluidValue("border-width", null, 1px);` for a custom px value converted to fluid `vw`), and `@include mq(<bp>)` for breakpoint overrides. Font sizes come from the typography settings map and scale automatically — don't hand-roll `clamp()`/`vw` for either
- **Design tokens:** use variables from `static/scss/settings/` (colors, spacings, typography) — never hardcode raw hex/px values in components
- **Colors:** hex is disallowed by Stylelint (`color-no-hex: true`) — use named/variable colors
- **`@include`/`@extend` ordering:** put `@include`/`@extend` at the top of a selector block, separated from plain declarations by a blank line; `@include mq(...)` and any hover mixin go at the bottom of the block instead (see `.claude/rules` example in project README-style docs / existing components for the pattern)
- **`!important`:** disallowed (`declaration-no-important: true`)
- **Lint:** `npm run lint:scss` (Stylelint); **format:** `npm run format` (Prettier over `static/**/*.{scss,js}`, also runs as a Husky pre-commit hook via `prepare`)

## JavaScript

- **Style:** ES modules, hand-rolled ES6 classes — no framework (no jQuery, no Alpine, no React)
- **Structure:** `static/js/` with `components/{animations,common,inputs,modals,sliders}/`, `helpers/`, `utilities/`; entry point `static/js/index.js`, built by webpack (`webpack.config.mjs`) into `static/dist/`
- **Component pattern:** ES6 class with `constructor(container = document)`, a `this.DOM` map of `.js-*` selectors (plus a nested `states` map for state classes like `is-active`), and an `init()` that early-returns when its required elements are missing — copy `static/js/components/common/TemplateComponent.js` as the starting point for a new component
- **Registration:** import and instantiate + `init()` inside the `ready()` callback in `static/js/index.js`
- Only `.js-*` classes are used for JS query selectors — never select on `.c-*`/`.o-*` styling classes
- No scroll/reveal/parallax animation library is wired up — see [frontend-animations.md](frontend-animations.md)
- Vendors currently in use: `instant.page` (link preloading), `vanilla-lazyload` (via `components/common/Lazy.js`, hooked on `.js-lazy-load`), `tua-body-scroll-lock` (modals — see `components/modals/Modal.js`)
- Console logs are stripped in production builds (`pure_funcs: ["console.log"]` in `webpack.config.mjs`), so feel free to leave them for debugging
- **Lint:** ESLint is configured (`eslint.config.mjs`, `eslint:recommended` + Prettier) but has no `package.json` script yet — run directly with `npx eslint static/js`; **format:** `npm run format`

## Build

- **Bundler:** webpack (`webpack.config.mjs`), **not** Vite. `npm run dev` watches + runs `browser-sync` against the local proxy defined in `webpack.config.mjs`; `npm run build` runs Prettier then a production webpack build
- Output goes to `static/dist/` (gitignored except `.gitkeep`) — never commit built files
- During local development, `npm run dev` is generally already running — don't run `npm run build` unless you specifically need to verify a production bundle

## Git & Commits

- Commit messages and PR descriptions in **English**
- Imperative mood, <= 72 char subject (e.g. `Add gallery module`)
- Never commit `static/dist/`, `vendor/` (except `.gitkeep`), `node_modules/`, `.env`, or `wp-config.php`
