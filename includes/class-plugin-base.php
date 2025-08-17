<?php
/**
 * Plugin initialization class.
 *
 * @package wp-plugin-base\includes\
 * @author Store Boost Kit <hello@storeboostkit.com>
 * @version 1.0
 */

namespace SBK_PB;

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
	 * Define plugin constants.
	 */
	private function define_constants() {
		define( 'SBK_PB_VERSION', '1.0.0' );
		define( 'SBK_PB_PATH', plugin_dir_path( dirname( __FILE__ ) ) );
		define( 'SBK_PB_URL', plugin_dir_url( dirname( __FILE__ ) ) );
	}

	/**
	 * Load required files.
	 */
	private function load_dependencies() {
		require_once SBK_PB_PATH . 'includes/class-utils.php';
		require_once SBK_PB_PATH . 'admin/class-settings.php';
	}

	/**
	 * Hook into WordPress.
	 */
	private function init_hooks() {
		// Register metaboxes.
		require_once SBK_PB_PATH . 'admin/class-metabox.php';
	}
}
