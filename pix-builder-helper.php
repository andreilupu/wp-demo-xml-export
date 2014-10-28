<?php
// This file needs to be used outside of plugin


/*
Plugin Name: Pix Builder Helper
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: This is it.
Author: Me
Version: 2.6
Author URI: http://ma.tt/
*/


add_filter('wxr_export_post_meta_value', 'pix_builder_filter_try', 10, 2);

function pix_builder_filter_try( $meta_key, $meta_value ) {

	if ( !empty($meta_value) && ( $meta_key === '_pile_page_builder' || $meta_key === '_pile_project_builder' ) ) {

		if ( class_exists('DemoXmlPlugin') ) {

			$blocks = json_decode( $meta_value );

			if ( !empty($blocks) ) {
				foreach ($blocks as $key => $block) {

					if ( $block->type === 'editor' ) {
						$block->content = wp_slash($block->content);
						var_dump($block->content);
					}
				}

				return json_encode($blocks);
			}

			// // I know for sure this meta_value has an id or ids separated with commas
			// $ids = explode(',', $meta_value);

			// // cache replacers
			// $replacers = DemoXmlPlugin::$attachment_replacers;
			// $new_meta = array();
			// foreach ($ids as $key => $id ) {
			// 	// always get the first id, and after that shift the id
			// 	$new_meta[$key] = $replacers[0];
			// 	$replacers = DemoXmlPlugin::rotate_array( $replacers );
			// }

			// $return_string = implode(',', $new_meta);

			// return $return_string;
		}
	}
	return $meta_value;
}
