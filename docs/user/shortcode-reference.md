# Shortcode Reference

The plugin registers the `[real3dflipbook]` shortcode. It is handled by `Real3DFlipbook::on_shortcode()` in `includes/Real3DFlipbook.php`.

## Basic Usage

```
[real3dflipbook id="123"]
```

## All Attributes

Every attribute below defaults to `-1` (unset), which means the value falls through to the per-flipbook setting, then to the global default.

### Source Attributes

| Attribute | Maps To            | Type   | Description                                                                                              |
| --------- | ------------------ | ------ | -------------------------------------------------------------------------------------------------------- |
| `id`      | (flipbook post ID) | string | Post ID of the flipbook to display. Use `id="all"` to display every published flipbook in lightbox mode. |
| `name`    | (flipbook title)   | string | Title of the flipbook (alternative to `id`). Resolved via `r3d_resolve_flipbook_by_name()`.              |
| `pdf`     | `pdfUrl`           | string | Direct URL to a PDF file. Overrides the flipbook's saved PDF URL.                                        |
| `pages`   | `pages`            | string | Comma-separated list of image URLs to use as pages.                                                      |
| `thumbs`  | (page thumbs)      | string | Comma-separated list of thumbnail image URLs, matching the `pages` list.                                 |

### Display Attributes

| Attribute    | Maps To            | Type   | Description                                                                             |
| ------------ | ------------------ | ------ | --------------------------------------------------------------------------------------- |
| `mode`       | `mode`             | string | Display mode: `normal`, `lightbox`, or `fullscreen`.                                    |
| `viewmode`   | `viewMode`         | string | Rendering mode: `webgl`, `3d`, `2d`, `swipe`, `scroll`, or `simple`.                    |
| `class`      | `lightboxCssClass` | string | CSS class for the lightbox trigger. Also forces lightbox mode and clears the thumbnail. |
| `aspect`     | `containerRatio`   | string | Width/height ratio of the flipbook container.                                           |
| `singlepage` | `singlePageMode`   | string | `true` to show one page at a time.                                                      |
| `startpage`  | `startPage`        | string | Page number to open at.                                                                 |
| `hidemenu`   | `hideMenu`         | string | `true` to hide the toolbar.                                                             |
| `rtl`        | `rightToLeft`      | string | `true` for right-to-left page flipping.                                                 |

### Lightbox Attributes

| Attribute              | Maps To                   | Type   | Description                                                                                          |
| ---------------------- | ------------------------- | ------ | ---------------------------------------------------------------------------------------------------- |
| `thumb`                | `lightboxThumbnailUrl`    | string | URL of the thumbnail image for lightbox mode.                                                        |
| `thumbalt`             | `thumbAlt`                | string | Alt text for the lightbox thumbnail image.                                                           |
| `thumbcss`             | `lightboxThumbnailUrlCSS` | string | CSS applied to the thumbnail `<img>` element.                                                        |
| `containercss`         | `lightboxContainerCSS`    | string | CSS applied to the thumbnail container `<div>`.                                                      |
| `title`                | `lightboxText`            | string | Text displayed with the lightbox trigger. Use `true` to display the flipbook name, `false` to clear. |
| `lightboxtext`         | `lightboxText`            | string | Explicit text for the lightbox trigger (alternative to `title`).                                     |
| `lightboxcssclass`     | `lightboxCssClass`        | string | CSS class for the lightbox trigger element.                                                          |
| `lightboxthumbnail`    | (thumbnail config)        | string | Lightbox thumbnail setting.                                                                          |
| `lightboxthumbnailurl` | `lightboxThumbnailUrl`    | string | Explicit URL for the lightbox thumbnail.                                                             |
| `lightboxopened`       | `lightBoxOpened`          | string | `true` to auto-open the lightbox on page load.                                                       |
| `lightboxfullscreen`   | `lightBoxFullscreen`      | string | `true` to open the lightbox in real browser fullscreen.                                              |

### Autoplay Attributes

| Attribute          | Maps To            | Type   | Description                                  |
| ------------------ | ------------------ | ------ | -------------------------------------------- |
| `autoplayonstart`  | `autoplayOnStart`  | string | `true` to auto-flip pages on load.           |
| `autoplayinterval` | `autoplayInterval` | string | Interval between auto-flips in milliseconds. |
| `autoplayloop`     | `autoplayLoop`     | string | `true` to loop autoplay.                     |

### Navigation Attributes

| Attribute           | Maps To               | Type   | Description                                   |
| ------------------- | --------------------- | ------ | --------------------------------------------- |
| `pagenumberoffset`  | `pageNumberOffset`    | string | Offset for displayed page numbers.            |
| `deeplinkingprefix` | `deeplinking[prefix]` | string | Custom prefix for deep link URL hashes.       |
| `search`            | `searchOnStart`       | string | Pre-fill and activate search with this query. |
| `zoom`              | `zoomLevels`          | string | Zoom level configuration.                     |
| `zoomdisabled`      | `zoomDisabled`        | string | `true` to disable zooming.                    |

### PDF Control Attributes

| Attribute           | Maps To             | Type   | Description                                                                                     |
| ------------------- | ------------------- | ------ | ----------------------------------------------------------------------------------------------- |
| `btndownloadpdfurl` | `btnDownloadPdfUrl` | string | Custom URL for the Download PDF button.                                                         |
| `securepdf`         | `securePdf`         | flag   | When present, serves the PDF through an AJAX proxy endpoint instead of exposing the direct URL. |
| `pagerangestart`    | `pageRangeStart`    | string | First page of a page range to display.                                                          |
| `pagerangeend`      | `pageRangeEnd`      | string | Last page of a page range to display.                                                           |
| `previewpages`      | `previewPages`      | string | Number of pages to show in preview mode.                                                        |

### Filtering Attributes

| Attribute  | Maps To            | Type   | Description                                                                                       |
| ---------- | ------------------ | ------ | ------------------------------------------------------------------------------------------------- |
| `category` | (taxonomy query)   | string | Slug of a `r3d_category` taxonomy term. Displays all flipbooks in that category in lightbox mode. |
| `author`   | (taxonomy query)   | string | Slug of a `r3d_author` taxonomy term. Displays all flipbooks by that author in lightbox mode.     |
| `num`      | (posts_per_page)   | string | Number of flipbooks to display when using `category` or `author`.                                 |
| `order`    | (WP_Query order)   | string | Sort order: `ASC` or `DESC`. Used with `category`.                                                |
| `orderby`  | (WP_Query orderby) | string | Sort field (e.g., `date`, `title`). Used with `category`.                                         |

### Localization Attributes

| Attribute | Maps To              | Type   | Description                                                                                                                                 |
| --------- | -------------------- | ------ | ------------------------------------------------------------------------------------------------------------------------------------------- |
| `lang`    | (conditional render) | string | Only render the flipbook if the current site language matches this value (e.g., `en`, `fr`). Works with WPML, Polylang, and `get_locale()`. |

## Examples

### Embed a specific flipbook inline

```
[real3dflipbook id="42"]
```

### Embed by name

```
[real3dflipbook name="Annual Report 2025"]
```

### Lightbox with custom thumbnail

```
[real3dflipbook id="42" mode="lightbox" thumb="https://example.com/cover.jpg" thumbalt="Annual Report"]
```

### Direct PDF in fullscreen

```
[real3dflipbook pdf="https://example.com/report.pdf" mode="fullscreen"]
```

### Display all flipbooks

```
[real3dflipbook id="all"]
```

### Display flipbooks by category

```
[real3dflipbook category="newsletters" num="6" order="DESC" orderby="date"]
```

### Display flipbooks by author

```
[real3dflipbook author="john-smith"]
```

### Swipe mode with autoplay

```
[real3dflipbook id="42" viewmode="swipe" autoplayonstart="true" autoplayinterval="5000"]
```

### Secure PDF (proxy through AJAX)

```
[real3dflipbook id="42" securepdf="true"]
```

### Page range

```
[real3dflipbook id="42" pagerangestart="5" pagerangeend="10"]
```

### Right-to-left with deep linking

```
[real3dflipbook id="42" rtl="true" deeplinkingprefix="book_"]
```

### Language-specific rendering (WPML/Polylang)

```
[real3dflipbook id="42" lang="en"]
[real3dflipbook id="43" lang="fr"]
```

Only the shortcode matching the current site language will render.

### Lightbox with CSS class trigger

```
[real3dflipbook id="42" class="open-book-btn"]
```

Any element with the class `open-book-btn` will open the flipbook lightbox when clicked.

### Image-based flipbook

```
[real3dflipbook pages="https://example.com/p1.jpg,https://example.com/p2.jpg,https://example.com/p3.jpg" thumbs="https://example.com/t1.jpg,https://example.com/t2.jpg,https://example.com/t3.jpg"]
```
