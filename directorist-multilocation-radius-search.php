<?php

/** 
 * @package  Directorist - Multi-Location Radius Search
 */

/**
 * Plugin Name:       Directorist - Multi-Location Radius Search
 * Plugin URI:        https://wpwax.com
 * Description:       Enable powerful radius-based searches across multiple saved addresses per listing, helping users find the nearest businesses or services with ease.
 * Version:           2.0.0
 * Requires at least: 5.2
 * Author:            wpWax
 * Author URI:        https://wpwax.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       directorist-multilocation-radius-search
 * Domain Path:       /languages
 */

/* Enable powerful radius-based searches across multiple saved addresses per listing, helping users find the nearest businesses or services with ease.*/

/**
 * If this file is called directly, abrot!!!
 */
if (!defined('ABSPATH')) {
    exit;                      // Exit if accessed
}

if (!class_exists('Directorist_Multilocation_Radius_Search')) {

    final class Directorist_Multilocation_Radius_Search
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
            if (!isset(self::$instance) && !(self::$instance instanceof Directorist_Multilocation_Radius_Search)) {
                self::$instance = new Directorist_Multilocation_Radius_Search;
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
            $this->enqueues();
            $this->hooks();
        }

        /**
         * Define
         */
        public function define_constant()
        {
            if ( !defined( 'DIRECTORIST_MLRS_URI' ) ) {
                define( 'DIRECTORIST_MLRS_URI', plugin_dir_url( __FILE__ ) );
            }

            if ( !defined( 'DIRECTORIST_MLRS_DIR' ) ) {
                define( 'DIRECTORIST_MLRS_DIR', plugin_dir_path( __FILE__ ) );
            }
        }

        /**
         * Included Files
         */
        public function includes()
        {
            include_once(DIRECTORIST_MLRS_DIR . '/inc/functions.php');
            include_once(DIRECTORIST_MLRS_DIR . '/inc/class-custom-field.php');
            include_once(DIRECTORIST_MLRS_DIR . '/inc/class-radius-search.php');
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
            add_filter('directorist_template', array($this, 'directorist_template'), 10, 2);
        }

        /**
         *  Enqueue JS file
         */
        public function enqueue_scripts()
        {
            // Replace 'your-plugin-name' with the actual name of your plugin's folder.
            wp_enqueue_script('directorist-multi-location-script', DIRECTORIST_MLRS_URI . 'assets/js/main.js', ['directorist-google-map'], time(), true);
        }

        /**
         *  Enqueue CSS file
         */
        public function enqueue_styles()
        {
            // Replace 'your-plugin-name' with the actual name of your plugin's folder.
            wp_enqueue_style('directorist-multi-location-style', DIRECTORIST_MLRS_URI . 'assets/css/main.css', array(), '2.0');
        }

        /**
         * Template Exists
         */
        public function template_exists($template_file)
        {
            $file = DIRECTORIST_MLRS_DIR . '/templates/' . $template_file . '.php';

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

            $file = DIRECTORIST_MLRS_DIR . '/templates/' . $template_file . '.php';

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

    function Directorist_Multilocation_Radius_Search()
    {
        return Directorist_Multilocation_Radius_Search::instance();
    }

    if (directorist_is_plugin_active('directorist/directorist-base.php')) {
        Directorist_Multilocation_Radius_Search(); // get the plugin running
    }
}


?>