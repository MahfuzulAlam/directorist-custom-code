# Directorist Custom Category List Shortcode

This WordPress plugin adds a custom Directorist shortcode:

```text
[directorist_category_list]
```

Use it to display Directorist parent categories with their child categories in a clean responsive grid. Each parent category includes a collapse/expand button, and category names link to their Directorist category archive pages.

## Features

- Displays Directorist parent categories.
- Displays child categories under each parent.
- Expand/collapse control for parent categories that have child categories.
- Responsive column layout.
- Optional listing counts.
- Optional empty-category hiding.
- Limit total parent categories.
- Limit child categories per parent.
- Display selected parent categories by ID, slug, or a mixed list.
- Choose whether child category lists are open or closed by default.

## Requirements

- WordPress installed and running.
- Directorist plugin installed and activated.
- This plugin must be activated after Directorist is active.
- PHP 7.4 or newer is recommended.
- A WordPress theme that supports shortcodes in pages, posts, widgets, or template content.

## Download From GitHub

1. Open the GitHub repository page for this plugin.
2. Click `Code`.
3. Click `Download ZIP`.
4. Save the ZIP file to your computer.

## Install From WordPress Admin

1. Log in to your WordPress dashboard.
2. Go to `Plugins > Add New`.
3. Click `Upload Plugin`.
4. Choose the downloaded ZIP file.
5. Click `Install Now`.
6. Click `Activate Plugin`.

If WordPress says the ZIP does not contain a valid plugin, unzip the downloaded file first and make sure the plugin files are inside the top-level plugin folder. The folder should contain:

```text
directorist-custom-code.php
README.md
inc/
assets/
templates/
```

Then zip that plugin folder again and upload it.

## Install With FTP or File Manager

1. Download the plugin ZIP from GitHub.
2. Unzip it on your computer.
3. Upload the plugin folder to:

```text
wp-content/plugins/
```

4. The final path should look like:

```text
wp-content/plugins/directorist-custom-code/directorist-custom-code.php
```

5. Log in to WordPress admin.
6. Go to `Plugins`.
7. Activate `Directorist - Custom Code`.

## Basic Usage

Add this shortcode to any WordPress page, post, shortcode block, or widget area:

```text
[directorist_category_list]
```

Default behavior:

- Shows parent categories.
- Shows all child categories.
- Uses 2 columns on desktop.
- Shows listing counts.
- Does not hide empty categories.
- Opens child category lists by default.
- Shows no collapse button when a parent category has no child categories.

## Shortcode Attributes

| Attribute | Default | Description |
| --- | --- | --- |
| `columns` | `2` | Number of desktop columns. Supports `1` to `6`. Mobile always displays 1 column. |
| `number` | `0` | Number of parent categories to display. Use `0` for all. |
| `category_num` | empty | Alias for `number`. |
| `category_number` | empty | Alias for `number`. |
| `number_of_category` | empty | Alias for `number`. |
| `max_child` | `0` | Maximum child categories to display under each parent. Use `0` for all. |
| `child_number` | empty | Alias for `max_child`. |
| `child_num` | empty | Alias for `max_child`. |
| `max_children` | empty | Alias for `max_child`. |
| `display_count` | `yes` | Show listing counts. Accepts `yes`, `no`, `true`, `false`, `1`, `0`. |
| `hide_empty` | `no` | Hide categories with no listings. Accepts `yes`, `no`, `true`, `false`, `1`, `0`. |
| `default_state` | `open` | Initial child category state. Use `open` or `closed`. Parent categories without children never show a collapse button. |
| `parent_ids` | empty | Comma-separated parent category IDs to display. |
| `parent_slugs` | empty | Comma-separated parent category slugs to display. |
| `parents` | empty | Comma-separated mixed parent IDs and slugs. |

## Examples

### Display All Parent and Child Categories

```text
[directorist_category_list]
```

### Display in 3 Columns

```text
[directorist_category_list columns="3"]
```

### Display Only 6 Parent Categories

```text
[directorist_category_list number="6"]
```

Equivalent aliases:

```text
[directorist_category_list category_num="6"]
[directorist_category_list category_number="6"]
[directorist_category_list number_of_category="6"]
```

### Display Maximum 4 Child Categories Per Parent

```text
[directorist_category_list max_child="4"]
```

Equivalent aliases:

```text
[directorist_category_list child_number="4"]
[directorist_category_list child_num="4"]
[directorist_category_list max_children="4"]
```

### Hide Listing Counts

```text
[directorist_category_list display_count="no"]
```

### Hide Empty Categories

```text
[directorist_category_list hide_empty="yes"]
```

### Make Child Categories Closed by Default

```text
[directorist_category_list default_state="closed"]
```

### Make Child Categories Open by Default

```text
[directorist_category_list default_state="open"]
```

### Display Specific Parent Categories by ID

```text
[directorist_category_list parent_ids="12,18,25"]
```

### Display Specific Parent Categories by Slug

```text
[directorist_category_list parent_slugs="transport,services,technique-installation"]
```

### Display Specific Parents With Mixed IDs and Slugs

```text
[directorist_category_list parents="12,transport,25,services"]
```

### Common Full Example

```text
[directorist_category_list columns="2" number="6" max_child="5" display_count="yes" hide_empty="no" default_state="open"]
```

### Filtered Full Example

```text
[directorist_category_list columns="3" parent_slugs="transport,services" max_child="4" display_count="yes" hide_empty="yes" default_state="closed"]
```

## Notes About Collapse Behavior

The collapse button only appears when a parent category has at least one visible child category.

If a parent category has no child categories, or if all child categories are hidden by `hide_empty="yes"`, the parent category displays as a simple category block without a collapse button.

Use this attribute to control the initial state:

```text
default_state="open"
```

or:

```text
default_state="closed"
```

## Code Structure

The shortcode is class based and organized like this:

```text
wp-content/plugins/directorist-custom-code/
|- directorist-custom-code.php
|- inc/
|  |- functions.php
|  |- class-category-list-shortcode.php
|  `- class-template-loader.php
|- assets/
|  |- css/main.css
|  `- js/main.js
|- templates/
`- README.md
```

Main shortcode class:

```php
Directorist_Custom_Code_Category_List_Shortcode
```

Class file:

```text
inc/class-category-list-shortcode.php
```

The shortcode class is loaded from:

```text
inc/functions.php
```

The class handles:

- shortcode registration
- shortcode attribute defaults
- attribute sanitization and normalization
- parent category queries
- child category queries
- category count output
- category links
- accessible collapse button output
- CSS class filtering
- markup actions and filters

## CSS Classes

These classes are available for design customization:

| Class | Purpose |
| --- | --- |
| `.directorist-custom-category-list` | Main shortcode wrapper. |
| `.directorist-custom-category-list--columns-{number}` | Wrapper modifier for selected desktop column count. |
| `.directorist-custom-category-list--default-open` | Wrapper modifier when child lists are open by default. |
| `.directorist-custom-category-list--default-closed` | Wrapper modifier when child lists are closed by default. |
| `.directorist-custom-category-list__card` | Parent category card. |
| `.directorist-custom-category-list__card--has-children` | Parent card with visible child categories. |
| `.directorist-custom-category-list__card--no-children` | Parent card without visible child categories. |
| `.is-open` | Parent card initial open state. |
| `.is-closed` | Parent card initial closed state. |
| `.directorist-custom-category-list__header` | Parent card header. |
| `.directorist-custom-category-list__parent-link` | Parent category link. |
| `.directorist-custom-category-list__toggle` | Collapse/expand button. |
| `.directorist-custom-category-list__icon` | Collapse/expand icon. |
| `.directorist-custom-category-list__children` | Child category container. |
| `.directorist-custom-category-list__children-list` | Child category list. |
| `.directorist-custom-category-list__child` | Child category list item. |
| `.directorist-custom-category-list__child-link` | Child category link. |
| `.directorist-custom-category-list__count` | Category listing count. |

## PHP Filters

Use these filters in a child theme, custom plugin, or this plugin's `inc/functions.php`.

| Filter | What it customizes |
| --- | --- |
| `directorist_custom_category_list_shortcode_defaults` | Default shortcode attributes. |
| `directorist_custom_category_list_atts` | Normalized shortcode attributes after sanitization. |
| `directorist_custom_category_list_taxonomy` | Taxonomy used by the shortcode. |
| `directorist_custom_category_list_parent_args` | `get_terms()` arguments for parent categories. |
| `directorist_custom_category_list_parent_terms` | Parent terms before rendering. |
| `directorist_custom_category_list_child_args` | `get_terms()` arguments for child categories. |
| `directorist_custom_category_list_child_terms` | Child terms before rendering. |
| `directorist_custom_category_list_category_name` | Parent or child category display name. |
| `directorist_custom_category_list_category_link` | Parent or child category link URL. |
| `directorist_custom_category_list_category_count` | Listing count value. |
| `directorist_custom_category_list_count_html` | Listing count HTML. |
| `directorist_custom_category_list_toggle_label` | Collapse button aria-label. |
| `directorist_custom_category_list_classes` | CSS classes for any shortcode element. |
| `directorist_custom_category_list_wrapper_style` | Inline wrapper style. |
| `directorist_custom_category_list_parent_html` | Full parent card HTML. Return non-empty HTML to replace default card markup. |
| `directorist_custom_category_list_shortcode_output` | Final shortcode HTML output. |

## PHP Actions

| Action | Where it runs |
| --- | --- |
| `directorist_custom_category_list_before_wrapper` | Before the shortcode wrapper. |
| `directorist_custom_category_list_after_wrapper` | After the shortcode wrapper. |
| `directorist_custom_category_list_before_parent` | Before each parent category card. |
| `directorist_custom_category_list_after_parent` | After each parent category card. |
| `directorist_custom_category_list_before_children` | Before the child category list. |
| `directorist_custom_category_list_after_children` | After the child category list. |

## Filter Context

Most filters receive the normalized shortcode attributes in `$atts`.

Common values inside `$atts`:

```php
$atts['columns'];
$atts['number'];
$atts['max_child'];
$atts['display_count'];
$atts['hide_empty'];
$atts['default_state'];
$atts['default_open'];
$atts['parent_ids'];
$atts['parent_slugs'];
$atts['parents'];
```

The `directorist_custom_category_list_classes` filter receives:

```php
apply_filters(
	'directorist_custom_category_list_classes',
	$classes,
	$element,
	$context
);
```

Possible `$element` values:

```text
wrapper
card
header
parent_link
toggle
icon
children
children_list
child_item
child_link
count
```

## Customization Examples

### Change Default Shortcode Settings

```php
add_filter( 'directorist_custom_category_list_shortcode_defaults', function ( $defaults ) {
	$defaults['columns']       = 3;
	$defaults['display_count'] = 'no';
	$defaults['default_state'] = 'closed';

	return $defaults;
} );
```

### Add a Custom CSS Class to Every Card

```php
add_filter( 'directorist_custom_category_list_classes', function ( $classes, $element, $context ) {
	if ( 'card' === $element ) {
		$classes[] = 'my-custom-category-card';
	}

	return $classes;
}, 10, 3 );
```

### Change Parent Category Ordering

```php
add_filter( 'directorist_custom_category_list_parent_args', function ( $args ) {
	$args['orderby'] = 'count';
	$args['order']   = 'DESC';

	return $args;
} );
```

### Change Child Category Ordering

```php
add_filter( 'directorist_custom_category_list_child_args', function ( $args ) {
	$args['orderby'] = 'name';
	$args['order']   = 'ASC';

	return $args;
} );
```

### Customize Count HTML

```php
add_filter( 'directorist_custom_category_list_count_html', function ( $html, $count ) {
	return sprintf(
		' <span class="my-count">%s listings</span>',
		esc_html( number_format_i18n( $count ) )
	);
}, 10, 2 );
```

### Add Markup Before Each Child List

```php
add_action( 'directorist_custom_category_list_before_children', function ( $parent ) {
	printf(
		'<div class="my-child-list-label">%s</div>',
		esc_html( $parent->name )
	);
} );
```

### Replace a Full Parent Card

Use this only when CSS and smaller hooks are not enough. Escape every dynamic value before output.

```php
add_filter( 'directorist_custom_category_list_parent_html', function ( $html, $parent, $children, $atts, $taxonomy, $context ) {
	if ( 'featured-category' !== $parent->slug ) {
		return $html;
	}

	$output  = '<section class="my-featured-category">';
	$output .= '<a href="' . esc_url( get_term_link( $parent ) ) . '">';
	$output .= esc_html( $parent->name );
	$output .= '</a>';
	$output .= '</section>';

	return $output;
}, 10, 6 );
```

## Security Notes for Customization

When customizing output with hooks:

- use `esc_html()` for visible text
- use `esc_attr()` for HTML attributes
- use `esc_url()` for URLs
- use `absint()` for IDs and numbers
- use `sanitize_text_field()` or `sanitize_key()` for custom input
- avoid echoing unsanitized shortcode attributes or term data

## Notes About Counts

When Directorist count helpers are available, the shortcode uses Directorist listing counts. Parent category counts include listings from child categories, matching Directorist behavior.

If `display_count="no"` is used, counts are hidden from the output.

If `hide_empty="yes"` is used:

- empty parent categories are not displayed
- empty child categories are not displayed

## Styling

The shortcode styles are located in:

```text
assets/css/main.css
```

The expand/collapse behavior is located in:

```text
assets/js/main.js
```

The shortcode PHP is located in:

```text
inc/functions.php
```

## Troubleshooting

### The shortcode does not show anything

Check that:

- Directorist is installed and activated.
- This plugin is installed and activated.
- You have Directorist categories created.
- The category slugs or IDs used in the shortcode are correct.

### Empty categories are missing

Check whether your shortcode uses:

```text
hide_empty="yes"
```

Change it to:

```text
hide_empty="no"
```

### The layout does not look correct

Clear your site cache and browser cache. If your theme overrides button, link, or grid styles strongly, adjust the shortcode CSS in:

```text
assets/css/main.css
```

## Updating

To update the plugin from GitHub:

1. Download the latest ZIP from GitHub.
2. Deactivate the current plugin in WordPress.
3. Replace the old plugin folder in `wp-content/plugins/`.
4. Activate the plugin again.
5. Clear site cache if needed.

Do not edit the main Directorist plugin files directly. Keep custom code in this plugin so Directorist can be updated safely.
