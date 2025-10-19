# Directorist Custom Code for Marinerotrade.com

The best way to implement custom code and override templates for the Directorist plugin.

## Features

- **Custom Registration Fields**: Add custom fields to user registration forms
- **Template Override System**: Override Directorist templates with custom designs
- **User Dashboard Integration**: Custom fields appear in user profile dashboard
- **Payment Receipt Integration**: Custom fields display on payment receipts
- **Admin Profile Integration**: Custom fields available in WordPress admin user profiles

## Custom Registration Field Class

The plugin includes a powerful `Directorist_Custom_Registration_Field` class that allows you to easily add custom fields to your Directorist registration system.

### Supported Field Types

- `text` - Single line text input
- `textarea` - Multi-line text input
- `url` - URL input with validation
- `number` - Numeric input
- `file` - File upload field

### Usage Guide

#### Basic Implementation

Add custom fields in the `inc/functions.php` file:

```php
add_action( 'init', function(){
    // Company Name field (text input)
    new Directorist_Custom_Registration_Field('Company Name', 'company_name', 'text');
    
    // VAT Number field (text input)
    new Directorist_Custom_Registration_Field('VAT Number', 'vat_number', 'text');
    
    // Company Description field (textarea)
    new Directorist_Custom_Registration_Field('Company Description', 'company_description', 'textarea');
    
    // Website URL field (URL input)
    new Directorist_Custom_Registration_Field('Company Website', 'company_website', 'url');
    
    // Phone Number field (number input)
    new Directorist_Custom_Registration_Field('Phone Number', 'phone_number', 'number');
    
    // Logo Upload field (file upload)
    new Directorist_Custom_Registration_Field('Company Logo', 'company_logo', 'file');
} );
```

#### Constructor Parameters

```php
new Directorist_Custom_Registration_Field($name, $slug, $type, $required);
```

- `$name` (string) - Display name for the field
- `$slug` (string) - Unique identifier for the field (used as meta key)
- `$type` (string) - Field type: 'text', 'textarea', 'url', 'number', 'file'
- `$required` (boolean) - Whether the field is required (optional, defaults to false)

#### Making Fields Required

```php
// Required company name field
new Directorist_Custom_Registration_Field('Company Name', 'company_name', 'text', true);
```

## Where Custom Fields Appear

### 1. Registration Form
Custom fields automatically appear on the Directorist registration form with proper styling and validation.

### 2. User Dashboard Profile
Users can view and edit their custom fields in the dashboard profile section under "My Profile".

### 3. Payment Receipt Page
Custom field data is automatically included in payment receipts, displaying customer information like company name, VAT number, address, and phone.

### 4. WordPress Admin
Custom fields are available in the WordPress admin user profile pages for administrators to view and edit.

## Template Override System

To override Directorist template files, place the file in the same directory structure as it is found in the Directorist plugin's template folder.

### Example:

If you want to override the `address` field in the single listing page, place `address.php` in the following path:

`wp-content/plugins/directorist-custom-code/templates/single/fields/address.php`

Ensure the directory structure matches exactly to successfully override the template.

## File Structure

```
directorist-custom-code/
├── inc/
│   ├── class-custom-registration.php  # Custom registration field class
│   └── functions.php                  # Field initialization
├── templates/
│   ├── account/
│   │   ├── registration.php          # Registration form template
│   │   └── login-registration-form.php
│   ├── dashboard/
│   │   └── tab-profile.php           # User dashboard profile
│   └── payment/
│       └── payment-receipt.php        # Payment receipt template
├── assets/
│   ├── css/
│   └── js/
└── directorist-marinerotrade-code.php # Main plugin file
```

## Advanced Usage

### Custom Field Validation

The class automatically handles:
- Input sanitization
- File upload validation
- Required field validation
- Data persistence across user sessions

### Accessing Field Data

```php
// Get user's custom field data
$company_name = get_user_meta($user_id, 'company_name', true);
$vat_number = get_user_meta($user_id, 'vat_number', true);
```

### File Upload Handling

File upload fields automatically:
- Validate file types
- Handle WordPress media library integration
- Store attachment IDs as user meta
- Provide secure file handling

## Requirements

- WordPress 5.2 or higher
- Directorist plugin (active)
- PHP 7.4 or higher

## Support

For support and customization requests, please contact the plugin author or refer to the Directorist documentation.
