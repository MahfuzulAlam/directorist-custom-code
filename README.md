
# 📍 Directorist – Google Reviews Extension

**Directorist – Google Reviews** is a third-party extension for the [Directorist](https://directorist.com) plugin that displays real user reviews from Google Places on the single listing page. Improve listing credibility and give users helpful insights from existing Google reviews.

---

## 🚀 Features

- Integrates Google Reviews into Directorist listings
- Autocomplete Google Places field in Add Listing form
- Two independent single-listing fields: **Google Rating** (summary bar) and **Google Reviews** (cards)
- Rating summary bar with the overall score, star rating and total review count
- Star-only reviews (no written comment) render as compact rating cards rather than being dropped
- Responsive review cards with reviewer avatar, star rating, relative date and a *Read more* toggle
- Reviews with written text shown first, then most recent, with a configurable display limit
- Results cached in a transient, so listing pages do not call the Places API on every view
- Inherits the site's Directorist theme colours through CSS custom properties
- Flexible display via drag-and-drop Single Listing Layout builder
- Easy customization with CSS classes

> ℹ️ A single Google Place Details request returns **at most 5 reviews**. The plugin requests both sort modes (`most_relevant` and `newest`), which return different sets, and merges them — so up to **10** reviews are available and the display limit of 6 is reachable. Reviews that have text are ordered first, then most recent.

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

- Go to: **Directorist → Settings → Listings → Map**
- Select the "Google Pap" and enter your **Google Maps API Key**

📷 [Screenshot](https://prnt.sc/cn7vRLiGWCMq)

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

The plugin adds **two independent fields**, so the rating and the reviews can be
placed in different sections of the layout:

| Field | Group in the builder | Renders |
| --- | --- | --- |
| **Google Rating** | Other Fields | The summary bar — average score, stars, review count, *View on Google* |
| **Google Reviews** | Preset Fields | The review cards |

1. Go to: **Directorist → Directory Builder → Single Listing Layout**
2. Create a new section
3. Add the **Google Rating** and/or **Google Reviews** field
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

The widget is themed with custom properties, so most changes need only a token override:

```css
.dgr-reviews {
    --dgr-radius: 12px;          /* card + summary corner radius */
    --dgr-border: #e9e9e9;       /* card border */
    --dgr-surface: #ffffff;      /* card background */
    --dgr-heading: #1a1a1a;      /* names and score */
    --dgr-text: #404040;         /* review body */
    --dgr-muted: #808080;        /* dates and counts */
    --dgr-accent: #4285f4;       /* links and Read more */
    --dgr-star: #fbbc04;         /* filled stars */
    --dgr-gap: 16px;             /* grid gap */
}
```

The full class list:

```css
.dgr-reviews {}                  /* wrapper */
.dgr-reviews__summary {}         /* rating summary bar */
.dgr-reviews__score {}
.dgr-reviews__score-value {}     /* the large average, e.g. 4.6 */
.dgr-reviews__score-count {}     /* "1,284 Google reviews" */
.dgr-reviews__source {}          /* View on Google pill */
.dgr-reviews__list {}            /* responsive card grid */
.dgr-review {}                   /* single review card */
.dgr-review__avatar {}
.dgr-review__author {}
.dgr-review__time {}
.dgr-review__text {}
.dgr-review__para {}             /* a paragraph inside a review */
.dgr-review__toggle {}           /* Read more / Show less */
.dgr-review__translated {}
.dgr-stars {}                    /* star row, fractional fill supported */
```

Add your styles via `Appearance → Customize → Additional CSS` or your theme's `style.css`.

---

## 🔧 Developer Filters

```php
// Maximum reviews rendered (Google returns at most 5). Default: 6
add_filter( 'dgr_reviews_display_limit', function() { return 3; } );

// Primary sort: 'most_relevant' or 'newest'. Default: 'most_relevant'
//
// Heads up: many Google reviews are a star rating with no written comment. On a
// busy place the *newest* reviews are often all star-only, so 'most_relevant'
// is the default because it returns the ones that actually contain text.
add_filter( 'dgr_reviews_sort', function() { return 'newest'; } );

// Request both sort modes and merge them. Default: true
//
// This is what allows more than 5 cards: one request is capped at 5 reviews,
// but the two sort modes return different sets. Costs 2 Places requests per
// place per cache period instead of 1. Set false to keep it to a single call.
add_filter( 'dgr_merge_review_sorts', '__return_false' );

// How long a Places response is cached, in seconds. Default: 12 hours
add_filter( 'dgr_place_cache_ttl', function() { return DAY_IN_SECONDS; } );
```

Template overrides still work — copy either template into your theme:

- `your-theme/directorist-google-reviews/single-listing.php` — review cards
- `your-theme/directorist-google-reviews/rating-summary.php` — rating summary bar

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
