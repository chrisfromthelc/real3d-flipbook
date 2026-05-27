# Gutenberg Block

The plugin registers a Gutenberg block `r3dfb/embed` for embedding flipbooks in the block editor.

## Block Registration

### PHP Side

In `Real3DFlipbook::init()`:

```php
register_block_type( 'r3dfb/embed', array() );
```

The block is registered with an empty configuration array because rendering is handled client-side via the shortcode saved in the block's HTML output.

### JavaScript Side

The block is defined in `js/blocks.js`, loaded via `enqueue_block_editor_assets`. The available flipbooks are passed as an inline script variable `r3dfb` (an array of `{ id, name, mode, pdfUrl }` objects).

## Block Metadata

| Property    | Value                             |
| ----------- | --------------------------------- |
| Name        | `r3dfb/embed`                     |
| Title       | Real3D FlipBook                   |
| Description | Display PDF or images as flipbook |
| Icon        | `book` (dashicon)                 |
| Category    | `media`                           |

## Block Attributes

| Attribute | Type   | Default  | Description                                                                 |
| --------- | ------ | -------- | --------------------------------------------------------------------------- |
| `id`      | string | (none)   | Post ID of the selected flipbook.                                           |
| `pdf`     | string | (none)   | Direct URL to a PDF file (currently commented out in the UI).               |
| `mode`    | string | `normal` | Display mode: `normal`, `lightbox`, or `fullscreen`.                        |
| `pages`   | string | (empty)  | Semicolon-separated list of image URLs (currently commented out in the UI). |

## Editor Interface

The block's `edit` function renders:

1. A header text "Real3D Flipbook".
2. A `SelectControl` dropdown listing all existing flipbooks (from the `r3dfb` inline data). The list is sorted by ID in descending order.
3. An `InspectorControls` sidebar panel with:
   - A flipbook selector dropdown (same as above).
   - A mode selector dropdown (`Normal`, `Lightbox`, `Fullscreen`).

The PDF upload and image upload UI elements exist in the source code but are commented out.

## Save Output

The `save` function generates a `[real3dflipbook]` shortcode string stored as raw HTML:

```js
save: function save(props) {
  var shortcodeString = "[real3dflipbook";
  if (id) shortcodeString += ' id="' + id + '"';
  if (pdf) shortcodeString += ' pdf="' + pdf + '"';
  if (pages) shortcodeString += ' pages="' + pages + '"';
  if (mode) shortcodeString += ' mode="' + mode + '"';
  shortcodeString += "]";

  return el(wp.element.RawHTML, null, shortcodeString);
}
```

The saved post content contains the literal shortcode text, which is processed by the `[real3dflipbook]` shortcode handler on the front end.

## Dependencies

The block editor script depends on:

- `wp-block-editor`
- `wp-blocks`
- `wp-i18n`
- `wp-element`

## How Flipbook Data is Passed

In `enqueue_block_editor_assets()`, the plugin queries all published flipbooks and outputs them as an inline script:

```php
wp_add_inline_script(
    'r3dfb-block-js',
    'var r3dfb = ' . wp_json_encode( $books ) . ';',
    'before'
);
```

Each entry in `r3dfb` contains:

```json
{
  "id": 123,
  "name": "My Flipbook",
  "mode": "normal",
  "pdfUrl": "https://example.com/file.pdf"
}
```

## Usage

1. In the block editor, click the **+** inserter.
2. Search for "Real3D FlipBook" or find it under the **Media** category.
3. Select a flipbook from the dropdown.
4. Optionally change the mode in the block inspector sidebar.
5. Publish or update the post.

The shortcode will be rendered on the front end using the standard shortcode handler.
