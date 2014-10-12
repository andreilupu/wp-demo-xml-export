<?php
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category YourThemeOrPlugin
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/webdevstudios/Custom-Metaboxes-and-Fields-for-WordPress
 */

add_filter( 'demo_xml_meta_boxes', 'demo_xml_sample_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function demo_xml_sample_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_demo_xml_';

	$meta_boxes['test_metabox'] = array(
		'id'         => 'pixroof_gallery',
		'title'      => __( 'DemoXml Gallery', 'demo_xml_l10n' ),
		'pages'      => array( 'proof_gallery', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'demo_xml_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
				'name' => __( 'Gallery', 'demo_xml_l10n' ),
				'id'   => $prefix . 'main_gallery',
				'type' => 'gallery',
				'show_names' => false,
			),
			array(
				'name' => __( 'Client Name', 'demo_xml_l10n' ),
//				'desc' => __( 'field description (optional)', 'demo_xml_l10n' ),
				'id'   => $prefix . 'client_name',
				'type' => 'text',
			),
			array(
				'name' => __( 'Date', 'demo_xml_l10n' ),
				'id'   => $prefix . 'event_date',
				'type' => 'text_date',
			),
			array(
				'name' => __( 'Client .zip archive', 'demo_xml_l10n' ),
				'desc' => __( 'Upload a .zip archive so the client can download it via the Download link. Leave it empty to hide the link.', 'demo_xml_l10n' ),
				'id'   => $prefix . 'file',
				'type' => 'file',
			),
			array(
				'name' => __('Photos Display Name', 'demo_xml_l10n'),
				'desc' => __('How would you like to identify each photo?', 'demo_xml_l10n'),
				'id'   => $prefix . 'photo_display_name',
				'type' => 'select',
				'options' => array(
					array(
						'name' => __('Unique IDs', 'demo_xml_l10n'),
						'value' => 'unique_ids'
					),
					array(
						'name' => __('Consecutive IDs', 'demo_xml_l10n'),
						'value' => 'consecutive_ids'
					),
					array(
						'name' => __('File Name', 'demo_xml_l10n'),
						'value' => 'file_name'
					),
					array(
						'name' => __('Unique IDs and Photo Title', 'demo_xml_l10n'),
						'value' => 'unique_ids_photo_title'
					),
					array(
						'name' => __('Consecutive IDs and Photo Title', 'demo_xml_l10n'),
						'value' => 'consecutive_ids_photo_title'
					),
				),
				'std' => 'fullwidth',
			),
		),
	);
	// Add other metaboxes as needed
	return $meta_boxes;
}

add_action( 'init', 'demo_xml_initialize_demo_xml_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function demo_xml_initialize_demo_xml_meta_boxes() {

	if ( ! class_exists( 'demo_xml_Meta_Box' ) )
		require_once 'init.php';

}
