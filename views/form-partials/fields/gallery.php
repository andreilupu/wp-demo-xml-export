<?php defined('ABSPATH') or die;
/* @var $field     PixlikesFormField */
/* @var $form      PixlikesForm  */
/* @var $default   mixed */
/* @var $name      string */
/* @var $idname    string */
/* @var $label     string */
/* @var $desc      string */
/* @var $rendering string  */

// [!!] the counter field needs to be able to work inside other fields; if
// the field is in another field it will have a null label

/**
 ?>

<select <?php echo $field->htmlattributes($attrs) ?>>
	<?php foreach ($this->getmeta('options', array()) as $key => $label): ?>
		<option <?php if ($key == $selected): ?>selected<?php endif; ?>
		        value="<?php echo $key ?>">
			<?php echo $label ?>
		</option>
	<?php endforeach; ?>
</select>

 */ ?>

<div class="pix_gallery">
	<?php $attrs = array (
		'name' => $name,
		'id' => $idname,
		'type' => 'hidden',
		'value' => $form->autovalue($name)
	); ?>

	<input class="pix_core_gallery" <?php echo $field->htmlattributes($attrs); ?>/>

	<a class="open_gallery" href="#">Open Gallery</a>
	<a class="clear_gallery" href="#">Clear Gallery</a>
	<ul class="preview_list"></ul>
</div>