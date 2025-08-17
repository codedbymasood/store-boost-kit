<?php
/**
 * Admin class.
 *
 * @package store-boost-kit\admin\
 * @author Store Boost Kit <hello@storeboostkit.com>
 * @version 1.0
 */

namespace STOBOKIT;

defined( 'ABSPATH' ) || exit;

/**
 * Settings class.
 */
class Admin {

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts( $hook ) {
		wp_enqueue_style( 'stobokit-admin', STOBOKIT_URL . 'admin/assets/css/admin.css', array(), '1.0' );
		wp_enqueue_script( 'stobokit-admin', STOBOKIT_URL . 'admin/assets/js/admin.js', array(), '1.0', true );
	}

	/**
	 * Add menu page hook callback.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_menu_page(
			esc_html__( 'Store Boost Kit', 'store-boost-kit' ),
			esc_html__( 'Store Boost Kit', 'store-boost-kit' ),
			'manage_options',
			'store-boost-kit',
			array( $this, 'dashboard' ),
			'email',
			6
		);

		add_submenu_page(
			'store-boost-kit',
			esc_html__( 'Status', 'store-boost-kit' ),
			esc_html__( 'Status', 'store-boost-kit' ),
			'manage_options',
			'stobokit-status',
			array( $this, 'status' )
		);

		if ( apply_filters( 'sbk_show_tools_menu', false ) ) {
			add_submenu_page(
				'store-boost-kit',
				esc_html__( 'Tools', 'store-boost-kit' ),
				esc_html__( 'Tools', 'store-boost-kit' ),
				'manage_options',
				'stobokit-tools',
				array( $this, 'tools' )
			);
		}

		if ( apply_filters( 'sbk_show_license_menu', false ) ) {
			add_submenu_page(
				'store-boost-kit',
				esc_html__( 'License', 'store-boost-kit' ),
				esc_html__( 'License', 'store-boost-kit' ),
				'manage_options',
				'stobokit-license',
				array( $this, 'license' )
			);
		}

	}

	public function dashboard() {

	}

	public function status() {

	}

	public function tools() {

	}

	public function license() {
		include_once STOBOKIT_PATH . '/admin/views/license.php';

	}
}

new Admin();

