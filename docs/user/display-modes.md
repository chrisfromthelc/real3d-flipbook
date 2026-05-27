# Display Modes

Real3D Flipbook has two independent mode axes: **rendering mode** (how pages flip) and **display mode** (where the flipbook appears on the page).

## Rendering Modes (viewMode)

The `viewMode` setting controls the visual rendering engine used for page flips.

### WebGL (3D)

- **Key**: `viewMode="webgl"` (default)
- Uses Three.js for realistic 3D page-turning with lighting, shadows, and paper physics.
- Supports configurable light position, intensity, shadow opacity, page hardness, roughness, and metalness.
- Requires a browser with WebGL support.
- Falls back to CSS3D automatically if WebGL is unavailable.
- Most visually impressive but most resource-intensive.

### CSS3D (2D/3D)

- **Key**: `viewMode="3d"` or `viewMode="2d"`
- Uses CSS 3D transforms for a flat page-flip animation.
- Lighter on resources than WebGL.
- Works in all modern browsers without WebGL.

### Swipe

- **Key**: `viewMode="swipe"`
- Horizontal swipe animation without 3D effects.
- Best for mobile or touch-heavy interfaces.
- Lowest resource usage.

### Scroll

- **Key**: `viewMode="scroll"`
- Pages displayed vertically in a scrollable container.
- No flip animation.

### Simple

- **Key**: `viewMode="simple"`
- No animation at all. Pages transition instantly.

### Setting the Rendering Mode

In the admin settings under the **General** tab, use the **View mode** dropdown. Per-flipbook overrides are set on the individual flipbook edit screen. Via shortcode:

```
[real3dflipbook id="123" viewmode="webgl"]
[real3dflipbook id="123" viewmode="swipe"]
```

Mobile devices can use a different rendering mode via the **Mobile** tab settings (`viewModeMobile`).

## Display Modes (mode)

The `mode` setting controls how the flipbook is placed on the page.

### Normal (Inline)

- **Key**: `mode="normal"` (default)
- Flipbook is embedded directly in the page content inside a responsive container `<div>`.
- The container height is calculated from its width (approximately width / 1.3 for landscape, width / 0.65 for portrait below the responsive threshold).
- Best for dedicated flipbook pages or when the flipbook is the primary content.

```
[real3dflipbook id="123" mode="normal"]
```

### Lightbox (Popup)

- **Key**: `mode="lightbox"`
- Displays a clickable thumbnail and/or text link. Clicking opens the flipbook in a fullscreen overlay.
- Configurable overlay background color, pattern, and image.
- Thumbnail image, height, CSS, info overlay, and text link are all configurable.
- The lightbox can be set to open automatically on page load (`lightBoxOpened`).
- Best for gallery-style layouts and when multiple flipbooks are shown on one page.

```
[real3dflipbook id="123" mode="lightbox"]
```

Key lightbox settings:

| Setting                               | Default                              | Description                               |
| ------------------------------------- | ------------------------------------ | ----------------------------------------- |
| `lightboxBackground`                  | `rgb(81, 85, 88)`                    | Overlay background color                  |
| `lightboxThumbnailHeight`             | `300`                                | Thumbnail image height in pixels          |
| `lightboxContainerCSS`                | `display:inline-block;padding:10px;` | CSS for the thumbnail container           |
| `lightBoxOpened`                      | `false`                              | Open lightbox automatically on page load  |
| `lightBoxFullscreen`                  | `false`                              | Open in real browser fullscreen           |
| `lightboxText`                        | (empty)                              | Text link displayed below/above thumbnail |
| `lightboxMarginV` / `lightboxMarginH` | `0`                                  | Overlay margins                           |

### Fullscreen

- **Key**: `mode="fullscreen"`
- Flipbook covers the entire browser viewport.
- Attempts to hide common theme header/footer elements.
- Optionally, a `menuSelector` can be set to position the flipbook below a fixed navigation bar.
- Body overflow is set to `hidden` to prevent scrolling.

```
[real3dflipbook id="123" mode="fullscreen"]
```

### Mobile Mode Override

Use `modeMobile` in global settings to override the display mode on mobile devices. For example, you can display inline on desktop but lightbox on mobile.

## Combining Modes

Rendering mode and display mode are independent. Any combination works:

```
[real3dflipbook id="123" mode="lightbox" viewmode="swipe"]
[real3dflipbook id="123" mode="fullscreen" viewmode="webgl"]
```
