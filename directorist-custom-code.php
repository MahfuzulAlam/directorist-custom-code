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
            $this->hooks();
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
            add_action('wp_enqueue_scripts', array($this, 'enqueue_dashboard_profile_map'), 20);
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
            wp_enqueue_script('directorist-custom-script', DIRECTORIST_CUSTOM_CODE_URI . 'assets/js/main.js', array('jquery'), '2.0', true);
        }

        /**
         *  Enqueue CSS file
         */
        public function enqueue_styles()
        {
            // Replace 'your-plugin-name' with the actual name of your plugin's folder.
            wp_enqueue_style('directorist-custom-style', DIRECTORIST_CUSTOM_CODE_URI . 'assets/css/main.css', array(), '2.0');
        }

        /**
         * Address autocomplete + lat/lng on user dashboard profile tab only.
         */
        public function enqueue_dashboard_profile_map()
        {
            if (!function_exists('get_directorist_option')) {
                return;
            }

            $dashboard_page_id = absint(get_directorist_option('user_dashboard'));
            if (!$dashboard_page_id || !is_page($dashboard_page_id)) {
                return;
            }

            $map_type = get_directorist_option('select_listing_map', 'google');
            if (!in_array($map_type, array('google', 'openstreet'), true)) {
                $map_type = 'google';
            }

            $handle = 'dcc-dashboard-profile-address';

            wp_enqueue_style(
                'dcc-dashboard-profile-address',
                DIRECTORIST_CUSTOM_CODE_URI . 'assets/css/dashboard-profile-address.css',
                array(),
                '2.0.1'
            );

            wp_enqueue_script(
                $handle,
                DIRECTORIST_CUSTOM_CODE_URI . 'assets/js/dashboard-profile-address.js',
                array('jquery'),
                '2.0.1',
                true
            );

            $restricted = get_directorist_option('restricted_countries');
            if (!is_array($restricted)) {
                $restricted = array_filter(array_map('trim', explode(',', (string) $restricted)));
            }

            wp_localize_script(
                $handle,
                'dccDashboardAddress',
                array(
                    'mapType'            => $map_type,
                    'googleApiKey'       => (string) get_directorist_option('map_api_key'),
                    'countryRestriction' => (bool) get_directorist_option('country_restriction'),
                    'restrictedCountries' => array_values($restricted),
                )
            );

            if ($map_type === 'google') {
                $key = trim((string) get_directorist_option('map_api_key'));
                if ($key === '') {
                    return;
                }
                $google_url = sprintf(
                    'https://maps.googleapis.com/maps/api/js?loading=async&libraries=places&callback=dccDashboardAddressGoogleInit&key=%s',
                    rawurlencode($key)
                );
                wp_enqueue_script(
                    'dcc-google-maps-places',
                    esc_url_raw($google_url),
                    array($handle),
                    null,
                    true
                );
            }
        }

        /**
         * Template Exists
         */
        public function template_exists($template_file)
        {
            $file = DIRECTORIST_CUSTOM_CODE_DIR . '/templates/' . $template_file . '.php';

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

            $file = DIRECTORIST_CUSTOM_CODE_DIR . '/templates/' . $template_file . '.php';

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

    function Directorist_Custom_Code()
    {
        return Directorist_Custom_Code::instance();
    }

    if (directorist_is_plugin_active('directorist/directorist-base.php')) {
        Directorist_Custom_Code(); // get the plugin running
    }
}


?>