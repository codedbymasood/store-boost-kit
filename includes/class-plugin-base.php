<?php
/**
 * Plugin initialization class.
 *
 * @package wp-plugin-base\includes\
 * @version 8.6.0
 */

namespace WPPB;

defined( 'ABSPATH' ) || exit;

/**
 * Core plugin loader.
 */
final class Plugin_Base {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin_Base|null
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Plugin_Base
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
		$this->init_hooks();
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	private function __wakeup() {}

	/**
	 * Define plugin constants.
	 */
	private function define_constants() {
		define( 'WPPB_VERSION', '1.0.0' );
		define( 'WPPB_PATH', plugin_dir_path( dirname( __FILE__ ) ) );
		define( 'WPPB_URL', plugin_dir_url( dirname( __FILE__ ) ) );
	}

	/**
	 * Load required files.
	 */
	private function load_dependencies() {
		require_once WPPB_PATH . 'includes/class-settings.php';
	}

	/**
	 * Hook into WordPress.
	 */
	private function init_hooks() {
	}
}
