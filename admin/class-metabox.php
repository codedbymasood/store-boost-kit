<?php
/**
 * Metabox register class.
 *
 * @package plugin-slug\core\
 * @author Store Boost Kit <storeboostkit@gmail.com>
 * @version 1.0
 */

namespace STOBOKIT;

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
		wp_enqueue_style( 'metabox', STOBOKIT_URL . '/admin/assets/css/metabox.css', array(), '1.0' );
		wp_enqueue_script( 'admin', STOBOKIT_URL . '/admin/assets/js/metabox.js', array(), '1.0', true );
	}

	public function render_metabox( $post ) {
		wp_nonce_field( $this->metabox_id . '_nonce', $this->metabox_id . '_nonce' );

		if ( count( $this->fields ) > 1 ) {
			echo '<div class="stobokit-wrapper no-spacing">';
			echo '<div class="nav-tab-wrapper vertical">';
			// Render tabs if multiple field groups.
			echo '<div class="nav-tabs">';
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
			echo '<div class="nav-tab-content-wrapper">';
			foreach ( $this->fields as $tab_name => $tab_fields ) {
				$active_class = $first ? 'active' : '';
				$tab_id       = sanitize_title( $tab_name );
				echo '<div id="' . esc_attr( $tab_id ) . '" class="nav-tab-content ' . esc_attr( $active_class ) . '">';
				$this->render_fields( $tab_fields, $post );
				echo '</div>';
				$first = false;
			}
			echo '</div>';
			echo '</div>';
			echo '</div>';
		} else {
			echo '<div class="stobokit-wrapper spacing">';
			// Render single group.
			$this->render_fields( reset( $this->fields ), $post );
			echo '</div>';
		}
	}

	private function render_fields( $fields, $post ) {
		foreach ( $fields as $field ) {
			$this->render_field( $field, $post );
		}
	}

	private function render_field( $field, $post, $is_repeater = false, $repeater_index = null, $parent_field_id = null ) {
		if ( $is_repeater ) {
			$field_id   = $parent_field_id . '_' . $repeater_index . '_' . $field['id'];
			$field_name = $parent_field_id . '[' . $repeater_index . '][' . $field['id'] . ']';

			// Get repeater field value - using direct field ID.
			$parent_value = get_post_meta( $post->ID, $parent_field_id, true );
			$field_value  = isset( $parent_value[ $repeater_index ][ $field['id'] ] ) ? $parent_value[ $repeater_index ][ $field['id'] ] : '';
		} else {
			$field_id    = $field['id'];
			$field_name  = $field['id'];
			$field_value = get_post_meta( $post->ID, $field['id'], true );
		}

		if ( isset( $field['condition'] ) && ! $is_repeater ) {
			printf(
				'<div class="field-wrap field-%s" %s>',
				esc_attr( $field['type'] ),
				'data-condition="' . esc_attr( wp_json_encode( $field['condition'] ) ) . '"'
			);
		} else {
			echo '<div class="field-wrap field-' . esc_attr( $field['type'] ) . '">';
		}

		if ( isset( $field['label'] ) ) {
			echo '<label for="' . esc_attr( $field_id ) . '">' . esc_html( $field['label'] ) . '</label>';
		}

		// Handle repeater field type.
		if ( 'repeater' === $field['type'] && ! $is_repeater ) {
			$this->render_repeater_field( $field, $post, $field_value );
		} else {
			// Render all other field types using callback.
			$this->render_field_input( $field, $field_id, $field_name, $field_value, $is_repeater );
		}

		if ( isset( $field['description'] ) ) {
			echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
		}

		echo '</div>';
	}

	/**
	 * Render repeater fields
	 *
	 * @param array   $field Field.
	 * @param WP_Post $post Post.
	 * @param array   $field_value Field value.
	 * @return void
	 */
	private function render_repeater_field( $field, $post, $field_value ) {
		$repeater_values = (array) $field_value;
		echo '<div class="repeater-container" data-field-id="' . esc_attr( $field['id'] ) . '">';

		// Render existing items.
		if ( ! empty( $field_value ) && ! empty( $repeater_values ) && is_array( $repeater_values ) ) {
			foreach ( $repeater_values as $index => $repeater_value ) {
				echo '<div class="repeater-item" data-index="' . esc_attr( $index ) . '">';
				echo '<div class="repeater-header">';
				echo '<span class="repeater-title">' . esc_html__( 'Item: ', 'store-boost-kit' ) . ' ' . esc_html( ( (int) $index + 1 ) ) . '</span>';
				echo '<span class="remove-item"><span class="dashicons dashicons-no-alt"></span></span>';
				echo '</div>';
				echo '<div class="repeater-content">';

				foreach ( $field['fields'] as $repeater_field ) {
					$this->render_field( $repeater_field, $post, true, $index, $field['id'] );
				}

				echo '</div>';
				echo '</div>';
			}
		}

		// Template for new items.
		echo '<script type="text/template" class="repeater-template">';
		echo '<div class="repeater-item" data-index="{INDEX}">';
		echo '<div class="repeater-header">';
		echo '<span class="repeater-title">' . esc_html__( 'Item: ', 'store-boost-kit' ) . ' {INDEX_DISPLAY}</span>';
		echo '<span class="remove-item"><span class="dashicons dashicons-no-alt"></span></span>';
		echo '</div>';
		echo '<div class="repeater-content">';

		foreach ( $field['fields'] as $repeater_field ) {
			$template_field_id = $field['id'] . '_{INDEX}_' . $repeater_field['id'];
			$template_field_name = $field['id'] . '[{INDEX}][' . $repeater_field['id'] . ']';

			echo '<div class="metabox-field">';
			if ( isset( $repeater_field['label'] ) ) {
				echo '<label for="' . esc_attr( $template_field_id ) . '">' . esc_html( $repeater_field['label'] ) . '</label>';
			}

			// Render field without value and with template placeholders.
			$this->render_field_input( $repeater_field, $template_field_id, $template_field_name, '', true );

			if ( isset( $repeater_field['description'] ) ) {
				echo '<p class="description">' . esc_html( $repeater_field['description'] ) . '</p>';
			}
			echo '</div>';
		}

		echo '</div>';
		echo '</div>';
		echo '</script>';

		echo '</div>';
		echo '<button type="button" class="button add-repeater-item">' . esc_html__( 'Add Item', 'store-boost-kit' ) . '</button>';
	}

	private function render_field_input( $field, $field_id, $field_name, $field_value, $is_repeater = false ) {
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
						printf(
							'<option value="%s" %s>%s</option>',
							esc_attr( $option_value ),
							selected( $field_value, $option_value, false ),
							esc_html( $option_label )
						);
					}
				}
				echo '</select>';
				break;

			case 'radio':
				if ( isset( $field['options'] ) ) {
					echo '<div class="radio-group">';
					foreach ( $field['options'] as $option_value => $option_label ) {
						$checked  = checked( $field_value, $option_value, false );
						$radio_id = $field_id . '_' . $option_value;

						echo '<label for="' . esc_attr( $radio_id ) . '">';
						printf(
							'<input type="radio" id="%s" name="%s" value="%s" %s />',
							esc_attr( $radio_id ),
							esc_attr( $field_name ),
							esc_attr( $option_value ),
							esc_attr( $checked )
						);
						echo esc_html( $option_label );
						echo '</label>';
					}
					echo '</div>';
				}
				break;

			case 'checkbox':
				printf(
					'<input type="checkbox" id="%s" name="%s" value="1" %s />',
					esc_attr( $field_id ),
					esc_attr( $field_name ),
					checked( $field_value, '1', false )
				);
				break;

			case 'switch':
				$checked = checked( $field_value, '1', false );
				echo '<label class="switch-control">';
				printf(
					'<input type="checkbox" id="%s" name="%s" value="1" %s />',
					esc_attr( $field_id ),
					esc_attr( $field_name ),
					checked( $field_value, '1', false )
				);
				echo '<span class="slider round"></span>';
				echo '</label>';
				break;

			case 'color':
				$color_class = $is_repeater ? 'color-picker-repeater' : 'color-picker';
				echo '<input type="text" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" class="' . esc_attr( $color_class ) . '" />';
				break;

			case 'media':
				echo '<div class="field-content">';

				$multiple  = isset( $field['multiple'] ) ? $field['multiple'] : false;
				$file_type = isset( $field['file_type'] ) ? $field['file_type'] : 'all'; // ['image', 'video'] or 'image' or 'all'.

				// Handle multiple vs single media.
				if ( $multiple ) {
					$media_ids = $field_value ? (array) $field_value : array();
					echo '<input type="hidden" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( implode( ',', $media_ids ) ) . '" />';
				} else {
					echo '<input type="hidden" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $field_value ) . '" />';
				}

				printf(
					'<button type="button" class="button upload-media-button" data-multiple="%s" data-file-type="%s">%s</button>',
					( $multiple ) ? 'true' : 'false',
					esc_attr( is_array( $file_type ) ? wp_json_encode( $file_type ) : $file_type ),
					esc_html__( 'Select Media', 'store-boost-kit' )
				);

				echo '<button type="button" class="button remove-media-button">' . esc_html__( 'Remove All', 'store-boost-kit' ) . '</button>';

				echo '<div class="media-preview">';

				if ( $multiple && ! empty( $media_ids ) ) {
					echo '<div class="media-gallery">';
					foreach ( $media_ids as $media_id ) {
						if ( $media_id ) {
							$media_url       = wp_get_attachment_url( $media_id );
							$media_filename  = basename( $media_url );
							$file_type_check = wp_check_filetype( $media_url );

							echo '<div class="media-item" data-id="' . esc_attr( $media_id ) . '">';
							echo '<span class="remove-single-media"><span class="dashicons dashicons-no-alt"></span></span>';

							if ( strpos( $file_type_check['type'], 'image' ) !== false ) {
								echo '<img src="' . esc_url( $media_url ) . '" style="max-width: 100px; height: auto;" />';
							} elseif ( strpos( $file_type_check['type'], 'video' ) !== false ) {
								echo '<video width="100" controls><source src="' . esc_url( $media_url ) . '" type="' . esc_attr( $file_type_check['type'] ) . '"></video>';
							} else {
								echo '<p class="file-name">' . esc_html( $media_filename ) . '</p>';
							}
							echo '</div>';
						}
					}
					echo '</div>';
				} elseif ( ! $multiple && $field_value ) {
					$media_url = wp_get_attachment_url( $field_value );
					if ( $media_url ) {
						$media_filename  = basename( $media_url );
						$file_type_check = wp_check_filetype( $media_url );

						echo '<div class="media-item" data-id="' . esc_attr( $field_value ) . '">';
						if ( strpos( $file_type_check['type'], 'image' ) !== false ) {
							echo '<img src="' . esc_url( $media_url ) . '" style="max-width: 150px; height: auto;" />';
						} elseif ( strpos( $file_type_check['type'], 'video' ) !== false ) {
							echo '<video width="150" controls><source src="' . esc_url( $media_url ) . '" type="' . esc_attr( $file_type_check['type'] ) . '"></video>';
						} else {
							echo '<p>' . esc_html( $media_filename ) . '</p>';
						}
						echo '</div>';
					}
				}

				echo '</div>';
				echo '</div>';
				break;
		}
	}

	public function save_metabox( $post_id ) {
		if ( ! isset( $_POST[ $this->metabox_id . '_nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $this->metabox_id . '_nonce' ] ) ), $this->metabox_id . '_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Get all fields with their types
		$all_fields = $this->get_all_fields_with_types( $this->fields );

		foreach ( $all_fields as $field_id => $field_type ) {
			if ( isset( $_POST[ $field_id ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$sanitized_value = $this->sanitize_field_value( wp_unslash( $_POST[ $field_id ] ), $field_type );
				update_post_meta( $post_id, $field_id, $sanitized_value );
			} else {
				delete_post_meta( $post_id, $field_id );
			}
		}
	}

	/**
	 * Get all fields with their types from the fields configuration.
	 *
	 * @param array $fields Fields.
	 * @return array Array of field_id => field_type pairs
	 */
	private function get_all_fields_with_types( $fields ) {
		$field_data = array();

		foreach ( $fields as $tab_fields ) {
			if ( is_array( $tab_fields ) ) {
				foreach ( $tab_fields as $field ) {
					$field_data[ $field['id'] ] = isset( $field['type'] ) ? $field['type'] : 'text';
					
					// Handle repeater fields
					if ( isset( $field['type'] ) && 'repeater' === $field['type'] && isset( $field['fields'] ) ) {
						foreach ( $field['fields'] as $repeater_field ) {
							// Store repeater sub-field types with parent context
							$field_data[ $field['id'] . '_repeater_' . $repeater_field['id'] ] = isset( $repeater_field['type'] ) ? $repeater_field['type'] : 'text';
						}
					}
				}
			}
		}
		return $field_data;
	}

	/**
	 * Sanitize field value based on field type
	 *
	 * @param mixed  $value Field value
	 * @param string $field_type Field type
	 * @return mixed Sanitized value
	 */
	private function sanitize_field_value( $value, $field_type = 'text' ) {
		$value = wp_unslash( $value );

		if ( is_array( $value ) ) {
			return array_map( function( $item ) use ( $field_type ) {
				return $this->sanitize_field_value( $item, $field_type );
			}, $value );
		}

		// Handle media field types specifically
		if ( 'media' === $field_type ) {
			// Handle comma-separated media IDs
			if ( strpos( $value, ',' ) !== false ) {
				$ids = explode( ',', $value );
				$sanitized_ids = array_filter( array_map( 'absint', $ids ) );
				return implode( ',', $sanitized_ids );
			}
			// Handle single media ID
			if ( is_numeric( $value ) ) {
				return absint( $value );
			}
		}

		// For all other field types, use appropriate sanitization
		switch ( $field_type ) {
			case 'textarea':
				return sanitize_textarea_field( $value );
			
			case 'text':
				return sanitize_text_field( $value );
			
			case 'checkbox':
			case 'switch':
				return $value === '1' ? '1' : '';
			
			case 'select':
			case 'radio':
				return sanitize_text_field( $value );
			
			case 'color':
				return sanitize_hex_color( $value );
			
			default:
				return sanitize_textarea_field( $value );
		}
	}

	public static function get_media_field( $post_id, $field_id, $multiple = false ) {
		$value = get_post_meta( $post_id, $field_id, true );

		if ( $multiple ) {
			if ( empty( $value ) ) {
				return array();
			}

			$ids = explode( ',', $value );
			$media_items = array();

			foreach ( $ids as $id ) {
				if ( $id ) {
					$url = wp_get_attachment_url( $id );
					if ( $url ) {
						$media_items[] = array(
							'id'       => $id,
							'url'      => $url,
							'filename' => basename( $url ),
							'type'     => wp_check_filetype( $url )['type'],
						);
					}
				}
			}

			return $media_items;
		}

		if ( $value ) {
			$url = wp_get_attachment_url( $value );
			if ( $url ) {
				return array(
					'id'       => $value,
					'url'      => $url,
					'filename' => basename( $url ),
					'type'     => wp_check_filetype( $url )['type'],
				);
			}
		}

		return null;
	}

	public static function get_field( $post_id, $field_id ) {
		return get_post_meta( $post_id, $field_id, true );
	}
}