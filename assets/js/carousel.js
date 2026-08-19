/**
 * Directorist - Google Reviews
 *
 * Carousel behaviour: arrows, dots and snap tracking for the review carousel.
 * Without this script the track is a plain scrollable row — nothing is hidden
 * behind dead controls, they simply never appear.
 */
(function () {
    'use strict';

    var motionOK = ! (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);

    function setup(root) {
        var track   = root.querySelector('.dgr-carousel__track');
        var prev    = root.querySelector('.dgr-carousel__arrow--prev');
        var next    = root.querySelector('.dgr-carousel__arrow--next');
        var dotsBox = root.querySelector('.dgr-carousel__dots');
        var slides  = track ? Array.prototype.slice.call(track.children) : [];

        if (!track || !prev || !next || !dotsBox || slides.length < 2) {
            return;
        }

        root.classList.add('dgr-carousel--enhanced');

        var label = dotsBox.getAttribute('data-label') || 'Go to review %d';

        var dots = slides.map(function (slide, i) {
            var dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'dgr-carousel__dot';
            dot.setAttribute('aria-label', label.replace('%d', String(i + 1)));
            dot.addEventListener('click', function () { goTo(i); });
            dotsBox.appendChild(dot);
            return dot;
        });

        function maxScroll() {
            return track.scrollWidth - track.clientWidth;
        }

        // Absolute value keeps the maths honest in RTL, where scrollLeft
        // runs negative.
        function position() {
            return Math.abs(track.scrollLeft);
        }

        function atStart() { return position() <= 2; }
        function atEnd() { return position() >= maxScroll() - 2; }

        /**
         * Index of the slide currently aligned to the track's start edge.
         * Rect based rather than scrollLeft based so RTL needs no special
         * casing beyond picking the comparison edge.
         */
        function nearestStart() {
            var rtl = getComputedStyle(track).direction === 'rtl';
            var trackRect = track.getBoundingClientRect();
            var best = 0;
            var bestDist = Infinity;

            slides.forEach(function (slide, i) {
                var rect = slide.getBoundingClientRect();
                var dist = rtl ? Math.abs(trackRect.right - rect.right) : Math.abs(rect.left - trackRect.left);

                if (dist < bestDist) {
                    bestDist = dist;
                    best = i;
                }
            });

            return best;
        }

        // While a smooth scroll is in flight, nearestStart() reports the
        // position mid-animation — so rapid clicks would keep re-targeting the
        // same slide. The pending target remembers where the track is headed
        // and clears once scrolling settles.
        var pendingTarget = null;
        var settleTimer = null;

        function currentIndex() {
            return null !== pendingTarget ? pendingTarget : nearestStart();
        }

        function goTo(i) {
            i = Math.max(0, Math.min(slides.length - 1, i));
            pendingTarget = i;
            slides[i].scrollIntoView({
                behavior: motionOK ? 'smooth' : 'auto',
                inline: 'start',
                block: 'nearest'
            });
        }

        function sync() {
            var scrollable = maxScroll() > 2;

            prev.hidden = !scrollable;
            next.hidden = !scrollable;
            dotsBox.hidden = !scrollable;

            if (!scrollable) {
                return;
            }

            // At the end the start-aligned slide can never be the last one,
            // so light the last dot once the track cannot scroll further.
            var index = atEnd() ? slides.length - 1 : nearestStart();

            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === index);

                if (i === index) {
                    dot.setAttribute('aria-current', 'true');
                } else {
                    dot.removeAttribute('aria-current');
                }
            });

            prev.disabled = atStart();
            next.disabled = atEnd();
        }

        prev.addEventListener('click', function () { goTo(currentIndex() - 1); });
        next.addEventListener('click', function () { goTo(currentIndex() + 1); });

        var pending = false;

        track.addEventListener('scroll', function () {
            if (pending) {
                return;
            }

            pending = true;

            window.requestAnimationFrame(function () {
                pending = false;
                sync();
            });

            // 150ms with no scroll events means the track has settled.
            window.clearTimeout(settleTimer);
            settleTimer = window.setTimeout(function () {
                pendingTarget = null;
            }, 150);
        }, { passive: true });

        window.addEventListener('resize', sync);

        sync();
    }

    function init() {
        Array.prototype.forEach.call(document.querySelectorAll('.dgr-carousel'), setup);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
