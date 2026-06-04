<?php
/**
 * Custom extension hooks and helpers.
 *
 * Add project-specific PHP code here.
 *
 * @package Directorist_Custom_Code
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once DIRECTORIST_CUSTOM_CODE_DIR . 'inc/class-category-list-shortcode.php';

Directorist_Custom_Code_Category_List_Shortcode::instance();
