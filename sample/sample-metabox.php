<?php
/**
 * Post metabox.
 *
 * @package wp-plugin-base\admin\metaboxes\
 * @author Store Boost Kit <hello@storeboostkit.com>
 * @version 1.0
 */

namespace SBK_PB;

defined( 'ABSPATH' ) || exit;

$fields = array(
	'General' => array(
		array(
			'id'    => 'subtitle',
			'label' => 'Subtitle',
			'type'  => 'text',
		),
		array(
			'id'    => 'description',
			'label' => 'Description',
			'type'  => 'textarea',
		),
		array(
			'id' => 'faq5',
			'label' => 'FAQs',
			'type' => 'repeater',
			'fields' => array(
				array(
					'id' => 'question',
					'label' => 'Question',
					'type' => 'textarea',
				),
				array(
					'id' => 'answer',
					'label' => 'Answer',
					'type' => 'text',
				),
			),
		),
	),
	'Design' => array(
		array(
			'id'      => 'layout',
			'label'   => 'Layout',
			'type'    => 'select',
			'options' => array(
				'full'  => 'Full Width',
				'boxed' => 'Boxed',
			),
		),
		array(
			'id'       => 'layout_bg_color',
			'label'    => 'Background Color',
			'type'     => 'color',
			'condition'=> array(
				'field' => 'layout',
				'value' => 'boxed',
			),
		),
		array(
			'id'       => 'advanced_options',
			'label'    => 'Advanced Options',
			'type'     => 'text',
			'condition'=> array(
				'relation' => 'AND',
				'conditions' => array(
					array(
						'field' => 'layout',
						'value' => 'boxed',
					),
					array(
						'field' => 'is_featured',
						'value' => '1',
					),
				),
			),
		),
	),
	'Options' => array(
		array(
			'id'    => 'is_featured',
			'label' => 'Is Featured?',
			'type'  => 'checkbox',
		),
		array(
			'id'    => 'enable_comments',
			'label' => 'Enable Comments?',
			'type'  => 'switch',
		),
		array(
			'id'      => 'post_format',
			'label'   => 'Post Format',
			'type'    => 'radio',
			'options' => array(
				'standard' => 'Standard',
				'gallery'  => 'Gallery',
				'video'    => 'Video',
			),
		),
		array(
			'id'    => 'featured_image',
			'label' => 'Featured Image',
			'type'  => 'media',
		),
	),
);

new Metabox(
	'sbk-metabox-custom-fields',
	'Custom Fields',
	array( 'post' ),
	$fields
);
