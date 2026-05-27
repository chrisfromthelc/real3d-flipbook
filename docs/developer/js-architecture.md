# JavaScript Architecture

## Overview

The front-end JavaScript is organized around the `FLIPBOOK` global namespace. The core library (`flipbook.js`) defines the main class and UI framework. Rendering backends (WebGL, CSS3D, swipe, scroll) are loaded as separate modules that extend the core.

## Module Structure

### Core: `flipbook.js`

Defines the `FLIPBOOK` global object and the `FLIPBOOK.Main` class.

**Entry points**:

```js
// jQuery plugin interface
jQuery.fn.flipBook = function (options) {
  return new FLIPBOOK.Main(options, this);
};

// Vanilla JS interface
window.FlipBook = function (el, options) {
  return new FLIPBOOK.Main(options, el);
};
```

**`FLIPBOOK.Main`** is the primary class (ES6 class syntax with `static defaultOptions`). It handles:

- Option merging and validation
- Page management and lazy loading
- UI rendering (toolbar, sidebar, thumbnails, TOC, search)
- Event system
- Zoom and pan controls
- Keyboard and touch input
- Autoplay logic
- Print and download functionality
- Deep linking

**Key static properties**:

- `FLIPBOOK.Main.defaultOptions` -- Default option values for the JS side
- `FLIPBOOK.version` -- Library version string

**Utility functions on `FLIPBOOK`**:

- `FLIPBOOK.extend(deep, target, ...sources)` -- Deep merge (similar to `jQuery.extend`)
- `FLIPBOOK.escapeHtml(str)` -- HTML entity escaping

### WebGL Renderer: `flipbook.webgl.js`

Three.js-based 3D renderer. Loaded when `viewMode` is `webgl`. Depends on `three.min.js`.

Handles:

- WebGL context creation and management
- 3D page mesh generation with configurable segments
- PBR materials (roughness, metalness)
- Light and shadow setup
- Camera positioning (pan, tilt)
- Mouse-drag camera rotation
- Page flip physics (hardness, curl)
- Corner curl animation

### CSS3D Renderer: `flipbook.book3.js`

CSS 3D transform-based renderer for the `3d`/`2d` view modes. No external dependencies beyond the core.

### Swipe Renderer: `flipbook.swipe.js`

Horizontal swipe animation renderer for the `swipe` view mode.

### Scroll Renderer: `flipbook.scroll.js`

Vertical scroll renderer for the `scroll` view mode.

### PDF Service: `flipbook.pdfservice.min.js`

Bridge between the flipbook and PDF.js. Handles PDF loading, page rendering to canvas, text layer extraction, and link annotation parsing.

## Front-End Bootstrap: `embed.js`

This is the script that connects the PHP-rendered HTML/JSON to the JavaScript FlipBook instances. It runs on `DOMContentLoaded`.

**Flow**:

1. Finds all `.real3dflipbook` elements on the page.
2. Reads the global options from `<script id="real3dflipbook-global-options">` (JSON).
3. For each flipbook element, reads its per-book options from the corresponding `<script class="real3dflipbook-options" data-book-id="...">` element.
4. Converts string values to appropriate types (`"true"` to `true`, numeric strings to numbers, empty strings deleted).
5. Strips backslashes and decodes HTML content.
6. Deep-merges global and per-book options via `FLIPBOOK.extend()`.
7. Sets up asset paths (preloader, shadow, sound, PDF.js worker, CMap URL).
8. Expands `basePath` for page image URLs (an optimization that stores only relative paths in the JSON).
9. Creates a `FlipBook` instance based on the `mode`:
   - `normal` -- Creates the instance directly in the container element.
   - `lightbox` -- Builds thumbnail/text link elements, then creates the instance bound to all elements with the lightbox CSS class.
   - `fullscreen` -- Moves the element to `document.body`, adds the `flipbook-browser-fullscreen` class, creates the instance.
10. Attaches event listeners for notes and resume-reading (sends AJAX requests back to WordPress).

**URL parameter overrides**: Any URL query parameter prefixed with `r3d-` is applied as an option override. For example, `?r3d-startPage=5` opens the flipbook at page 5.

## PDF Link Conversion: `frontend.js`

Loaded only when `convertPDFLinks` is enabled in global settings. Runs on `DOMContentLoaded`.

**Flow**:

1. Finds all `<a href="...pdf">` links on the page.
2. Filters by `convertPDFLinksWithClass` (include) and `convertPDFLinksWithoutClass` (exclude) settings.
3. For each qualifying link, creates a `FlipBook` instance in lightbox mode with the link's `href` as the PDF URL.
4. Supports class-based option overrides: CSS classes on the link or its ancestors with the format `r3d-{key}-{value}` are parsed and applied as options.

If the FLIPBOOK core is not already loaded, `frontend.js` dynamically loads `flipbook.min.js` and `flipbook.min.css` via `Promise`-based script/link injection.

## Events

The `FlipBook` instance exposes an event system via `.on(eventName, callback)`. Known events used in `embed.js`:

| Event             | Description                   | Callback Data                        |
| ----------------- | ----------------------------- | ------------------------------------ |
| `r3d-update-note` | A note was created or updated | `{ note: {...} }`                    |
| `r3d-delete-note` | A note was deleted            | `{ note: {...} }`                    |
| `pagechange`      | The visible page changed      | `{ page: "N" }` (on the DOM element) |

## Data Flow Summary

```
PHP (shortcode handler)
  |
  +--> <div class="real3dflipbook" id="{bookId}">
  +--> <script class="real3dflipbook-options" data-book-id="{bookId}">{JSON}</script>
  +--> <script id="real3dflipbook-global-options">{JSON}</script>  (once per page)
  |
embed.js (DOMContentLoaded)
  |
  +--> Parse JSON from <script> elements
  +--> Merge global + per-book options
  +--> new FlipBook(element, mergedOptions)
         |
         +--> FLIPBOOK.Main constructor
                |
                +--> Load renderer (webgl / book3 / swipe / scroll)
                +--> Load PDF via pdf.js (if pdfUrl set)
                +--> Render UI (toolbar, sidebar, thumbnails)
```

## Script Dependencies (wp_register_script)

| Handle                        | File                         | Dependencies                                 |
| ----------------------------- | ---------------------------- | -------------------------------------------- |
| `real3d-flipbook`             | `flipbook.min.js`            | (none)                                       |
| `real3d-flipbook-book3`       | `flipbook.book3.min.js`      | `real3d-flipbook`                            |
| `real3d-flipbook-bookswipe`   | `flipbook.swipe.min.js`      | `real3d-flipbook`                            |
| `real3d-flipbook-threejs`     | `libs/three.min.js`          | (none)                                       |
| `real3d-flipbook-webgl`       | `flipbook.webgl.min.js`      | `real3d-flipbook`, `real3d-flipbook-threejs` |
| `real3d-flipbook-pdfjs`       | `libs/pdf.min.js`            | (none)                                       |
| `real3d-flipbook-pdfworkerjs` | `libs/pdf.worker.min.js`     | (none)                                       |
| `real3d-flipbook-pdfservice`  | `flipbook.pdfservice.min.js` | (none)                                       |
| `real3d-flipbook-embed`       | `embed.js`                   | `real3d-flipbook`                            |

The `embed.js` script also receives inline data:

```js
var r3d = { ajax_url: "...", nonce: "..." };
```

This is used for AJAX calls (resume reading, notes).
