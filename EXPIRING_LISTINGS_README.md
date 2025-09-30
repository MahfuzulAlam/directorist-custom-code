# Listing Expiry Manager

A WordPress admin page for managing expiring Directorist listings.

## Features

- **Admin Submenu Page**: Located under Directorist settings (`edit.php?post_type=at_biz_dir`)
- **Tabbed Interface**: Two tabs for different time periods
  - Expiring in 1 Week
  - Expiring in 1 Month
- **Comprehensive Table Display**:
  - Listing Title (with edit/view links)
  - Expiry Date (formatted according to WordPress settings)
  - Days Remaining (with color-coded urgency indicators)
  - Action buttons (Edit Listing, View Listing)
- **Smart Filtering**: Excludes listings with `_never_expire` meta set to true
- **Responsive Design**: Works on desktop and mobile devices
- **Security**: Proper sanitization, escaping, and WordPress coding standards

## Technical Details

### Database Query
The system queries the `at_biz_dir` post type with the following criteria:
- Post status: `publish`
- Has `_expiry_date` meta field
- Expiry date within the specified time range
- Does NOT have `_never_expire` meta set to true

### Color Coding
- **Red (Urgent)**: 3 days or less remaining
- **Yellow (Warning)**: 4-7 days remaining  
- **Green (Normal)**: More than 7 days remaining

### File Structure
```
directorist-custom-code/
├── inc/
│   └── functions.php (main implementation)
├── assets/
│   ├── css/
│   │   └── admin-expiring-listings.css
│   └── js/
│       └── admin-expiring-listings.js
└── EXPIRING_LISTINGS_README.md
```

## Usage

1. Navigate to **Directorist > Listing Expiry Manager** in the WordPress admin
2. Use the tabs to switch between "Expiring in 1 Week" and "Expiring in 1 Month"
3. Review the listings table with expiry information
4. Use the action buttons to edit or view individual listings

## Security Features

- All user input is sanitized using `sanitize_text_field()`
- All output is escaped using `esc_html()`, `esc_url()`, `esc_attr()`
- Database queries use `$wpdb->prepare()` for SQL injection prevention
- Proper capability checks (`manage_options`)
- Direct access prevention with `ABSPATH` check

## WordPress Standards Compliance

- Follows WordPress Coding Standards
- Uses proper hooks and filters
- Implements proper internationalization with `__()` and `esc_html__()`
- Uses WordPress admin UI patterns and styling
- Proper class structure and documentation

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for mobile devices
- Accessibility features (ARIA labels, keyboard navigation)

## Future Enhancements

Potential features that could be added:
- Export functionality (CSV/Excel)
- Email notifications for expiring listings
- Bulk actions (extend expiry, send notifications)
- Advanced filtering options
- Dashboard widget
- REST API endpoints
