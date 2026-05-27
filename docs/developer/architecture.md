# Architecture

## Overview

Real3D Flipbook is a WordPress plugin built around a singleton PHP class (`Real3DFlipbook`) that registers a custom post type (`r3d`), handles shortcode rendering, REST API endpoints, AJAX handlers, and asset enqueuing. The front-end rendering is handled by the `FLIPBOOK` JavaScript global, which instantiates a `FlipBook` object for each flipbook on the page.

## File Map

```
real3d-flipbook.php              Main plugin bootstrap (version constant, autoload, update checker)
index.php                        Silence is golden

includes/
  Real3DFlipbook.php             Singleton class: actions, shortcode, REST API, admin, defaults
  post-type.php                  R3D_Post_Type class: CPT + taxonomy registration, admin columns
  r3d-flipbook-data.php          Data abstraction layer: CRUD for flipbook post meta
  plugin-admin.php               AJAX handlers for admin (save settings, reset, save thumbnail)
  settings.php                   Global settings admin page template
  edit-flipbook-post.php         Single flipbook edit screen template
  single-r3d.php                 Front-end template for single flipbook posts
  archive-r3d.php                Front-end template for flipbook archive
  taxonomy-r3d_category.php      Front-end template for category taxonomy archives
  addons.php                     Addons management page
  import.php                     Import/export page template
  help.php                       Help page template
  upgrade-to-pro.php             Upgrade prompt page

js/
  flipbook.js / flipbook.min.js              Core FLIPBOOK library (main class, UI, events)
  flipbook.webgl.js / flipbook.webgl.min.js  WebGL renderer (Three.js integration)
  flipbook.book3.js / flipbook.book3.min.js  CSS3D renderer
  flipbook.swipe.js / flipbook.swipe.min.js  Swipe renderer
  flipbook.scroll.js / flipbook.scroll.min.js  Scroll renderer
  flipbook.pdfservice.min.js                 PDF.js service worker bridge
  embed.js                                   Front-end bootstrap (reads JSON configs, creates FlipBook instances)
  frontend.js                                Auto-convert PDF links to flipbooks
  blocks.js                                  Gutenberg block registration (r3dfb/embed)
  settings.js                                Admin settings page JS (option definitions, form builder)
  edit_flipbook_post.js                      Admin flipbook editor JS
  flipbooks.js                               Admin flipbooks list JS
  posts.js                                   Admin post list enhancements
  categories.js                              Admin category list enhancements
  import.js                                  Import page JS
  insert-flipbook.js                         Classic editor "Insert Flipbook" modal JS
  alpha-color-picker.js                      Color picker with alpha support

  libs/
    three.min.js                 Three.js (WebGL 3D library)
    pdf.min.js                   PDF.js (PDF rendering library)
    pdf.worker.min.js            PDF.js web worker
    sweetalert2.all.min.js       SweetAlert2 (modal dialogs)

css/
  flipbook.css / flipbook.min.css   Front-end flipbook styles
  flipbook-admin.css                Admin styles
  posts.css                         Admin post list styles
  insert-flipbook.css               Classic editor modal styles
  alpha-color-picker.css            Color picker styles
  sweetalert2.min.css               SweetAlert2 styles

assets/
  images/                        Preloader, shadow, overlay, logo images
  mp3/                           Page flip sound effect
  cmaps/                         PDF.js CMap files for CJK font support

lib/
  plugin-update-checker/         GitHub release update checker library

tests/
  bootstrap.php                  PHPUnit bootstrap
  php/                           PHP unit tests
  js/                            JavaScript tests (Jest)

languages/                       Translation files (.po/.mo)
```

## Singleton Pattern

`Real3DFlipbook` uses a private constructor with a static `get_instance()` method:

```php
private static $instance = null;

public static function get_instance() {
    if ( null == self::$instance ) {
        self::$instance = new self();
    }
    return self::$instance;
}
```

The singleton is instantiated at the bottom of `Real3DFlipbook.php`:

```php
Real3DFlipbook::get_instance();
```

`R3D_Post_Type` also uses the singleton pattern.

## Initialization Flow

1. `real3d-flipbook.php` defines constants (`REAL3D_FLIPBOOK_VERSION`, `REAL3D_FLIPBOOK_FILE`), requires `Real3DFlipbook.php`, and sets up the update checker.
2. `Real3DFlipbook::__construct()` registers activation/deactivation hooks and calls `add_actions()`.
3. `add_actions()` hooks into:
   - `init` -- registers the Gutenberg block, loads global options, registers scripts/styles, adds the shortcode, includes `r3d-flipbook-data.php` and `post-type.php`.
   - `plugins_loaded` -- loads text domain, checks addon availability.
   - `init` (priority 100) -- overrides competing plugin shortcodes.
   - `admin_enqueue_scripts` -- registers admin scripts.
   - `admin_menu` -- builds the admin menu tree.
   - Various AJAX actions for import, export, last-page saving, PDF serving.
   - `rest_api_init` -- registers REST routes.
   - `single_template` / `taxonomy_template` -- loads custom templates.

## Data Flow

1. **Storage**: Flipbook configuration is stored as `r3d_flipbook_options` post meta on `r3d` posts. Global defaults are in the `real3dflipbook_global` option.
2. **Read path**: `r3d_get_flipbook()` reads post meta. If not found, it attempts legacy migration from `real3dflipbook_{id}` options.
3. **Merge path**: Global defaults (`r3dfb_getDefaults()`) are deep-merged with the stored global options, then per-flipbook options override on top.
4. **Render path**: The shortcode handler merges shortcode attributes into the flipbook config, strips server-only keys, serializes to JSON, and outputs a `<div>` + `<script type="application/json">` pair.
5. **Front-end**: `embed.js` reads the JSON configs on `DOMContentLoaded`, merges global and per-book options, and creates `FlipBook` instances.

## Custom Post Type

- **Post type**: `r3d`
- **Taxonomies**: `r3d_category` (hierarchical), `r3d_author` (hierarchical)
- **Supports**: `title`, `thumbnail`, `slug`, `author`
- **Rewrite slug**: `flipbook` (configurable via global settings)
- **Archive**: Enabled
- **Search**: Excluded from WordPress search (`exclude_from_search: true`)

## Permission Model

Access to admin functions is controlled by a configurable capability stored in `real3dflipbook_capability`. The allowlist is:

- `manage_options` (Administrator)
- `manage_woocommerce` (Shop Manager)
- `publish_pages` (Editor)
- `edit_others_posts` (Author, default)

The `validated_capability()` method enforces this allowlist, falling back to `edit_others_posts` if an invalid capability is stored. The Settings page always requires `manage_options`.
