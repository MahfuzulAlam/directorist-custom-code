# Directorist - Reorder Types

Directorist - Reorder Types adds a drag-and-drop control for setting the display order of Directorist directory types.

## Requirements

- WordPress 5.2 or later
- Directorist with Multi-directory enabled

## Usage

1. In WordPress admin, open **Directorist > Directory Type Order**.
2. Drag the directory types into the desired order.
3. Select **Save Order**.

The order is saved in the WordPress options table under `directorist_reorder_types_order` and is applied to:

- add listing directory selection
- archive directory navigation
- search form directory navigation

New directory types are appended automatically until the administrator saves the order again. Deleted directory type IDs are removed the next time the order is saved.

## Template Overrides

The extension provides the following Directorist template overrides:

```text
templates/listing-form/add-listing-type.php
templates/archive/directory-type-nav.php
templates/search-form/directory-type-nav.php
```

The template loader prefers these files and otherwise preserves Directorist's normal child theme, parent theme, and core template resolution.
