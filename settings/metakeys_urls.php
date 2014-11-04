<?php

return array(
	'type' => 'postbox',
	'label' => __('Metakeys replaced by url Settings', 'demo_xml_txtd'),
	'options' => array(
		'demo_xml_meta_keys_replaced_by_url' => array (
			'label' => __('Metakeys to replace', 'demo_xml_txtd'),
			'default' => array(),
			'type' => 'meta_keys_select',
			'display_option' => ''
		),
	)
);