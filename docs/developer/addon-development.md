# Addon Development

This guide enables developers (including AI agents) to build addons that extend Real3D Flipbook. It covers the extension points, the data layer, and includes a complete working scaffold.

## How Addons Work

Real3D Flipbook recognizes addons through a product registry in `Real3DFlipbook::__construct()`. Each addon is a separate WordPress plugin that defines a specific PHP class. The main plugin checks `class_exists()` on `plugins_loaded` to detect active addons.

Existing addon entries in the registry:

| Key        | Class                       | Name              |
| ---------- | --------------------------- | ----------------- |
| `pefrf`    | `R3D_Page_Editor`           | Page Editor Addon |
| `ptfrf`    | `R3D_PDF_Tools`             | PDF Tools Addon   |
| `bs`       | `Bookshelf_Addon`           | Bookshelf Addon   |
| `wafrf`    | `R3D_Woo`                   | WooCommerce Addon |
| `eafrf`    | `Elementor_Real3D_Flipbook` | Elementor Addon   |
| `wpb_r3d`  | `Real3DFlipbook_VCAddon`    | WPBakery Addon    |
| `prev_r3d` | `R3D_Preview`               | Preview Addon     |

Your addon does not need to be in this registry to work. The registry is used only for the Addons management page. You can build a fully functional addon using the hooks and data layer documented below.

## Extension Points

### 1. Admin Menu

Use the `real3d_flipbook_menu` action to add submenu pages:

```php
add_action( 'real3d_flipbook_menu', function() {
    add_submenu_page(
        'real3d_flipbook_admin',       // Parent slug
        'My Addon Settings',           // Page title
        'My Addon',                    // Menu title
        'edit_others_posts',           // Capability
        'my_addon_settings',           // Menu slug
        'my_addon_render_page'         // Callback
    );
});
```

### 2. Settings Tabs

Use the `r3d_settings_add_nav_tab` filter to add a tab to the global settings page:

```php
add_filter( 'r3d_settings_add_nav_tab', function( $tabs_html ) {
    $tabs_html .= '<a href="#" class="nav-tab" data-tab="tab-my-addon">My Addon</a>';
    return $tabs_html;
});
```

You will also need to enqueue JavaScript that renders your tab's content into a `<div id="tab-my-addon">` on the settings page.

### 3. Classic Editor Insert Modal

Use the `r3d_select_flipbook_before_insert` filter to add fields to the "Insert Flipbook" modal:

```php
add_filter( 'r3d_select_flipbook_before_insert', function( $html ) {
    $html .= '<div class="r3d-row">';
    $html .= '<label for="r3d-my-field">My Field</label>';
    $html .= '<input type="text" id="r3d-my-field">';
    $html .= '</div>';
    return $html;
});
```

### 4. Access Control

Use the `r3d_woo_purchased_or_subscription` filter to implement custom access logic:

```php
add_filter( 'r3d_woo_purchased_or_subscription', function( $has_access ) {
    // Custom logic to determine if user has access
    if ( my_custom_access_check() ) {
        return true;
    }
    return $has_access;
});
```

### 5. REST API

The REST API endpoints (`flipbook/v1/create` and `flipbook/v1/update`) accept a `flipbook_options` parameter that can include any custom keys. These are saved alongside standard options in the `r3d_flipbook_options` post meta.

### 6. Post Save Hook

Hook into `save_post_r3d` to run custom logic when a flipbook is saved:

```php
add_action( 'save_post_r3d', function( $post_id, $post, $update ) {
    // Your custom save logic
    $flipbook = r3d_get_flipbook( $post_id );
    if ( $flipbook && isset( $flipbook['my_custom_key'] ) ) {
        // Process custom data
    }
}, 20, 3 ); // Priority 20 to run after the main plugin's save handler
```

### 7. Template Override

Hook into `single_template` at a higher priority to provide custom templates:

```php
add_filter( 'single_template', function( $template ) {
    global $post;
    if ( 'r3d' === $post->post_type ) {
        $custom = plugin_dir_path( __FILE__ ) . 'templates/single-r3d.php';
        if ( file_exists( $custom ) ) {
            return $custom;
        }
    }
    return $template;
}, 20 );
```

## Data Layer API

All flipbook data access should go through these functions defined in `includes/r3d-flipbook-data.php`:

### Reading

```php
// Get a single flipbook by post ID
$flipbook = r3d_get_flipbook( $post_id );
// Returns: array with all options, or null

// Get all published flipbooks
$all = r3d_get_all_flipbooks();
// Returns: array keyed by post ID

// Get flipbooks with custom query args
$filtered = r3d_get_all_flipbooks( array(
    'posts_per_page' => 10,
    'orderby'        => 'title',
    'order'          => 'ASC',
) );

// Find a flipbook by title
$flipbook = r3d_resolve_flipbook_by_name( 'My Book' );
// Returns: array or null
```

### Writing

```php
// Save flipbook options (post must already exist with type 'r3d')
r3d_save_flipbook( $post_id, 'Book Title', array(
    'pdfUrl'  => 'https://example.com/file.pdf',
    'mode'    => 'lightbox',
    'my_key'  => 'my_value',
) );
```

### Deleting

```php
// Delete flipbook options (not the post itself)
r3d_delete_flipbook_data( $post_id );
```

### Global Options

```php
// Read global options
$global = get_option( 'real3dflipbook_global' );

// Get defaults
$defaults = r3dfb_getDefaults();

// Merge (deep)
$merged = r3d_array_merge_deep( $defaults, $global );
```

## Complete Addon Scaffold

Below is a complete, working addon plugin. Copy this into a new directory under `wp-content/plugins/`.

### File Structure

```
real3d-flipbook-my-addon/
  real3d-flipbook-my-addon.php    Main plugin file
  includes/
    class-my-addon.php            Addon class
  js/
    my-addon-admin.js             Admin JavaScript
```

### `real3d-flipbook-my-addon.php`

```php
<?php
/*
 * Plugin Name: Real3D Flipbook - My Addon
 * Description: Example addon for Real3D Flipbook
 * Version:     1.0.0
 * Author:      Your Name
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License:     GPL-2.0-or-later
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'MY_ADDON_VERSION', '1.0.0' );
define( 'MY_ADDON_FILE', __FILE__ );
define( 'MY_ADDON_DIR', plugin_dir_path( __FILE__ ) );
define( 'MY_ADDON_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', function() {
    // Ensure Real3D Flipbook is active
    if ( ! class_exists( 'Real3DFlipbook' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo esc_html__( 'My Addon requires Real3D Flipbook to be installed and activated.', 'my-addon' );
            echo '</p></div>';
        });
        return;
    }

    require_once MY_ADDON_DIR . 'includes/class-my-addon.php';
    My_Addon::get_instance();
});
```

### `includes/class-my-addon.php`

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class My_Addon {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Admin menu
        add_action( 'real3d_flipbook_menu', array( $this, 'add_menu_page' ) );

        // Settings tab
        add_filter( 'r3d_settings_add_nav_tab', array( $this, 'add_settings_tab' ) );

        // Custom shortcode attribute processing
        add_filter( 'r3d_select_flipbook_before_insert', array( $this, 'add_insert_fields' ) );

        // Admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // Save hook
        add_action( 'save_post_r3d', array( $this, 'on_save_flipbook' ), 20, 3 );

        // AJAX handler
        add_action( 'wp_ajax_my_addon_save', array( $this, 'ajax_save' ) );

        // Front-end: modify flipbook output
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
    }

    /**
     * Add a submenu page under Real3D Flipbook.
     */
    public function add_menu_page() {
        add_submenu_page(
            'real3d_flipbook_admin',
            __( 'My Addon', 'my-addon' ),
            __( 'My Addon', 'my-addon' ),
            'edit_others_posts',
            'my_addon_settings',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Render the admin page.
     */
    public function render_admin_page() {
        $options = get_option( 'my_addon_options', array() );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'My Addon Settings', 'my-addon' ); ?></h1>
            <form id="my-addon-form">
                <?php wp_nonce_field( 'my_addon_nonce', 'my_addon_security' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="my-addon-option1">
                                <?php esc_html_e( 'Option 1', 'my-addon' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text" id="my-addon-option1" name="option1"
                                value="<?php echo esc_attr( $options['option1'] ?? '' ); ?>"
                                class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="my-addon-enabled">
                                <?php esc_html_e( 'Enabled', 'my-addon' ); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox" id="my-addon-enabled" name="enabled"
                                <?php checked( $options['enabled'] ?? false ); ?>>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php esc_html_e( 'Save Changes', 'my-addon' ); ?>
                    </button>
                </p>
            </form>
        </div>
        <?php
    }

    /**
     * Add a tab to the global settings page.
     */
    public function add_settings_tab( $tabs_html ) {
        $tabs_html .= '<a href="#" class="nav-tab" data-tab="tab-my-addon">'
            . esc_html__( 'My Addon', 'my-addon' ) . '</a>';
        return $tabs_html;
    }

    /**
     * Add fields to the classic editor Insert Flipbook modal.
     */
    public function add_insert_fields( $html ) {
        $html .= '<div class="r3d-row">';
        $html .= '<span class="r3d-label-wrapper">';
        $html .= '<label for="r3d-my-option">' . esc_html__( 'My Option', 'my-addon' ) . '</label>';
        $html .= '</span>';
        $html .= '<input type="text" id="r3d-my-option" class="r3d-setting">';
        $html .= '</div>';
        return $html;
    }

    /**
     * Enqueue admin scripts on relevant pages.
     */
    public function enqueue_admin_scripts( $hook_suffix ) {
        // Only load on our addon page or flipbook edit pages
        if ( strpos( $hook_suffix, 'my_addon' ) === false ) {
            $screen = get_current_screen();
            if ( ! $screen || 'r3d' !== $screen->post_type ) {
                return;
            }
        }

        wp_enqueue_script(
            'my-addon-admin',
            MY_ADDON_URL . 'js/my-addon-admin.js',
            array( 'jquery' ),
            MY_ADDON_VERSION,
            true
        );

        wp_localize_script( 'my-addon-admin', 'myAddon', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'my_addon_nonce' ),
        ) );
    }

    /**
     * Enqueue front-end scripts.
     */
    public function enqueue_frontend_scripts() {
        // Only enqueue if flipbook scripts are registered
        if ( wp_script_is( 'real3d-flipbook', 'registered' ) ) {
            // Add your front-end script or inline modifications here
        }
    }

    /**
     * Handle flipbook save to process custom data.
     */
    public function on_save_flipbook( $post_id, $post, $update ) {
        $flipbook = r3d_get_flipbook( $post_id );
        if ( ! $flipbook ) {
            return;
        }

        // Read and process any custom keys from the flipbook options
        $my_data = $flipbook['my_custom_key'] ?? '';
        if ( $my_data ) {
            // Do something with the custom data
            update_post_meta( $post_id, '_my_addon_processed', sanitize_text_field( $my_data ) );
        }
    }

    /**
     * AJAX handler for saving addon options.
     */
    public function ajax_save() {
        check_ajax_referer( 'my_addon_nonce', 'my_addon_security' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Insufficient permissions.' );
        }

        $options = array(
            'option1' => isset( $_POST['option1'] ) ? sanitize_text_field( wp_unslash( $_POST['option1'] ) ) : '',
            'enabled' => isset( $_POST['enabled'] ) && 'on' === $_POST['enabled'],
        );

        update_option( 'my_addon_options', $options );

        wp_send_json_success( 'Settings saved.' );
    }
}
```

### `js/my-addon-admin.js`

```js
"use strict";
(function ($) {
  $(document).ready(function () {
    // Handle form submission
    $("#my-addon-form").on("submit", function (e) {
      e.preventDefault();

      var data = {
        action: "my_addon_save",
        my_addon_security: $("#my_addon_security").val(),
        option1: $("#my-addon-option1").val(),
        enabled: $("#my-addon-enabled").is(":checked") ? "on" : "off",
      };

      $.post(myAddon.ajax_url, data, function (response) {
        if (response.success) {
          alert("Settings saved.");
        } else {
          alert("Error: " + response.data);
        }
      });
    });
  });
})(jQuery);
```

## Working with Flipbook Options on the Front End

If your addon needs to modify the flipbook options before they are sent to the JavaScript renderer, hook into the shortcode output. The simplest approach is to use `r3d_get_flipbook()` to read options and `r3d_save_flipbook()` to modify them.

For runtime modifications (without changing saved data), you can use the `wp_add_inline_script` approach to inject JavaScript:

```php
add_action( 'wp_enqueue_scripts', function() {
    if ( wp_script_is( 'real3d-flipbook-embed', 'enqueued' ) ) {
        wp_add_inline_script( 'real3d-flipbook-embed', '
            document.addEventListener("DOMContentLoaded", function() {
                // Access global FLIPBOOK after embed.js runs
                // Modify options, add event listeners, etc.
            });
        ' );
    }
});
```

## Best Practices

1. **Check for the main plugin**: Always verify `class_exists( 'Real3DFlipbook' )` before initializing your addon.
2. **Use the data layer**: Always use `r3d_get_flipbook()` and `r3d_save_flipbook()` instead of direct `get_post_meta()` / `update_post_meta()` calls. The data layer handles migration, key normalization, and notes preservation.
3. **Namespace your keys**: Prefix custom option keys with your addon slug (e.g., `my_addon_watermark`) to avoid collisions with core settings.
4. **Sanitize inputs**: Use WordPress sanitization functions. The core plugin uses `r3d_sanitize_array()` for recursive sanitization.
5. **Verify capabilities**: Use the same capability check as the main plugin for consistency:
   ```php
   $capability = get_option( 'real3dflipbook_capability', 'edit_others_posts' );
   ```
6. **Nonce verification**: Always verify nonces in AJAX handlers and form submissions.
7. **Text domain**: Use a separate text domain for your addon (not `real3d-flipbook`).
8. **Uninstall cleanup**: Implement `uninstall.php` or `register_uninstall_hook()` to clean up your addon's options and meta.
