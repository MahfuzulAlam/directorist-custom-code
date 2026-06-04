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
