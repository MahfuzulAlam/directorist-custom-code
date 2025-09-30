<?php

/**
 * Add your custom php code here
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Expiring Listings Admin Page
 * 
 * Creates a submenu page under Directorist settings to manage expiring listings
 */
class Directorist_Expiring_Listings_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=at_biz_dir',
            __('Listing Expiry Manager', 'directorist-custom-code'),
            __('Listing Expiry Manager', 'directorist-custom-code'),
            'manage_options',
            'directorist-expiring-listings',
            array($this, 'admin_page_callback')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if ('at_biz_dir_page_directorist-expiring-listings' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'directorist-expiring-listings-admin',
            DIRECTORIST_CUSTOM_CODE_URI . 'assets/css/admin-expiring-listings.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'directorist-expiring-listings-admin',
            DIRECTORIST_CUSTOM_CODE_URI . 'assets/js/admin-expiring-listings.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
    
    /**
     * Admin page callback
     */
    public function admin_page_callback() {
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'one_week';
        $allowed_tabs = array('one_week', 'one_month');
        
        if (!in_array($current_tab, $allowed_tabs)) {
            $current_tab = 'one_week';
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Listing Expiry Manager', 'directorist-custom-code'); ?></h1>
            <p class="description">
                <?php echo esc_html__('Monitor and manage listings that are approaching their expiry date.', 'directorist-custom-code'); ?>
            </p>
            
            <nav class="nav-tab-wrapper">
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=at_biz_dir&page=directorist-expiring-listings&tab=one_week')); ?>" 
                   class="nav-tab <?php echo $current_tab === 'one_week' ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html__('Expiring in 1 Week', 'directorist-custom-code'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=at_biz_dir&page=directorist-expiring-listings&tab=one_month')); ?>" 
                   class="nav-tab <?php echo $current_tab === 'one_month' ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html__('Expiring in 1 Month', 'directorist-custom-code'); ?>
                </a>
            </nav>
            
            <div class="tab-content">
                <?php
                if ($current_tab === 'one_week') {
                    $this->display_expiring_listings('one_week');
                } else {
                    $this->display_expiring_listings('one_month');
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Display expiring listings table
     */
    private function display_expiring_listings($period) {
        $listings = $this->get_expiring_listings($period);
        
        if (empty($listings)) {
            echo '<div class="notice notice-info"><p>';
            if ($period === 'one_week') {
                echo esc_html__('No listings are expiring within the next week.', 'directorist-custom-code');
            } else {
                echo esc_html__('No listings are expiring within the next month.', 'directorist-custom-code');
            }
            echo '</p></div>';
            return;
        }
        
        ?>
        <div class="expiring-listings-table-container">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-title column-primary">
                            <?php echo esc_html__('Listing Title', 'directorist-custom-code'); ?>
                        </th>
                        <th scope="col" class="manage-column column-expiry-date">
                            <?php echo esc_html__('Expiry Date', 'directorist-custom-code'); ?>
                        </th>
                        <th scope="col" class="manage-column column-days-remaining">
                            <?php echo esc_html__('Days Remaining', 'directorist-custom-code'); ?>
                        </th>
                        <th scope="col" class="manage-column column-actions">
                            <?php echo esc_html__('Actions', 'directorist-custom-code'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listings as $listing): ?>
                        <tr>
                            <td class="title column-title column-primary">
                                <strong>
                                    <a href="<?php echo esc_url(get_edit_post_link($listing->ID)); ?>" class="row-title">
                                        <?php echo esc_html($listing->post_title); ?>
                                    </a>
                                </strong>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a href="<?php echo esc_url(get_edit_post_link($listing->ID)); ?>">
                                            <?php echo esc_html__('Edit', 'directorist-custom-code'); ?>
                                        </a> |
                                    </span>
                                    <span class="view">
                                        <a href="<?php echo esc_url(get_permalink($listing->ID)); ?>" target="_blank">
                                            <?php echo esc_html__('View', 'directorist-custom-code'); ?>
                                        </a>
                                    </span>
                                </div>
                            </td>
                            <td class="expiry-date column-expiry-date">
                                <?php echo esc_html($this->format_expiry_date($listing->expiry_date)); ?>
                            </td>
                            <td class="days-remaining column-days-remaining">
                                <?php 
                                $days_remaining = $this->calculate_days_remaining($listing->expiry_date);
                                $class = $days_remaining <= 3 ? 'urgent' : ($days_remaining <= 7 ? 'warning' : 'normal');
                                echo '<span class="days-remaining-' . esc_attr($class) . '">' . esc_html($days_remaining) . '</span>';
                                ?>
                            </td>
                            <td class="actions column-actions">
                                <a href="<?php echo esc_url(get_edit_post_link($listing->ID)); ?>" class="button button-small">
                                    <?php echo esc_html__('Edit Listing', 'directorist-custom-code'); ?>
                                </a>
                                <a href="<?php echo esc_url(get_permalink($listing->ID)); ?>" class="button button-small" target="_blank">
                                    <?php echo esc_html__('View Listing', 'directorist-custom-code'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="expiring-listings-summary">
            <p>
                <strong><?php echo esc_html__('Total Listings:', 'directorist-custom-code'); ?></strong> 
                <?php echo esc_html(count($listings)); ?>
            </p>
        </div>
        <?php
    }
    
    /**
     * Get expiring listings based on period
     */
    private function get_expiring_listings($period) {
        global $wpdb;
        
        // Calculate date range based on period
        $current_date = current_time('Y-m-d');
        if ($period === 'one_week') {
            $end_date = date('Y-m-d', strtotime('+1 week'));
        } else {
            $end_date = date('Y-m-d', strtotime('+1 month'));
        }
        
        // Query for listings with expiry dates in the specified range
        $query = $wpdb->prepare("
            SELECT p.ID, p.post_title, pm.meta_value as expiry_date
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_never_expire'
            WHERE p.post_type = 'at_biz_dir'
            AND p.post_status = 'publish'
            AND pm.meta_key = '_expiry_date'
            AND pm.meta_value >= %s
            AND pm.meta_value <= %s
            AND (pm2.meta_value IS NULL OR pm2.meta_value != '1')
            ORDER BY pm.meta_value ASC
        ", $current_date, $end_date);
        
        $results = $wpdb->get_results($query);
        
        return $results;
    }
    
    /**
     * Format expiry date for display
     */
    private function format_expiry_date($expiry_date) {
        if (empty($expiry_date)) {
            return __('No expiry date set', 'directorist-custom-code');
        }
        
        $timestamp = strtotime($expiry_date);
        if ($timestamp === false) {
            return $expiry_date;
        }
        
        return date_i18n(get_option('date_format'), $timestamp);
    }
    
    /**
     * Calculate days remaining until expiry
     */
    private function calculate_days_remaining($expiry_date) {
        if (empty($expiry_date)) {
            return 0;
        }
        
        $expiry_timestamp = strtotime($expiry_date);
        $current_timestamp = current_time('timestamp');
        
        if ($expiry_timestamp === false) {
            return 0;
        }
        
        $days_remaining = ceil(($expiry_timestamp - $current_timestamp) / DAY_IN_SECONDS);
        
        return max(0, $days_remaining);
    }
}

// Initialize the admin page
new Directorist_Expiring_Listings_Admin();


