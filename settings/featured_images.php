<?php

return array(
	'type' => 'postbox',
	'label' => __('Replace Featured Images with', 'demo_xml_txtd'),
	'class' => 'half-box',
	'options' => array(
		'demo_xml_featured_images' => array (
			'label' => __('Replacing Feature images', 'demo_xml_txtd'),
			'default' => array(),
			'type' => 'gallery',
			'display_option' => ''
		),
	)
);