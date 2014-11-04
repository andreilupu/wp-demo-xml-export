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

 */

$current_val = $form->autovalue($name, $default); ?>

<div class="meta_select_field">
	<ul class="post_types_list">

	<?php
	$meta_keys = get_meta_keys();

	if ( ! empty( $meta_keys ) ) {

		foreach ( $meta_keys as $meta ) {

			$attrs = array (
				'name' => $name . '[' . $meta . ']',
				'id' => $idname . '[' . $meta . ']',
			);

			if ( isset( $current_val[$meta] ) && $current_val[$meta] == 'on' ){
				$attrs['checked'] = 'checked';
			}

			echo '<li><input type="checkbox" ' . $field->htmlattributes($attrs) .' />';
			echo '<label for="' . $idname . '[' . $meta . ']">' . $meta . '</label></li>';

		}
	} ?>

	</ul>
</div>