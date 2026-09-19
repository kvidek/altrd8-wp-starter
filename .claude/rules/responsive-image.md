# Responsive Media (Image & Video)

## Overview

Responsive media is rendered through two global PHP helper functions defined in [app/global-theme-functions.php](app/global-theme-functions.php), backed by plain-PHP partials:

- `get_responsive_image(array $args): string` → `partials/components/responsive-image.php`
- `get_responsive_video(array $args): string` → `partials/components/responsive-video.php`

Both output a `<figure><picture>...</picture></figure>` (image) or equivalent video markup with per-breakpoint `<source>` tags, styled by the `.c-responsive-media` BEM component (`static/scss/components/`) and the `.c-media-loader` overlay.

## Usage — image by URLs

```php
echo get_responsive_image([
    'urls' => [
        'widescreen'        => $url_2000,
        'widescreen_retina' => $url_2400,
        'desktop'           => $url_1600,
        'desktop_retina'    => $url_1800,
        'tablet'            => $url_1000,
        'tablet_retina'     => $url_1200,
        'mobile'            => $url_600,
        'mobile_retina'     => $url_800,
    ],
    'alt'             => 'Image',
    'aspect_ratio'    => '1-1',
    'object_fit'      => 'cover',
    'object_position' => 'center',
]);
```

## Usage — image by WordPress attachment ID + size names

Size names must exist in `App\config\Config::get_image_sizes()`. `get_responsive_image()` resolves each breakpoint's URL through `App\helpers\ImageHelper` (extends `wp-helpers-bf`'s `ImageProvider`), which generates/caches a WordPress intermediate image size on demand — no third-party resize library, no `uploads/resized/` cache dir like other projects use.

```php
echo get_responsive_image([
    'image' => $image_id, // WP attachment ID
    'sizes' => [
        'widescreen'        => 'image_1920',
        'widescreen_retina' => 'image_2880',
        'desktop'           => 'image_1200',
        'desktop_retina'    => 'image_1440',
        'tablet'            => 'image_800',
        'tablet_retina'     => 'image_900',
        'mobile'            => 'image_600',
        'mobile_retina'     => 'image_700',
    ],
    'aspect_ratio' => '16-9',
]);
```

A breakpoint value can also be `[$other_attachment_id, 'image_800']` to use a **different** attachment (e.g. an art-directed crop) for that breakpoint instead of the main `image` ID.

`alt` is auto-filled from the attachment's alt text when using the `image` + `sizes` form — no need to pass it manually unless overriding.

## Key args (both image & video)

| Arg | Default | Description |
|-----|---------|-------------|
| `urls` / (`image` + `sizes`) | — | one of these is required |
| `alt` | `'Image'` | ignored if using `image` (auto-filled) unless overridden |
| `aspect_ratio` | `'1-1'` | `1-1`, `2-1`, `1-2`, `3-1`, `1-3`, `3-2`, `2-3`, `4-3`, `3-4`, `16-9`, `9-16`, `auto`, `adopt` |
| `object_fit` | `'cover'` | `cover` or `contain` |
| `object_position` | `'center'` | `center`, `top`, `bottom`, `left`, `right` |
| `is_background` | `false` | use with `aspect_ratio: 'adopt'` to fill the first `position: relative` parent |
| `lazy` | `true` | lazy-load via `vanilla-lazyload` (`data-src`/`data-srcset` + `.js-lazy-load`) — don't combine with `priority` or above-the-fold media |
| `native_lazy` | `false` | native `loading="lazy"` instead of the JS lib |
| `priority` | `false` | `fetchpriority="high"` for above-the-fold media — disable both lazy options when using this |
| `animate` | `true` | show/hide the media-loader overlay while a lazy-loaded asset loads |
| `width` / `height` | — | only needed when `aspect_ratio` is `'auto'` |
| `loader_bg` | — | sync the media-loader background with the surrounding section background |
| `modifier_class` | `''` | extra class(es) on the outer wrapper |
| `show_sources` | auto | set to `false` to skip `<source>` breakpoints and just render the base `<img>`; auto-disabled for SVG |

## Missing breakpoints

Only `desktop` + `desktop_retina` are required — `widescreen(_retina)`, `tablet(_retina)`, and `mobile(_retina)` all fall back to the `desktop` values when omitted (see the fallback block at the top of `partials/components/responsive-image.php`).

## GIF handling

If the resolved attachment is a `.gif`, sources are hidden and the original (unresized) GIF URL is used directly for `desktop` — GIFs are never resized.

## CSS

BEM classes in `static/scss/components/common/` (or equivalent): `.c-responsive-media`, `.c-responsive-media__inner--{aspect-ratio}`, `.c-responsive-media__img--{object-fit|object-position}`, plus the `.c-media-loader` overlay (`partials/components/media-loader.php`).
