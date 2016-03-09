<?php
	/**
	 * Represents the view for the administration dashboard.
	 *
	 * This includes the header, options, and other information that should
	 * provide the user interface to the end user.
	 *
	 * @package   PixTypes
	 * @author    Pixelgrade <contact@pixelgrade.com>
	 * @license   GPL-2.0+
	 * @link      http://pixelgrade.com
	 * @copyright 2013 Pixel Grade Media
	 */

	$config = include demo_xml::pluginpath().'plugin-config'.EXT;

	// invoke processor
	$processor = demo_xml::processor($config);
	$status = $processor->status();
	$errors = $processor->errors(); ?>

<div class="wrap" id="demo_xml_form">

	<div id="icon-options-general" class="icon32"><br></div>

	<h2><?php _e('DemoXml', 'demo_xml_txtd'); ?></h2>

	<?php if ($processor->ok()): ?>

		<?php if ( ! empty($errors)): ?>
			<br/>
			<p class="update-nag">
				<strong><?php _e('Unable to save settings.', 'demo_xml_txtd'); ?></strong>
				<?php _e('Please check the fields for errors and typos.', 'demo_xml_txtd'); ?>
			</p>
		<?php endif; ?>

		<?php if ($processor->performed_update()): ?>
			<br/>
			<p class="update-nag">
				<?php _e('Settings have been updated.', 'demo_xml_txtd');?>
			</p>
		<?php endif; ?>

		<?php echo $f = demo_xml::form($config, $processor);

		wp_enqueue_media(); ?>

		<button type="submit" name="export_xml_submit" class="button button-primary">
			<?php _e('Export', 'demo_xml_txtd'); ?>
		</button>

		<button type="submit" class="button button-primary">
			<?php _e('Save Changes', 'demo_xml_txtd'); ?>
		</button>

		<?php

		echo $f->field('hiddens')->render();
		echo $f->field('rest_export')->render();

		echo $f->field('post_metadata')->render();

//		echo $f->field('general')->render();

		echo '<div class="clear"></div>';
		echo $f->field('wp_options')->render();

		echo '<div class="clear"></div>';

		echo $f->field('replacers')->render();
		echo $f->field('ignores')->render();

		echo '<div class="clear"></div>';

		echo $f->field('featured_images')->render();
		echo $f->field('customify_options')->render();


		echo '<div class="clear"></div>';

		echo $f->field('metakeys_ids')->render();

		echo '<div class="clear"></div>';

//		echo $f->field('metakeys_urls')->render();

//		DemoXmlPlugin::demo_export(
//			array(
//				'replacers' => array( '278', '279', '280', '281' ),
//				'ignored_by_replace' => array( '53' ),
//				'featured_image_replacers' => array( '278' ),
//				'replace_in_contents' => array('any'), // custom post types in which the content should have replaced urls
//				'replace_in_metadata' => array(
//					'by_id' => array(''), // meta keys which should have replaced their values with attachments ids
//					'by_url' => '' // meta keys which where urls should be replaced
//				)
//			)
//		); ?>

		<button type="submit" name="export_xml_submit" class="button button-primary">
			<?php _e('Export', 'demo_xml_txtd'); ?>
		</button>

		<button type="submit" class="button button-primary">
			<?php _e('Save Changes', 'demo_xml_txtd'); ?>
		</button>


		<?php echo $f->endform() ?>

	<?php elseif ($status['state'] == 'error'): ?>

		<h3>Critical Error</h3>

		<p><?php echo $status['message'] ?></p>

	<?php endif; ?>
</div>