# Directorist Custom Code

## Download and install (from GitHub)

1. Open this repository on GitHub.
2. Click the green **Code** button.
3. Choose **Download ZIP** and save the file to your computer.
4. In your WordPress admin, go to **Plugins → Add New → Upload Plugin**.
5. Click **Choose File**, select the ZIP you downloaded, then **Install Now**.
6. Click **Activate Plugin** once installation finishes.

**Requirements:** [Directorist](https://wordpress.org/plugins/directorist/) should be installed and active (or compatible with your setup) before or after you activate this extension.

**ZIP folder name:** GitHub ZIPs usually contain a folder like `directorist-custom-code-main`. WordPress installs whatever folder is inside the archive. If the plugin does not activate or load correctly, extract the ZIP on your computer, rename the inner folder to `directorist-custom-code`, zip that folder again, and upload the new ZIP—or upload via FTP/SFTP into `wp-content/plugins/directorist-custom-code/`.

---

`Directorist Custom Code` is a developer-focused extension for keeping Directorist customizations outside the main Directorist plugin.

Use it when you want to:

- write project-specific PHP for Directorist
- add custom hooks, filters, and integrations
- override Directorist templates without touching core plugin files
- keep CSS and JavaScript changes in one dedicated extension

This approach is update-safe compared to editing files inside the Directorist plugin directly.

## What This Extension Loads

When Directorist is active, this extension loads:

- `inc/functions.php`
  Use this file for custom PHP logic.
- `templates/`
  Use this directory for Directorist template overrides.
- `assets/css/main.css`
  Use this file for custom frontend styles.
- `assets/js/main.js`
  Use this file for custom frontend JavaScript.

## Recommended Folder Structure

```text
wp-content/plugins/directorist-custom-code/
|- assets/
|  |- css/main.css
|  `- js/main.js
|- inc/
|  |- class-template-loader.php
|  `- functions.php
|- templates/
`- directorist-custom-code.php
```

## Developer Workflow

Use this extension in three layers:

1. Put business logic and integrations in `inc/functions.php`.
2. Put markup changes in `templates/`.
3. Put presentation and behavior changes in `assets/css/main.css` and `assets/js/main.js`.

This keeps logic, markup, and styling separated and makes maintenance easier after Directorist updates.

## 1. Writing Custom Code

The main entry point for PHP customization is:

```text
wp-content/plugins/directorist-custom-code/inc/functions.php
```

This file is loaded automatically when the extension boots, so it is the right place to add:

- WordPress actions and filters
- Directorist actions and filters
- helper functions
- integrations with other plugins or APIs
- extra includes for larger customization files

### Basic Example

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'myproject_directorist_custom_setup' );

function myproject_directorist_custom_setup() {
	// Register your custom behavior here.
	// Example: add actions, filters, shortcodes, REST callbacks, etc.
}
```

### Organizing Larger Customizations

If your customization grows, keep `inc/functions.php` as a loader and split logic into additional files:

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once DIRECTORIST_CUSTOM_CODE_DIR . 'inc/custom/listing-hooks.php';
require_once DIRECTORIST_CUSTOM_CODE_DIR . 'inc/custom/search-hooks.php';
require_once DIRECTORIST_CUSTOM_CODE_DIR . 'inc/custom/dashboard-hooks.php';
```

That structure is easier to maintain than putting all custom logic in a single file.

### Good Use Cases for `inc/functions.php`

- change how Directorist data is processed before save
- add or remove actions around listing forms
- extend search or archive behavior
- connect Directorist data with third-party services
- register helper functions used by custom templates
- conditionally enqueue additional assets for Directorist pages

### Naming Recommendations

To avoid conflicts, always use your own prefix for:

- functions
- classes
- constants
- option keys
- custom hooks

Example:

```php
function myproject_directorist_sync_listing_meta() {
	// Custom code.
}
```

Avoid generic names such as:

- `helper_function()`
- `custom_code()`
- `listing_data()`

### Best Practices for Custom PHP

- Do not edit Directorist core plugin files.
- Keep custom functions prefixed.
- Validate and sanitize incoming data before saving it.
- Escape output when rendering HTML.
- Keep one responsibility per function where possible.
- Split large customizations into separate include files.

## 2. Overriding Templates

This extension can override Directorist templates from its own `templates/` directory.

The rule is simple:

> Copy the Directorist template into this extension and keep the same relative path.

### Override Path Rule

If the original Directorist template is:

```text
wp-content/plugins/directorist/templates/single/fields/address.php
```

Your override should be:

```text
wp-content/plugins/directorist-custom-code/templates/single/fields/address.php
```

### How Template Resolution Works

This extension checks for templates in the following order:

1. `directorist-custom-code/templates/...`
2. child theme `directorist/...`
3. parent theme `directorist/...`
4. default Directorist template

That means if the same template exists in both this extension and your theme, the version inside this extension will be used first.

### Template Override Workflow

1. Find the original template inside the Directorist plugin.
2. Copy that file into `directorist-custom-code/templates/`.
3. Keep the same relative directory structure.
4. Modify only the parts you need.
5. Test the output after Directorist updates.

### Example

To customize the single listing address field:

1. Locate the original file in Directorist.
2. Create this file:

```text
wp-content/plugins/directorist-custom-code/templates/single/fields/address.php
```

3. Paste the original template content.
4. Edit the markup, classes, or display logic as needed.

### Template Override Tips

- Only override templates you actually need to change.
- Keep your changes minimal so future updates are easier to compare.
- Review overrides after updating Directorist.
- Preserve escaping and security checks from the original template.
- If a template uses variables passed by Directorist, keep those variable names intact unless you fully understand the impact.

## When to Use Custom Code vs Template Overrides

Use `inc/functions.php` when you need to change logic:

- saving behavior
- filtering data
- adding hooks
- changing queries
- integrating services

Use `templates/` when you need to change markup:

- layout
- HTML structure
- template-specific output
- element order
- display wrappers and classes

In many real projects you will use both:

- add logic in `inc/functions.php`
- render the final output through a template override

## Frontend Assets

This extension also loads:

- `assets/css/main.css`
- `assets/js/main.js`

Use them for small Directorist-specific UI adjustments, such as:

- custom listing card styles
- single listing UI tweaks
- form interactions
- directory page enhancements

## Safe Customization Checklist

Before shipping a customization:

- keep all PHP in `inc/functions.php` or included files
- keep all template overrides inside `templates/`
- prefix your custom function names
- sanitize inputs and escape outputs
- avoid modifying the Directorist plugin directly
- re-test custom templates after Directorist updates

## Summary

Use this extension as your custom layer on top of Directorist:

- `inc/functions.php` for PHP logic
- `templates/` for template overrides
- `assets/css/main.css` for styles
- `assets/js/main.js` for scripts

If you keep all Directorist customization inside this extension, your project stays cleaner, safer, and easier to maintain.
