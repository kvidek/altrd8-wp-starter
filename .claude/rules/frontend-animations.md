# Frontend Animations & Interactions

## Library boundaries

There is **no scroll-driven animation library** in this project — no Luge, no GSAP, no AOS. Motion is handled by:

- **Native CSS transitions** for simple state changes (hover color, `is-active` toggles, accordions) — see `static/scss/components/` for existing transition patterns
- **Hand-rolled ES6 classes** (`static/js/components/`) for interaction/state: `Navigation.js`, `Modal.js` (uses `tua-body-scroll-lock`), `AccordionAnimation.js`, `ScrollToAnimation.js` (smooth in-page anchor scrolling via the native `scrollIntoView` API — see the doc comment at the top of that file for the `scroll-margin-top` companion CSS), input components under `components/inputs/`
- **Swiper** is not currently a dependency — if a slider is needed, `static/js/components/sliders/Slider.js` is a scaffold to build from; add `swiper` to `package.json` and dynamically `import()` it rather than bundling it into the main entry

Do not add GSAP, Luge, AOS, or any other animation library without discussing it first — the performance budget here assumes plain CSS + small hand-rolled classes.

## Scroll-based reveals

Not currently implemented anywhere in the theme. If a task needs on-scroll reveal/parallax behavior, build it as a small ES6 class following the existing component pattern (`TemplateComponent.js`), using the native `IntersectionObserver` API — there's no existing reveal utility to hook into, so check with the user before introducing one project-wide rather than scoping it to the one component that needs it.

## Reduced motion

There is no existing `prefers-reduced-motion` handling utility in this codebase (no equivalent of a `Motion.js`/`prefersReducedMotion()` helper). Any new motion feature (an `IntersectionObserver`-based reveal, a CSS transition-based animation, etc.) should check `window.matchMedia('(prefers-reduced-motion: reduce)').matches` (JS) or wrap the animated CSS in `@media (prefers-reduced-motion: no-preference) { ... }` and degrade to instant visibility otherwise. Since this is new ground for the project, keep the check local to the component you're adding rather than inventing a shared convention unprompted.

## Cookie consent

Not currently present in this theme (no cookie-consent plugin or in-theme consent UI referenced anywhere in `app/` or `static/`). If cookie consent is needed, confirm with the user how they want it handled before building anything — don't assume a specific plugin.

## Performance

- Main JS entry (`static/js/index.js`) bundles into `static/dist/` via webpack with `splitChunks` (vendor chunk separated from app code) and Terser minification (`console.log` calls stripped in production)
- Heavy/optional libraries (a slider, a date picker, etc.) should be dynamically `import()`-ed and gated behind a `document.querySelector('.js-...')` check rather than added to the main bundle unconditionally
- Lazy media via `vanilla-lazyload` (`components/common/Lazy.js`, `.js-lazy-load` — see [responsive-image.md](responsive-image.md)); video handling via `ResponsiveVideo.js` / `VideoOnScroll.js` / `VideoPlayButton.js`
- `instant.page` preloads links on hover/touchstart — already wired in `index.js`, no extra work needed
