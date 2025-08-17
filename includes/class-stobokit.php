<?php
/**
 * Plugin initialization class.
 *
 * @package store-boost-kit\includes\
 * @author Store Boost Kit <hello@storeboostkit.com>
 * @version 1.0
 */

namespace STOBOKIT;

defined( 'ABSPATH' ) || exit;

/**
 * Core plugin loader.
 */
final class STOBOKIT {

	public $utils;

	/**
	 * Singleton instance.
	 *
	 * @var STOBOKIT|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return STOBOKIT
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->load_dependencies();
	}

	/**
	 * Define plugin constants.
	 */
	private function define_constants() {
		define( 'STOBOKIT_VERSION', '1.0.0' );
		define( 'STOBOKIT_PATH', plugin_dir_path( dirname( __FILE__ ) ) );
		define( 'STOBOKIT_URL', plugin_dir_url( dirname( __FILE__ ) ) );
	}

	/**
	 * Load required files.
	 */
	private function load_dependencies() {
		require_once STOBOKIT_PATH . 'includes/class-utils.php';
		require_once STOBOKIT_PATH . 'admin/class-license.php';
		require_once STOBOKIT_PATH . 'admin/class-admin.php';
		require_once STOBOKIT_PATH . 'admin/class-settings.php';
		require_once STOBOKIT_PATH . 'admin/class-metabox.php';
	}
}
