<?php defined('ABSPATH') or die;
	/* @var $form DemoXmlForm */
	/* @var $conf DemoXmlMeta */

	/* @var $f DemoXmlForm */
	$f = &$form;
?>

<?php foreach ($conf->get('fields', array()) as $fieldname): ?>

	<?php echo $f->field($fieldname)
		->addmeta('special_sekrit_property', '!!')
		->render() ?>

<?php endforeach; ?>
