<?php
add_action(
	'init',
	function() {
		$fields = array(
			'General Settings' => array(
				array(
					'title' => 'General',
					'type'  => 'group_start',
				),
				array(
					'id'          => 'site_name',
					'type'        => 'text',
					'label'       => 'Site Name',
					'description' => 'Enter your site name',
					'default'     => 'My Awesome Site',
				),
				array(
					'id'          => 'site_description',
					'type'        => 'textarea',
					'label'       => 'Site Description',
					'description' => 'Brief description of your site',
					'default'     => 'This is my awesome website',
				),
				array(
					'id'          => 'enable_feature',
					'type'        => 'checkbox',
					'label'       => 'Enable Special Feature',
					'description' => 'Check to enable the special feature',
					'default'     => '1',
				),
				array(
					'type' => 'group_end',
				),
				array(
					'id'          => 'feature_toggle',
					'type'        => 'switch',
					'label'       => 'Feature Toggle',
					'description' => 'Use switch to toggle feature on/off',
					'default'     => '1',
				),
				array(
					'id'          => 'primary_color',
					'type'        => 'color',
					'label'       => 'Primary Color',
					'description' => 'Choose your primary brand color',
					'default'     => '#0073aa',
				),
			),
			'Display Options' => array(
				array(
					'id'          => 'layout_type',
					'type'        => 'select',
					'label'       => 'Layout Type',
					'description' => 'Choose the layout for your pages',
					'default'     => 'grid',
					'options'     => array(
						'list'   => 'List View',
						'grid'   => 'Grid View',
						'cards'  => 'Card View',
						'table'  => 'Table View',
					),
				),
				array(
					'id'          => 'display_position',
					'type'        => 'radio',
					'label'       => 'Display Position',
					'description' => 'Where to display the content',
					'default'     => 'top',
					'options'     => array(
						'top'    => 'Top of Page',
						'bottom' => 'Bottom of Page',
						'left'   => 'Left Sidebar',
						'right'  => 'Right Sidebar',
					),
				),
				array(
					'id'          => 'items_per_page',
					'type'        => 'number',
					'label'       => 'Items Per Page',
					'description' => 'Number of items to display per page',
					'default'     => '10',
				),
			),
			'Advanced Settings' => array(
				array(
					'id'             => 'custom_css_html',
					'type'           => 'richtext_editor',
					'label'          => 'Custom CSS & HTML',
					'description'    => 'Add custom CSS and HTML code',
					'default_editor' => 'html',
					'options'        => array( 'html', 'css' ),
					'default'        => array(
						'html' => '<div class="custom-content">Hello World</div>',
						'css'  => '.custom-content { color: #333; font-size: 16px; }',
					),
				),
				array(
					'id'          => 'api_settings',
					'type'        => 'textarea',
					'label'       => 'API Configuration',
					'description' => 'JSON configuration for API settings',
					'default'     => '{"timeout": 30, "retries": 3}',
				),
			),
		);

		new SBK_PB\Settings(
			'options-general.php',      // Parent menu slug.
			'sbk-settings-sample',      // menu slug.
			esc_html__( 'Sample Settings', 'product-expiry-manager-for-woocommerce' ),
			esc_html__( 'Sample Settings', 'product-expiry-manager-for-woocommerce' ),
			'manage_options',
			$fields
		);
	}
);
