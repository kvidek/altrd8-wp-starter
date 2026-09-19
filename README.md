# altrd8-wp-starter

# Table of Contents

1. [Project info](#project-info)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Installation (Outsource)](#installation-outsource)
5. [NPM](#npm)
6. [Development scripts](#development-scripts)
7. [Development process](#development-process)
8. [Code style standards and best practices](#code-style-standards-and-best-practices)
8. [ACF field naming](#acf-field-naming)
8. [Theme architecture overview](#theme-architecture-overview)

## Project info

- github: git@github.com:kvidek/altrd8-wp-starter.git
- figma: {figma_link}

## Requirements

- php v7.4
- composer v1
- mariadb v10.5+
- node v14
- npm v7

## Installation

1. Clone BWP repository

2. Clone repository inside the `bwp_projects` folder.

- `git clone git@github.com:kvidek/altrd8-wp-starter.git`

## Installation (Outsource)

1. Download and install WordPress locally

2. Clone repository inside the `wp-content` folder. (Add “`.`” at the end, so the parent folder is not created)

- `git clone git@github.com:kvidek/altrd8-wp-starter.git .`

## NPM

1. Navigate inside the theme folder and install `node_modules`. Theme folder will contain `package.json`

- `npm install`

2. Inside theme folder run `build` script to generate initial `dist` files

- `npm run build`

3. Run `dev` inside theme folder script to start working

- `npm run dev`

## Development scripts

- `npm run dev`: watches and compiles files on changes, opens new local server with `browser-sync`
- `npm run build`: creates production ready versions of all files
- `npm run build:vendor`: creates production ready version of `vendor.js` file
- `npm run format`: runs `prettier` for `scss` and `js` files according to Bornfight standards (this also runs as
  pre-commit hook)

Scripts trigger `gulp` tasks which is used as task runner.

## Development process

It's forbidden to work directly on `master` branch. Every feature/page/fix should be different branch on `git`.
On `GitHub`, `master` branch is protected and process must be respected to submit changes.

#### Process:

1. On GitHub create a new branch from `master` and use standard naming convention for branches:

- `feature/name-of-the-feature`
- `fix/name-of-the-fix`

2. After you finished working commit and push your code to your `feature/fix` branch and create pull request to merge
   this branch to `master`. Push only production ready code so don't forget to run `build` script. Make sure you
   followed code style standards and run the `format` script.

3. Assign someone from team for code review. The goal here is to learn from each other and to make sure all standards
   are met. If you don't know who to assign check the `CODEOWNERS` file in the root folder.

4. After successful code review, the branch will be merged into `master` branch and `feature/fix` branch would be
   automatically deleted.

## Code style standards and best practices

### HTML

Write well documented HTML code.

Use minimal line breaks if possible.

Write semantic HTML.

All typography options should be controlled with utility classes.

All javascript classes should be prefixed with `js-`.

Example:

```
<!--PRE FOOTER CTA-->
<section class="o-section o-section--black">
    <div class="o-container">
        <div class="c-pre-footer-cta u-text-center">
            <!--title-->
            <h3 class="c-pre-footer-cta__title u-a2 u-fw-900">
                Title
            </h3>
            <!--end title-->
            <!--description-->
            <p class="c-pre-footer-cta__description u-a1">
                Description
            </p>
            <!--end description-->
            <!--buttons-->
            <div class="c-pre-footer-cta__buttons">
                <a role="button" target="" href="#" class="c-button u-b2 u-fw-700">
                    Button
                </a>
            </div>
            <!--end buttons-->
        </div>
    </div>
</section>
<!--end PRE FOOTER CTA-->
```

Example of complete HTML file according to Bornfight standards

```
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
    <title>Home</title>
    <link href="static/dist/style.css" rel="stylesheet">
</head>
<body>
<!--LOADER-->
<div class="o-loader js-loader">
    <div class="o-container">
        <div class="c-loader">
            loader
        </div>
    </div>
</div>
<!--end LOADER-->
<!--NAVIGATION-->
<nav class="o-navigation js-navigation">
    <div class="o-container">
        <div class="c-navigation">
            navigation
        </div>
    </div>
</nav>
<!--end NAVIGATION-->
<!--MOBILE NAVIGATION-->
<nav class="o-mobile-navigation js-mobile-navigation">
    <div class="o-container">
        <div class="c-mobile-navigation">
            mobile navigation
        </div>
    </div>
</nav>
<!--end MOBILE NAVIGATION-->
<!--PAGE WRAPPER-->
<div id="home" class="o-page o-page--home">
    <!--HEADER-->
    <header class="o-header">
        <div class="o-container">
            <div class="c-header">
                header
            </div>
        </div>
    </header>
    <!--end HEADER-->
    <!--PAGE CONTENT-->
    <main class="o-page__inner o-page__inner--home">
        <!--SECTION-->
        <section class="o-section">
            <div class="o-container">
                <div class="c-section-component-name">
                    home section
                </div>
            </div>
        </section>
        <!--end SECTION-->
        <!--MODULE-->
        <section class="o-section">
            <div class="o-container">
                <div class="c-module-name-module">
                    module
                </div>
            </div>
        </section>
        <!--end MODULE-->
    </main>
    <!--end PAGE CONTENT-->
    <!--FOOTER-->
    <footer class="o-footer js-footer">
        <div class="o-container">
            <div class="c-footer">
                footer
            </div>
        </div>
    </footer>
    <!--end FOOTER-->
    <!--COOKIE MESSAGE-->
    <div class="o-cookie-message js-cookie-message">
        <div class="o-container">
            <div class="c-cookie-message">
                cookie message
            </div>
        </div>
    </div>
    <!--end COOKIE MESSAGE-->
</div>
<!--end PAGE WRAPPER-->
<script src="static/dist/vendor.js"></script>
<script src="static/dist/bundle.js"></script>
</body>
</html>
```

### SCSS

Use B-creative SCSS code style.

Write BEM, comment when necessary.

SCSS `@include` and `@extend` goes on top, and it's separated from standard code with one line. Exception
is `@include mq` and `@include hover` which always goes in the end.

Use `npm run format` to help you with consistent code style.

Example:

```
.c-pre-footer-cta {
    &__title,
    &__description {
        @include fluidValue("max-width", null, 960px);
        @include fluidValue("margin-bottom", "s-32");
        @include fluidValue("border-radius", null, 2px);
        @include fluidValue("border-width", null, 1px);

        @extend .u-b0;

        border-style: solid;
        border-color: $green;
        margin-left: auto;
        margin-right: auto;
    }

    &__buttons {
        display: flex;
        align-items: center;
        justify-content: center;

        @include mq(md) {
            flex-direction: column;
        }

        @include hover {
            background-color: $green;
            color: $white;
        }

        &--justify {
            justify-content: space-between;
        }
    }

    &--blue {
        background-color: $blue;
    }
}
```

##### Architecture overview

SCSS's architecture is following rules from
`inuitcss` ([https://github.com/inuitcss/inuitcss](https://github.com/inuitcss/inuitcss)), but customised for
Bornfight's needs. Functions, mixins and additional code is from Bornfight's internal
startkit `b-creative` ([https://github.com/bornfight/b-creative](https://github.com/bornfight/b-creative)).

Main SCSS file:

- `style.scss`

SCSS folders:

- `/tools`: site-wide mixins and functions (e.g. mixins and functions).
- `/settings`: global variables, site-wide settings, config switches, etc. (e.g. font sizes and colors).
- `/generics`: low-specificity, far-reaching rulesets (e.g. resets).
- `/elements`: un-classed HTML elements (e.g. a {}, blockquote {}, h1 {}).
- `/objects`: objects, abstractions, and design patterns (e.g. .o-header {}).
- `/components`: discrete, complete chunks of UI (e.g. .c-button {}).
- `/pages`: page specific styles that could not be modularized.
- `/utilities`: high-specificity, very explicit selectors. Overrides and helper classes (e.g. .u-text-center {}).
- `/vendors`: imports of vendor styles or overrides of vendor styles.

SCSS files naming convention:

- tools: `_tools.tool-name.scss` for example `_tools.breakpoints.scss`
- settings: `_settings.setting-name.scss` for example `_settings.colors.scss`
- generics: `_generics.generic-name.scss` for example `_generics.reset.scss`
- elements: `_elements.element-name.scss` for example `_elements.html.scss`
- objects: `_objects.object-name.scss` for example `_objects.header.scss`
- components: `_components.component-name.scss` for example `_components.button.scss`
- components (modules): `_components.component-name-module.scss` for example `_components.hero-module.scss`
- pages: `_pages.page-name.scss` for example `_pages.homepage.scss`
- utilities: `_utilities.utility-name.scss` for example `_utilities.typography.scss`
- vendors: `_vendors.vendor-name.scss` for example `_vendors.swiper.scss`

Note: when creating new module component suffix file name with `-module`

Don't bloat the `style.scss` with all the files. Every subdirectory should have its index file which contains all the
files from that subdirectory, for example:

`@import 'components/components.index';`

##### Writing SCSS

SCSS classes naming convention:

- objects: `.o-object-name {}` for example `.o-header {}`
- components: `.c-component-name {}` for example `.c-button {}`
- components (modules): `.c-component-name-module {}` for example `.c-hero-module {}`
- pages: `.o-page-name {}` for example `.p-homepage {}`
- utilities: `.u-utility-name {}` for example `.u-uppercase {}`

##### Achieving responsiveness

B-creative comes with mixins and functions to achieve perfect responsive solutions for both mobile and ultra-wide
screens.

###### Font sizes

- font sizes are defined for 1440px and mobile 480px and contained in `_settings.typography.scss`.

- for resolutions between 1440px and 480px font sizes are scaling down using `poly-fluid-sizing` lib from 1440px value
  from map to 480px value from map.

- for resolutions from 1400px and above font size value on 1440px is converted to `viewport width` unit and is scaling
  with the resolution

- use `letter-spacing` with `em` units

- use `line-height` with `unitless` or `percentage` unit

###### All other properties

Use `fluidValue` mixin for all other sizes. It's necessary that all values in css are fluid with any described method.
There are two ways of using `fluidValue`: with defined value from spacings map or custom.

1. Spacing map

- spacings are defined for 1440px and mobile 480px and contained in `_settings.spacings.scss`.

- Try to use spacing map as much as possible, feel free to round numbers from design to a first similar value from
  settings map. For example if in design spacing between two element is `34px`, use `s-32` from spacing map. Or if
  spacing is `38px` use `s-40`.

- For resolutions between 1440px and 480px spacings are scaling down using `poly-fluid-sizing` lib from 1440px value
  from map to 480px value from map.

- for resolutions from 1400px and above spacing value on 1440px is converted to `viewport width` unit and is scaling
  with the resolution

- example:

`@include fluidValue("margin-bottom", "s-32");`
`@include fluidValue("padding-top", "s-32");`

2. Custom fluid spacing

- pass `null` to spacing parameter of `fluidValue` mixin and pass a custom value in `px` you want to convert to `vw`
  unit to make it responsive

- use this for situations where you could not apply any value from spacing, and for very specific values like 1px for
  border width

- use this to define `max-width` and similar properties of elements which you don't want to scale down from 1440px to
  480px.

- For resolutions between 1440px and 480px customo value passed to mixin is not scaling down

- for resolutions from 1400px and above custom value passed to mixin is converted to `viewport width` unit and is
  scaling with the resolution

- example:

`@include fluidValue("border-width", null, 1px);`

`@include fluidValue("max-width", null, 720px);`

### Javascript

Write `ES6 classes` for components and `ES6 arrow functions` for helpers.

Use `esdoc` to comment and explain what you are doing.

Only use classes prefixed with `.js-` for query selections.

Console logs are removed on build so feel free to use them.

There is template JS class component named `TemplateComponent.js` that can be used to generate new JS components.

Use `npm run format` to help you with consistent code style.

Example class component:

```
/**
 * Template component
 * explain what this class is doing
 */
export default class TemplateComponent {
    constructor() {
        /**
         * Template component DOM selectors
         * @type {{templateComponentArray: string, templateComponent: string, states: {isActive: string}}}
         */
        this.DOM = {
            templateComponent: ".js-template-component",
            templateComponentArray: ".js-template-component-array",
            states: {
                isActive: "is-active",
            },
        };

        /**
         * Fetch template component DOM element
         * @type {Element}
         */
        this.templateComponent = document.querySelector(this.DOM.templateComponent);

        /**
         * Fetch template component list of DOM elements
         * @type {NodeListOf<Element>}
         */
        this.templateComponentArray = document.querySelectorAll(this.DOM.templateComponentArray);
    }

    /**
     * Init
     */
    init() {
        if (this.templateComponent === null) {
            return;
        }
        console.log("Template component init");
        this.templateMethod();
    }

    /**
     * Template method
     * explain what this method is doing
     */
    templateMethod() {
        console.log("Template method init");
    }
}
```

Example helper component:

```
/**
 * Get viewport size
 * @returns {number}
 */
export const getViewportSize = () => {
    if (typeof window !== "undefined") {
        if (window.innerWidth !== undefined) {
            return window.innerWidth;
        } else {
            return document.documentElement.clientWidth;
        }
    }
};
```

##### Working with vendors

Third party scripts should be included as `ES6 modules`. Vendors are built into separate file than main project code for
performance but not automatically.

In root folder there is `plugins.js` file which contains array of vendor paths that will be ignored when building main
project code. Browserify will then ignore these paths when building main JS file, but use them to know which files to
include in vendor JS when building vendor file.

In array `plugins.js` you must always put the full `path` of your import, the same one you are using when importing
vendor to component or any other file.

For example here are some common imports you could be using:

```
import { gsap } from "gsap";
import { SplitText } from "gsap/SplitText";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import Swiper, { Thumbs } from "swiper";
import LazyLoad from "vanilla-lazyload";
```

Then you should update `plugins.js` array to look like this:

```
export const plugins = [
    "gsap",
    "gsap/SplitText",
    "gsap/ScrollTrigger",
    "swiper",
    "vanilla-lazyload",
];
```

Adding new plugin to array requires killing `dev` script and starting it again (`npm run dev`) because vendors are not
watched for changes, and their build is only triggered on initial running of `dev` or `build` scripts.

If you are working with legacy vendors which are **not** `ES6 Modules` you must firstly create a `ES6 Module ` from the
legacy vendor and also convert it to local `npm package`.

Process:

1. Inside `JS` folder create new folder called `vendors`

2. Create new folder inside `vendors` folder you just created and name it the same name you plan to name
   the `ES6 Class` (PascalCase).

- example: `MedialElement`

3. Inside that folder create new `JS` file with same name as the folder you created in the step 3

- example: `MediaElement.js`

4. Inside `MediaElement.js` create new `ES6 Class` and export it as default. Put all legacy vendor code inside
   constructor. Legacy code will execute once constructor of the Class is executed, and we can use it as a `ES6 Module`.

- example:

```
export default class MediaElement {
    constructor() {
        // legacy vendor code here
    }
}
```

5. In terminal navigate to the folder from step 2 (folder inside `vendors` folder which you created to
   contain `ES6 Class`). This folder in this stage should only contain `JS` file

- example: `static/js/vendors/MedialElement/`

6. Run `npm init` in terminal to init this folder as local `npm package`. Confirm all configuration questions
   with `enter key` , you don't have to write anything because npm will recognise and prefill all required fields. This
   folder is now local `npm package`. In this stage your folder should now contain `JS` file and `package.json`

7. In `package.json` now add this newly created package as `dependency` but mark it as file with path to folder of the
   created local package

- example:

```
"dependencies": {
   ...
   "mediaelement": "file:static/js/vendors/MedialElement",
   ...
}
```

8. Run `npm install` to install local `npm package`

9. Use legacy vendor as a `ES6 Module` now

- example:

`import MedialElement from "mediaelement";`

`new MedialElement();`

10. Include it in `plugins.js` array to mark it as vendor

- example:

```
export const plugins = [
    "mediaelement",
];
```

### PHP

- write clean code and OOP
- every class should exist in the APP folder, subfolders can be created
- avoid putting functions to functions.php, create a class that holds the function
- use hooks and filters as in WordPressDefaults class
- all logic should be written in classes and only used or echoed in partials

### WordPress

- [WordPress coding standards](https://github.com/WordPress/WordPress-Coding-Standards) should be applied - with and exception of naming classes, because of autoloader
- naming of classes should be CamelCase
- before pushing, the code should be formatted by the coding standard
- functions and variables need to be snake_case


## ACF field naming
- `Options Page` - Options - {name of the options}
    - example: Option - Theme, Option - Contact, Option - Form etc.
- `Archive Pages` - Template - {name of CPT}
    - example: Template - News, Template - Project etc.
- `Single Pages` - Template - {name of CPT} Single
    - example: Template - News Single, Template - Project Single etc.
- `Basic Page Template` - Template - {name of page template}
    - example: Home Page, Contact Page etc.
- `Category Pages` - Taxonomy - {Name of Taxonomy}
    - example: Taxonomy - Product Category etc.
- `Custom Gutenberg Blocks` - Block - {name of Block}
    - example: Block - Big Image, Block - Video Embed etc.
- `Flexible Modules` - Module - {name of Module}
    - example: Module - Big Image, Module - Video Embed etc.
- `Components that are used on multiple places` - Component - {Name of Component}
    - example: Component - Footer Callout, Component - Page Links etc.

## Icons
- to get an icon use the function `get_icon( $name )`

## Theme architecture overview

Theme consists of these folders:

- `acf-json`: json files of ACF fields
- `app`: represents the main theme folder where all classes exist
- `page-templates`: represents WordPress page templates
- `partials`: complete chunks of UI, organized in folders for each page and layout
- `static`: all static files like `JS`, `SCSS` etc. from Bornfight's `b-creative`
- `vendor`: composer vendor, where all PHP packages exist

#### Partials

Partials are complete chunks of any type of UI that can also be reused on different occasions. Goal is to have all parts
of website organized in partials and separated by their type.

Common types of partials:

- `page specific partials` - partials that belong to specific page (e.g. `/about-us/content.php`).
- `components` - discrete, complete chunks of UI (e.g. `/components/button.php`).
- `modules` - a logical section of a page that can be reused (e.g. `/modules/module_image.php`).
- `layout` - global components not specific to any page or section which are in most cases displayed on all pages (
  e.g. `/layout/navigation.php` or `/layout/cookie-message.php`)
- `slice` - used for creating "slice only" partials that follows same architecture as main `partials` folder (
  e.g. `/slice/about-us/content.php` or `/slice/components/button.php`)

##### Slice folder

Only static (slice only) files in this folder we don’t connect with backend. This will be later used by WordPress
developers to create dynamic version of the partial, or connect it with WordPress. Slice folder will not be deleted and
will be used as source of truth for `front-end` (slice) part of the partial.

- example:

`http://localhost/jamesbeard/wp-content/themes/sweden-unlimited-james-beard-web-2020/slice/home.php`

Put slice partials in `/partials/slice/*`, create separate folders for pages to keep it organized by following the same
rules for main `partials` folder.

- example: `/partials/slice/home/about-section.php`

##### Rendering partials

Partials are rendered with `get_partial` function. This function already points to `partials` folder of the theme.

Examples of rendering partials:

- simple:

`<?php get_partial('layout/footer'); ?>`

`<?php get_partial('components/button'); ?>`

`<?php get_partial('home/header'); ?>`

- with variables:

```
<?php
get_partial('home/header', array(
    'modifier' => 'o-section--medium',
    'style' => 'extended',
    'id' => 'header'
));
?>
```

#### Page templates

`page-templates` folder represents WordPress page templates. They are named by the page they are representing.

- example:

`page-templates/about.php`

If you are doing only slice part, create a file for page template and suffix its name with `-slice`.

- example: `page-templates/about-slice.php`

In the file, name the template and write `SLICE` after page name this template represents.

- example: `/** Template Name: About SLICE template */`

#### Slice page template example with everything in partials

```
<?php
/** Template Name: About SLICE template */

$template_name = 'about';

get_header();
get_partial('slice/layout/navigation');
?>
<!--PAGE WRAPPER-->
<div id="<?php echo esc_attr($template_name); ?>" class="o-page o-page--<?php echo esc_attr($template_name); ?>">
    <!--HEADER-->
    <?php get_partial('slice/about/header', array(
        'modifier => 'o-header--" . $template_name,
    )); ?>
    <!--end HEADER-->
    <!--page content-->
    <main class="o-page__inner o-page__inner--<?php echo esc_attr($template_name); ?>">
        <?php
        get_partial('slice/about/content');
        ?>
    </main>
    <!--end page content-->
    <!--FOOTER-->
    <?php get_partial('slice/layout/footer'); ?>
    <!--end FOOTER-->
</div>
<!--end PAGE WRAPPER-->
<?php get_footer(); ?>
```

#### Using static assets

To use static assets (e.g. `static/images`) use base URL function `bu()`. This function points to `static` folder.

- example:

`<?= bu('images/blog-image-dummy.jpg') ?>`

### SVG icons

All icons should be in `SVG` format.

`SVG` must be done with `fill` and not `stroke`.

Icons are controlled with `CSS` with utility class `.u-icon`.

Properly prepared icon (with `fill`) in combination with icon utility class enables us to control size and color of icon
with `CSS`.

##### Example of properly prepared SVG icon

Besides converting to `fill` instead of `stroke` you need to remove `width` and `height` attributes from `SVG` and use
that values in `viewBox` attribute.

For example if your `SVG` comes with attributes `width="512"` and `height="512"` use that values to create `viewBox`
attribute and delete them.

The value of the `viewBox` attribute is a list of four numbers: `min-x`, `min-y`, `width` and `height`. So
your `viewBox` should look like this:

`viewBox="0 0 512 512"`

Also, remove all of the `fill` related attributes as color of icon is controlled with `CSS`.

Complete example:

```
<svg class="u-icon u-icon--twitter" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    <path d="M512 97.248c-19.04 8.352-39.328 13.888-60.48 16.576 21.76-12.992 38.368-33.408 46.176-58.016-20.288 12.096-42.688 20.64-66.56 25.408C411.872 60.704 384.416 48 354.464 48c-58.112 0-104.896 47.168-104.896 104.992 0 8.32.704 16.32 2.432 23.936-87.264-4.256-164.48-46.08-216.352-109.792-9.056 15.712-14.368 33.696-14.368 53.056 0 36.352 18.72 68.576 46.624 87.232-16.864-.32-33.408-5.216-47.424-12.928v1.152c0 51.008 36.384 93.376 84.096 103.136-8.544 2.336-17.856 3.456-27.52 3.456-6.72 0-13.504-.384-19.872-1.792 13.6 41.568 52.192 72.128 98.08 73.12-35.712 27.936-81.056 44.768-130.144 44.768-8.608 0-16.864-.384-25.12-1.44C46.496 446.88 101.6 464 161.024 464c193.152 0 298.752-160 298.752-298.688 0-4.64-.16-9.12-.384-13.568 20.832-14.784 38.336-33.248 52.608-54.496z"/>
</svg>
```

##### Change SVG icon color

SVG icon will inherit parent `color` property as default color. To change color of `SVG` icon simply override
its `CSS` `color` value.

- example:

```
.u-icon {
    color: $red;
}
```

##### Change SVG icon size

SVG icon will inherit parent `font-size` property as default size. To change size of `SVG` icon simply override
its `CSS` `font-size` value.

```
.u-icon {
    @include fluidValue("font-size", "s-32");
}
```