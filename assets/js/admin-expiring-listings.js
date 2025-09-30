/**
 * Admin JavaScript for Expiring Listings Page
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize the page
    initExpiringListings();
    
    /**
     * Initialize expiring listings functionality
     */
    function initExpiringListings() {
        // Add loading states to buttons
        addLoadingStates();
        
        // Add confirmation dialogs for actions
        addConfirmationDialogs();
        
        // Add keyboard shortcuts
        addKeyboardShortcuts();
        
        // Add auto-refresh functionality (optional)
        // addAutoRefresh();
    }
    
    /**
     * Add loading states to action buttons
     */
    function addLoadingStates() {
        $('.column-actions .button').on('click', function() {
            var $button = $(this);
            var originalText = $button.text();
            
            // Add loading state
            $button.prop('disabled', true)
                   .text('Loading...')
                   .addClass('button-disabled');
            
            // Remove loading state after 2 seconds (in case page doesn't redirect)
            setTimeout(function() {
                $button.prop('disabled', false)
                       .text(originalText)
                       .removeClass('button-disabled');
            }, 2000);
        });
    }
    
    /**
     * Add confirmation dialogs for important actions
     */
    function addConfirmationDialogs() {
        // Add confirmation for edit actions if needed
        $('.column-actions .button[href*="post.php"]').on('click', function(e) {
            // Only show confirmation if there are unsaved changes (optional)
            if (window.wp && window.wp.autosave && window.wp.autosave.server.postChanged()) {
                if (!confirm('You have unsaved changes. Are you sure you want to leave this page?')) {
                    e.preventDefault();
                }
            }
        });
    }
    
    /**
     * Add keyboard shortcuts
     */
    function addKeyboardShortcuts() {
        $(document).on('keydown', function(e) {
            // Ctrl/Cmd + R to refresh the page
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 82) {
                e.preventDefault();
                window.location.reload();
            }
        });
    }
    
    /**
     * Add auto-refresh functionality (optional - commented out by default)
     */
    function addAutoRefresh() {
        // Auto-refresh every 5 minutes
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 300000); // 5 minutes
    }
    
    /**
     * Add tooltips for better UX
     */
    function addTooltips() {
        $('.days-remaining-urgent').attr('title', 'This listing expires very soon!');
        $('.days-remaining-warning').attr('title', 'This listing expires soon');
        $('.days-remaining-normal').attr('title', 'This listing has time remaining');
    }
    
    /**
     * Add search functionality (if needed in future)
     */
    function addSearchFunctionality() {
        // This could be implemented if the table gets very large
        // For now, we'll keep it simple
    }
    
    /**
     * Add export functionality (if needed in future)
     */
    function addExportFunctionality() {
        // This could be implemented to export expiring listings to CSV
        // For now, we'll keep it simple
    }
    
    /**
     * Handle tab switching with smooth transitions
     */
    $('.nav-tab').on('click', function(e) {
        var $tab = $(this);
        var $content = $('.tab-content');
        
        // Add loading state
        $content.addClass('loading');
        
        // Remove loading state after a short delay
        setTimeout(function() {
            $content.removeClass('loading');
        }, 300);
    });
    
    /**
     * Add responsive table handling
     */
    function handleResponsiveTable() {
        if ($(window).width() < 782) {
            $('.expiring-listings-table-container').addClass('mobile-view');
        } else {
            $('.expiring-listings-table-container').removeClass('mobile-view');
        }
    }
    
    // Handle window resize
    $(window).on('resize', handleResponsiveTable);
    
    // Initial call
    handleResponsiveTable();
    
    /**
     * Add accessibility improvements
     */
    function addAccessibilityImprovements() {
        // Add ARIA labels
        $('.nav-tab').attr('role', 'tab');
        $('.tab-content').attr('role', 'tabpanel');
        
        // Add keyboard navigation for tabs
        $('.nav-tab').on('keydown', function(e) {
            if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
                e.preventDefault();
                $(this).click();
            }
        });
    }
    
    // Initialize accessibility improvements
    addAccessibilityImprovements();
    
    /**
     * Add performance monitoring (optional)
     */
    function addPerformanceMonitoring() {
        if (window.performance && window.performance.mark) {
            window.performance.mark('expiring-listings-page-loaded');
        }
    }
    
    // Initialize performance monitoring
    addPerformanceMonitoring();
});
