# Hooks Reference

This document lists all custom actions and filters defined by the Real3D Flipbook plugin. These are found by searching all PHP files for `do_action` and `apply_filters`.

---

## Actions

### `real3d_flipbook_menu`

**File**: `includes/Real3DFlipbook.php` (line 1184)
**Type**: Action
**When**: Fired at the end of `admin_menu()`, after all built-in submenu pages are registered.
**Who can trigger**: Only fires if the current user has the configured flipbook management capability.

**Parameters**: None.

**Description**: Allows addons and extensions to register their own submenu pages under the Real3D Flipbook admin menu.

**Usage example**:

```php
add_action( 'real3d_flipbook_menu', function() {
    add_submenu_page(
        'real3d_flipbook_admin',
        'My Addon',
        'My Addon',
        'edit_others_posts',
        'my_addon_page',
        'my_addon_render_callback'
    );
});
```

---

## Filters

### `r3d_settings_add_nav_tab`

**File**: `includes/settings.php` (line 88)
**Type**: Filter
**When**: During rendering of the global settings page, after the built-in tabs are output.

**Parameters**:

| Parameter    | Type   | Description                                                    |
| ------------ | ------ | -------------------------------------------------------------- |
| `$tabs_html` | string | HTML string of additional tab `<a>` elements. Initially empty. |

**Return**: String of HTML `<a>` elements to append to the settings tab bar.

**Description**: Allows addons to inject additional tabs into the global settings page. The returned HTML is sanitized with `wp_kses()` allowing `<a>` tags with `href`, `class`, and `data-tab` attributes.

**Usage example**:

```php
add_filter( 'r3d_settings_add_nav_tab', function( $tabs_html ) {
    $tabs_html .= '<a href="#" class="nav-tab" data-tab="tab-my-addon">My Addon</a>';
    return $tabs_html;
});
```

### `r3d_select_flipbook_before_insert`

**File**: `includes/Real3DFlipbook.php` (line 1297)
**Type**: Filter
**When**: During rendering of the "Insert Flipbook" modal in the classic editor, just before the insert button.

**Parameters**:

| Parameter | Type   | Description                                         |
| --------- | ------ | --------------------------------------------------- |
| `$html`   | string | Additional HTML to display. Initially empty string. |

**Return**: String of HTML content to insert before the "Insert flipbook" button.

**Description**: Allows addons to add custom fields or options to the classic editor flipbook insertion modal.

**Usage example**:

```php
add_filter( 'r3d_select_flipbook_before_insert', function( $html ) {
    $html .= '<div class="r3d-row"><label>Custom Field</label><input type="text" id="r3d-custom"></div>';
    return $html;
});
```

### `r3d_woo_purchased_or_subscription`

**File**: `includes/Real3DFlipbook.php` (line 1870)
**Type**: Filter
**When**: During shortcode rendering, when `previewMode` is set to `woo_purchased_or_subscription`.

**Parameters**:

| Parameter      | Type | Description                                                  |
| -------------- | ---- | ------------------------------------------------------------ |
| `$full_access` | bool | Whether the current user has full access. Initially `false`. |

**Return**: Boolean. Return `true` to grant full access (all pages visible), `false` to enforce preview mode.

**Description**: Allows the WooCommerce addon (or custom code) to determine whether the current user has purchased the associated product or has an active subscription, thus granting full access to the flipbook instead of the preview-limited version.

**Usage example**:

```php
add_filter( 'r3d_woo_purchased_or_subscription', function( $full_access ) {
    if ( is_user_logged_in() && user_has_purchased_product() ) {
        return true;
    }
    return $full_access;
});
```

---

## WordPress Core Hooks Used

The plugin also hooks into many standard WordPress actions and filters. Key ones include:

| Hook                    | Purpose                                                          |
| ----------------------- | ---------------------------------------------------------------- |
| `init`                  | Register CPT, taxonomies, block type, shortcode, load options    |
| `plugins_loaded`        | Load text domain, check addon availability                       |
| `admin_menu`            | Register admin menu pages                                        |
| `admin_enqueue_scripts` | Register and enqueue admin JS/CSS                                |
| `wp_enqueue_scripts`    | Register front-end JS/CSS                                        |
| `rest_api_init`         | Register REST API routes                                         |
| `save_post_r3d`         | Save flipbook data when the post is saved                        |
| `add_meta_boxes`        | Add shortcode meta box to the edit screen                        |
| `before_delete_post`    | Clean up flipbook data when a post is deleted                    |
| `single_template`       | Load `single-r3d.php` for flipbook posts                         |
| `taxonomy_template`     | Load `taxonomy-r3d_category.php` for category archives           |
| `widget_text`           | Enable shortcode processing in text widgets                      |
| `media_buttons`         | Add "Insert Flipbook" button to the classic editor               |
| `wp_ajax_*`             | Various AJAX handlers (see [ajax-handlers.md](ajax-handlers.md)) |
