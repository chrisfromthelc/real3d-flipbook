# Data Model

## Custom Post Type: `r3d`

Flipbooks are stored as WordPress posts with post type `r3d`. The WordPress post ID is the flipbook ID.

### Post Type Registration

Defined in `includes/post-type.php` via the `R3D_Post_Type` class:

```php
register_post_type( 'r3d', array(
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_menu'        => false,    // Menu is managed manually
    'has_archive'         => true,
    'hierarchical'        => false,
    'supports'            => array( 'title', 'thumbnail', 'slug', 'author' ),
    'exclude_from_search' => true,
    'rewrite'             => array( 'slug' => $rewriteSlug, 'with_front' => false ),
    'capability_type'     => 'post',
));
```

The rewrite slug defaults to `flipbook` but can be customized in global settings.

### Taxonomies

#### `r3d_category`

- Hierarchical (like WordPress categories)
- Rewrite slug: `r3d_category`
- Shown in admin column, navigation menus

#### `r3d_author`

- Hierarchical
- Rewrite slug: `r3d_author`
- Shown in admin column, navigation menus
- No parent item support (`parent_item` and `parent_item_colon` set to `null`)

## Post Meta

### `r3d_flipbook_options`

The primary meta key. Stores a serialized associative array containing the entire flipbook configuration. This array contains all the keys documented in [configuration.md](../user/configuration.md) plus additional per-flipbook data:

| Key                    | Type   | Description                                                                      |
| ---------------------- | ------ | -------------------------------------------------------------------------------- |
| `id`                   | int    | Same as the post ID (set on read).                                               |
| `post_id`              | int    | Same as the post ID (set on read).                                               |
| `name`                 | string | Flipbook title (synced with post title).                                         |
| `pdfUrl`               | string | URL to the PDF file.                                                             |
| `lightboxThumbnailUrl` | string | URL to the thumbnail image.                                                      |
| `pages`                | array  | Array of page objects, each with `src`, `thumb`, `json`, `htmlContent`, `items`. |
| `tableOfContent`       | array  | Table of contents entries.                                                       |
| `notes`                | array  | User notes, each with `userId`, `page`, `text`, `type`, `readonly`.              |
| `viewMode`             | string | Rendering mode.                                                                  |
| `mode`                 | string | Display mode.                                                                    |
| `date`                 | string | Creation date.                                                                   |
| (all other settings)   | mixed  | Any setting from `r3dfb_getDefaults()` can be stored per-flipbook.               |

### `flipbook_id` (Legacy)

Used in older versions to map a post to a legacy `real3dflipbook_{id}` option. Automatically migrated and deleted by `r3d_migrate_legacy_flipbook()`.

## Options (wp_options)

| Option Key                       | Type   | Description                                                                   |
| -------------------------------- | ------ | ----------------------------------------------------------------------------- |
| `real3dflipbook_global`          | array  | Global default settings for all flipbooks. Merged with `r3dfb_getDefaults()`. |
| `real3dflipbook_capability`      | string | WordPress capability required to manage flipbooks.                            |
| `r3d_version`                    | string | Currently installed plugin version. Used for update detection.                |
| `r3d_flush_rewrite_rules`        | bool   | Flag to trigger `flush_rewrite_rules()` on next load.                         |
| `r3d_woo_show_thankyou_flipbook` | bool   | Whether to show flipbooks on WooCommerce thank-you page.                      |

## User Meta

| Meta Key                            | Type | Description                                                                   |
| ----------------------------------- | ---- | ----------------------------------------------------------------------------- |
| `real3dflipbook_last_page_{bookId}` | int  | Last viewed page for resume-reading feature. One entry per flipbook per user. |

## Transients

| Transient Key              | TTL      | Description                                                                                                                   |
| -------------------------- | -------- | ----------------------------------------------------------------------------------------------------------------------------- |
| `flipbook_pdf_{unique_id}` | 12 hours | Maps a unique ID to a local PDF file path for the secure PDF proxy. Created per shortcode render when `securepdf` is enabled. |

## Data Abstraction Layer

All data access goes through functions in `includes/r3d-flipbook-data.php`:

### `r3d_get_flipbook( int $post_id ): ?array`

Reads `r3d_flipbook_options` post meta. If not found, attempts legacy migration. Returns the options array with `id` and `post_id` injected, or `null`.

### `r3d_save_flipbook( int $post_id, string $title, array $options ): bool`

Writes the options array to `r3d_flipbook_options` post meta. Handles `viewMode`/`webgl` reconciliation. Sets `name`, `post_id`, and `id` in the array. Preserves `notes` from the existing record if not present in the new data.

### `r3d_delete_flipbook_data( int $post_id ): void`

Deletes `r3d_flipbook_options` post meta. Called from the `before_delete_post` hook.

### `r3d_get_all_flipbooks( array $args = array() ): array`

Returns all published flipbooks as an associative array keyed by post ID. Accepts `WP_Query` arguments for filtering.

### `r3d_resolve_flipbook_by_name( string $name ): ?array`

Looks up a flipbook by its post title. Returns the first matching published flipbook's options, or `null`.

### `r3d_migrate_legacy_flipbook( int $post_id ): ?array`

Migrates a flipbook from the legacy `real3dflipbook_{id}` option to `r3d_flipbook_options` post meta. Deletes the old `flipbook_id` post meta and the old option after migration. Returns the migrated options array, or `null` if no legacy data exists.

## Cleanup on Uninstall

`uninstall.php` removes all plugin data:

1. Deletes options: `real3dflipbook_global`, `real3dflipbook_capability`, `r3d_version`, `r3d_flush_rewrite_rules`.
2. Deletes all `r3d` posts (including trash) via `wp_delete_post( $id, true )`.
3. Deletes all `real3dflipbook_last_page_*` user meta entries.
4. Deletes all `flipbook_pdf_*` transients.
