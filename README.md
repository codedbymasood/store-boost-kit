
# WordPress Base Plugin Framework

A lightweight, extendable WordPress **plugin boilerplate** built to streamline your development workflow. This base framework includes:

- Modular structure
- Custom metabox system

Perfect for building small to medium-sized plugins without starting from scratch every time.

---

## 🔧 Installation

1. Download and unzip the plugin folder.
2. Upload the folder to your `/wp-content/plugins/` directory.
3. Activate the plugin via the **Plugins** menu in WordPress.

---

## 🧩 How to Register a metabox

You can register a custom metabox like this:

```php
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
			'id' => 'faq',
			'label' => 'FAQs',
			'type' => 'repeater',
			'fields' => array(
				array(
					'id' => 'question',
					'label' => 'Question',
				),
				array(
					'id' => 'answer',
					'label' => 'Answer',
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
	'custom_metabox', // Metabox ID
	'Custom Fields',  // Title
	array( 'post' ),  // Post types
	$fields           // Fields array
);
```

---

## 📩 Feedback or Issues?

If you find any bugs or want to contribute, feel free to open a GitHub issue or pull request.

---

Crafted with ❤️ for WordPress devs who love clean structure.
