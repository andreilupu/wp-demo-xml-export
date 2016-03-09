<?php defined('ABSPATH') or die;
/* @var PixFieldsFormField $field */
/* @var PixFieldsForm $form */
/* @var mixed $default */
/* @var string $name */
/* @var string $idname */
/* @var string $label */
/* @var string $desc */
/* @var string $rendering */

// [!!] the counter field needs to be able to work inside other fields; if
// the field is in another field it will have a null label

$checked = $form->autovalue($name, $default);

$all_options = wp_load_alloptions();

if ( ! empty( $all_options ) ) { ?>
	<div class="post_types_checkbox">
		<?php if ( isset( $label ) && !empty( $label ) ) { ?>
			<h3 class="field_title"><?php echo $label; ?></h3>
		<?php } ?>
		<?php if ( isset( $description ) && !empty( $description ) ) { ?>
			<span class="field_description"><?php echo $description; ?></span>
		<?php } ?>
		<ul>
			<?php
			foreach ( $all_options as $option =>  $value ) {
				$attrs = array(
					'name' => $name . '[' . $option . ']',
					'id'   => $idname . '[' . $option . ']',
				); ?>
				<li>
					<input type="checkbox" <?php echo $field->htmlattributes( $attrs ); ?> <?php if ( isset( $checked[ $option ] ) && $checked[ $option ] == 'on' ) {
						echo 'checked="checked"';
					} ?>/>
					<?php echo $option; ?>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php
}