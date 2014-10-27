<?php

return array(
	'type' => 'postbox',
	'label' => __('Featured Image', 'demo_xml_txtd'),
	'options' => array(
		'demo_xml_featured_images' => array (
			'label' => __('Replacing Feature images', 'demo_xml_txtd'),
			'default' => array(),
			'type' => 'gallery',
			'display_option' => ''
		),
	)
);