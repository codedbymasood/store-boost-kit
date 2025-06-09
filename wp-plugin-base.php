<?php
/**
 * Plugin Name: WP Plugin Base
 * Plugin URI: https://github.com/masoodmohamed90/wp-plugin-base
 * Description: A lightweight and reusable starter framework for building structured, scalable WordPress plugins with clean code and modular architecture.
 * Version: 1.0
 * Author: Masood Mohamed
 * Author URI: https://github.com/masoodmohamed90/
 * Text Domain: wp-plugin-base
 * Domain Path: /languages/
 * Requires at least: 6.6
 * Requires PHP: 7.4
 *
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WPPB_PLUGIN_FILE' ) ) {
	define( 'WPPB_PLUGIN_FILE', __FILE__ );
}

// Include the main class.
if ( ! class_exists( 'Plugin_Base', false ) ) {
	include_once dirname( WPPB_PLUGIN_FILE ) . '/includes/class-plugin-base.php';
}

/**
 * Returns the main instance of WP_Plugin_Base.
 *
 * @since  1.0
 * @return WP_Plugin_Base
 */
function wp_plugin_base() {
	return \WPPB\Plugin_Base::instance();
}

// Global for backwards compatibility.
$GLOBALS['wp_plugin_base'] = wp_plugin_base();
