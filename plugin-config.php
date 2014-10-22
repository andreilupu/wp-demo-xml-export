<?php defined('ABSPATH') or die;

$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;

$debug = false;
if ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' ) {
	$debug = true;
}
$debug = true;
$options = get_option('demo_xml_settings');

$display_settings = false;

if ( isset( $options['display_settings'] ) ){
	$display_settings = $options['display_settings'];
}

return array
	(
		'plugin-name' => 'demo_xml',

		'settings-key' => 'demo_xml_settings',

		'textdomain' => 'demo_xml_txtd',

		'template-paths' => array
			(
				$basepath.'core/views/form-partials/',
				$basepath.'views/form-partials/',
			),

		'fields' => array
			(
				'hiddens'
					=> include 'settings/hiddens'.EXT,
//				'general'
//					=> include 'settings/general'.EXT,
				'post_types'
					=> include 'settings/post_types'.EXT,
//				'taxonomies'
//					=> include 'settings/taxonomies'.EXT,
			),

		'processor' => array
			(
				// callback signature: (array $input, PixtypesProcessor $processor)

				'preupdate' => array
				(
					// callbacks to run before update process
					// cleanup and validation has been performed on data
				),
				'postupdate' => array
				(
					'save_settings'
				),
			),

		'cleanup' => array
			(
				'switch' => array('switch_not_available'),
			),

		'checks' => array
			(
				'counter' => array('is_numeric', 'not_empty'),
			),

		'errors' => array
			(
				'not_empty' => __('Invalid Value.', demo_xml::textdomain()),
			),

		'callbacks' => array
			(
				'save_settings' => 'save_proof_settings'
			),

//		'display_settings' => $display_settings,

//		'github_updater' => array(
//			'slug' => basename(dirname(__FILE__)).'/demo_xml.php',
//			'api_url' => 'https://api.github.com/repos/pixelgrade/demo_xml',
//			'raw_url' => 'https://raw.github.com/pixelgrade/demo_xml/update',
//			'github_url' => 'https://github.com/pixelgrade/demo_xml/tree/update',
//			'zip_url' => 'https://github.com/pixelgrade/demo_xml/archive/update.zip',
//			'sslverify' => false,
//			'requires' => '3.0',
//			'tested' => '3.3',
//			'readme' => 'README.md',
//			'textdomain' => 'demo_xml',
//			'debug_mode' => $debug
//			//'access_token' => '',
//		),

		// shows exception traces on error
		'debug' => $debug,

		'replacers' => array(
			'replacers' => array( '278', '279', '280', '281' ),
			'ignored_by_replace' => array( '53' ),
			'featured_image_replacers' => array( '278' ),
			'replace_in_contents' => array('any'), // custom post types in which the content should have replaced urls
			'replace_in_metadata' => array(
				'by_id' => array('_pile_second_image'), // meta keys which should have replaced their values with attachments ids
				'by_url' => '' // meta keys which where urls should be replaced
			)
		)

	); # config
