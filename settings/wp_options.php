<?php
//not used yet - moved them to a per gallery option
return array(
	'type'    => 'postbox',
	'label'   => 'Export Options',
	'options' => array(
		'enable_rest_wp_options_export' => array(
			'label'      => __( 'Selective export', 'pixcustomify_txtd' ),
			'default'    => false,
			'type'       => 'switch',
			'show_group' => 'post_rest_wp_options_group'
		),
		'post_rest_wp_options_group'         => array(
			'type'    => 'group',
			'options' => array(
				'select_wp_options_to_export' => array(
					'label'       => __( 'Options', 'pixfields_txtd' ),
					'type'        => 'wp_options_checkbox',
					'description' => 'Which options be available on rest'
				),
			)
		),
	)
); # config