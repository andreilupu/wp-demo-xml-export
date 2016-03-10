<?php

return array(
	'type'    => 'postbox',
	'label'   => 'Rest Export',
	'class' => 'half-box',
	'options' => array(
		'enable_rest_export' => array(
			'label'      => __( 'Enable Rest Export', 'pixcustomify_txtd' ),
			'default'    => false,
			'type'       => 'switch',
			'show_group' => 'rest_types_group'
		),
		'rest_types_group'         => array(
			'type'    => 'group',
			'options' => array(

				'enable_rest_export_post_types' => array(
					'label'      => __( 'Enable Rest Post Types Export', 'pixcustomify_txtd' ),
					'default'    => false,
					'type'       => 'switch',
					'show_group' => 'rest_types_group_post_types'
				),
				'rest_types_group_post_types'         => array(
					'type'    => 'group',
					'options' => array(
						'rest_types_export' => array(
							'label'       => __( 'Taxonomies', 'pixfields_txtd' ),
							'default'     => array( 'post' => 'on', 'page' => 'on' ),
							'type'        => 'post_types_checkbox',
							'description' => 'Which post types should have export box'
						),
					)
				),

				'enable_rest_export_taxonomies' => array(
					'label'      => __( 'Enable Rest Post Types Export', 'pixcustomify_txtd' ),
					'default'    => false,
					'type'       => 'switch',
					'show_group' => 'rest_types_group_taxonomies'
				),
				'rest_types_group_taxonomies'         => array(
					'type'    => 'group',
					'options' => array(
						'rest_taxes_export' => array(
							'label'       => __( 'Taxonomies', 'pixfields_txtd' ),
							'default'     => array( 'categories' => 'on' ),
							'type'        => 'taxonomies_checkbox',
							'description' => 'Which post types should have export box'
						),
					)
				),

			)
		),
	)
); # config