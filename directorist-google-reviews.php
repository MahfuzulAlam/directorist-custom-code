<?php

/** 
 * @package  Directorist - Google Reviews
 */

/**
 * Plugin Name:       Directorist - Google Reviews
 * Plugin URI:        https://wpxplore.com/tools/directorist-google-reviews/
 * Description:       Display Google ratings and reviews on Directorist listings — stored locally, refreshed automatically, with summary, stats, review cards and carousel fields.
 * Version:           3.3.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Requires Plugins:  directorist
 * Author:            wpXplore
 * Author URI:        https://wpxplore.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       directorist-google-reviews
 * Domain Path:       /languages
 */

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {
    exit;                      // Exit if accessed
}

if (!class_exists('Directorist_Google_Reviews')) {

    final class Directorist_Google_Reviews
    {
        /**
         * Instance
         */
        private static $instance;

        /**
         * Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof Directorist_Google_Reviews)) {
                self::$instance = new Directorist_Google_Reviews;
                self::$instance->init();
            }
            return self::$instance;
        }

        /**
         * Init
         */
        public function init()
        {
            $this->define_constant();
            $this->includes();
            $this->boot();
            $this->enqueues();
            $this->hooks();
        }

        /**
         * Define
         */
        public function define_constant()
        {
            if (!defined('DIRECTORIST_GOOGLE_REVIEWS_VERSION')) {
                define('DIRECTORIST_GOOGLE_REVIEWS_VERSION', '3.3.0');
            }

            if (!defined('DIRECTORIST_GOOGLE_REVIEWS_URI')) {
                define('DIRECTORIST_GOOGLE_REVIEWS_URI', plugin_dir_url(__FILE__));
            }

            if (!defined('DIRECTORIST_GOOGLE_REVIEWS_DIR')) {
                define('DIRECTORIST_GOOGLE_REVIEWS_DIR', plugin_dir_path(__FILE__));
            }

            if (!defined('DIRECTORIST_GOOGLE_REVIEWS_PATH')) {
                define('DIRECTORIST_GOOGLE_REVIEWS_PATH', DIRECTORIST_GOOGLE_REVIEWS_DIR . 'templates/');
            }
        }

        /**
         * Included Files
         */
        public function includes()
        {
            include_once(DIRECTORIST_GOOGLE_REVIEWS_DIR . '/inc/functions.php');
            include_once(DIRECTORIST_GOOGLE_REVIEWS_DIR . '/inc/class-install.php');
            include_once(DIRECTORIST_GOOGLE_REVIEWS_DIR . '/inc/class-reviews-table.php');
            include_once(DIRECTORIST_GOOGLE_REVIEWS_DIR . '/inc/class-places-api.php');
            include_once(DIRECTORIST_GOOGLE_REVIEWS_DIR . '/inc/class-sync.php');
            include_once(DIRECTORIST_GOOGLE_REVIEWS_DIR . '/inc/class-custom-field.php');
            include_once(DIRECTORIST_GOOGLE_REVIEWS_DIR . '/inc/class-listing-hooks.php');
        }

        /**
         * Boot the services that need to be live on every request.
         */
        public function boot()
        {
            DGR_Install::maybe_install();

            $sync = new DGR_Sync();

            new DGR_Custom_Field($sync);
            new DGR_Listing_Hooks($sync);
        }

        /**
         * Enqueues
         */
        public function enqueues()
        {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

            add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        /**
         * Hooks
         */
        public function hooks()
        {
            add_action('init', array($this, 'load_textdomain'));
            add_filter('directorist_template', array($this, 'directorist_template'), 10, 2);
        }

        /**
         * Load translations from the plugin's languages directory.
         */
        public function load_textdomain()
        {
            load_plugin_textdomain('directorist-google-reviews', false, dirname(plugin_basename(__FILE__)) . '/languages');
        }

        /**
         *  Enqueue JS file
         */
        public function enqueue_scripts()
        {
            // Places autocomplete for the add listing form — needs the Google Maps SDK.
            wp_enqueue_script('directorist-custom-script', DIRECTORIST_GOOGLE_REVIEWS_URI . 'assets/js/main.js', ['directorist-google-map'], DIRECTORIST_GOOGLE_REVIEWS_VERSION, true);

            // Review card behaviour. Deliberately dependency free: the single
            // listing page does not always load the Google Maps SDK, and the
            // read more toggle must work regardless.
            if (! is_admin()) {
                wp_enqueue_script('directorist-google-reviews', DIRECTORIST_GOOGLE_REVIEWS_URI . 'assets/js/reviews.js', [], DIRECTORIST_GOOGLE_REVIEWS_VERSION, true);
                wp_enqueue_script('directorist-google-reviews-carousel', DIRECTORIST_GOOGLE_REVIEWS_URI . 'assets/js/carousel.js', [], DIRECTORIST_GOOGLE_REVIEWS_VERSION, true);
            }
        }

        /**
         *  Enqueue CSS file
         */
        public function enqueue_styles()
        {
            wp_enqueue_style('directorist-custom-style', DIRECTORIST_GOOGLE_REVIEWS_URI . 'assets/css/main.css', array(), DIRECTORIST_GOOGLE_REVIEWS_VERSION);
        }

        /**
         * Template Exists
         */
        public function template_exists($template_file)
        {
            $file = DIRECTORIST_GOOGLE_REVIEWS_DIR . '/templates/' . $template_file . '.php';

            if (file_exists($file)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Get Template
         */
        public function get_template($template_file, $args = array())
        {
            if (is_array($args)) {
                extract($args);
            }
            $data = $args;

            if (isset($args['form'])) $listing_form = $args['form'];

            $file = DIRECTORIST_GOOGLE_REVIEWS_DIR . '/templates/' . $template_file . '.php';

            if ($this->template_exists($template_file)) {
                include $file;
            }
        }

        /**
         * Directorist Template
         */
        public function directorist_template($template, $field_data)
        {
            if ($this->template_exists($template)) $template = $this->get_template($template, $field_data);
            return $template;
        }
    }

    if (!function_exists('directorist_is_plugin_active')) {
        function directorist_is_plugin_active($plugin)
        {
            return in_array($plugin, (array) get_option('active_plugins', array()), true) || directorist_is_plugin_active_for_network($plugin);
        }
    }

    if (!function_exists('directorist_is_plugin_active_for_network')) {
        function directorist_is_plugin_active_for_network($plugin)
        {
            if (!is_multisite()) {
                return false;
            }

            $plugins = get_site_option('active_sitewide_plugins');
            if (isset($plugins[$plugin])) {
                return true;
            }

            return false;
        }
    }

    function Directorist_Google_Reviews()
    {
        return Directorist_Google_Reviews::instance();
    }

    register_activation_hook(__FILE__, function () {
        include_once plugin_dir_path(__FILE__) . 'inc/class-install.php';
        DGR_Install::install();
    });

    if (directorist_is_plugin_active('directorist/directorist-base.php')) {
        Directorist_Google_Reviews(); // get the plugin running
    }
}
