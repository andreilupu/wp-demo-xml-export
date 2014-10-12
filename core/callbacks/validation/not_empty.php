<?php defined('ABSPATH') or die;

	function demo_xml_validate_not_empty($fieldvalue, $processor) {
		return ! empty($fieldvalue);
	}
