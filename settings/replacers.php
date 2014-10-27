<?php

return array(
	'type' => 'postbox',
	'label' => __('Replacers Settings', 'demo_xml_txtd'),
	'options' => array(
		'demo_xml_replacers' => array (
			'label' => __('Replacing images', 'demo_xml_txtd'),
			'default' => array(),
			'type' => 'gallery',
			'display_option' => ''
		),
	)
);