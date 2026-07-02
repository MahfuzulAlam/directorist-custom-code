# Directorist - Taxonomy List

Directorist - Taxonomy List version `3.0.0` adds custom Directorist category and location shortcodes:

```text
[directorist_category_list]
[directorist_location_list]
```

Use them to display Directorist taxonomy terms up to 3 levels deep in a clean responsive grid. Each top-level parent term includes a collapse/expand button, and term names link to the appropriate Directorist category or location page.

## Features

- Displays Directorist parent categories or locations.
- Displays child and grandchild terms under each parent.
- Expand/collapse control for parent terms and second-level terms that have visible children.
- Responsive column layout.
- Optional listing counts.
- Optional empty-term hiding.
- Limit total parent terms.
- Limit child terms per parent or child term level.
- Display selected parent terms by ID, slug, or a mixed list.
- Choose whether child and grandchild term lists are open or closed by default.

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
directorist-taxonomy-list.php
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
wp-content/plugins/directorist-custom-code/directorist-taxonomy-list.php
```

5. Log in to WordPress admin.
6. Go to `Plugins`.
7. Activate `Directorist - Taxonomy List`.

## Basic Usage

Add this shortcode to any WordPress page, post, shortcode block, or widget area:

```text
[directorist_category_list]
```

For locations, use:

```text
[directorist_location_list]
```

Default behavior:

- Shows parent categories or locations.
- Shows child and grandchild terms up to 3 taxonomy levels.
- Uses 2 columns on desktop.
- Shows listing counts.
- Does not hide empty categories or locations.
- Opens child and grandchild term lists by default.
- Shows no collapse button when a parent or second-level term has no visible children.

## Category Shortcode Attributes

| Attribute | Default | Description |
| --- | --- | --- |
| `columns` | `2` | Number of desktop columns. Supports `1` to `6`. Mobile always displays 1 column. |
| `number` | `0` | Number of parent categories to display. Use `0` for all. |
| `category_num` | empty | Alias for `number`. |
| `category_number` | empty | Alias for `number`. |
| `number_of_category` | empty | Alias for `number`. |
| `max_child` | `0` | Maximum child categories to display under each parent or child term level. Use `0` for all. |
| `child_number` | empty | Alias for `max_child`. |
| `child_num` | empty | Alias for `max_child`. |
| `max_children` | empty | Alias for `max_child`. |
| `display_count` | `yes` | Show listing counts. Accepts `yes`, `no`, `true`, `false`, `1`, `0`. |
| `hide_empty` | `no` | Hide categories with no listings. Accepts `yes`, `no`, `true`, `false`, `1`, `0`. |
| `default_state` | `open` | Initial child and grandchild category state. Use `open` or `closed`. Terms without children never show a collapse button. |
| `parent_ids` | empty | Comma-separated parent category IDs to display. |
| `parent_slugs` | empty | Comma-separated parent category slugs to display. |
| `parents` | empty | Comma-separated mixed parent IDs and slugs. |

## Location Shortcode Attributes

`[directorist_location_list]` supports the same behavior and shared attributes as the category shortcode. Its parent-number aliases use location terminology:

| Attribute | Default | Description |
| --- | --- | --- |
| `columns` | `2` | Number of desktop columns. Supports `1` to `6`. Mobile always displays 1 column. |
| `number` | `0` | Number of parent locations to display. Use `0` for all. |
| `location_num` | empty | Alias for `number`. |
| `location_number` | empty | Alias for `number`. |
| `number_of_location` | empty | Alias for `number`. |
| `max_child` | `0` | Maximum child locations to display under each parent or child term level. Use `0` for all. |
| `child_number` | empty | Alias for `max_child`. |
| `child_num` | empty | Alias for `max_child`. |
| `max_children` | empty | Alias for `max_child`. |
| `display_count` | `yes` | Show listing counts. Accepts `yes`, `no`, `true`, `false`, `1`, `0`. |
| `hide_empty` | `no` | Hide locations with no listings. Accepts `yes`, `no`, `true`, `false`, `1`, `0`. |
| `default_state` | `open` | Initial child and grandchild location state. Use `open` or `closed`. |
| `parent_ids` | empty | Comma-separated parent location IDs to display. |
| `parent_slugs` | empty | Comma-separated parent location slugs to display. |
| `parents` | empty | Comma-separated mixed parent location IDs and slugs. |

## Examples

### Display All Parent, Child, and Grandchild Categories

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

### Display Maximum 4 Child Categories Per Level

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

## Location Examples

### Display All Parent, Child, and Grandchild Locations

```text
[directorist_location_list]
```

### Display Only 6 Parent Locations in 3 Columns

```text
[directorist_location_list columns="3" number="6"]
```

Equivalent parent-number aliases:

```text
[directorist_location_list location_num="6"]
[directorist_location_list location_number="6"]
[directorist_location_list number_of_location="6"]
```

### Display Selected Locations and Limit Children Per Level

```text
[directorist_location_list parent_slugs="dhaka,chattogram" max_child="4"]
```

### Common Full Location Example

```text
[directorist_location_list columns="2" number="6" max_child="5" display_count="yes" hide_empty="no" default_state="open"]
```

All shared controls shown in the category examples work identically with `directorist_location_list`.

## Notes About Collapse Behavior

The collapse button appears when a top-level parent category or location has at least one visible child term, and when a second-level term has at least one visible grandchild term.

If a term has no children, or if all children are hidden by `hide_empty="yes"`, it displays as a simple link without a collapse button. Grandchildren are displayed nested under their direct child term and can be collapsed independently from the top-level parent.

Use this attribute to control the initial state:

```text
default_state="open"
```

or:

```text
default_state="closed"
```

## Code Structure

The shortcodes are class based and organized like this:

```text
wp-content/plugins/directorist-custom-code/
|- directorist-taxonomy-list.php
|- inc/
|  |- functions.php
|  |- class-taxonomy-list-shortcode.php
|  |- class-category-list-shortcode.php
|  |- class-location-list-shortcode.php
|  `- class-template-loader.php
|- assets/
|  |- css/main.css
|  `- js/main.js
|- templates/
`- README.md
```

Main shortcode classes:

```php
Directorist_Custom_Code_Category_List_Shortcode
Directorist_Custom_Code_Location_List_Shortcode
Directorist_Custom_Code_Taxonomy_List_Shortcode
```

Class file:

```text
inc/class-taxonomy-list-shortcode.php
inc/class-category-list-shortcode.php
inc/class-location-list-shortcode.php
```

The shortcode class is loaded from:

```text
inc/functions.php
```

The shared taxonomy class handles:

- shortcode registration
- shortcode attribute defaults
- attribute sanitization and normalization
- parent term queries
- child and grandchild term queries
- term count output
- term links
- accessible collapse button output
- CSS class filtering
- markup actions and filters

The category and location classes only provide taxonomy-specific configuration, Directorist count helpers, Directorist permalink helpers, hook prefixes, and shortcode aliases.

## CSS Classes

Category classes are listed below. The location shortcode exposes the same suffixes under the `.directorist-custom-location-list` namespace; for example, `.directorist-custom-location-list__card`, `.directorist-custom-location-list__toggle`, and `.directorist-custom-location-list__count`.

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
| `.directorist-custom-category-list__child-header` | Child category row containing the child link and optional toggle. |
| `.directorist-custom-category-list__child-link` | Child category link. |
| `.directorist-custom-category-list__child-toggle` | Collapse/expand button for second-level terms with grandchildren. |
| `.directorist-custom-category-list__child-icon` | Collapse/expand icon for second-level terms. |
| `.directorist-custom-category-list__children--level-3` | Grandchild category container. |
| `.directorist-custom-category-list__children-list--level-3` | Grandchild category list. |
| `.directorist-custom-category-list__child--level-3` | Grandchild category list item. |
| `.directorist-custom-category-list__child-link--level-3` | Grandchild category link. |
| `.directorist-custom-category-list__count` | Category listing count. |

## PHP Filters

Use these filters in a child theme, custom plugin, or this plugin's `inc/functions.php`.

The location shortcode provides matching filters using the `directorist_custom_location_list_` prefix. Its term-specific filters are `directorist_custom_location_list_location_name`, `directorist_custom_location_list_location_link`, and `directorist_custom_location_list_location_count`.

| Filter | What it customizes |
| --- | --- |
| `directorist_custom_category_list_shortcode_defaults` | Default shortcode attributes. |
| `directorist_custom_category_list_atts` | Normalized shortcode attributes after sanitization. |
| `directorist_custom_category_list_taxonomy` | Taxonomy used by the shortcode. |
| `directorist_custom_category_list_parent_args` | `get_terms()` arguments for parent categories. |
| `directorist_custom_category_list_parent_terms` | Parent terms before rendering. |
| `directorist_custom_category_list_child_args` | `get_terms()` arguments for child and grandchild categories. Receives the current level as the fifth argument. |
| `directorist_custom_category_list_child_terms` | Child or grandchild terms before rendering. Receives the current level as the fifth argument. |
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

The location shortcode provides matching actions using the `directorist_custom_location_list_` prefix.

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
child_header
child_link
child_toggle
child_icon
count
```

Child-related class contexts include `$context['level']`, where direct children are level `2` and grandchildren are level `3`.

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

When Directorist count helpers are available, each shortcode uses its matching Directorist listing count helper. Parent category and location counts include listings from child terms, matching Directorist behavior.

If `display_count="no"` is used, counts are hidden from the output.

If `hide_empty="yes"` is used:

- empty parent categories or locations are not displayed
- empty child terms are not displayed

## Styling

The shortcode styles are located in:

```text
assets/css/main.css
```

The expand/collapse behavior is located in:

```text
assets/js/main.js
```

The shortcode registration and PHP classes are located in:

```text
inc/functions.php
inc/class-taxonomy-list-shortcode.php
inc/class-category-list-shortcode.php
inc/class-location-list-shortcode.php
```

## Troubleshooting

### The shortcode does not show anything

Check that:

- Directorist is installed and activated.
- This plugin is installed and activated.
- You have Directorist categories or locations created.
- The category or location slugs/IDs used in the shortcode are correct.

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
