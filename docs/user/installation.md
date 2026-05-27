# Installation

## Requirements

- **PHP**: 7.4 or higher
- **WordPress**: 6.0 or higher
- A modern browser with JavaScript enabled (WebGL support recommended for 3D mode)

## Install Steps

1. Download the latest `.zip` file from the [GitHub Releases page](https://github.com/chrisfromthelc/real3d-flipbook/releases).
2. Log in to your WordPress admin dashboard.
3. Navigate to **Plugins > Add New**.
4. Click **Upload Plugin** at the top of the page.
5. Click **Choose File**, select the downloaded ZIP, and click **Install Now**.
6. After installation completes, click **Activate Plugin**.

## Activation

On activation the plugin:

- Registers the `r3d` custom post type and its taxonomies (`r3d_category`, `r3d_author`).
- Schedules a rewrite-rules flush so that flipbook permalinks work immediately.
- Creates the `real3dflipbook_global` option with default settings if it does not already exist.

## Verifying Installation

1. Look for the **Real3D Flipbook** menu item in the WordPress admin sidebar (dashicon: book).
2. Navigate to **Real3D Flipbook > Flipbooks**. You should see an empty list if this is a fresh install.
3. Click **Add new** to create your first flipbook.

## File Storage

Flipbook thumbnails and converted assets are stored in `wp-content/uploads/real3d-flipbook/flipbook_{ID}/`. The uploads directory must be writable by the web server.

## Optional: Imagick Extension

If the Imagick PHP extension is installed, the plugin will automatically generate a thumbnail image from the first page of uploaded PDFs. Without Imagick, thumbnails must be set manually.

## Uninstallation

When the plugin is deleted through the WordPress admin (not just deactivated), the `uninstall.php` file runs and cleans up:

- All `real3dflipbook_*` options from `wp_options`.
- All `r3d` posts and their associated post meta.
- All `real3dflipbook_last_page_*` user meta entries (resume-reading data).
- All `flipbook_pdf_*` transients.
