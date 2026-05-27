# Theming

Real3D Flipbook uses custom templates for rendering flipbook posts and taxonomy archives. Theme developers can understand these templates to customize the flipbook presentation.

## Template Loading

The plugin intercepts WordPress template loading via two filters in `Real3DFlipbook::add_actions()`:

```php
add_filter( 'single_template', array( $this, 'load_r3d_template' ) );
add_filter( 'taxonomy_template', array( $this, 'load_r3d_taxonomy_template' ) );
```

These filters check the post type or taxonomy and return the plugin's built-in template files from the `includes/` directory.

## Template Files

### `single-r3d.php`

**Location**: `includes/single-r3d.php`
**Used for**: Single flipbook post pages (e.g., `example.com/flipbook/my-book/`)

**Behavior**:

1. Checks `post_password_required()`. If the post has a password, shows the password form instead of the flipbook.
2. Loads the flipbook options via `r3d_get_flipbook()` and merges with global options.
3. Checks the `access` setting:
   - `free` -- always shows the flipbook.
   - `woo_subscription` -- checks `r3d_user_has_woo_subscription()` (looks for active or pending-cancel WooCommerce subscriptions).
   - `none` -- shows "Forbidden" text.
4. If the flipbook mode is `fullscreen`, injects CSS to hide common header/footer selectors (`.site-header`, `#masthead`, `.site-footer`, `#colophon`, etc.).
5. Wraps the flipbook shortcode output between `get_header()` and `get_footer()`.

**Key output**:

```php
get_header();
echo do_shortcode( '[real3dflipbook id="' . esc_attr( $r3d_post_id ) . '"]' );
get_footer();
```

### `archive-r3d.php`

**Location**: `includes/archive-r3d.php`
**Used for**: The flipbook archive page (e.g., `example.com/flipbook/`)

**Behavior**: A minimal archive template that loops through posts and outputs `the_title()` and `the_content()` wrapped in `get_header()` / `get_footer()`.

### `taxonomy-r3d_category.php`

**Location**: `includes/taxonomy-r3d_category.php`
**Used for**: Category taxonomy archive pages (e.g., `example.com/r3d_category/newsletters/`)

**Behavior**:

1. Queries all published `r3d` posts in the current taxonomy term.
2. For each post, loads flipbook options and merges with global options.
3. Checks the `access` setting (same logic as `single-r3d.php`).
4. Renders each accessible flipbook in lightbox mode via shortcode.
5. Content is centered in a wrapper `<div>`.

## Customizing Templates

The plugin does **not** currently support theme template overrides via `locate_template()` or a theme's `r3d/` subdirectory. The template filters in the plugin always return the plugin's own template files.

To customize the templates, you have two options:

### Option 1: Override the filter

Remove the plugin's template filter and provide your own:

```php
// In your theme's functions.php
add_filter( 'single_template', function( $template ) {
    global $post;
    if ( 'r3d' === $post->post_type ) {
        $custom = locate_template( 'single-r3d.php' );
        if ( $custom ) {
            return $custom;
        }
    }
    return $template;
}, 20 ); // Priority 20 to run after the plugin's priority 10
```

Then place your custom `single-r3d.php` in your theme root.

### Option 2: Use shortcodes in page templates

Instead of relying on the single post template, embed flipbooks in regular pages using the `[real3dflipbook]` shortcode or Gutenberg block. This gives you full control over the surrounding layout.

## CSS Customization

The flipbook front-end styles are in `css/flipbook.min.css`. Key CSS classes:

| Class                          | Description                                   |
| ------------------------------ | --------------------------------------------- |
| `.real3dflipbook`              | Container element for each flipbook instance. |
| `.flipbook-browser-fullscreen` | Applied in fullscreen mode.                   |
| `.flipbook-menu`               | Bottom toolbar container.                     |
| `.flipbook-menu2`              | Top toolbar container.                        |
| `.flipbook-nav`                | Side navigation arrow container.              |
| `.flipbook-page`               | Individual page element.                      |
| `.flipbook-thumb`              | Thumbnail in the thumbnails panel.            |

The flipbook container element gets a dynamically generated class based on its unique ID: `.real3dflipbook-{bookId}`.

## URL Structure

| URL Pattern             | Template                    | Description             |
| ----------------------- | --------------------------- | ----------------------- |
| `/flipbook/`            | `archive-r3d.php`           | Flipbook archive        |
| `/flipbook/{slug}/`     | `single-r3d.php`            | Single flipbook         |
| `/r3d_category/{slug}/` | `taxonomy-r3d_category.php` | Category archive        |
| `/r3d_author/{slug}/`   | (WordPress default)         | Author taxonomy archive |

The base slug `flipbook` is configurable via the `slug` global setting. After changing, visit **Settings > Permalinks** and click **Save Changes** to flush rewrite rules.
