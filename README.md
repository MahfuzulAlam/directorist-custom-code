# Directorist Custom Code

Companion plugin for [Directorist](https://directorist.com/). It overrides selected Directorist templates so vendors can **save a canonical address (and coordinates) once** on their **My Profile** dashboard tab—using the same **Google Maps** or **OpenStreetMap** setup as Directorist—and have **Add Listing** fields prefilled so they do not retype location, email, phone, or website on every new listing.

---

## Requirements

- **WordPress** 5.2 or higher (as declared in the plugin header).
- **Directorist** installed and **activated**. This plugin only loads when Directorist is active (`directorist/directorist-base.php`).
- For **Google** map mode on the profile address picker: a **Google Maps API key** with the **Places** library enabled, configured in Directorist (same key used for listings).
- For **OpenStreetMap** mode: Directorist configured to use OpenStreetMap; no Google key is required for that path.

---

## Installation

### Option A: Upload a ZIP (typical)

1. Zip the entire `directorist-custom-code` folder (the folder that contains `directorist-custom-code.php`).
2. In WordPress admin, go to **Plugins → Add New → Upload Plugin**.
3. Choose the ZIP file and click **Install Now**.

### Option B: Copy via FTP / SFTP / file manager

1. Upload the `directorist-custom-code` folder into:

   `wp-content/plugins/directorist-custom-code/`

2. Ensure the main file exists at:

   `wp-content/plugins/directorist-custom-code/directorist-custom-code.php`

---

## Activation

1. Install Directorist first and activate it.
2. Go to **Plugins** in the WordPress admin menu.
3. Find **Directorist - Custom Code** and click **Activate**.

If Directorist is not active, this extension does not run (by design), so activate Directorist before relying on any overrides.

---

## Purpose and how it works

### Problem

Vendors often enter the same **business address**, **map pin**, **email**, **phone**, and **website** on every new listing. That is repetitive and error-prone.

### Solution (high level)

1. **My Profile (Directorist user dashboard)**  
   The custom **Profile** template adds address autocomplete (Google or OpenStreetMap, following Directorist’s map setting) and saves **address** plus **latitude** and **longitude** to the user’s profile meta when they save the profile.

2. **Add Listing**  
   Custom listing form field templates read that saved profile data. When a listing field is **still empty**, it is **prefilled** from the logged-in user’s profile (on the front end) or from the listing author when editing in the admin—same pattern as Directorist’s own profile storage (`user_email`, `user_url`, `atbdp_phone`, address meta, etc.).

### Address and map

- On **My Profile**, the vendor picks an address from the **autocomplete** (Google Places or OpenStreetMap/Nominatim-style behavior, depending on Directorist’s **Select Listing Map** option).
- Values are stored on the **user** so they persist across listings.
- On **Add Listing**, **Address** and **Map** fields can show those defaults when the listing has not already saved its own values—so the vendor does not have to search for the same location again and again.

### Website, phone, and email

- These come from the same sources Directorist uses on the dashboard profile: account **email**, **website** (`user_url`), and **phone** (`atbdp_phone` user meta).
- If the corresponding listing field is empty, it is prefilled from the profile when the user creates a new listing (logged-in, front end).

### What vendors should do in practice

1. Open the Directorist **user dashboard** page and go to **My Profile**.
2. Set **Email**, **Phone**, **Website**, and use **address autocomplete** to set **Address** (and coordinates where applicable), then **Save Changes**.
3. Open **Add Listing**: empty location/contact fields will default from that profile until the listing itself stores different values.

---

## Directorist settings to check

| Setting | Why it matters |
|--------|----------------|
| **User Dashboard** page | Profile scripts load on that page only. |
| **Select Listing Map** (`google` / `openstreet`) | Chooses Google vs OpenStreetMap for profile address behavior. |
| **Map API Key** (Google) | Required for Google autocomplete on the profile when map type is Google. If the key is empty, Google scripts are not loaded for this feature. |

These options live in Directorist’s settings screens (exact labels may vary slightly by version).

---

## How to override more Directorist templates

Place override files under this plugin’s `templates/` directory using the **same path** as in Directorist’s `templates/` folder.

**Example:** to override the single-listing address field:

`wp-content/plugins/directorist-custom-code/templates/single/fields/address.php`

The directory structure must match Directorist’s template structure so the custom template loader can replace the core file.

---

## Support and customization

This package is intended as a **starting point** for site-specific Directorist behavior. Test after each Directorist upgrade, because template hooks and markup can change between major versions.
