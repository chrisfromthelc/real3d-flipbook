![CI](https://github.com/chrisfromthelc/real3d-flipbook/actions/workflows/ci.yml/badge.svg)

# Real3D Flipbook PDF Viewer PRO

Interactive 3D flipbook plugin for WordPress with WebGL, CSS3D, and swipe rendering modes. Renders PDFs and images as realistic page-turning flipbooks with lightbox, inline, and fullscreen display options.

## Requirements

- PHP 7.4+
- WordPress 6.0+

## Installation

1. Download the latest ZIP from [GitHub Releases](https://github.com/chrisfromthelc/real3d-flipbook/releases).
2. In your WordPress admin, go to **Plugins > Add New > Upload Plugin**.
3. Upload the ZIP file and click **Install Now**.
4. Activate the plugin.

## Quick Start

1. Navigate to **Real3D Flipbook > Add new** in the WordPress admin menu.
2. Enter a title for your flipbook.
3. Upload a PDF or select images as pages.
4. Publish the flipbook.
5. Embed the flipbook using the shortcode or Gutenberg block.

## Shortcode Reference

```
[real3dflipbook id="123"]
[real3dflipbook name="My Book"]
[real3dflipbook pdf="https://example.com/file.pdf"]
[real3dflipbook id="123" mode="lightbox"]
```

| Attribute | Description                                                       |
| --------- | ----------------------------------------------------------------- |
| `id`      | Post ID of the flipbook. Use `id="all"` to display all flipbooks. |
| `name`    | Flipbook title (alternative to `id`).                             |
| `pdf`     | Direct URL to a PDF file.                                         |
| `mode`    | Display mode: `normal`, `lightbox`, or `fullscreen`.              |

See [docs/user/shortcode-reference.md](docs/user/shortcode-reference.md) for the full list of attributes.

## Gutenberg Block

Search for "Real3D FlipBook" in the block inserter. Select a pre-created flipbook and choose a display mode from the block inspector.

## Development

```bash
# PHP dependencies and linting
composer install
vendor/bin/phpunit
vendor/bin/phpcs

# JavaScript dependencies, tests, and linting
npm install
npm run test:js
npm run lint:js

# Build minified assets
npm run build
```

## Auto-Updates

The plugin uses the [Plugin Update Checker](https://github.com/YahniS/plugin-update-checker) library to check for new releases on GitHub. When a new release is published on the `main` branch, WordPress will show an update notification in the Plugins screen. Updates can be installed with a single click, just like plugins from the WordPress.org repository. Release assets (ZIP files) are used for the update package.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE) for details.

## Documentation

### User Documentation

- [Installation](docs/user/installation.md)
- [Configuration](docs/user/configuration.md)
- [Shortcode Reference](docs/user/shortcode-reference.md)
- [Display Modes](docs/user/display-modes.md)
- [FAQ](docs/user/faq.md)
- [Updating](docs/user/updating.md)

### Developer Documentation

- [Architecture](docs/developer/architecture.md)
- [Data Model](docs/developer/data-model.md)
- [Hooks Reference](docs/developer/hooks-reference.md)
- [REST API](docs/developer/rest-api.md)
- [AJAX Handlers](docs/developer/ajax-handlers.md)
- [Addon Development](docs/developer/addon-development.md)
- [Gutenberg Block](docs/developer/gutenberg-block.md)
- [Theming](docs/developer/theming.md)
- [JS Architecture](docs/developer/js-architecture.md)
- [Contributing](docs/developer/contributing.md)
