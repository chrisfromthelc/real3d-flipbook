# Frequently Asked Questions

## Blank flipbook / PDF not loading

**Symptoms**: The flipbook container appears but no pages are displayed, or the preloader spins indefinitely.

**Causes and solutions**:

1. **CORS policy**: If the PDF is hosted on a different domain than your WordPress site, the browser blocks the request. Host the PDF on the same domain or configure CORS headers on the remote server.
2. **Invalid PDF URL**: Verify the URL is accessible by opening it directly in the browser. Check for typos or expired URLs.
3. **Mixed content**: If your site uses HTTPS, the PDF URL must also use HTTPS. The plugin attempts to convert `http://` to `https://` for lightbox thumbnails, but the PDF URL must be correct at the source.
4. **PDF too large**: Very large PDFs may time out during loading. Consider using the PDF Tools Addon to pre-convert PDF pages to images, or increase the `rangeChunkSize` setting.
5. **JavaScript errors**: Open the browser developer console (F12) and check for errors. Conflicts with other plugins or themes can prevent the flipbook scripts from loading.

## WebGL not working

**Symptoms**: The flipbook renders without 3D effects or shows a flat page flip.

**Solutions**:

1. The plugin automatically falls back from WebGL to CSS3D if WebGL is not available. This is expected behavior on older devices.
2. Check `chrome://gpu` (Chrome) or equivalent to verify WebGL is enabled in the browser.
3. Some hosting-level firewalls or content security policies may block WebGL. Check the browser console for errors.
4. Set `viewMode` to `3d` or `swipe` explicitly if WebGL is consistently unavailable for your audience.

## Shortcode not rendering

**Symptoms**: The shortcode text `[real3dflipbook ...]` appears literally on the page instead of a flipbook.

**Solutions**:

1. Verify the plugin is activated in **Plugins**.
2. If using a page builder, ensure shortcodes are processed. Some builders require a "shortcode" widget or block.
3. If used in a widget, confirm the `widget_text` filter is active (the plugin adds `do_shortcode` to it automatically).
4. Check that the flipbook ID or name in the shortcode matches an existing published flipbook.

## Performance tips

1. **Use PDF Tools Addon**: Converting PDF to pre-rendered images eliminates client-side PDF rendering and significantly improves load times.
2. **Reduce page texture size**: Lower `pageTextureSize` (default 3000) and `pageTextureSizeSmall` (default 1500) for faster rendering at the cost of resolution.
3. **Limit pages in memory**: The `pagesInMemory` setting (default 20) controls how many pages are kept in WebGL memory. Reduce for large books on low-memory devices.
4. **Use swipe mode on mobile**: Set `viewModeMobile` to `swipe` for better mobile performance.
5. **Disable lights and shadows**: In WebGL mode, disabling `lights` and `shadows` improves frame rate.
6. **Disable PDF text layer**: Set `pdfTextLayer` to `false` if text selection and search are not needed.
7. **Enable range requests**: Keep `disableRange` as `false` so PDFs are loaded progressively rather than all at once.

## Flipbook appears behind other elements

Set the `zIndex` setting to a value higher than the overlapping element (e.g., `999`). For lightbox mode, this is usually handled automatically.

## Right-to-left (RTL) books

Set `rightToLeft` to `true` in the flipbook settings or use the shortcode attribute:

```
[real3dflipbook id="123" rtl="true"]
```

## Multiple flipbooks on one page

Each flipbook instance gets a unique ID. Multiple shortcodes on the same page work without conflict. For displaying all flipbooks at once:

```
[real3dflipbook id="all"]
```

This renders every published flipbook in lightbox mode.

## Password-protected flipbooks

The `single-r3d.php` template checks for `post_password_required()`. If the flipbook post has a password set in WordPress, visitors must enter it before the flipbook is displayed.

## Resume reading not working

The resume reading feature (`resumeReading` setting) requires users to be logged in. It stores the last viewed page in user meta (`real3dflipbook_last_page_{bookId}`). Anonymous visitors cannot use this feature.
