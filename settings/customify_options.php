<?php
//not used yet - moved them to a per gallery option
return array(
	'type'    => 'postbox',
	'label'   => 'Customify',
	'class' => 'half-box',
	'options' => array(
		'enable_selective_export' => array(
			'label'      => __( 'Export', 'pixcustomify_txtd' ),
			'type'       => 'export_customify'
		),
	)
); # config