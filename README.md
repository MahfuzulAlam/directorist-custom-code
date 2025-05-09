# Directorist Refined Taxonomy

**Directorist Refined Taxonomy** is a custom extension for the [Directorist](https://directorist.com) plugin that restores and enhances the legacy single term page handling for taxonomies such as Location and Category.

With this extension, you can regain proper SEO-friendly, multi-level URL structures for your taxonomy terms—moving away from the default Directorist behavior that uses a single page to handle all term listings.

Note that this is a third-party plugin and directorist has no liabilty for this.

---

## 🚀 Features

- ✅ Use legacy single term pages for taxonomies like `location` and `category`
- ✅ Enables SEO-friendly multi-step URLs  
  Example:  https://yourwebsite.com/location/london/east-ham
- ✅ Customize the URL slug (e.g., use `city`, `area`, or `region` instead of `location`)
- ✅ Works with nested terms (parent-child taxonomy structure)
- ✅ Seamless fallback and compatibility with existing Directorist listings

---

## 📦 Installation

1. Download the plugin from this link:
  https://github.com/MahfuzulAlam/directorist-custom-code/tree/refined-taxonomy
  ![Screenshot](https://prnt.sc/an00y8M74JNs)


2. Upload it to your WordPress installation:
  - Via FTP: Upload the folder to `/wp-content/plugins/`
  - Or, Zip the folder and upload through **WordPress Dashboard > Plugins > Add New > Upload Plugin**

3. Activate the plugin via **Plugins > Installed Plugins**

---

## 🔧 Configuration

After activating the plugin:

### 1. Enable Legacy Taxonomy URLs

Please navigate to the Directorist Listings > Settings > Listings > Category & Location and you can enable the legacy location and category from the Legacy Taxonomy Settings.

You will also be able to update the "Legacy Location Slug" and "Legacy Category Slug".

https://prnt.sc/nfdHOhObkA5S

### 2. Customize Slug (Optional)

You can change the base slug for each taxonomy:

- For **Locations**, you can change `/location/` to `/city/`, `/area/`, `/region/`, etc.
- For **Categories**, you can change `/category/` to `/services/`, `/types/`, etc.

https://prnt.sc/nfdHOhObkA5S

---

## ⚠️ Notes

- This extension is not officially supported by Directorist.
- **Disclaimer:** This is a third-party plugin developed independently. Directorist and its parent company hold no responsibility or liability for any issues arising from its use.
- Ensure that your permalink settings are set to "Post name" for SEO-friendly URLs.
- After activating or changing slugs, **go to Settings > Permalinks** and click **Save Changes** to flush rewrite rules.

---

## 👨‍💻 Developer Guide

To ensure the category term page works smoothly with this extension, you may need to manually add the following filter hook if it is not already present in the Directorist core plugin:

📍 **File Reference:**  
Refer to the appropriate controller or redirection handler in the Directorist plugin as shown here:  
![Screenshot](https://prnt.sc/an00y8M74JNs)

🧩 **Hook to Add:**

```php
apply_filters( 'directorist_category_page_redirection_enabled', true );
