# Front-end

Sources are in `static/`, built by webpack into `static/dist/` (git-ignored, never committed). The conventions below are short; the root `README.md` has the long-form HTML/SCSS/JS style guide, and `static/scss/README.md` documents the SCSS layers.

## Build pipeline

`webpack.config.mjs` defines three entries:

| Entry | Source | Output | Loaded |
|-------|--------|--------|--------|
| `bundle` | `static/js/index.js` | `bundle.js` (+ `vendor.js` chunk) | front end |
| `style` | `static/scss/style.scss` | `style.css` | front end |
| `editor` | `static/scss/editor.scss` | `editor.css` | block editor only |

- **Development** (`npm run dev`): stable file names, source maps, watch mode, BrowserSync proxy (see [Getting started](getting-started.md#local-url-configuration)).
- **Production** (`npm run build`): `[name].[contenthash]` file names, Terser (with `console.log` stripped), and a `manifest.json` mapping names to hashed files. The build also runs Prettier over `static/**/*.{scss,js}` first.
- `static/dist/` keeps `.gitkeep` and `vendor.js` across cleans.
- Heavy or optional libraries (a slider, a date picker) should be loaded with a dynamic `import()` behind a `document.querySelector('.js-...')` check, not bundled into the main entry.

### Asset loading

`App\bundles\<Project>Assets` reads `static/dist/manifest.json` and enqueues each hashed file (handles are `bwp-<name>`). If the manifest is missing it falls back to `dist/bundle.js`, `dist/vendor.js` and `dist/style.css` with timestamp cache busting. The main bundle gets a `frontend_rest_object.rest_url` global pointing at the theme's REST namespace.

The **editor stylesheet** is never enqueued in wp-admin. Its CSS text is inlined into the block editor's canvas iframe (via `block_editor_settings_all`), and relative `url()`s (fonts, images) are rewritten to absolute theme URLs. Otherwise the stylesheet, which resets fonts and spacing, would break core admin UI.

## SCSS

ITCSS layers under `static/scss/`, entry `style.scss`:

```
settings/  tools/  generics/  elements/  objects/  components/  utilities/  vendors/
```

- **Files:** `_{layer}.{name}.scss` (for example `_components.hero-module.scss`); every folder has an index file that forwards its files. Never add per-file imports to `style.scss`.
- **Naming:** BEM with prefixes: `.c-` component, `.o-` object, `.u-` utility, `.p-` page-specific, `.js-` JS hook (never styled). Stylelint enforces `c`, `o`, `u`.
- **Tokens:** colors, spacing and typography come from `settings/`. No raw hex (Stylelint `color-no-hex`), no hard-coded px in components, no `!important`.
- **Responsive sizing:** use the `fluidValue` mixin instead of hand-written `clamp()`/`vw`:
  ```scss
  @include fluidValue("margin-bottom", "s-32");        // from the spacing map
  @include fluidValue("border-width", null, 1px);      // custom px converted to fluid vw
  ```
  Font sizes come from the typography map and scale on their own. Breakpoint overrides use `@include mq(<bp>)`.
- **Breakpoints:** `sm` 480, `sm-md` 640, `md` 800, `md-lg` 960, `lg` 1140, `lg-xl` 1280, `xl` 1440, `xxl` 1920, `xxxl` 3840 (`settings/_settings.breakpoint.scss`).
- **Ordering inside a block:** `@include` / `@extend` first, blank line, plain declarations, then `@include mq(...)` and hover mixins at the bottom.
- **Utilities the blocks rely on:** `u-color-scheme-*`, `u-pt-*` / `u-pb-*` (spacing), `u-border-top/bottom`, `u-content-editor`, `u-a2` / `u-b0` typography classes.
- **Vendors:** styles for third-party pieces (`swiper`, `air-datepicker`, `slim-select`, CF7, WPML, WordPress) live in `vendors/`.
- **Brand:** the color palette in `settings/_settings.color.scss` and `utilities/_utilities.color-scheme.scss` is inherited from the project this starter grew out of. Replace it for each project.

Lint and format: `npm run lint:scss`, `npm run format`.

## JavaScript

ES modules and hand-rolled ES6 classes. No framework, no jQuery.

- **Layout:** `static/js/components/{animations,common,inputs,modals,sliders}/`, plus `helpers/` and `utilities/`. Entry: `static/js/index.js`.
- **Component pattern:** a class with `constructor(container = document)`, a `this.DOM` map of `.js-*` selectors (with a nested `states` map for classes like `is-active`), and an `init()` that returns early when its elements are absent. Copy `components/common/TemplateComponent.js`.
- **Registration:** import the class in `index.js`, instantiate it and call `init()` inside the `ready()` callback.
- **Selectors:** only `.js-*` classes; never select on `.c-*` / `.o-*`.
- **Included components:** `Lazy` (vanilla-lazyload on `.js-lazy-load`), `ResponsiveVideo`, `VideoOnScroll`, `VideoPlayButton`, `ScrollToAnimation` (native `scrollIntoView`; pair with `scroll-margin-top` CSS), `AccordionAnimation`, `Navigation`, `Modal` (uses `tua-body-scroll-lock`; also drives the mobile navigation and CTA "open modal" actions), input components under `inputs/`, and a `Slider` scaffold. `instant.page` preloads links on hover.
- **Console:** `console.log` is stripped from production builds, so leaving debug logs is fine.
- **Lint:** `npx eslint static/js`; **format:** `npm run format`.

### Animation and motion

No scroll-driven animation library (GSAP, Luge, AOS) is used or should be added without discussion; the performance budget assumes plain CSS plus small classes. For simple state changes use CSS transitions.
Any new motion must respect reduced motion: check `window.matchMedia('(prefers-reduced-motion: reduce)').matches` in JS, or wrap the animated CSS in `@media (prefers-reduced-motion: no-preference)` and fall back to instant visibility. Scroll reveals, if needed, are a small class built on `IntersectionObserver`.

## Responsive images and video

Use the two global helpers instead of writing `<picture>` by hand. They output a `.c-responsive-media` figure with per-breakpoint `<source>` tags, lazy loading and a loader overlay.

**By attachment ID and size names** (sizes come from `App\config\Config::get_image_sizes()`: `image_200`, `image_480`, `image_600`, `image_700`, `image_800`, `image_900`, `image_1200`, `image_1440`, `image_1920`, `image_2880`; intermediate sizes are generated on demand, no resize cache folder):

```php
echo get_responsive_image( array(
    'image'        => $image_id,
    'sizes'        => array(
        'desktop'        => 'image_1200',
        'desktop_retina' => 'image_1440',
        'tablet'         => 'image_800',
        'mobile'         => 'image_600',
    ),
    'aspect_ratio' => '16-9',
) );
```

**By URLs:** pass `'urls' => array( 'desktop' => $url, ... )` and an `alt`.

| Arg | Default | Notes |
|-----|---------|-------|
| `aspect_ratio` | `1-1` | `1-1`, `2-1`, `1-2`, `3-1`, `1-3`, `3-2`, `2-3`, `4-3`, `3-4`, `16-9`, `9-16`, `auto` (needs `width`/`height`), `adopt` |
| `object_fit` / `object_position` | `cover` / `center` | |
| `lazy` | `true` | JS lazy-loading. Turn off for above-the-fold images |
| `native_lazy` | `false` | native `loading="lazy"` instead |
| `priority` | `false` | `fetchpriority="high"` for the hero. Disable both lazy options with it |
| `is_background` | `false` | with `aspect_ratio: 'adopt'`, fills the nearest `position: relative` parent |
| `alt` | attachment alt | auto-filled from the attachment in the ID form |

Only `desktop` (+ `desktop_retina`) is required; other breakpoints fall back to it. A breakpoint can also be `array( $other_id, 'image_800' )` for an art-directed crop. GIFs are never resized; SVGs skip `<source>` tags. `get_responsive_video()` takes the same kind of arguments; feed it `VideoHelper::normalize()` output. Full reference: `.claude/rules/responsive-image.md`.

## Icons and static assets

- `get_icon('arrow')` renders the SVG partial `static/icons/icon-arrow`. The starter ships no icons; add SVGs there (root README has the SVG preparation rules and how to recolor and resize).
- `bu('images/foo.jpg')` gives a URL under `static/`; `au(...)` the filesystem path.
- Fonts live in `static/fonts/`; favicons in `static/ui/`.
