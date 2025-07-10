
# 📍 Directorist – Google Reviews Extension

**Directorist – Google Reviews** is a third-party extension for the [Directorist](https://directorist.com) plugin that displays real user reviews from Google Places on the single listing page. Improve listing credibility and give users helpful insights from existing Google reviews.

---

## 🚀 Features

- Integrates Google Reviews into Directorist listings
- Autocomplete Google Places field in Add Listing form
- Fetches and displays Place ID, star ratings, and review content
- Flexible display via drag-and-drop Single Listing Layout builder
- Easy customization with CSS classes

---

## 📥 Installation

### 🔄 Step 1: Download the Plugin

1. Go to the [GitHub Repo](https://github.com/MahfuzulAlam/directorist-custom-code)
2. Select the **`google/reviews`** branch
3. Click **Code → Download ZIP**
4. Unzip the downloaded file

Alternatively, clone the branch directly:

```bash
git clone --branch google/reviews https://github.com/MahfuzulAlam/directorist-custom-code
```

### 🔌 Step 2: Upload to WordPress

1. Login to your **WordPress dashboard**
2. Go to **Plugins → Add New → Upload Plugin**
3. Upload the unzipped folder or ZIP it before uploading
4. Click **Install Now**, then **Activate**

---

## ⚙️ Setup Instructions

### ✅ 1. Enable Google Maps in Directorist

- Go to: **Directorist → Settings → Extensions → Google Maps**
- Enable the extension and enter your **Google Maps API Key**

### ✅ 2. Enable Required Google APIs

From the [Google Cloud Console](https://console.cloud.google.com/), make sure the following APIs are **enabled**:

- **Maps JavaScript API**
- **Places API**

> 🧾 Billing must be active on your Google Cloud project.

---

## 📝 Form Configuration

### 📌 Add Listing Form

1. Go to: **Directorist → Directory Builder → Add Listing Form**
2. Add the **Google Place** field
3. Save changes

📷 [Screenshot](https://prnt.sc/Pszx0AVXSIVs)

### 🧩 Single Listing Layout

1. Go to: **Directorist → Directory Builder → Single Listing Layout**
2. Create a new section
3. Add the **Google Reviews** field
4. Save changes

📷 [Screenshot](https://prnt.sc/hIp0T-rnswWr)

---

## 📊 Output Preview

### Add Listing Page:
Users can search for a business and select it from the Google suggestions.

📷 [Screenshot](https://prnt.sc/TSAX6qwMKg9P)

### Single Listing Page:
Google Reviews will appear for the selected place.

📷 [Screenshot](https://prnt.sc/jJc_y6gJeMAr)

---

## 🎨 CSS Customization

You can override styles using the following CSS classes:

```css
.google-reviews {}
.google-reviews .review {}
.google-reviews .review:hover {}
.review-title {}
.review-title strong {}
.stars {}
.review-description {}
```

Add your styles via `Appearance → Customize → Additional CSS` or your theme’s `style.css`.

---

## 🛠 Support

This is a **third-party plugin**. For support, please contact:

📩 **Email:** asayeedalam@gmail.com

---

## 🧾 License

This extension is released under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

## 🤝 Credits

Developed for use with the [Directorist](https://directorist.com) plugin by SovWare.

---
