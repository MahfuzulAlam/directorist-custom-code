<?php

/** 
 * @package  Directorist - TikTok Social Link
 */

/**
 * Plugin Name:       Directorist - TikTok Social Link
 * Plugin URI:        https://wpwax.com
 * Description:       TokTok social link for direcorist
 * Version:           1.0.0
 * Requires at least: 5.2
 * Author:            wpWax
 * Author URI:        https://wpwax.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       directorist-tiktok-social
 * Domain Path:       /languages
 */

/* This is an extension for Directorist plugin. It helps using custom code and template overriding of Directorist plugin.*/

/**
 * If this file is called directly, abrot!!!
 */
if (!defined('ABSPATH')) {
    exit;                      // Exit if accessed
}

if (!class_exists('Directorist_TikTok_Social')) {

    final class Directorist_TikTok_Social
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
            if (!isset(self::$instance) && !(self::$instance instanceof Directorist_TikTok_Social)) {
                self::$instance = new Directorist_TikTok_Social;
                self::$instance->define_constant();
                add_filter('directorist_template', array(self::$instance, 'directorist_template'), 10, 2);
            }
            return self::$instance;
        }

        /**
         * Define
         */
        public function define_constant()
        {
            define('TIKTOK_URI', plugin_dir_url(__FILE__));
        }

        /**
         * Base Directory
         */
        public function base_dir()
        {
            return plugin_dir_path(__FILE__);
        }

        /**
         * Template Exists
         */
        public function template_exists($template_file)
        {
            $file = $this->base_dir() . '/templates/' . $template_file . '.php';

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

            $file = $this->base_dir() . '/templates/' . $template_file . '.php';

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

    function Directorist_TikTok_Social()
    {
        return Directorist_TikTok_Social::instance();
    }

    if (directorist_is_plugin_active('directorist/directorist-base.php')) {
        Directorist_TikTok_Social(); // get the plugin running
    }
}
