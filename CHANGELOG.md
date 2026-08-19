# Changelog

All notable changes to **Directorist – Google Reviews** are documented here.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [3.3.0] – 2026-08-19

### Added
- `uninstall.php` — removes the reviews table, sync meta, options and legacy
  transients on uninstall (multisite aware). The owner-selected `_google_place`
  meta is kept so a reinstall resumes cleanly.
- Translation template at `languages/directorist-google-reviews.pot`; text
  domain now loaded from the `languages` directory.
- `Requires PHP` and `Requires Plugins: directorist` plugin headers.

### Changed
- Plugin rebranded under [wpXplore](https://wpxplore.com/); product page at
  [wpxplore.com/tools/directorist-google-reviews](https://wpxplore.com/tools/directorist-google-reviews/).
- Plugin description now describes what the plugin actually does.

## [3.2.0] – 2026-08-19

### Added
- **Google Review Carousel** single-listing field: native CSS scroll-snap track
  with arrow and dot controls, no third-party slider library. Cards reuse the
  review grid design; long text clamps with CSS alone. Controls stay hidden
  until JavaScript enhances, so the no-JS fallback is a plain scrollable row.
- `dgr_carousel_display_limit` filter.

### Fixed
- Rapid arrow clicks during a smooth scroll now accumulate instead of
  re-targeting the same slide.

## [3.1.0] – 2026-08-19

### Added
- Save-time sync: changing the Google place on the Add Listing form purges the
  old place's data and refetches immediately (deferred to `shutdown`, so saves
  are never blocked and delete+add meta writes cannot race).
- Stored data is cleaned up when a listing is permanently deleted.
- `dgr_sync_on_place_change` filter.

## [3.0.0] – 2026-08-19

### Added
- Reviews stored in a custom table (`{prefix}dgr_google_reviews`); pages render
  from the database, refreshed from Google every 7 days with a 15-minute
  failure backoff. Average rating, total review count, place URL, place ID and
  sync timestamps stored in listing post meta.
- **Google Rating & Total Reviews** single-listing field (compact stat tiles).
- Re-pointing a listing at a different place invalidates stored data on read.
- `dgr_reviews_store_limit`, `dgr_refresh_interval` filters and the
  `dgr_listing_synced` action.
- `DOCUMENTATION.md` technical reference.

### Changed
- Code reorganised into single-purpose classes: `DGR_Install`,
  `DGR_Reviews_Table`, `DGR_Places_API`, `DGR_Sync`, `DGR_Custom_Field`,
  `DGR_Listing_Hooks`.

## [2.1.0] – 2026-08-19

### Added
- Redesigned review display: rating summary bar, responsive review cards with
  avatars, fractional SVG stars, relative dates and a *Read more* toggle.
- **Google Rating** single-listing field (standalone summary bar).
- Both Places sort modes fetched and merged, lifting the per-request cap of 5
  reviews to 10; reviews with text ordered first.
- Star-only reviews render as compact rating cards instead of being dropped.
- Transient caching of Places responses, including brief failure caching.
- `dgr_reviews_display_limit`, `dgr_reviews_sort`, `dgr_merge_review_sorts`
  and `dgr_place_cache_ttl` filters.

### Fixed
- Review author names and text are now escaped on output.
- Empty review lists no longer render an empty styled container.

## [2.0.0] – 2025-06-30

### Added
- Google reviews fetched from the Places Details API and displayed on the
  single listing page.
- **Google Place** autocomplete field for the Add Listing form.

## [1.0.0] – 2023-07-20

### Added
- Initial release as a Directorist custom-code extension.
