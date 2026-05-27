# REST API

The plugin registers REST API endpoints under the `flipbook/v1` namespace via `Real3DFlipbook::register_flipbook_api_routes()` in `includes/Real3DFlipbook.php`.

## Authentication

All endpoints require the user to have the configured flipbook management capability. The permission callback is:

```php
public function rest_permission_callback() {
    $capability = self::validated_capability(
        get_option( 'real3dflipbook_capability', 'edit_others_posts' )
    );
    return current_user_can( $capability );
}
```

The capability is validated against an allowlist: `manage_options`, `manage_woocommerce`, `publish_pages`, `edit_others_posts`. Invalid values fall back to `edit_others_posts`.

Requests must include a valid WordPress authentication cookie or nonce (standard `X-WP-Nonce` header for REST API requests).

---

## POST `/wp-json/flipbook/v1/create`

Creates a new flipbook.

### Request Body (JSON)

| Parameter          | Type    | Required | Description                                                                                         |
| ------------------ | ------- | -------- | --------------------------------------------------------------------------------------------------- |
| `title`            | string  | Yes      | Title for the new flipbook.                                                                         |
| `attachmentId`     | integer | No       | WordPress media attachment ID for a PDF. If provided, the PDF URL is resolved from this attachment. |
| `pdfUrl`           | string  | No       | Direct URL to a PDF file. Used if `attachmentId` is not provided.                                   |
| `flipbook_options` | string  | No       | URL-encoded JSON string of additional flipbook options.                                             |

### Behavior

1. Validates the `title` is not empty.
2. Resolves the PDF URL from `attachmentId` (via `wp_get_attachment_url()`) or from `pdfUrl` directly.
3. Creates an `r3d` post with `post_status: publish`.
4. If the Imagick PHP extension is available, generates a thumbnail from the first page of the PDF.
5. Decodes and merges `flipbook_options` into the options array.
6. Saves the flipbook via `r3d_save_flipbook()`.

### Response (200)

```json
{
    "post_id": 123,
    "message": "Flipbook created",
    "flipbook_options": { ... }
}
```

### Error Responses

| Code | Error Key              | Description                                     |
| ---- | ---------------------- | ----------------------------------------------- |
| 400  | `missing_title`        | Title is required.                              |
| 400  | `invalid_attachment`   | The attachment ID does not exist or has no URL. |
| 400  | `invalid_url`          | The PDF URL is malformed.                       |
| 500  | `post_creation_failed` | `wp_insert_post()` returned a `WP_Error`.       |

---

## POST `/wp-json/flipbook/v1/update`

Updates an existing flipbook.

### Request Body (JSON)

| Parameter          | Type    | Required | Description                                           |
| ------------------ | ------- | -------- | ----------------------------------------------------- |
| `postId`           | integer | Yes      | Post ID of the flipbook to update.                    |
| `title`            | string  | No       | New title. If empty, the existing title is preserved. |
| `attachmentId`     | integer | No       | WordPress media attachment ID for a new PDF.          |
| `pdfUrl`           | string  | No       | Direct URL to a new PDF file.                         |
| `flipbook_options` | string  | No       | URL-encoded JSON string of flipbook options to merge. |

### Behavior

1. Validates that `postId` corresponds to an existing `r3d` post.
2. If `title` is provided, updates the post title via `wp_update_post()`.
3. Resolves the PDF URL from `attachmentId` or `pdfUrl` if provided.
4. If a new PDF URL is set, regenerates the thumbnail.
5. Decodes and merges `flipbook_options` into the existing options.
6. Saves via `r3d_save_flipbook()`.

### Response (200)

```json
{
    "post_id": 123,
    "message": "Flipbook updated",
    "flipbook_options": { ... }
}
```

### Error Responses

| Code | Error Key            | Description                                         |
| ---- | -------------------- | --------------------------------------------------- |
| 400  | `invalid_post`       | The post ID does not exist or is not an `r3d` post. |
| 400  | `invalid_attachment` | The attachment ID does not exist.                   |
| 400  | `invalid_url`        | The PDF URL is malformed.                           |

---

## Example: Create a flipbook with cURL

```bash
curl -X POST "https://example.com/wp-json/flipbook/v1/create" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: YOUR_NONCE" \
  --cookie "wordpress_logged_in_xxx=..." \
  -d '{
    "title": "Quarterly Report",
    "pdfUrl": "https://example.com/wp-content/uploads/report.pdf",
    "flipbook_options": "%7B%22mode%22%3A%22lightbox%22%7D"
  }'
```

## Example: Update a flipbook with cURL

```bash
curl -X POST "https://example.com/wp-json/flipbook/v1/update" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: YOUR_NONCE" \
  --cookie "wordpress_logged_in_xxx=..." \
  -d '{
    "postId": 123,
    "title": "Q2 Quarterly Report",
    "flipbook_options": "%7B%22viewMode%22%3A%22swipe%22%7D"
  }'
```

## Thumbnail Generation

Both endpoints attempt to generate a thumbnail from the PDF's first page using ImageMagick (the Imagick PHP extension). The process:

1. If the PDF is hosted on the same domain, resolves the local file path.
2. If remote, downloads the PDF via `wp_safe_remote_get()` to a temporary file.
3. Uses `Imagick::readImage()` to read the first page, renders it as JPEG at 72 DPI, resized to 200px wide.
4. Saves the thumbnail to `wp-content/uploads/real3d-flipbook/flipbook_{post_id}/thumbnail.jpg`.

If Imagick is not installed, thumbnail generation is skipped and a `WP_Error` is returned (but the flipbook is still created/updated successfully).
