<?php
//not used yet - moved them to a per gallery option
return array
	(
		'type' => 'postbox',
		'label' => 'General Settings',
		'options' => array
			(
				'image_display_name' => array (
						'name' => 'image_display_name',
						'label' => __('Images Name', 'demo_xml_txtd'),
						'desc' => __('What image name should we display under each one?', 'demo_xml_txtd'),
						'default' => 'unique_ids',
						'type' => 'select',
						'options' => array (
								'unique_ids' => __('Unique IDs', 'demo_xml_txtd'),
								'consecutive_ids' => __('Consecutive IDs', 'demo_xml_txtd'),
								'file_name' => __('File Name', 'demo_xml_txtd'),
								'consecutive_ids_image_title' => __('IDs and Image Title', 'demo_xml_txtd'),
							),
					),
			)
	); # config