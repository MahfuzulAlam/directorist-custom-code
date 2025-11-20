<?php

/** 
 * @package  Directorist - Custom Code
 */

/**
 * Plugin Name:       Directorist - Custom Code
 * Plugin URI:        https://wpwax.com
 * Description:       Best way to implement custom code for directorist plugin
 * Version:           2.0.0
 * Requires at least: 5.2
 * Author:            wpWax
 * Author URI:        https://wpwax.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       directorist-custom-code
 * Domain Path:       /languages
 */

/* This is an extension for Directorist plugin. It helps using custom code and template overriding of Directorist plugin.*/

/**
 * If this file is called directly, abrot!!!
 */
if (!defined('ABSPATH')) {
    exit;                      // Exit if accessed
}

if (!class_exists('Directorist_Custom_Code')) {

    final class Directorist_Custom_Code
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
            if (!isset(self::$instance) && !(self::$instance instanceof Directorist_Custom_Code)) {
                self::$instance = new Directorist_Custom_Code;
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
        }

        /**
         * Define
         */
        public function define_constant()
        {
            if ( !defined( 'DIRECTORIST_CUSTOM_CODE_URI' ) ) {
                define( 'DIRECTORIST_CUSTOM_CODE_URI', plugin_dir_url( __FILE__ ) );
            }

            if ( !defined( 'DIRECTORIST_CUSTOM_CODE_DIR' ) ) {
                define( 'DIRECTORIST_CUSTOM_CODE_DIR', plugin_dir_path( __FILE__ ) );
            }
        }

        /**
         * Included Files
         */
        public function includes()
        {
            include_once(DIRECTORIST_CUSTOM_CODE_DIR . '/inc/functions.php');
        }

        /**
         * Enqueues
         */
        public function enqueues()
        {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        /**
         *  Enqueue JS file
         */
        public function enqueue_scripts()
        {
            if( is_singular('at_biz_dir') ) return;
            // Replace 'your-plugin-name' with the actual name of your plugin's folder.
            wp_enqueue_script('directorist-custom-claim-script', DIRECTORIST_CUSTOM_CODE_URI . 'assets/js/main.js', array('jquery'), '2.0', true);
            wp_localize_script('directorist-custom-claim-script', 'dir_claim_badge', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'directorist_claim_nonce' => wp_create_nonce('directorist_claim_nonce'),
            ));
        }

        /**
         *  Enqueue CSS file
         */
        public function enqueue_styles()
        {
            if( is_singular('at_biz_dir') ) return;
            // Replace 'your-plugin-name' with the actual name of your plugin's folder.
            wp_enqueue_style('directorist-custom-claim-style', DIRECTORIST_CUSTOM_CODE_URI . 'assets/css/main.css', array(), '2.0');
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

    function Directorist_Custom_Code()
    {
        return Directorist_Custom_Code::instance();
    }

    if (directorist_is_plugin_active('directorist/directorist-base.php')) {
        Directorist_Custom_Code(); // get the plugin running
    }
}


?>