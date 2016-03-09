<?php
//not used yet - moved them to a per gallery option
return array(
	'type'    => 'postbox',
	'label'   => 'Metadata Options',
	'class' => 'half-box',
	'options' => array(
//		'enable_selective_export' => array(
//			'label'      => __( 'Selective export', 'pixcustomify_txtd' ),
//			'default'    => false,
//			'type'       => 'switch',
//			'show_group' => 'post_types_group'
//		),
//		'post_types_group'         => array(
//			'type'    => 'group',
//			'options' => array(
				'select_wp_options_to_export' => array(
					'label'       => __( 'Options', 'pixfields_txtd' ),
//					'default'     => array( 'post' => 'on', 'page' => 'on' ),
					'type'        => 'post_metadata_checkbox',
					'description' => 'Which metadata be available on rest'
				),
//			)
//		),
	)
); # config