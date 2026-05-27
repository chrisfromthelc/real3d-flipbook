# Contributing

## Development Setup

### Prerequisites

- PHP 7.4+
- Composer
- Node.js (LTS) and npm
- A local WordPress development environment (e.g., [Local by Flywheel](https://localwp.com/))

### Installation

```bash
git clone https://github.com/chrisfromthelc/real3d-flipbook.git
cd real3d-flipbook

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Link to WordPress

Symlink or copy the plugin directory into your local WordPress installation's `wp-content/plugins/` directory, then activate it in the WordPress admin.

## Coding Standards

### PHP

The project uses [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) enforced via PHP_CodeSniffer with the `wpcs` ruleset. Configuration is in `phpcs.xml`.

```bash
# Run the linter
vendor/bin/phpcs

# Auto-fix what can be fixed
vendor/bin/phpcbf
```

### JavaScript

ESLint is configured via `eslint.config.mjs` using the `@wordpress/eslint-plugin` ruleset.

```bash
npm run lint:js
```

## Testing

### PHP Tests

PHPUnit tests are in `tests/php/`. The test bootstrap is at `tests/bootstrap.php`. Tests require the WordPress test library (`wp-phpunit`).

```bash
# Run PHP tests
vendor/bin/phpunit

# Or via composer script
composer test
```

### JavaScript Tests

Jest tests are in `tests/js/`. Configuration is in `jest.config.js`.

```bash
npm run test:js
```

## Building Assets

Minified JS and CSS are committed to the repository. After modifying source files, rebuild:

```bash
npm run build
```

This runs:

- `npm run minify:js` -- Minifies `flipbook.js`, `flipbook.webgl.js`, `flipbook.book3.js`, `flipbook.scroll.js`, and `flipbook.swipe.js` via Terser.
- `npm run minify:css` -- Minifies `css/flipbook.css` via clean-css.

## CI

GitHub Actions CI (`.github/workflows/ci.yml`) runs on every push and pull request. It checks:

- PHP linting (`phpcs`)
- PHP tests (`phpunit`)
- JavaScript linting (`eslint`)
- JavaScript tests (`jest`)

All CI checks must pass before a pull request can be merged.

## Pull Request Process

1. Create a feature branch from `main`:
   ```bash
   git checkout -b feature/my-change
   ```
2. Make your changes. Follow the coding standards above.
3. Run all checks locally:
   ```bash
   vendor/bin/phpcs
   vendor/bin/phpunit
   npm run lint:js
   npm run test:js
   ```
4. If you modified front-end JS or CSS source files, rebuild minified assets:
   ```bash
   npm run build
   ```
5. Commit your changes with a clear, descriptive message.
6. Push your branch and open a pull request against `main`.
7. Ensure CI passes. Address any review feedback.

## Release Process

Releases are published via GitHub Releases (`.github/workflows/release.yml`). When a new release is created:

1. A release ZIP is built and attached as a release asset.
2. The Plugin Update Checker library detects the new release and notifies WordPress installations.

## Project Structure

See [architecture.md](architecture.md) for a complete file map and architectural overview.

## License

All contributions are licensed under GPL-2.0-or-later, consistent with the project license.
