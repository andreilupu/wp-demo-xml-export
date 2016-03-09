<?php defined('ABSPATH') or die;

$basepath = dirname(__FILE__).DIRECTORY_SEPARATOR;

$debug = false;
if ( isset( $_GET['debug'] ) && $_GET['debug'] == 'true' ) {
	$debug = true;
}
$debug = true;
$options = get_option('demo_xml_settings');

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
				'hiddens'  => include 'settings/hiddens'.EXT,

				'rest_export' => include 'settings/rest_export' . EXT,
				'post_metadata' => include 'settings/post_metadata' . EXT,
				'wp_options' => include 'settings/wp_options' . EXT,
				'general' => include 'settings/general' . EXT,

				'replacers'  => include 'settings/replacers'.EXT,
				'ignores' => include 'settings/ignores'.EXT,
				'featured_images'  => include 'settings/featured_images'.EXT,
				'metakeys_ids' => include 'settings/metakeys_ids'.EXT,
				'metakeys_urls' => include 'settings/metakeys_urls'.EXT,
				'customify_options' => include 'settings/customify_options'.EXT,
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
				'save_settings' => 'save_demo_xml_settings'
			),

		// shows exception traces on error
		'debug' => $debug,

		'replace_args' => array(
			'replacers' => array(),
			'ignored_by_replace' => array(),
			'featured_image_replacers' => array(),
			'replace_in_contents' => array('any'), // custom post types in which the content should have replaced urls
			'replace_in_metadata' => array(
				'by_id' => array('_pile_second_image'), // meta keys which should have replaced their values with attachments ids
				'by_url' => '' // meta keys which where urls should be replaced
			)
		)

	); # config
