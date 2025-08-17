<?php
/**
 * Plugin Name: Store Boost Kit
 * Plugin URI: https://github.com/masoodmohamed90/store-boost-kit
 * Description: A lightweight and reusable starter framework for building structured, scalable WordPress plugins with clean code and modular architecture.
 * Version: 1.0
 * Author: Store Boost Kit
 * Author URI: https://storeboostkit.com
 * Text Domain: store-boost-kit
 * Domain Path: /languages/
 * Requires at least: 6.6
 * Requires PHP: 7.4
 *
 * @package store-boost-kit
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'STOBOKIT_PLUGIN_FILE' ) ) {
	define( 'STOBOKIT_PLUGIN_FILE', __FILE__ );
}

// Include the main class.
if ( ! class_exists( 'Plugin_Base', false ) ) {
	include_once dirname( STOBOKIT_PLUGIN_FILE ) . '/includes/class-stobokit.php';
}

/**
 * Returns the main instance of WP_Plugin_Base.
 *
 * @since  1.0
 * @return WP_Plugin_Base
 */
function stobokit() {
	return \STOBOKIT\STOBOKIT::instance();
}

// Global for backwards compatibility.
$GLOBALS['stobokit'] = stobokit();
