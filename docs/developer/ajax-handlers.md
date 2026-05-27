# AJAX Handlers

The plugin registers several WordPress AJAX handlers for admin and front-end operations. All admin handlers verify nonces and capabilities before processing.

---

## Admin AJAX Handlers

### `r3d_save_general`

**File**: `includes/plugin-admin.php`
**Hook**: `wp_ajax_r3d_save_general`
**Nonce**: `r3d_nonce` (field: `security`)
**Capability**: `manage_options`

Saves global plugin settings. Accepts POST data with setting key-value pairs.

**Behavior**:

1. Validates nonce and `manage_options` capability.
2. Strips `security` and `action` keys from POST data.
3. Filters POST data against the allowed keys from `r3dfb_getDefaults()` plus `slug`, `manageFlipbooks`, and `wooShowThankyouFlipbook`.
4. Sanitizes all values via `r3d_sanitize_array()`.
5. If `slug` changed, sets `r3d_flush_rewrite_rules` to trigger a rewrite flush.
6. If `wooShowThankyouFlipbook` is set, saves it as a separate boolean option (`r3d_woo_show_thankyou_flipbook`).
7. If `manageFlipbooks` is set, maps the role name to a capability and saves it to `real3dflipbook_capability`:
   - Administrator -> `manage_options`
   - Shop Manager -> `manage_woocommerce`
   - Editor -> `publish_pages`
   - Author -> `edit_others_posts`
8. Saves the remaining data to the `real3dflipbook_global` option.

---

### `r3d_reset_general`

**File**: `includes/plugin-admin.php`
**Hook**: `wp_ajax_r3d_reset_general`
**Nonce**: `r3d_nonce` (field: `security`)
**Capability**: `manage_options`

Resets all global settings to defaults by calling `r3dfb_setDefaults()`, which deletes and recreates the `real3dflipbook_global` option with values from `r3dfb_getDefaults()`.

---

### `r3d_save_thumbnail`

**File**: `includes/plugin-admin.php`
**Hook**: `wp_ajax_r3d_save_thumbnail`
**Nonce**: `saving-real3d-flipbook` (field: `security`)
**Capability**: `upload_files`

Uploads a custom thumbnail image for a flipbook.

**POST Parameters**:

| Parameter | Type | Description                                               |
| --------- | ---- | --------------------------------------------------------- |
| `id`      | int  | Flipbook post ID.                                         |
| `file`    | file | Uploaded image file (jpg, jpeg, png, gif, webp; max 2MB). |

**Behavior**:

1. Validates the flipbook exists via `r3d_get_flipbook()`.
2. Creates the upload directory `wp-content/uploads/real3d-flipbook/flipbook_{id}/` if needed.
3. Validates file extension, size (2MB max), and that it is a valid image (`getimagesize()`).
4. Generates a unique filename with timestamp and random string.
5. Moves the uploaded file to the flipbook's upload directory.
6. Deletes the old thumbnail file if one exists (with path-traversal protection via `realpath()`).
7. Saves the new thumbnail URL to the flipbook's `lightboxThumbnailUrl` option.

**Success Response**: `{ success: true, data: { thumbnail_url: "..." } }`
**Error Response**: `{ success: false, data: { message: "..." } }`

---

### `r3d_import`

**File**: `includes/Real3DFlipbook.php`
**Hook**: `wp_ajax_r3d_import`
**Nonce**: `r3d_nonce` (field: `security`)
**Capability**: `manage_options`

Imports flipbooks from a JSON export.

**POST Parameters**:

| Parameter   | Type   | Description                                          |
| ----------- | ------ | ---------------------------------------------------- |
| `flipbooks` | string | JSON string containing an array of flipbook objects. |

**Behavior**:

1. Parses the JSON string.
2. Deletes all existing `r3d` posts (including trashed).
3. Iterates over the imported flipbooks:
   - If `id` is `global`, updates the `real3dflipbook_global` option.
   - If `id` is numeric, creates a new `r3d` post and saves the flipbook data via `r3d_save_flipbook()`.

**Warning**: This is a destructive operation that replaces all existing flipbooks.

---

### `r3d_get_json`

**File**: `includes/Real3DFlipbook.php`
**Hook**: `wp_ajax_r3d_get_json`
**Nonce**: `r3d_nonce` (field: `security`)
**Capability**: `manage_options`

Exports all flipbooks as JSON for backup/transfer.

**Response**: `{ success: true, data: { post_id: { ...options, post_status: "..." }, ... } }`

---

## Front-End AJAX Handlers

### `r3d_last_page`

**File**: `includes/Real3DFlipbook.php`
**Hook**: `wp_ajax_r3d_last_page`
**Nonce**: `nonce_flipbook_embed` (field: `nonce`)
**Capability**: Must be logged in.

Saves the last viewed page for the resume-reading feature.

**POST Parameters**:

| Parameter | Type | Description              |
| --------- | ---- | ------------------------ |
| `bookId`  | int  | Flipbook post ID.        |
| `page`    | int  | Last viewed page number. |

**Behavior**: Saves the page number to user meta key `real3dflipbook_last_page_{bookId}`.

---

### `pdf` (Secure PDF Proxy)

**File**: `includes/Real3DFlipbook.php`
**Hooks**: `wp_ajax_pdf`, `wp_ajax_nopriv_pdf`
**Nonce**: `nonce_flipbook_embed` (GET parameter: `_wpnonce`)

Serves a PDF file through an AJAX proxy to hide the real file URL. Used when the `securepdf` shortcode attribute is set.

**GET Parameters**:

| Parameter  | Type   | Description                                          |
| ---------- | ------ | ---------------------------------------------------- |
| `id`       | string | Unique identifier for the PDF (maps to a transient). |
| `_wpnonce` | string | WordPress nonce for verification.                    |

**Behavior**:

1. Verifies the nonce.
2. Looks up the file path from the `flipbook_pdf_{id}` transient.
3. Validates the file is within the uploads directory (path-traversal protection).
4. Verifies the file MIME type is `application/pdf`.
5. Supports HTTP Range requests for partial content delivery (PDF.js uses this).
6. Streams the file with appropriate headers (`Content-Type: application/pdf`, `Accept-Ranges: bytes`).

**Security measures**:

- `realpath()` canonicalization to prevent directory traversal.
- File must be within `wp_upload_dir()['basedir']`.
- MIME type verification via `finfo_file()`.
- Transients expire after 12 hours.
