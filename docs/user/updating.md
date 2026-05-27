# Updating

## Automatic Updates from GitHub

Real3D Flipbook checks for updates from the GitHub repository using the [Plugin Update Checker](https://github.com/YahniS/plugin-update-checker) library. The update checker is configured in `real3d-flipbook.php`:

- **Repository**: `https://github.com/chrisfromthelc/real3d-flipbook/`
- **Branch**: `main`
- **Release assets**: Enabled (the ZIP file attached to a GitHub release is used as the update package)

When a new GitHub release is published:

1. WordPress checks for updates on its regular schedule (typically twice daily).
2. If a newer version is found, an update notification appears on the **Plugins** screen and in the **Dashboard > Updates** page.
3. Click **Update Now** to install the update. WordPress downloads the release asset ZIP and replaces the plugin files.

## Manual Update

1. Download the latest release ZIP from [GitHub Releases](https://github.com/chrisfromthelc/real3d-flipbook/releases).
2. Deactivate the plugin in **Plugins**.
3. Delete the existing plugin via **Plugins > Delete** (this will not remove your flipbook data -- flipbook content is stored in the database as post meta, not in plugin files).
4. Upload and install the new ZIP via **Plugins > Add New > Upload Plugin**.
5. Activate the plugin.

Alternatively, replace the plugin folder via FTP/SFTP:

1. Download and extract the new release ZIP.
2. Connect to your server via FTP/SFTP.
3. Navigate to `wp-content/plugins/`.
4. Replace the `real3d-flipbook/` directory with the extracted folder.
5. If needed, deactivate and reactivate the plugin in WordPress admin.

## Version Checking

The plugin tracks its installed version in the `r3d_version` option. On every `init` hook, it compares the stored version with the `REAL3D_FLIPBOOK_VERSION` constant. If they differ (i.e., after an update), it:

1. Updates the `r3d_version` option to the current version.
2. Sets the `r3d_flush_rewrite_rules` flag to `true`, which triggers a `flush_rewrite_rules()` call on the next request. This ensures that any changes to the custom post type slug or rewrite rules take effect.

## Data Migration

When upgrading from older versions that stored flipbook data in `wp_options` (as `real3dflipbook_{id}`), the plugin automatically migrates data to the current `r3d_flipbook_options` post meta format. This migration happens lazily -- each flipbook is migrated the first time it is loaded via `r3d_get_flipbook()`. The old option is deleted after successful migration.
