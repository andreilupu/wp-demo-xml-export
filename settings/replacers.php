<?php

return array(
	'type' => 'postbox',
	'label' => __('Replacers Images with', 'demo_xml_txtd'),
	'class' => 'half-box',
	'options' => array(
		'demo_xml_replacers' => array (
			'label' => __('Replacing images', 'demo_xml_txtd'),
			'default' => array(),
			'type' => 'gallery',
			'display_option' => ''
		),
	)
);