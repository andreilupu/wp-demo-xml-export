<?php defined('ABSPATH') or die;
/* @var PixFieldsFormField $field */
/* @var PixFieldsForm $form */
/* @var mixed $default */
/* @var string $name */
/* @var string $idname */
/* @var string $label */
/* @var string $desc */
/* @var string $rendering */


if ( ! function_exists( 'get_all_meta_keyss') ) {

	function get_all_meta_keyss( $post_type ){
//		global $wpdb;
		$posts = get_posts( array( 'post_type' => $post_type, 'numberposts' => -1 ) );
		$keys = array();
		foreach ( $posts as $post ) {
			$metas = get_post_custom_keys( $post->ID );

			if (empty( $metas ) ) {
				continue;
			}
			foreach ( $metas as $meta_key => $meta_value ) {
				$keys[$post->post_type][$meta_value] = 1;
			}
		}

		return $keys;
	}
}

// [!!] the counter field needs to be able to work inside other fields; if
// the field is in another field it will have a null label

$checked = $form->autovalue($name, $default);
$post_types = get_post_types();

if ( ! empty( $post_types ) ) {

	foreach ( $post_types as $post_type ) { ?>
	<div class="post_metas_checkbox" data-type="<?php echo $post_type ?>">
		<h3><?php echo $post_type ?></h3>
		<ul>
			<?php
			$meta_keys = get_all_meta_keyss( $post_type );
			if ( isset( $meta_keys[$post_type] ) && ! empty( $meta_keys[$post_type] ) ) {
				foreach ( $meta_keys[$post_type] as $meta_key => $key ) {
					$attrs = array(
						'name' => $name . '[' . $post_type . ']' . '[' . $meta_key . ']',
						'id'   => $idname . '[' . $post_type . ']' . '[' . $meta_key . ']',
					); ?>
					<li><input type="checkbox" <?php echo $field->htmlattributes( $attrs ); ?> <?php if ( isset( $checked[ $post_type ][ $meta_key ] ) && $checked[ $post_type ][ $meta_key ] == 'on' ) {echo 'checked="checked"'; } ?>/><?php echo $meta_key; ?></li>
					<?php
				}
			} ?>
		</ul>
	</div>
	<?php }
}

