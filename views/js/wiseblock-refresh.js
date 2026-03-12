/**
 * WiseBlock - Dynamic refresh & lazy loading
 * Listens for PrestaShop cart events and refreshes blocks with data-wiseblock-refresh="true"
 * Implements Intersection Observer lazy loading for blocks with data-wiseblock-lazy="true"
 */
(function() {
    'use strict';

    var ajaxUrl = prestashop.urls ? prestashop.urls.base_url : '/';
    ajaxUrl += 'index.php?fc=module&module=wiseblock&controller=ajax';

    // ========== AJAX REFRESH ON CART UPDATE ==========

    function refreshBlock(blockEl) {
        var idBlock = blockEl.getAttribute('data-wiseblock-id');
        if (!idBlock) return;

        fetch(ajaxUrl + '&action=refreshBlock&id_block=' + idBlock, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.html !== undefined) {
                blockEl.innerHTML = data.html;
            }
        })
        .catch(function() {
            // Silently fail - block keeps old content
        });
    }

    function refreshAllBlocks() {
        var blocks = document.querySelectorAll('[data-wiseblock-refresh="true"]');
        blocks.forEach(function(block) {
            refreshBlock(block);
        });
    }

    // Listen for PrestaShop cart update events
    if (typeof prestashop !== 'undefined') {
        prestashop.on('updatedCart', refreshAllBlocks);
        prestashop.on('updateCart', refreshAllBlocks);
    }

    // ========== LAZY LOADING WITH INTERSECTION OBSERVER ==========

    function initLazyLoading() {
        var lazyBlocks = document.querySelectorAll('[data-wiseblock-lazy="true"]');
        if (!lazyBlocks.length) return;

        if (!('IntersectionObserver' in window)) {
            // Fallback: load all immediately
            lazyBlocks.forEach(function(block) { loadLazyBlock(block); });
            return;
        }

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    loadLazyBlock(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: '200px' }); // Load 200px before visible

        lazyBlocks.forEach(function(block) {
            observer.observe(block);
        });
    }

    function loadLazyBlock(blockEl) {
        var idBlock = blockEl.getAttribute('data-wiseblock-id');
        if (!idBlock) return;

        fetch(ajaxUrl + '&action=refreshBlock&id_block=' + idBlock, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success && data.html !== undefined) {
                blockEl.innerHTML = data.html;
                blockEl.removeAttribute('data-wiseblock-lazy');
            }
        })
        .catch(function() {});
    }

    // ========== VIEW TRACKING ==========

    function trackView(blockEl) {
        var idBlock = blockEl.getAttribute('data-wiseblock-id');
        var variant = blockEl.getAttribute('data-wiseblock-variant') || 'A';
        if (!idBlock) return;

        // Use sendBeacon if available (non-blocking), else fetch
        var trackUrl = ajaxUrl + '&action=trackView&id_block=' + idBlock + '&variant=' + variant;
        if (navigator.sendBeacon) {
            navigator.sendBeacon(trackUrl);
        } else {
            fetch(trackUrl, { method: 'GET' }).catch(function() {});
        }
    }

    function initViewTracking() {
        // Track views for all visible wiseblock elements
        var blocks = document.querySelectorAll('[data-wiseblock-id]');
        if (!blocks.length) return;

        if (!('IntersectionObserver' in window)) {
            blocks.forEach(function(b) { trackView(b); });
            return;
        }

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    trackView(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 }); // Track when 50% visible

        blocks.forEach(function(block) {
            observer.observe(block);
        });
    }

    // ========== CLICK TRACKING (A/B TESTING) ==========

    function trackClick(blockEl) {
        var idBlock = blockEl.getAttribute('data-wiseblock-id');
        var variant = blockEl.getAttribute('data-wiseblock-variant') || 'A';
        if (!idBlock) return;

        var trackUrl = ajaxUrl + '&action=trackClick&id_block=' + idBlock + '&variant=' + variant;
        if (navigator.sendBeacon) {
            navigator.sendBeacon(trackUrl);
        } else {
            fetch(trackUrl, { method: 'GET' }).catch(function() {});
        }
    }

    function initClickTracking() {
        // Delegate click events on all wiseblock containers
        document.addEventListener('click', function(e) {
            // Find the closest clickable element (a, button, [role=button])
            var clickable = e.target.closest('a, button, [role="button"], input[type="submit"]');
            if (!clickable) return;

            // Find parent wiseblock container
            var blockEl = clickable.closest('[data-wiseblock-id]');
            if (!blockEl) return;

            // Avoid tracking the same block click more than once per session
            var idBlock = blockEl.getAttribute('data-wiseblock-id');
            var clickKey = 'wb_clicked_' + idBlock;
            if (sessionStorage.getItem(clickKey)) return;

            sessionStorage.setItem(clickKey, '1');
            trackClick(blockEl);
        });
    }

    // ========== INIT ==========

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initLazyLoading();
            initViewTracking();
            initClickTracking();
        });
    } else {
        initLazyLoading();
        initViewTracking();
        initClickTracking();
    }

})();
