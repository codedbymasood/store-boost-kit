<?php
/**
 * Metabox register class.
 *
 * @package wp-plugin-base\includes\
 * @author Masood Mohamed <iam.masoodmohd@gmail.com>
 * @version 1.0
 */

namespace WPPB;

defined( 'ABSPATH' ) || exit;

/**
 * Metabox class.
 */
class Metabox {

	private $metabox_id;
	private $metabox_title;
	private $post_types;
	private $fields;
	private $context;
	private $priority;

	public function __construct( $metabox_id, $metabox_title, $post_types, $fields, $context = 'normal', $priority = 'default' ) {
		$this->metabox_id    = $metabox_id;
		$this->metabox_title = $metabox_title;
		$this->post_types    = (array) $post_types;
		$this->fields        = $fields;
		$this->context       = $context;
		$this->priority      = $priority;

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function add_metabox() {
		foreach ( $this->post_types as $post_type ) {
			add_meta_box(
				$this->metabox_id,
				$this->metabox_title,
				array( $this, 'render_metabox' ),
				$post_type,
				$this->context,
				$this->priority
			);
		}
	}

	public function enqueue_scripts( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
				return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'metabox', WPPB_URL . 'admin/assets/css/metabox.css', array(), '1.0' );
		wp_enqueue_script( 'admin', WPPB_URL . 'admin/assets/js/admin.js', array(), '1.0', true );
	}

	public function render_metabox( $post ) {
		wp_nonce_field( $this->metabox_id . '_nonce', $this->metabox_id . '_nonce' );

		if ( count( $this->fields ) > 1 ) {
			// Render tabs if multiple field groups.
			echo '<div class="metabox-tabs">';
			$first = true;
			foreach ( $this->fields as $tab_name => $tab_fields ) {
				$active_class = $first ? 'nav-tab-active' : '';
				$tab_id       = sanitize_title( $tab_name );

				echo '<span class="nav-tab ' . esc_attr( $active_class ) . '" data-target="' . esc_attr( $tab_id ) . '">' . esc_html( $tab_name ) . '</span>';
				$first = false;
			}
			echo '</div>';

			// Render tab content.
			$first = true;
			foreach ( $this->fields as $tab_name => $tab_fields ) {
				$active_class = $first ? 'active' : '';
				$tab_id       = sanitize_title( $tab_name );
				echo '<div id="' . esc_attr( $tab_id ) . '" class="metabox-tab-content ' . esc_attr( $active_class ) . '">';
				$this->render_fields( $tab_fields, $post );
				echo '</div>';
				$first = false;
			}
		} else {
			// Render single group.
			$this->render_fields( reset( $this->fields ), $post );
		}
	}

	private function render_fields( $fields, $post ) {
		foreach ( $fields as $field ) {
			$this->render_field( $field, $post );
		}
	}

	private function render_field( $field, $post ) {
		$field_id    = $this->metabox_id . '_' . $field['id'];
		$field_name  = $this->metabox_id . '[' . $field['id'] . ']';
		$field_value = get_post_meta( $post->ID, $field_name, true );

		$condition_attr = '';
		if ( isset( $field['condition'] ) ) {
			$condition_attr = 'data-condition="' . esc_attr( wp_json_encode( $field['condition'] ) ) . '"';
		}

		echo '<div class="metabox-field" ' . $condition_attr . '>';

		if ( isset( $field['label'] ) ) {
			echo '<label for="' . esc_attr( $field_id ) . '">' . esc_html( $field['label'] ) . '</label>';
		}

		switch ( $field['type'] ) {
			case 'text':
				echo '<input type="text" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" />';
				break;

			case 'textarea':
				echo '<textarea id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '">' . esc_textarea( $field_value ) . '</textarea>';
				break;

			case 'select':
				echo '<select id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '">';
				if ( isset( $field['options'] ) ) {
					foreach ( $field['options'] as $option_value => $option_label ) {
						$selected = selected( $field_value, $option_value, false );
						echo '<option value="' . esc_attr( $option_value ) . '" ' . $selected . '>' . esc_html( $option_label ) . '</option>';
					}
				}
				echo '</select>';
				break;

			case 'radio':
				if ( isset( $field['options'] ) ) {
					foreach ( $field['options'] as $option_value => $option_label ) {
						$checked = checked( $field_value, $option_value, false );
						echo '<label><input type="radio" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $option_value ) . '" ' . $checked . ' /> ' . esc_html( $option_label ) . '</label>';
					}
				}
				break;

			case 'checkbox':
				$checked = checked( $field_value, '1', false );
				echo '<input type="checkbox" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="1" ' . $checked . ' />';
				break;

			case 'switch':
				$checked = checked( $field_value, '1', false );
				echo '<div class="switch-container">';
				echo '<input type="checkbox" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="1" ' . $checked . ' />';
				echo '<span class="switch-slider"></span>';
				echo '</div>';
				break;

			case 'color':
				echo '<input type="text" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" class="color-picker" />';
				break;

			case 'media':
				$media_url      = '';
				$media_filename = '';
				if ( $field_value ) {
					$attachment = wp_get_attachment_url( $field_value );
					if ( $attachment ) {
						$media_url = $attachment;
						$media_filename = basename( $attachment );
					}
				}
				echo '<input type="hidden" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" />';
				echo '<button type="button" class="button upload-media-button">' . esc_html__( 'Select Media', 'wp-plugin-base' ) . '</button>';
				echo '<button type="button" class="button remove-media-button">' . esc_html__( 'Remove', 'wp-plugin-base' ) . '</button>';
				echo '<div class="media-preview">';
				if ( $media_url ) {
					$file_type = wp_check_filetype( $media_url );
					if ( strpos( file_type['type'], 'image') !== false ) {
						echo '<img src="' . esc_url( $media_url ) . '" />';
					} else {
						echo '<p>' . esc_html( $media_filename ) . '</p>';
					}
				}
				echo '</div>';
				break;

			case 'repeater':
				$repeater_values = (array) $field_value;
				echo '<div class="repeater-container">';

				foreach ( $repeater_values as $index => $repeater_value ) {
					echo '<div class="repeater-item">';
					echo '<span class="remove-item">×</span>';
					foreach ( $field['fields'] as $repeater_field ) {
						$repeater_field['id'] = $field['id'] . '][' . $index . '][' . $repeater_field['id'];
						$repeater_field_value = isset( $repeater_value[ $repeater_field['id'] ] ) ? $repeater_value[ $repeater_field['id'] ] : '';

						echo '<div class="metabox-field">';
						if ( isset( $repeater_field['label'] ) ) {
							echo '<label>' . esc_html( $repeater_field['label'] ) . '</label>';
						}

						$repeater_field_name = $this->metabox_id . '[' . $field['id'] . '][' . $index . '][' . $repeater_field['id'] . ']';
						echo '<input type="text" name="' . esc_attr( $repeater_field_name ) . '" value="' . esc_attr( $repeater_field_value ) . '" />';
						echo '</div>';
					}
					echo '</div>';
				}

				// Template for new items.
				echo '<div class="repeater-template" style="display: none;">';
				echo '<div class="repeater-item">';
				echo '<span class="remove-item">×</span>';
				foreach ( $field['fields'] as $repeater_field ) {
					echo '<div class="metabox-field">';
					if ( isset( $repeater_field['label'] ) ) {
						echo '<label>' . esc_html( $repeater_field['label'] ) . '</label>';
					}
					$repeater_field_name = $this->metabox_id . '[' . $field['id'] . '][{INDEX}][' . $repeater_field['id'] . ']';
					echo '<input type="text" name="' . esc_attr( $repeater_field_name ) . '" value="" />';
					echo '</div>';
				}
				echo '</div>';
				echo '</div>';

				echo '</div>';
				echo '<button type="button" class="add-repeater-item">Add Item</button>';
				break;
		}

		if ( isset( $field['description'] ) ) {
			echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
		}

		echo '</div>';
	}

	public function save_metabox( $post_id ) {
		if ( ! isset( $_POST[ $this->metabox_id . '_nonce' ] ) || ! wp_verify_nonce( wp_unslash( $_POST[ $this->metabox_id . '_nonce' ] ), $this->metabox_id . '_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST[ $this->metabox_id ] ) ) {
			$data = $_POST[ $this->metabox_id ];
			foreach ( $data as $key => $value ) {
				update_post_meta( $post_id, $this->metabox_id . '[' . $key . ']', $value );
			}
		}
	}

	// Helper method to get field value.
	public static function get_field( $post_id, $metabox_id, $field_id ) {
		return get_post_meta( $post_id, $metabox_id . '[' . $field_id . ']', true );
	}
}
