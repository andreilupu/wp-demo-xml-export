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

		// first, ignore some meta_keys which will never hold an attachment id
		$ignore_meta_keys = array(
			'_additional_settings',
			'_edit_last',
			'_edit_lock',
			'_locale',
			'_mail',
			'_mail_2',
			'_menu_item_target',
			'_form',
			'_menu_item_classes',
			'_menu_item_menu_item_parent',
			'_menu_item_object',
			'_menu_item_object_id',
			'_menu_item_object_id',
			'_menu_item_type',
			'_menu_item_url',
			'_menu_item_xfn',
			'_messages',
			'_pixlikes',
			'attribute_size',
			'attribute_type',
			'total_sales',
			'_backorders',
			'_billing_address_1',
			'_billing_address_2',
			'_billing_city',
			'_billing_company',
			'_billing_country',
			'_billing_email',
			'_billing_first_name',
			'_billing_last_name',
			'_billing_phone',
			'_billing_postcode',
			'_billing_state',
			'_cart_discount',
			'_completed_date',
			'_customer_ip_address',
			'_customer_user',
			'_customer_user_agent',
			'default_attributes',
			'_downloadable',
			'_download_limit',
			'_download_expiry',
			'_manage_stock',
			'_wp_attachment_metadata',
			'_wp_page_template',
			'_menu_item_orphaned'
		);

		foreach ( $meta_keys as $meta ) {

			if ( in_array( $meta, $ignore_meta_keys ) ) {
				continue;
			}

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