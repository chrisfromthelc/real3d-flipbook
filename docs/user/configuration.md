# Configuration

All settings are configured globally in **Real3D Flipbook > Settings**. Individual flipbooks can override any global setting on their edit screen. Settings are stored in the `real3dflipbook_global` option and as `r3d_flipbook_options` post meta for per-flipbook overrides.

The global defaults are defined in the `r3dfb_getDefaults()` function in `includes/Real3DFlipbook.php`.

---

## General Settings

| Key                      | Type   | Default  | Description                                                                                             |
| ------------------------ | ------ | -------- | ------------------------------------------------------------------------------------------------------- |
| `mode`                   | string | `normal` | Display mode: `normal` (inline), `lightbox` (popup), or `fullscreen`.                                   |
| `viewMode`               | string | `webgl`  | Rendering engine: `webgl`, `3d`, `2d`, `swipe`, `scroll`, or `simple`.                                  |
| `startPage`              | string | `1`      | Page number to display when the flipbook first opens.                                                   |
| `pageNumberOffset`       | string | `0`      | Offset applied to displayed page numbers (e.g., set to `-2` if page 1 in the PDF is actually page iii). |
| `sound`                  | string | `true`   | Enable page-flip sound effect.                                                                          |
| `backgroundMusic`        | string | (empty)  | URL to a background music MP3 file.                                                                     |
| `rightToLeft`            | string | `false`  | Enable right-to-left page flipping.                                                                     |
| `singlePageMode`         | string | `false`  | Display one page at a time instead of a two-page spread.                                                |
| `pageFlipDuration`       | string | `1`      | Duration of the flip animation in seconds (recommended 0.5--2).                                         |
| `responsiveView`         | string | `true`   | Switch from two-page to single-page layout when container width is below the threshold.                 |
| `responsiveViewTreshold` | string | `768`    | Container width in pixels below which responsive view activates.                                        |
| `responsiveViewRatio`    | string | `1`      | Width/height ratio below which responsive view activates.                                               |
| `minimalView`            | string | `true`   | Show only fullscreen button and navigation arrows when container width is below the breakpoint.         |
| `minimalViewBreakpoint`  | string | `600`    | Container width in pixels below which minimal view activates.                                           |
| `height`                 | string | `400`    | Container height in pixels (used as a base for calculations).                                           |
| `responsiveHeight`       | string | `true`   | Automatically adjust container height based on width.                                                   |
| `containerRatio`         | string | (empty)  | Width/height ratio of the flipbook container. Overrides automatic sizing when set.                      |
| `cover`                  | string | `true`   | Display a front cover (first page displayed alone).                                                     |
| `backCover`              | string | `true`   | Display a back cover (last page displayed alone).                                                       |
| `scaleCover`             | string | `false`  | Force cover and spread sizing when all pages are the same dimensions.                                   |
| `pageCaptions`           | string | `false`  | Show page captions.                                                                                     |
| `pageWidth`              | string | (empty)  | Override page width in pixels.                                                                          |
| `pageHeight`             | string | (empty)  | Override page height in pixels.                                                                         |

## PDF Settings

| Key                          | Type   | Default | Description                                                                          |
| ---------------------------- | ------ | ------- | ------------------------------------------------------------------------------------ |
| `pdfUrl`                     | string | (empty) | URL to the PDF file.                                                                 |
| `printPdfUrl`                | string | (empty) | Alternate PDF URL used for printing.                                                 |
| `pageTextureSize`            | string | `3000`  | Height in pixels of rendered PDF pages at full resolution.                           |
| `pageTextureSizeSmall`       | string | `1500`  | Height in pixels of rendered PDF pages at reduced resolution.                        |
| `pageTextureSizeMobile`      | string | `1500`  | Full-resolution page height for mobile devices.                                      |
| `pageTextureSizeMobileSmall` | string | `1000`  | Reduced-resolution page height for mobile devices.                                   |
| `rangeChunkSize`             | string | `256`   | PDF range request chunk size in KB. Larger values are better for big PDFs.           |
| `minPixelRatio`              | string | `1`     | Override device pixel ratio to force higher quality in WebGL.                        |
| `pdfTextLayer`               | string | `true`  | Enable the PDF text layer for text selection and search. Disable for faster loading. |
| `pdfAutoLinks`               | string | `false` | Automatically convert PDF text to clickable links.                                   |
| `disableRange`               | string | `false` | Disable partial PDF download (range requests).                                       |
| `pdfBrowserViewerIfIE`       | string | `false` | Download PDF instead of displaying flipbook in Internet Explorer.                    |

## Zoom Settings

| Key               | Type   | Default | Description                                                                                 |
| ----------------- | ------ | ------- | ------------------------------------------------------------------------------------------- |
| `zoomMin`         | string | `0.9`   | Initial zoom level (recommended 0.8--1).                                                    |
| `zoomStep`        | string | `2`     | Zoom multiplier per step (1.1--4).                                                          |
| `zoomSize`        | string | (empty) | Override maximum zoom. For example, `4000` zooms until the page height on screen is 4000px. |
| `zoomReset`       | string | `false` | Reset zoom after page flip, window resize, or panel toggle.                                 |
| `doubleClickZoom` | string | `true`  | Enable double-click to zoom.                                                                |
| `pageDrag`        | string | `true`  | Enable turning pages with click-and-drag.                                                   |

## Navigation Settings

| Key                                | Type   | Default | Description                                                         |
| ---------------------------------- | ------ | ------- | ------------------------------------------------------------------- |
| `touchSwipeEnabled`                | string | `true`  | Enable touch swipe to turn pages.                                   |
| `fitToWidth`                       | string | `false` | Fit flipbook to container width (scroll view mode).                 |
| `arrowsAlwaysEnabledForNavigation` | string | `true`  | Enable keyboard arrows for navigation even when not in fullscreen.  |
| `arrowsDisabledNotFullscreen`      | string | `true`  | Disable side arrows when not in fullscreen.                         |
| `sideNavigationButtons`            | string | `true`  | Show side arrow buttons.                                            |
| `menuNavigationButtons`            | string | `false` | Show arrow buttons in the menu bar.                                 |
| `rightClickEnabled`                | string | `true`  | Allow right-click context menu. Disable to prevent image downloads. |

## Deep Linking

| Key                    | Type   | Default | Description                                                               |
| ---------------------- | ------ | ------- | ------------------------------------------------------------------------- |
| `deeplinking[enabled]` | string | `false` | Enable URL hash deep linking (e.g., `#2` opens page 2).                   |
| `deeplinking[prefix]`  | string | (empty) | Prefix for deep link hashes (e.g., `book1_` makes links like `#book1_2`). |

## Autoplay Settings

| Key                 | Type   | Default | Description                                 |
| ------------------- | ------ | ------- | ------------------------------------------- |
| `autoplayOnStart`   | string | `false` | Automatically start flipping pages on load. |
| `autoplayInterval`  | string | `3000`  | Time between auto-flips in milliseconds.    |
| `autoplayLoop`      | string | `true`  | Loop back to the first page after the last. |
| `autoplayStartPage` | string | `1`     | Page to begin autoplay from.                |

## Thumbnail and Table of Contents

| Key                          | Type   | Default | Description                                               |
| ---------------------------- | ------ | ------- | --------------------------------------------------------- |
| `thumbSize`                  | string | `130`   | Thumbnail height in pixels for the thumbnails panel.      |
| `thumbnailsOnStart`          | string | `false` | Show the thumbnails panel when the flipbook opens.        |
| `contentOnStart`             | string | `false` | Show the table of contents panel when the flipbook opens. |
| `searchOnStart`              | string | (empty) | Pre-fill the search box with this query on open.          |
| `searchResultsThumbs`        | string | `false` | Display search results as page thumbnails.                |
| `tableOfContentCloseOnClick` | string | `true`  | Close the TOC panel when a page link is clicked.          |
| `thumbsCloseOnClick`         | string | `true`  | Close the thumbnails panel when a thumbnail is clicked.   |

## Lightbox Settings

| Key                         | Type   | Default                                                                                              | Description                                                 |
| --------------------------- | ------ | ---------------------------------------------------------------------------------------------------- | ----------------------------------------------------------- |
| `lightboxBackground`        | string | `rgb(81, 85, 88)`                                                                                    | Overlay background color.                                   |
| `lightboxBackgroundPattern` | string | (empty)                                                                                              | Overlay background pattern image (tiled).                   |
| `lightboxBackgroundImage`   | string | (empty)                                                                                              | Overlay background image (single).                          |
| `lightboxContainerCSS`      | string | `display:inline-block;padding:10px;`                                                                 | CSS for the thumbnail container element.                    |
| `lightboxThumbnailUrl`      | string | (empty)                                                                                              | URL of the thumbnail image shown before the lightbox opens. |
| `lightboxThumbnailHeight`   | string | `300`                                                                                                | Height of the auto-generated thumbnail from PDF.            |
| `lightboxThumbnailUrlCSS`   | string | `display:block;`                                                                                     | CSS applied to the thumbnail `<img>`.                       |
| `lightboxThumbnailInfo`     | string | `false`                                                                                              | Show info overlay on thumbnail hover.                       |
| `lightboxThumbnailInfoText` | string | (empty)                                                                                              | Text for the info overlay (defaults to book name).          |
| `lightboxThumbnailInfoCSS`  | string | `top: 0; width: 100%; height: 100%; font-size: 16px; color: #000; background: rgba(255,255,255,.8);` | CSS for the info overlay.                                   |
| `lightboxCssClass`          | string | (empty)                                                                                              | CSS class applied to the lightbox trigger element.          |
| `lightboxLink`              | string | (empty)                                                                                              | URL to open on click instead of the flipbook lightbox.      |
| `lightboxLinkNewWindow`     | string | `true`                                                                                               | Open the lightbox link in a new window.                     |
| `lightboxText`              | string | (empty)                                                                                              | Text link displayed alongside the thumbnail.                |
| `lightboxTextCSS`           | string | `display:block;`                                                                                     | CSS for the text link.                                      |
| `lightboxTextPosition`      | string | `top`                                                                                                | Position of the text link: `top` or `bottom`.               |
| `lightBoxOpened`            | string | `false`                                                                                              | Open the lightbox automatically on page load.               |
| `lightBoxFullscreen`        | string | `false`                                                                                              | Open the lightbox in real browser fullscreen.               |
| `lightboxStartPage`         | string | (empty)                                                                                              | Always open the lightbox at this specific page.             |
| `lightboxMarginV`           | string | `0`                                                                                                  | Vertical margin of the lightbox overlay in pixels.          |
| `lightboxMarginH`           | string | `0`                                                                                                  | Horizontal margin of the lightbox overlay in pixels.        |
| `showTitle`                 | string | `false`                                                                                              | Show the flipbook title in the lightbox.                    |
| `showDate`                  | string | `false`                                                                                              | Show the flipbook date in the lightbox.                     |
| `hideThumbnail`             | string | `false`                                                                                              | Hide the clickable thumbnail entirely.                      |

## WebGL Settings

| Key                       | Type   | Default   | Description                                                                           |
| ------------------------- | ------ | --------- | ------------------------------------------------------------------------------------- |
| `lights`                  | string | `true`    | Enable realistic lighting. Disable for better performance.                            |
| `lightPositionX`          | string | `0`       | Light X position (-500 to 500).                                                       |
| `lightPositionY`          | string | `150`     | Light Y position (-500 to 500).                                                       |
| `lightPositionZ`          | string | `1400`    | Light Z position (1000 to 2000).                                                      |
| `lightIntensity`          | string | `0.6`     | Light intensity (0 to 1).                                                             |
| `shadows`                 | string | `true`    | Enable page shadows. Disable for better performance.                                  |
| `shadowMapSize`           | string | `2048`    | Shadow map resolution.                                                                |
| `shadowOpacity`           | string | `0.2`     | Shadow opacity (0 to 1).                                                              |
| `shadowDistance`          | string | `15`      | Shadow distance from the page.                                                        |
| `pageHardness`            | string | `2`       | Page stiffness during flip (1 to 5).                                                  |
| `coverHardness`           | string | `2`       | Cover stiffness during flip (1 to 5).                                                 |
| `pageRoughness`           | string | `1`       | Page material roughness for PBR rendering (0 to 1).                                   |
| `pageMetalness`           | string | `0`       | Page material metalness for PBR rendering (0 to 1).                                   |
| `pageSegmentsW`           | string | `6`       | Number of horizontal segments per page mesh (3 to 20). More segments = smoother flip. |
| `pageSegmentsH`           | string | `1`       | Number of vertical segments per page mesh.                                            |
| `pagesInMemory`           | string | `20`      | Number of pages kept in WebGL memory. Reduce for large books.                         |
| `bitmapResizeHeight`      | string | (empty)   | Resize bitmap images to this height before WebGL rendering.                           |
| `bitmapResizeQuality`     | string | (empty)   | Bitmap resize quality: `low`, `medium`, or `high`.                                    |
| `pageMiddleShadowSize`    | string | `4`       | Size of the shadow in the book spine.                                                 |
| `pageMiddleShadowColorL`  | string | `#7F7F7F` | Left page spine shadow color.                                                         |
| `pageMiddleShadowColorR`  | string | `#AAAAAA` | Right page spine shadow color.                                                        |
| `antialias`               | string | `false`   | Enable WebGL antialiasing. Disable for better performance.                            |
| `pan`                     | string | `0`       | Camera horizontal rotation angle (-10 to 10).                                         |
| `tilt`                    | string | `0`       | Camera vertical tilt angle (-30 to 0).                                                |
| `rotateCameraOnMouseDrag` | string | `true`    | Allow camera rotation by dragging.                                                    |
| `panMax`                  | string | `20`      | Maximum camera pan angle (0 to 20).                                                   |
| `panMin`                  | string | `-20`     | Minimum camera pan angle (-20 to 0).                                                  |
| `tiltMax`                 | string | `0`       | Maximum camera tilt angle (-60 to 0).                                                 |
| `tiltMin`                 | string | `0`       | Minimum camera tilt angle (-60 to 0).                                                 |
| `cornerCurl`              | string | `false`   | Show corner curl animation on the cover page.                                         |

## Mobile Settings

| Key                             | Type   | Default | Description                                   |
| ------------------------------- | ------ | ------- | --------------------------------------------- |
| `modeMobile`                    | string | (empty) | Override display mode for mobile devices.     |
| `viewModeMobile`                | string | (empty) | Override rendering mode for mobile devices.   |
| `aspectMobile`                  | string | (empty) | Override aspect ratio for mobile.             |
| `aspectRatioMobile`             | string | `0.71`  | Mobile aspect ratio.                          |
| `singlePageModeIfMobile`        | string | `false` | Force single-page view on mobile.             |
| `logoHideOnMobile`              | string | `false` | Hide the logo on mobile devices.              |
| `mobile[thumbnailsOnStart]`     | string | `false` | Show thumbnails panel on start (mobile).      |
| `mobile[contentOnStart]`        | string | `false` | Show TOC panel on start (mobile).             |
| `mobile[pagesInMemory]`         | string | `6`     | Pages kept in memory on mobile (WebGL mode).  |
| `mobile[bitmapResizeHeight]`    | string | (empty) | Resize bitmaps to this height on mobile.      |
| `mobile[bitmapResizeQuality]`   | string | (empty) | Bitmap resize quality on mobile.              |
| `mobile[currentPage][enabled]`  | string | `false` | Show current page indicator on mobile.        |
| `mobile[pdfUrl]`                | string | (empty) | Alternate PDF URL for mobile devices.         |
| `mobile[minimalViewBreakpoint]` | string | `360`   | Minimal view breakpoint for mobile in pixels. |

## UI and Appearance

| Key                     | Type    | Default           | Description                                   |
| ----------------------- | ------- | ----------------- | --------------------------------------------- |
| `layout`                | integer | `1`               | Premade UI layout (1, 2, 3, or 4).            |
| `icons`                 | string  | `FontAwesome`     | Icon set: `FontAwesome` or `material`.        |
| `skin`                  | string  | `light`           | Premade skin: `light`, `dark`, or `gradient`. |
| `useFontAwesome5`       | string  | `true`            | Use Font Awesome 5 icons.                     |
| `skinColor`             | string  | (empty)           | Global UI text/icon color.                    |
| `skinBackground`        | string  | (empty)           | Global UI background color.                   |
| `backgroundColor`       | string  | `rgb(81, 85, 88)` | Flipbook container background color.          |
| `backgroundPattern`     | string  | (empty)           | Container background pattern image (tiled).   |
| `backgroundImage`       | string  | (empty)           | Container background image.                   |
| `backgroundTransparent` | string  | `false`           | Make the container background transparent.    |
| `hideMenu`              | string  | `false`           | Hide the menu bar entirely.                   |
| `menuAlignHorizontal`   | string  | `center`          | Horizontal alignment of the menu bar.         |
| `zIndex`                | string  | `auto`            | CSS z-index of the flipbook container.        |
| `preloaderText`         | string  | (empty)           | Text displayed under the preloader spinner.   |

## Bottom Menu Bar

| Key               | Type   | Default | Description                            |
| ----------------- | ------ | ------- | -------------------------------------- |
| `menuBackground`  | string | (empty) | Bottom menu background color.          |
| `menuShadow`      | string | (empty) | Bottom menu box shadow CSS.            |
| `menuMargin`      | string | `0`     | Bottom menu margin.                    |
| `menuPadding`     | string | `0`     | Bottom menu padding.                   |
| `menuOverBook`    | string | `false` | Bottom menu overlays the book.         |
| `menuFloating`    | string | `false` | Floating bottom menu (not full width). |
| `menuTransparent` | string | `false` | Transparent bottom menu background.    |

## Top Menu Bar

| Key                | Type   | Default | Description                      |
| ------------------ | ------ | ------- | -------------------------------- |
| `menu2Background`  | string | (empty) | Top menu background color.       |
| `menu2Shadow`      | string | (empty) | Top menu box shadow CSS.         |
| `menu2Margin`      | string | `0`     | Top menu margin.                 |
| `menu2Padding`     | string | `0`     | Top menu padding.                |
| `menu2OverBook`    | string | `true`  | Top menu overlays the book.      |
| `menu2Floating`    | string | `false` | Floating top menu.               |
| `menu2Transparent` | string | `true`  | Transparent top menu background. |

## Menu Button Styling

| Key             | Type   | Default | Description                          |
| --------------- | ------ | ------- | ------------------------------------ |
| `btnColor`      | string | (empty) | Button icon color.                   |
| `btnColorHover` | string | (empty) | Button icon hover color.             |
| `btnBackground` | string | `none`  | Button background color.             |
| `btnRadius`     | string | `0`     | Button border radius in pixels.      |
| `btnMargin`     | string | `0`     | Button margin in pixels.             |
| `btnSize`       | string | `18`    | Button icon size in pixels.          |
| `btnPaddingV`   | string | `10`    | Button vertical padding in pixels.   |
| `btnPaddingH`   | string | `10`    | Button horizontal padding in pixels. |
| `btnShadow`     | string | (empty) | Button box shadow CSS.               |
| `btnTextShadow` | string | (empty) | Button text shadow CSS.              |
| `btnBorder`     | string | (empty) | Button border CSS.                   |

## Side Arrow Buttons

| Key                    | Type   | Default                        | Description                    |
| ---------------------- | ------ | ------------------------------ | ------------------------------ |
| `arrowColor`           | string | `#fff`                         | Arrow color.                   |
| `arrowColorHover`      | string | `#fff`                         | Arrow hover color.             |
| `arrowBackground`      | string | `rgba(0,0,0,0)`                | Arrow background color.        |
| `arrowBackgroundHover` | string | `rgba(0, 0, 0, .15)`           | Arrow background hover color.  |
| `arrowRadius`          | string | `4`                            | Arrow border radius in pixels. |
| `arrowMargin`          | string | `4`                            | Arrow margin in pixels.        |
| `arrowSize`            | string | `40`                           | Arrow icon size in pixels.     |
| `arrowPadding`         | string | `10`                           | Arrow padding in pixels.       |
| `arrowTextShadow`      | string | `0px 0px 1px rgba(0, 0, 0, 1)` | Arrow text shadow CSS.         |
| `arrowBorder`          | string | (empty)                        | Arrow border CSS.              |

## Close Button (Lightbox)

| Key                  | Type   | Default          | Description                    |
| -------------------- | ------ | ---------------- | ------------------------------ |
| `closeBtnColorHover` | string | `#FFF`           | Close button hover color.      |
| `closeBtnBackground` | string | `rgba(0,0,0,.4)` | Close button background color. |
| `closeBtnRadius`     | string | `0`              | Close button border radius.    |
| `closeBtnMargin`     | string | `0`              | Close button margin.           |
| `closeBtnSize`       | string | `20`             | Close button icon size.        |
| `closeBtnPadding`    | string | `5`              | Close button padding.          |
| `closeBtnTextShadow` | string | (empty)          | Close button text shadow CSS.  |
| `closeBtnBorder`     | string | (empty)          | Close button border CSS.       |

## Floating Buttons (Transparent Menu)

| Key                          | Type   | Default | Description                       |
| ---------------------------- | ------ | ------- | --------------------------------- |
| `floatingBtnColor`           | string | (empty) | Floating button color.            |
| `floatingBtnColorHover`      | string | (empty) | Floating button hover color.      |
| `floatingBtnBackground`      | string | (empty) | Floating button background.       |
| `floatingBtnBackgroundHover` | string | (empty) | Floating button background hover. |
| `floatingBtnRadius`          | string | (empty) | Floating button border radius.    |
| `floatingBtnMargin`          | string | (empty) | Floating button margin.           |
| `floatingBtnSize`            | string | (empty) | Floating button size.             |
| `floatingBtnPadding`         | string | (empty) | Floating button padding.          |
| `floatingBtnShadow`          | string | (empty) | Floating button box shadow CSS.   |
| `floatingBtnTextShadow`      | string | (empty) | Floating button text shadow CSS.  |
| `floatingBtnBorder`          | string | (empty) | Floating button border CSS.       |

## Current Page Indicator

| Key                    | Type   | Default        | Description                              |
| ---------------------- | ------ | -------------- | ---------------------------------------- |
| `currentPage[enabled]` | string | `true`         | Show the current page indicator.         |
| `currentPage[title]`   | string | `Current page` | Tooltip text.                            |
| `currentPage[hAlign]`  | string | `left`         | Horizontal alignment.                    |
| `currentPage[vAlign]`  | string | `top`          | Vertical alignment.                      |
| `currentPageMarginV`   | string | `5`            | Vertical margin of the page indicator.   |
| `currentPageMarginH`   | string | `5`            | Horizontal margin of the page indicator. |

## Toolbar Buttons

Each button is an array with `enabled` (string `true`/`false`) and `title` (translatable string) keys. Some buttons have additional keys.

| Button Key         | Default Enabled | Default Title      | Extra Keys                                                  |
| ------------------ | --------------- | ------------------ | ----------------------------------------------------------- |
| `btnAutoplay`      | `true`          | Auto flip          |                                                             |
| `btnNext`          | `true`          | Next Page          |                                                             |
| `btnPrev`          | `true`          | Previous Page      |                                                             |
| `btnFirst`         | `false`         | First Page         |                                                             |
| `btnLast`          | `false`         | Last Page          |                                                             |
| `btnZoomIn`        | `true`          | Zoom in            |                                                             |
| `btnZoomOut`       | `true`          | Zoom out           |                                                             |
| `btnToc`           | `true`          | Table of Contents  |                                                             |
| `btnThumbs`        | `true`          | Pages              |                                                             |
| `btnShare`         | `true`          | Share              |                                                             |
| `btnNotes`         | `false`         | Notes              |                                                             |
| `btnDownloadPages` | `false`         | Download pages     | `url`: download URL                                         |
| `btnDownloadPdf`   | `true`          | Download PDF       | `url`, `forceDownload` (`true`), `openInNewWindow` (`true`) |
| `btnSound`         | `true`          | Sound              |                                                             |
| `btnExpand`        | `true`          | Toggle fullscreen  |                                                             |
| `btnSingle`        | `true`          | Toggle single page |                                                             |
| `btnSearch`        | `false`         | Search             |                                                             |
| `search`           | `false`         | Search             | (search input field)                                        |
| `btnBookmark`      | `false`         | Bookmark           |                                                             |
| `btnPrint`         | `true`          | Print              |                                                             |
| `btnTools`         | `true`          | More               |                                                             |
| `btnClose`         | `true`          | Close              |                                                             |

## Social Sharing

| Key                  | Type   | Default Enabled | Description                                         |
| -------------------- | ------ | --------------- | --------------------------------------------------- |
| `whatsapp[enabled]`  | string | `true`          | Show WhatsApp share button.                         |
| `twitter[enabled]`   | string | `true`          | Show Twitter/X share button.                        |
| `facebook[enabled]`  | string | `true`          | Show Facebook share button.                         |
| `pinterest[enabled]` | string | `true`          | Show Pinterest share button.                        |
| `email[enabled]`     | string | `true`          | Show email share button.                            |
| `linkedin[enabled]`  | string | `true`          | Show LinkedIn share button.                         |
| `digg[enabled]`      | string | `false`         | Show Digg share button.                             |
| `reddit[enabled]`    | string | `false`         | Show Reddit share button.                           |
| `shareUrl`           | string | (empty)         | Custom URL to share (defaults to current page URL). |
| `shareTitle`         | string | (empty)         | Custom title for sharing.                           |
| `shareImage`         | string | (empty)         | Custom image URL for sharing.                       |

## Link Styling

| Key              | Type   | Default                | Description                                     |
| ---------------- | ------ | ---------------------- | ----------------------------------------------- |
| `linkColor`      | string | `rgba(0, 0, 0, 0)`     | Color of clickable link areas on PDF pages.     |
| `linkColorHover` | string | `rgba(255, 255, 0, 1)` | Hover color of link areas.                      |
| `linkOpacity`    | string | `0.4`                  | Opacity of link highlight areas.                |
| `linkTarget`     | string | `_blank`               | Link target: `_blank`, `_self`, or `spotlight`. |

## Logo

| Key             | Type   | Default                           | Description                                        |
| --------------- | ------ | --------------------------------- | -------------------------------------------------- |
| `logoImg`       | string | (empty)                           | URL of a logo image displayed inside the flipbook. |
| `logoUrl`       | string | (empty)                           | URL opened when the logo is clicked.               |
| `logoUrlTarget` | string | (empty)                           | Logo link target: `_blank` or `_self`.             |
| `logoCSS`       | string | `position:absolute;left:0;top:0;` | CSS applied to the logo element.                   |

## Override Settings

These settings allow Real3D Flipbook to replace other flipbook/PDF plugins.

| Key                           | Type   | Default | Description                                                       |
| ----------------------------- | ------ | ------- | ----------------------------------------------------------------- |
| `convertPDFLinks`             | string | `true`  | Convert all `<a href="...pdf">` links to open in Real3D lightbox. |
| `convertPDFLinksWithClass`    | string | (empty) | Only convert PDF links with this CSS class.                       |
| `convertPDFLinksWithoutClass` | string | (empty) | Exclude PDF links with this CSS class.                            |
| `overridePDFEmbedder`         | string | `true`  | Replace `[pdf-embedder]` shortcode with Real3D.                   |
| `overrideDflip`               | string | `true`  | Replace `[dflip]` shortcode with Real3D.                          |
| `overrideWonderPDFEmbed`      | string | `true`  | Replace `[wonderplugin_pdf]` shortcode with Real3D.               |
| `override3DFlipBook`          | string | `true`  | Replace `[3d-flip-book]` shortcode with Real3D.                   |
| `overridePDFjsViewer`         | string | `true`  | Replace `[pdfjs-viewer]` shortcode with Real3D.                   |

## Advanced Settings

| Key                           | Type   | Default | Description                                                                                                     |
| ----------------------------- | ------ | ------- | --------------------------------------------------------------------------------------------------------------- |
| `slug`                        | string | (empty) | Custom rewrite slug for the flipbook post type (default `flipbook`). Requires a Permalinks save after changing. |
| `resumeReading`               | string | `false` | Save and restore the last viewed page for logged-in users.                                                      |
| `access`                      | string | `free`  | Direct access control for flipbook permalinks: `free`, `woo_subscription`, or `none`.                           |
| `previewPages`                | string | (empty) | Number of pages to show in preview mode.                                                                        |
| `previewMode`                 | string | (empty) | When to show preview: empty (never), `logged_out`, or `woo_purchased_or_subscription`.                          |
| `googleAnalyticsTrackingCode` | string | (empty) | Google Analytics tracking code.                                                                                 |
| `menuSelector`                | string | (empty) | CSS selector for a fixed navigation bar (used in fullscreen mode to position the flipbook below it).            |

## PDF Tools Settings

These settings control the PDF Tools Addon (requires separate addon installation).

| Key                     | Type    | Default | Description                                      |
| ----------------------- | ------- | ------- | ------------------------------------------------ |
| `pdfTools[pageHeight]`  | integer | `1500`  | Height of converted page images in pixels.       |
| `pdfTools[thumbHeight]` | integer | `200`   | Height of converted thumbnail images in pixels.  |
| `pdfTools[quality]`     | float   | `0.8`   | JPEG quality for converted images (0 to 1).      |
| `pdfTools[textLayer]`   | string  | `true`  | Include PDF text layer data in converted output. |
| `pdfTools[autoConvert]` | string  | `true`  | Automatically convert PDFs when saved.           |

## Translatable Strings

All UI strings can be customized in the **Translate** tab. They are stored in the `strings` array. See the full list of string keys in the `r3dfb_getDefaults()` function. The plugin also supports WordPress i18n via the `real3d-flipbook` text domain.
