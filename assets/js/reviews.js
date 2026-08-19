/**
 * Directorist - Google Reviews
 *
 * Progressive enhancement for the review cards: clamps long reviews and wires
 * up the read more toggle. Without this script the markup simply shows every
 * review in full, so nothing is ever hidden behind an inert button.
 */
(function () {
    'use strict';

    var CLAMPED = 'dgr-review--clamped';
    var EXPANDED = 'is-expanded';

    /**
     * Decide whether a card actually overflows its clamp, and show or hide the
     * toggle accordingly. Measured with the clamp applied, then reverted.
     */
    function syncCard(card) {
        var text = card.querySelector('.dgr-review__text');
        var toggle = card.querySelector('.dgr-review__toggle');

        if (!text || !toggle || card.classList.contains(EXPANDED)) {
            return;
        }

        card.classList.add(CLAMPED);

        var overflows = text.scrollHeight - text.clientHeight > 2;

        if (!overflows) {
            card.classList.remove(CLAMPED);
        }

        toggle.hidden = !overflows;
    }

    function syncAll(root) {
        var cards = (root || document).querySelectorAll('.dgr-review');
        Array.prototype.forEach.call(cards, syncCard);
    }

    function toggleCard(button) {
        var card = button.closest('.dgr-review');

        if (!card) {
            return;
        }

        var expanded = card.classList.toggle(EXPANDED);

        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        button.textContent = expanded
            ? (button.dataset.less || 'Show less')
            : (button.dataset.more || 'Read more');

        if (!expanded) {
            card.classList.add(CLAMPED);
        }
    }

    function init() {
        var wrappers = document.querySelectorAll('.dgr-reviews');

        if (!wrappers.length) {
            return;
        }

        Array.prototype.forEach.call(wrappers, function (wrapper) {
            wrapper.classList.add('dgr-reviews--enhanced');
        });

        syncAll();

        // Drop profile photos that fail to load so the tinted monogram
        // underneath shows through instead of a broken image glyph.
        var avatars = document.querySelectorAll('.dgr-review__avatar img');

        Array.prototype.forEach.call(avatars, function (image) {
            if (image.complete && image.naturalWidth === 0) {
                image.remove();
                return;
            }

            image.addEventListener('error', function () {
                image.remove();
            });
        });

        document.addEventListener('click', function (event) {
            var target = event.target;

            if (!target || typeof target.closest !== 'function') {
                return;
            }

            var button = target.closest('.dgr-review__toggle');

            if (button) {
                toggleCard(button);
            }
        });

        // Card width drives how many lines fit, so re-measure when it changes.
        // Only width is checked: clamping alters height, and reacting to that
        // would feed the observer its own output.
        if (typeof ResizeObserver === 'function') {
            var widths = new WeakMap();

            var observer = new ResizeObserver(function (entries) {
                entries.forEach(function (entry) {
                    var width = Math.round(entry.contentRect.width);

                    if (widths.get(entry.target) === width) {
                        return;
                    }

                    widths.set(entry.target, width);
                    syncAll(entry.target);
                });
            });

            Array.prototype.forEach.call(wrappers, function (wrapper) {
                observer.observe(wrapper);
            });
        } else {
            var timer = null;

            window.addEventListener('resize', function () {
                window.clearTimeout(timer);
                timer = window.setTimeout(function () {
                    syncAll();
                }, 150);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
