# Directorist – Google Reviews · Technical Documentation

Reference for developers and site owners maintaining this extension.
For installation and setup, see [README.md](README.md).

---

## 1. What the plugin does

Listing owners pick their business from Google Places autocomplete on the Add
Listing form. On the single listing page the plugin shows that place's Google
rating and reviews.

Reviews are **not** fetched from Google on every page view. They are stored
locally and refreshed on a schedule, so a page view is a database read.

![The three Google fields rendered on a single listing page](assets/img/doc/frontend_single_google-review-fields.png)

Above: three of the single listing fields on one page — the stat tiles
(*Google Rating & Total Reviews*), the summary bar (*Google Rating*) and the
cards (*Google Reviews*). Each is placed independently in the layout builder.

---

## 2. Architecture

```
directorist-google-reviews.php     Bootstrap: constants, includes, enqueues, activation
│
├── inc/functions.php              Presentation helpers (stars, avatar, Google mark)
├── inc/class-install.php          DGR_Install       — table schema, dbDelta, versioning
├── inc/class-reviews-table.php    DGR_Reviews_Table — read/write stored reviews
├── inc/class-places-api.php       DGR_Places_API    — Google Places client
├── inc/class-sync.php             DGR_Sync          — freshness rules, persistence
├── inc/class-custom-field.php     DGR_Custom_Field  — Directorist fields + rendering
└── inc/class-listing-hooks.php    DGR_Listing_Hooks — reacts to listing saves and deletes
```

Each class has one job:

| Class | Responsibility |
| --- | --- |
| `DGR_Install` | Creates and upgrades the reviews table. Runs on activation and on load. |
| `DGR_Reviews_Table` | All SQL for reviews. Returns rows in the Places API shape. |
| `DGR_Places_API` | Talks to Google. Knows nothing about the database. |
| `DGR_Sync` | Decides *whether* to call Google, then persists what comes back. |
| `DGR_Custom_Field` | Registers Directorist fields and renders templates. |
| `DGR_Listing_Hooks` | Watches the listing itself — resyncs when the place changes, cleans up on delete. |

`DGR_Sync` is the only class that touches both the API and the database, so the
refresh policy lives in exactly one place.

---

## 3. Data flow on a single listing page

```
Single listing renders a Google field
        │
        ▼
DGR_Custom_Field::get_listing_place()
        │  resolves place_id from the field value or _google_place meta
        ▼
DGR_Sync::get_listing_data( $listing_id, $place_id )
        │
        ├── needs_refresh()?  ── no ──┐
        │        │ yes                │
        │        ▼                    │
        │   DGR_Places_API::fetch_place()
        │        │                    │
        │        ├── failed ──► record _dgr_sync_failed_at, keep old data
        │        │                    │
        │        ▼                    │
        │   DGR_Reviews_Table::replace_reviews()
        │   update _dgr_rating / _dgr_reviews_total / _dgr_place_url
        │   update _dgr_place_id / _dgr_synced_at
        │        │                    │
        ▼        ▼                    ▼
   Reviews are ALWAYS read back from the database
        │
        ▼
   Template renders
```

The review list rendered on screen always comes from the database, including on
the request that just refreshed it.

---

## 4. Refresh rules

`DGR_Sync::needs_refresh()` returns true when **any** of these hold:

1. The listing has no stored reviews yet (first ever page view).
2. The listing's `_dgr_place_id` differs from the place currently selected —
   the owner re-pointed the listing at a different business, so stored reviews
   belong to the wrong place and must not be shown.
3. `_dgr_synced_at` is older than the refresh interval (default **7 days**).

…and in every case, only if the last attempt did **not** fail within the last
15 minutes. That backoff stops a Google outage from turning every page view
into another blocking HTTP request.

If a refresh fails, previously stored reviews stay on the page. Nothing is
deleted until a successful response replaces it.

### Cost

A refresh makes **2** Place Details requests (one per sort mode). With the
default 7-day interval that is 2 requests per listing per week, regardless of
traffic. Set `dgr_merge_review_sorts` to false to halve it, at the cost of
capping reviews at 5.

---

## 5. Save-time sync

Page views alone are not enough. If an owner re-points a listing at a different
business, waiting for the refresh interval would show the *previous* business's
reviews for up to a week. `DGR_Listing_Hooks` closes that gap.

### How a save is handled

The submission form may write the place meta as an add, an update, or a delete
followed by an add. Rather than guess which, the class flags the listing on any
touch of `_google_place` (`added_post_meta`, `updated_post_meta`,
`deleted_post_meta`) and reads the **final stored value once** on `shutdown`.

Deferring to shutdown means the save itself is never blocked by an HTTP call,
and a delete/add pair cannot race.

`reconcile()` then decides:

| Stored place vs. selected place | Action | API calls |
| --- | --- | --- |
| Place cleared | Purge reviews and meta | 0 |
| Same place | Nothing — the refresh interval governs freshness | 0 |
| Different place | Purge, then refresh from Google | 2 |

The purge happens *before* the API call deliberately. If Google is unreachable
the listing shows nothing rather than the wrong business's reviews, and the next
page view retries because no reviews are stored.

Set `dgr_sync_on_place_change` to false to purge on change but defer the API
call to the next page view.

### Deletion

`before_delete_post` purges stored reviews and meta for the listing, so the
custom table never accumulates orphan rows.

### Belt and braces

Even with all of the above, `DGR_Sync::needs_refresh()` still compares
`_dgr_place_id` against the selected place on every read. Any save path that
bypasses the meta hooks entirely — a direct SQL import, for instance — is still
caught the first time the listing is viewed.

---

## 6. Database schema

Table: `{$wpdb->prefix}dgr_google_reviews`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | bigint unsigned, AI | Primary key |
| `listing_id` | bigint unsigned | Directorist listing post ID |
| `place_id` | varchar(191) | Google place the review belongs to |
| `review_key` | varchar(191) | md5 of author + timestamp; keeps the unique index honest |
| `sort_order` | smallint unsigned | Display order as computed at sync time |
| `author_name` | varchar(191) | |
| `author_url` | varchar(255) | Reviewer's Google profile |
| `profile_photo_url` | varchar(255) | |
| `rating` | decimal(2,1) | 1–5 |
| `review_text` | longtext | Empty for star-only reviews |
| `relative_time` | varchar(100) | Google's phrasing, e.g. "2 months ago" |
| `review_time` | bigint unsigned | Unix timestamp |
| `translated` | tinyint(1) | Google auto-translated the text |
| `synced_at` | datetime | UTC, when this row was written |

Indexes: `UNIQUE KEY (listing_id, review_key)`, `KEY listing_id`, `KEY place_id`.

Schema version is tracked in the `dgr_db_version` option. Bump
`DGR_Install::DB_VERSION` to trigger `dbDelta` again.

### Writes are a full replace

`replace_reviews()` deletes every row for the listing and reinserts. Google only
exposes a small rolling window of reviews, so a review that has dropped out of
that window should disappear from the listing too.

---

## 7. Post meta reference

| Key | Type | Meaning |
| --- | --- | --- |
| `_google_place` | JSON string | `{place_id, place_address}` — written by the Add Listing form |
| `_dgr_rating` | float | Average Google rating |
| `_dgr_reviews_total` | int | Total number of Google reviews |
| `_dgr_place_url` | string | Google Maps URL for the place |
| `_dgr_place_id` | string | Place the stored data belongs to |
| `_dgr_synced_at` | datetime | UTC datetime of the last successful sync |
| `_dgr_sync_failed_at` | datetime | UTC datetime of the last failed attempt |

`_google_place` is produced by the **Google Place** field on the Add Listing
form, which is registered under *Preset Fields* in the form builder:

![Adding the Google Place field to the Add Listing Form](assets/img/doc/directory-builder_add-listing-form.png)

On the frontend that field renders as a Google Places autocomplete, and the
place the owner selects is what gets written to `_google_place`:

![The Google Place field showing Google Places autocomplete suggestions](assets/img/doc/frontend_add-listing-form.png)

Everything else in the table is derived from the place selected there.

`_dgr_rating` and `_dgr_reviews_total` are plain post meta, so they can be used
in queries, sorting, or your own templates:

```php
$rating = (float) get_post_meta( $listing_id, '_dgr_rating', true );
$total  = (int)   get_post_meta( $listing_id, '_dgr_reviews_total', true );
```

---

## 8. Single listing fields

Registered in `DGR_Custom_Field`. All four render from stored data.

| Field | Builder group | Template | Renders |
| --- | --- | --- | --- |
| **Google Rating** | Other Fields | `rating-summary.php` | Summary bar: score, stars, count, *View on Google* |
| **Google Rating & Total Reviews** | Other Fields | `rating-stats.php` | Two compact stat tiles |
| **Google Reviews** | Preset Fields | `single-listing.php` | The review cards |
| **Google Review Carousel** | Other Fields | `review-carousel.php` | The same cards in a scroll-snap carousel |

The carousel reuses the `.dgr-review` card design from the grid, so the two
displays always stay visually in step. Carousel cards clamp long text with CSS
alone (no *Read more* toggle) — a card that changes height mid-swipe would make
the track jump.

![The three Google fields placed in a Single Page Layout section](assets/img/doc/directory-builder_single-listing_google-review-rating-fields.png)

Above: all three added to a *Single Page Layout* section under
**Directorist → Directory Builder → Single Page Layout**. Each field has
`allowMultiple` off, so once placed it disappears from the palette on the left.

**Why two groups.** Directorist skips a *Preset Fields* widget when its
submission-form value is empty. The rating and carousel fields are derived from
stored data rather than from a submitted value, so they are registered under
*Other Fields*, where that rule does not apply, and read `_google_place` from
post meta directly.

![Dragging the Google Review Carousel field into a section](assets/img/doc/builder_single_carousel.png)

### Template overrides

Copy any template into your theme:

```
your-theme/directorist-google-reviews/single-listing.php
your-theme/directorist-google-reviews/rating-summary.php
your-theme/directorist-google-reviews/rating-stats.php
your-theme/directorist-google-reviews/review-carousel.php
your-theme/directorist-google-reviews/add-listing.php
```

Templates receive `$place` (`rating`, `user_ratings_total`, `url`, `reviews`)
and, for the review list, `$reviews`. Review entries use the Places API shape:
`author_name`, `author_url`, `profile_photo_url`, `rating`, `text`,
`relative_time_description`, `time`, `translated`.

---

## 9. Rendering mechanism (important)

`directorist_single_item_template` looks like it should return markup. It does
not — Directorist treats the filtered value as a **template path** and passes it
to `Helper::get_template()`. This plugin therefore **echoes** its markup inside
the filter and returns the path unchanged.

Do not "fix" `load_template()` to return a string instead of echoing; that
silently breaks every field.

---

## 10. Frontend

- `assets/css/main.css` — all styles, themed with `--dgr-*` custom properties
  that inherit from Directorist's own `--directorist-*` variables.
- `assets/js/reviews.js` — the *Read more* toggle. Deliberately dependency free,
  because the single listing page does not always load the Google Maps SDK.
  Clamping is applied *by* the script, so with JavaScript disabled visitors get
  full review text instead of truncated text behind a dead button.
- `assets/js/carousel.js` — arrows, dots and snap tracking for the review
  carousel. Also dependency free: the track is native CSS scroll-snap, so with
  JavaScript disabled it degrades to a plain scrollable row with its scrollbar
  visible — the controls simply never appear.
- `assets/js/main.js` — Places autocomplete for the Add Listing form. Depends on
  `directorist-google-map`.

![The Google Review Carousel rendered on a single listing page](assets/img/doc/frontend_single_carousel.png)

Above: the carousel in its enhanced state — arrows visible, scrollbar hidden.
The previous arrow is dimmed because the track is at its start.

---

## 11. Filters and actions

| Hook | Type | Default | Purpose |
| --- | --- | --- | --- |
| `dgr_reviews_store_limit` | filter | `6` | Reviews kept per listing |
| `dgr_reviews_display_limit` | filter | `6` | Reviews rendered in the grid |
| `dgr_carousel_display_limit` | filter | `6` | Reviews shown in the carousel |
| `dgr_refresh_interval` | filter | `7 * DAY_IN_SECONDS` | Freshness window |
| `dgr_reviews_sort` | filter | `most_relevant` | Primary sort mode |
| `dgr_merge_review_sorts` | filter | `true` | Request both sorts and merge |
| `dgr_sync_on_place_change` | filter | `true` | Sync immediately when the place changes |
| `dgr_listing_synced` | action | — | Fires after a successful refresh |

---

## 12. Operations

### Force a refresh for one listing

```php
( new DGR_Sync() )->refresh( $listing_id, $place_id );
```

### Clear everything stored for a listing

```php
( new DGR_Sync() )->purge( $listing_id );
```

The next page view repopulates it.

### Inspect stored reviews

```sql
SELECT sort_order, author_name, rating, relative_time, CHAR_LENGTH(review_text)
FROM wp_dgr_google_reviews
WHERE listing_id = 123
ORDER BY sort_order;
```

---

## 13. Troubleshooting

**No reviews appear at all.**
Check `_google_place` is set on the listing, and that the Directorist map API
key is present under *Directorist → Settings → Directory → Map*, with the
provider set to *Google Map*:

![Setting the map provider to Google Map and entering the API key](assets/img/doc/settings_directory_map_api-key.png)

Then check
`_dgr_sync_failed_at` — if it is set, the API call is failing.

**Reviews show but the rating is missing (or vice versa).**
The *Google Rating* and *Google Reviews* fields are separate; add both to the
Single Page Layout.

**Cards show a name and stars but no text.**
Those are star-only reviews — very common on busy places, especially with
`dgr_reviews_sort` set to `newest`. Google returns no text for them.

**Fewer than 6 cards.**
Google exposes at most 5 reviews per sort mode, and the two sets sometimes
overlap. When they do, fewer than 6 unique reviews exist to store.

**Reviews look out of date.**
They refresh every 7 days. Call `purge()` or lower `dgr_refresh_interval` to
force an earlier update.

**Changed the place but old reviews still show.**
Should not happen — the change is picked up on save, and again on the next page
view as a fallback. If it does, check that `_dgr_place_id` matches the
`place_id` inside `_google_place`, and that `dgr_sync_on_place_change` has not
been filtered to false.

**Table missing.**
`DGR_Install::maybe_install()` runs on every load and compares
`dgr_db_version`. Deleting that option forces a reinstall.

---

## 14. Google API notes

- Endpoint: legacy `maps.googleapis.com/maps/api/place/details/json`. Google is
  migrating to Places API (New) at `places.googleapis.com/v1`; this is a known
  future migration.
- Required APIs in Google Cloud: **Maps JavaScript API** and **Places API**,
  with billing enabled.
- Displaying Places data requires Google attribution — the *View on Google*
  control and the Google mark in the summary bar satisfy this. Do not remove
  them without providing equivalent attribution.
- A single Place Details response returns at most 5 reviews. This is an API
  limit and cannot be raised by parameters.
