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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'show_user_profile', array( $this, 'add_social_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_social_profile_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_social_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_social_profile_fields' ) );
	}

	public function enqueue_scripts( $hook ) {
		wp_enqueue_style( 'stobokit-admin', STOBOKIT_URL . 'admin/assets/css/admin.css', array(), '1.0' );
		wp_enqueue_script( 'stobokit-admin', STOBOKIT_URL . 'admin/assets/js/admin.js', array(), '1.0', true );
	}

	/**
	 * Add social profile fields to user profile
	 */
	public function add_social_profile_fields( $user ) {
		wp_nonce_field( 'update_social_profiles', 'social_profiles_nonce' );
		?>
		<h3><?php esc_html_e( 'Social Profiles', 'store-boost-kit' ); ?></h3>
		
		<table class="form-table">
				<tr>
					<th><label for="linkedin_url"><?php esc_html_e( 'LinkedIn URL', 'store-boost-kit' ); ?></label></th>
					<td>
						<input type="url" 
							name="linkedin_url" 
							id="linkedin_url" 
							value="<?php echo esc_attr( get_user_meta( $user->ID, 'linkedin_url', true ) ); ?>" 
							class="regular-text" 
							placeholder="https://linkedin.com/in/yourprofile" />
						<p class="description"><?php esc_html_e( 'Enter your LinkedIn profile URL', 'store-boost-kit' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th><label for="facebook_url"><?php esc_html_e( 'Facebook URL', 'store-boost-kit' ); ?></label></th>
					<td>
						<input type="url" 
							name="facebook_url" 
							id="facebook_url" 
							value="<?php echo esc_attr( get_user_meta( $user->ID, 'facebook_url', true ) ); ?>" 
							class="regular-text" 
							placeholder="https://facebook.com/yourprofile" />
						<p class="description"><?php esc_html_e( 'Enter your Facebook profile URL', 'store-boost-kit' ); ?></p>
					</td>
				</tr>
				
				<tr>
					<th><label for="twitter_url"><?php esc_html_e( 'Twitter/X URL', 'store-boost-kit' ); ?></label></th>
					<td>
						<input type="url" 
							name="twitter_url" 
							id="twitter_url" 
							value="<?php echo esc_attr( get_user_meta( $user->ID, 'twitter_url', true ) ); ?>" 
							class="regular-text" 
							placeholder="https://twitter.com/yourhandle" />
						<p class="description"><?php esc_html_e( 'Enter your Twitter/X profile URL', 'store-boost-kit' ); ?></p>
					</td>
				</tr>
		</table>
		<?php
	}

	/**
	 * Save social profile fields
	 */
	public function save_social_profile_fields( $user_id ) {
		// Check user capabilities.
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		// Verify nonce.
		if ( ! isset( $_POST['social_profiles_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['social_profiles_nonce'] ) ), 'update_social_profiles' ) ) {
			return false;
		}

		// Save fields.
		$social_fields = array( 'linkedin_url', 'facebook_url', 'twitter_url' );

		foreach ( $social_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_user_meta( $user_id, $field, esc_url( sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) ) );
			}
		}
	}
}

new Admin();

