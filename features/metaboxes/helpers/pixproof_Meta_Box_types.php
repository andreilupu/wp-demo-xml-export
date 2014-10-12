<?php

/**
 * CMB field types
 * @since  1.0.0
 */
class demo_xml_Meta_Box_types {

	/**
	 * @todo test taxonomy methods with non-post objects
	 * @todo test all methods with non-post objects
	 */

	/**
	 * A single instance of this class.
	 * @var   demo_xml_Meta_Box_types object
	 * @since 1.0.0
	 */
	public static $instance = null;

	/**
	 * An iterator value for repeatable fields
	 * @var   integer
	 * @since 1.0.0
	 */
	public static $iterator = 0;

	/**
	 * Holds demo_xml_valid_img_types
	 * @var   array
	 * @since 1.0.0
	 */
	public static $valid = array();

	/**
	 * Current field type
	 * @var   string
	 * @since 1.0.0
	 */
	public static $type = 'text';

	/**
	 * Current field
	 * @var   array
	 * @since 1.0.0
	 */
	public static $field;

	/**
	 * Current field meta value
	 * @var   mixed
	 * @since 1.0.0
	 */
	public static $meta;

	/**
	 * Creates or returns an instance of this class.
	 * @since  1.0.0
	 * @return demo_xml_Meta_Box_types A single instance of this class.
	 */
	public static function get() {
		if ( self::$instance === null )
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Generates a field's description markup
	 * @since  1.0.0
	 * @param  string  $desc      Field's description
	 * @param  boolean $paragraph Paragraph tag or span
	 * @return strgin             Field's description markup
	 */
	private static function desc( $paragraph = false ) {
		$tag = $paragraph ? 'p' : 'span';
		$desc = demo_xml_Meta_Box::$field['desc'];
		return "\n<$tag class=\"demo_xml_metabox_description\">$desc</$tag>\n";
	}

	/**
	 * Generates repeatable text fields
	 * @since  1.0.0
	 * @param  string  $field Metabox field
	 * @param  mixed   $meta  Field's meta value
	 * @param  string  $class Field's class attribute
	 * @param  string  $type  Field Type
	 */
	private static function repeat_text_field( $meta, $class = '', $type = 'text' ) {

		$field = demo_xml_Meta_Box::$field; self::$meta = $meta; self::$type = $type;

		// check for default content
		$default = isset( $field['default'] ) ? array( $field['default'] ) : false;
		// check for saved data
		if ( !empty( $meta ) ) {
			$meta = is_array( $meta ) ? array_filter( $meta ) : $meta;
			$meta = ! empty( $meta ) ? $meta : $default;
		} else {
			$meta = $default;
		}

		self::repeat_table_open( $class );

		$class = $class ? $class .' widefat' : 'widefat';

		if ( !empty( $meta ) ) {
			foreach ( (array) $meta as $val ) {
				self::repeat_row( self::text_input( $class, $val ) );
			}
		} else {
			self::repeat_row( self::text_input( $class ) );
		}

		self::empty_row( self::text_input( $class ) );
		self::repeat_table_close();
		// reset iterator
		self::$iterator = 0;
	}

	/**
	 * Text input field used by repeatable fields
	 * @since  1.0.0
	 * @param  string  $class Field's class attribute
	 * @param  mixed   $val   Field's meta value
	 * @return string         HTML text input
	 */
	private static function text_input( $class = '', $val = '' ) {
		self::$iterator = self::$iterator ? self::$iterator + 1 : 1;
		$before = '';
		if ( demo_xml_Meta_Box::$field['type'] == 'text_money' )
			$before = ! empty( demo_xml_Meta_Box::$field['before'] ) ? ' ' : '$ ';
		return $before . '<input type="'. self::$type .'" class="'. $class .'" name="'. demo_xml_Meta_Box::$field['id'] .'[]" id="'. demo_xml_Meta_Box::$field['id'] .'_'. self::$iterator .'" value="'. self::esc( $val ) .'" data-id="'. demo_xml_Meta_Box::$field['id'] .'" data-count="'. self::$iterator .'"/>';
	}

	/**
	 * Generates repeatable field opening table markup for repeatable fields
	 * @since  1.0.0
	 * @param  string $class Field's class attribute
	 */
	private static function repeat_table_open( $class = '' ) {
		echo self::desc(), '<table id="', demo_xml_Meta_Box::$field['id'], '_repeat" class="cmb-repeat-table ', $class ,'"><tbody>';
	}

	/**
	 * Generates repeatable feild closing table markup for repeatable fields
	 * @since 1.0.0
	 */
	private static function repeat_table_close() {
		echo '</tbody></table><p class="add-row"><a data-selector="', demo_xml_Meta_Box::$field['id'] ,'_repeat" class="add-row-button button" href="#">'. __( 'Add Row', 'demo_xml_l10n' ) .'</a></p>';
	}

	/**
	 * Generates table row markup for repeatable fields
	 * @since 1.0.0
	 * @param string $input Table cell markup
	 */
	private static function repeat_row( $input ) {
		echo '<tr class="repeat-row">', self::repeat_cell( $input ) ,'</tr>';
	}

	/**
	 * Generates the empty table row markup (for duplication) for repeatable fields
	 * @since 1.0.0
	 * @param string $input Table cell markup
	 */
	private static function empty_row( $input ) {
		echo '<tr class="empty-row">', self::repeat_cell( $input ) ,'</tr>';
	}

	/**
	 * Generates table cell markup for repeatable fields
	 * @since  1.0.0
	 * @param  string $input Text input field
	 * @return string        HTML table cell markup
	 */
	private static function repeat_cell( $input ) {
		return '<td>'. $input .'</td><td class="remove-row"><a class="button remove-row-button" href="#">'. __( 'Remove', 'demo_xml_l10n' ) .'</a></td>';
	}

	/**
	 * Determine a file's extension
	 * @since  1.0.0
	 * @param  string       $file File url
	 * @return string|false       File extension or false
	 */
	public static function get_file_ext( $file ) {
		$parsed = @parse_url( $file, PHP_URL_PATH );
		return $parsed ? strtolower( pathinfo( $parsed, PATHINFO_EXTENSION ) ) : false;
	}

	/**
	 * Determines if a file has a valid image extension
	 * @since  1.0.0
	 * @param  string $file File url
	 * @return bool         Whether file has a valid image extension
	 */
	public static function is_valid_img_ext( $file ) {
		$file_ext = self::get_file_ext( $file );

		self::$valid = empty( self::$valid ) ? (array) apply_filters( 'demo_xml_valid_img_types', array( 'jpg', 'jpeg', 'png', 'gif', 'ico', 'icon' ) ) : self::$valid;

		return ( $file_ext && in_array( $file_ext, self::$valid ) );
	}

	/**
	 * Checks if we can get a post object, and if so, uses `get_the_terms` which utilizes caching
	 * @since  1.0.2
	 * @param  integer $object_id Object ID
	 * @param  string  $taxonomy  Taxonomy terms to return
	 * @return mixed              Array of terms on success
	 */
	public static function get_object_terms( $object_id, $taxonomy ) {

		if ( ! $post = get_post( $object_id ) ) {

			$cache_key = 'cmb-cache-'. $taxonomy .'-'. $object_id;

			// Check cache
			$cached = $test = get_transient( $cache_key );
			if ( $cached )
				return $cached;

			$cached = wp_get_object_terms( $object_id, $taxonomy );
			// Do our own (minimal) caching. Long enough for a page-load.
			$set = set_transient( $cache_key, $cached, 60 );
			return $cached;
		}

		// WP caches internally so it's better to use
		return get_the_terms( $post, $taxonomy );

	}

	/**
	 * Escape the value before output. Defaults to 'esc_attr()'
	 * @since  1.0.1
	 * @param  mixed  $meta_value Meta value
	 * @param  mixed  $func       Escaping function (if not esc_attr())
	 * @return mixed              Final value
	 */
	public static function esc( $meta_value, $func = '' ) {
		$field = demo_xml_Meta_Box::$field;

		// Check if the field has a registered escaping callback
		$cb = demo_xml_Meta_Box::maybe_callback( $field, 'escape_cb' );
		if ( false === $cb ) {
			// If requesting NO escaping, return meta value
			return $meta_value;
		} elseif ( $cb ) {
			// Ok, callback is good, let's run it.
			return call_user_func( $cb, $meta_value, $field );
		}

		// Or custom escaping filter can be used
		$esc = apply_filters( 'demo_xml_types_esc_'. $field['type'], null, $meta_value, $field );
		if ( null !== $esc ) {
			return $esc;
		}

		// escaping function passed in?
		$func       = is_string( $func ) && ! empty( $func ) ? $func : 'esc_attr';
		$meta_value = ! empty( $meta_value ) ? $meta_value : $field['default'];

		return call_user_func( $func, $meta_value );
	}


	/**
	 * Begin Field Types
	 */

	public static function text( $field, $meta ) {
		if ( $field['repeatable'] ) {
			return self::repeat_text_field( $meta );
		}

		echo '<input type="text" class="regular-text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" />', self::desc( true );
	}

	public static function text_small( $field, $meta ) {
		if ( $field['repeatable'] ) {
			return self::repeat_text_field( $meta, 'demo_xml_text_small' );
		}

		echo '<input class="demo_xml_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" />', self::desc();
	}

	public static function text_medium( $field, $meta ) {
		if ( $field['repeatable'] ) {
			return self::repeat_text_field( $meta, 'demo_xml_text_medium' );
		}
		echo '<input class="demo_xml_text_medium" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" />', self::desc();
	}

	public static function text_email( $field, $meta ) {
		if ( $field['repeatable'] ) {
			return self::repeat_text_field( $meta, 'demo_xml_text_email demo_xml_text_medium', 'email' );
		}

		echo '<input class="demo_xml_text_email demo_xml_text_medium" type="email" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" />', self::desc( true );
	}

	public static function text_url( $field, $meta ) {
		if ( $field['repeatable'] ) {
			return self::repeat_text_field( $meta, 'demo_xml_text_url demo_xml_text_medium' );
		}

		echo '<input class="demo_xml_text_url demo_xml_text_medium regular-text" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta, 'esc_url' ), '" />', self::desc( true );
	}

	public static function text_date( $field, $meta ) {
		echo '<input class="demo_xml_text_small demo_xml_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" />', self::desc();
	}

	public static function text_date_timestamp( $field, $meta ) {
		echo '<input class="demo_xml_text_small demo_xml_datepicker" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', ! empty( $meta ) ? date( 'm\/d\/Y', $meta ) : $field['default'], '" />', self::desc();
	}

	public static function text_datetime_timestamp( $field, $meta, $object_id ) {

		// This will be used if there is a select_timezone set for this field
		$tz_offset = demo_xml_Meta_Box::field_timezone_offset( $object_id );
		if ( ! empty( $tz_offset ) ) {
			$meta -= $tz_offset;
		}

		echo '<input class="demo_xml_text_small demo_xml_datepicker" type="text" name="', $field['id'], '[date]" id="', $field['id'], '_date" value="', ! empty( $meta ) ? date( 'm\/d\/Y', $meta ) : $field['default'], '" />';
		echo '<input class="demo_xml_timepicker text_time" type="text" name="', $field['id'], '[time]" id="', $field['id'], '_time" value="', ! empty( $meta ) ? date( 'h:i A', $meta ) : $field['default'], '" />', self::desc();
	}

	public static function text_datetime_timestamp_timezone( $field, $meta ) {

		$datetime = unserialize( $meta );
		$meta = $tzstring = false;

		if ( $datetime && $datetime instanceof DateTime ) {
			$tz = $datetime->getTimezone();
			$tzstring = $tz->getName();

			$meta = $datetime->getTimestamp() + $tz->getOffset( new DateTime('NOW') );
		}

		echo '<input class="demo_xml_text_small demo_xml_datepicker" type="text" name="', $field['id'], '[date]" id="', $field['id'], '_date" value="', ! empty( $meta ) ? date( 'm\/d\/Y', $meta ) : $field['default'], '" />';
		echo '<input class="demo_xml_timepicker text_time" type="text" name="', $field['id'], '[time]" id="', $field['id'], '_time" value="', ! empty( $meta ) ? date( 'h:i A', $meta ) : $field['default'], '" />';

		echo '<select name="', $field['id'], '[timezone]" id="', $field['id'], '_timezone">';
		echo wp_timezone_choice( $tzstring );
		echo '</select>', self::desc();
	}

	public static function text_time( $field, $meta ) {
		echo '<input class="demo_xml_timepicker text_time" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" />', self::desc();
	}

	public static function select_timezone( $field, $meta ) {
		$meta = self::esc( $meta );
		if ( '' === $meta )
			$meta = demo_xml_Meta_Box::timezone_string();

		echo '<select name="', $field['id'], '" id="', $field['id'], '">';
		echo wp_timezone_choice( $meta );
		echo '</select>';
	}

	public static function text_money( $field, $meta ) {
		if ( $field['repeatable'] ) {
			return self::repeat_text_field( $meta, 'demo_xml_text_money' );
		}

		echo ! empty( $field['before'] ) ? '' : '$', ' <input class="demo_xml_text_money" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" />', self::desc();
	}

	public static function colorpicker( $field, $meta ) {
		$meta = self::esc( $meta );
		$hex_color = '(([a-fA-F0-9]){3}){1,2}$';
		if ( preg_match( '/^' . $hex_color . '/i', $meta ) ) // Value is just 123abc, so prepend #.
			$meta = '#' . $meta;
		elseif ( ! preg_match( '/^#' . $hex_color . '/i', $meta ) ) // Value doesn't match #123abc, so sanitize to just #.
			$meta = "#";
		echo '<input class="demo_xml_colorpicker demo_xml_text_small" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta, '" />', self::desc();
	}

	public static function textarea( $field, $meta ) {
		echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10">', self::esc( $meta, 'esc_textarea' ), '</textarea>', self::desc( true );
	}

	public static function textarea_small( $field, $meta ) {
		echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4">', self::esc( $meta, 'esc_textarea' ), '</textarea>', self::desc( true );
	}

	public static function textarea_code( $field, $meta ) {
		echo '<pre><textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="10" class="demo_xml_textarea_code">', ! empty( $meta ) ? $meta : $field['default'], '</textarea></pre>', self::desc( true );
	}

	public static function select( $field, $meta ) {
		$meta = self::esc( $meta );
		echo '<select name="', $field['id'], '" id="', $field['id'], '">';
		foreach ( $field['options'] as $option_key => $option ) {

			// Check for the "old" way
			$label = isset( $option['name'] ) ? $option['name'] : $option;
			$value = isset( $option['value'] ) ? $option['value'] : $option_key;

			echo '<option value="', $value, '" ', selected( $meta == $value ) ,'>', $label, '</option>';

		}
		echo '</select>', self::desc( true );
	}

	public static function radio( $field, $meta ) {
		$meta = self::esc( $meta );
		echo '<ul>';
		$i = 1;
		foreach ( $field['options'] as $option_key => $option ) {

			// Check for the "old" way
			$label = isset( $option['name'] ) ? $option['name'] : $option;
			$value = isset( $option['value'] ) ? $option['value'] : $option_key;

			echo '<li class="demo_xml_option"><input type="radio" name="', $field['id'], '" id="', $field['id'], $i,'" value="', $value, '" ', checked( $meta == $value ), ' /> <label for="', $field['id'], $i, '">', $label ,'</label></li>';
			$i++;
		}
		echo '</ul>', self::desc( true );
	}

	public static function radio_inline( $field, $meta ) {
		self::radio( $field, $meta );
	}

	public static function checkbox( $field, $meta ) {
		echo '<input class="demo_xml_option" type="checkbox" name="', $field['id'], '" id="', $field['id'], '" ', checked( ! empty( $meta ) ), ' value="on"/> <label for="', $field['id'], '">', self::desc() ,'</label>';
	}

	public static function multicheck( $field, $meta ) {
		echo '<ul>';
		$i = 1;
		foreach ( $field['options'] as $value => $name ) {
			echo '<li><input class="demo_xml_option" type="checkbox" name="', $field['id'], '[]" id="', $field['id'], $i, '" value="', $value, '" ', checked( is_array( $meta ) && in_array( $value, $meta ) ), '  /> <label for="', $field['id'], $i, '">', $name, '</label></li>';
			$i++;
		}
		echo '</ul>', self::desc();
	}

	public static function multicheck_inline( $field, $meta ) {
		self::multicheck( $field, $meta );
	}

	public static function title( $field, $meta, $object_id, $object_type ) {
		$tag = $object_type == 'post' ? 'h5' : 'h3';
		echo '<'. $tag .' class="demo_xml_metabox_title">', $field['name'], '</'. $tag .'>', self::desc( true );
	}

	public static function wysiwyg( $field, $meta ) {
		wp_editor( stripslashes( html_entity_decode( self::esc( $meta, 'esc_html' ) ) ), $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
		echo self::desc( true );
	}

	public static function taxonomy_select( $field, $meta, $object_id ) {

		echo '<select name="', $field['id'], '" id="', $field['id'], '">';
		$names    = self::get_object_terms( $object_id, $field['taxonomy'] );
		$terms    = get_terms( $field['taxonomy'], 'hide_empty=0' );
		$has_term = false;

		foreach ( $terms as $term ) {
			if ( !is_wp_error( $names ) && !empty( $names ) && ! strcmp( $term->slug, $names[0]->slug ) ) {
				echo '<option value="' . $term->slug . '" selected>' . $term->name . '</option>';
				$has_term = true;
			}
			else if ( ! $has_term == false && $term->slug == $field['default'] ) {
				echo '<option value="' . $term->slug . '" selected>' . $term->name . '</option>';
			} else {
				echo '<option value="' . $term->slug . '  ' , $meta == $term->slug ? $meta : ' ' ,'  ">' . $term->name . '</option>';
			}
		}
		echo '</select>', self::desc( true );
	}

	public static function taxonomy_radio( $field, $meta, $object_id ) {

		$names = self::get_object_terms( $object_id, $field['taxonomy'] );
		$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
		echo '<ul>';
		$i = 1;
		foreach ( $terms as $term ) {
			$checked = ( !is_wp_error( $names ) && !empty( $names ) && !strcmp( $term->slug, $names[0]->slug ) );

			echo '<li class="demo_xml_option"><input type="radio" name="', $field['id'], '" id="', $field['id'], $i,'" value="'. $term->slug . '" ', checked( $checked ), ' /> <label for="', $field['id'], $i, '">' . $term->name . '</label></li>';
			$i++;
		}
		echo '</ul>', self::desc( true );
	}

	public static function taxonomy_radio_inline( $field, $meta ) {
		self::taxonomy_radio( $field, $meta );
	}

	public static function taxonomy_multicheck( $field, $meta, $object_id ) {

		$names = self::get_object_terms( $object_id, $field['taxonomy'] );

		$terms = get_terms( $field['taxonomy'], 'hide_empty=0' );
		if(empty($terms) || empty($names)) {
			return;
		}

		echo '<ul>';
		$i = 1;
		foreach ( $terms as $term ) {
			echo '<li><input class="demo_xml_option" type="checkbox" name="', $field['id'], '[]" id="', $field['id'], $i,'" value="'. $term->slug . '" ';
			foreach ($names as $name) {
				checked( $term->slug == $name->slug );
			}

			echo ' /> <label for="', $field['id'], $i, '">' . $term->name . '</label></li>';
			$i++;
		}
		echo '</ul>', self::desc();
	}

	public static function taxonomy_multicheck_inline( $field, $meta ) {
		self::taxonomy_multicheck( $field, $meta );
	}

	public static function file_list( $field, $meta, $object_id ) {

		echo '<input class="demo_xml_upload_file demo_xml_upload_list" type="hidden" size="45" id="', $field['id'], '" name="', $field['id'], '" value="" />';
		echo '<input class="demo_xml_upload_button button demo_xml_upload_list" type="button" value="'. __( 'Add or Upload File', 'demo_xml_l10n' ) .'" />', self::desc( true );

		echo '<ul id="', $field['id'], '_status" class="demo_xml_media_status attach_list">';

		if ( $meta && is_array( $meta ) ) {

			$preview_size = empty( $field['preview_size'] ) ? array( 50, 50 ) : $field['preview_size'];

			foreach ( $meta as $id => $fullurl ) {
				if ( self::is_valid_img_ext( $fullurl ) ) {
					echo
					'<li class="img_status">',
						wp_get_attachment_image( $id, $preview_size ),
						'<p><a href="#" class="demo_xml_remove_file_button">'. __( 'Remove Image', 'demo_xml_l10n' ) .'</a></p>
						<input type="hidden" id="filelist-', $id ,'" name="', $field['id'] ,'[', $id ,']" value="', $fullurl ,'" />
					</li>';

				} else {
					$parts = explode( '/', $fullurl );
					for ( $i = 0; $i < count( $parts ); ++$i ) {
						$title = $parts[$i];
					}
					echo
					'<li>',
						__( 'File:', 'demo_xml_l10n' ), ' <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $fullurl, '" target="_blank" rel="external">'. __( 'Download', 'demo_xml_l10n' ) .'</a> / <a href="#" class="demo_xml_remove_file_button">'. __( 'Remove', 'demo_xml_l10n' ) .'</a>)
						<input type="hidden" id="filelist-', $id ,'" name="', $field['id'] ,'[', $id ,']" value="', $fullurl ,'" />
					</li>';
				}
			}
		}

		echo '</ul>';
	}

	public static function file( $field, $meta, $object_id, $object_type ) {

		$input_type_url = 'hidden';
		if ( 'url' == $field['allow'] || ( is_array( $field['allow'] ) && in_array( 'url', $field['allow'] ) ) )
			$input_type_url = 'text';
		echo '<input class="demo_xml_upload_file" type="' . $input_type_url . '" size="45" id="', $field['id'], '" name="', $field['id'], '" value="', $meta, '" />';
		echo '<input class="demo_xml_upload_button button" type="button" value="'. __( 'Add or Upload File', 'demo_xml_l10n' ) .'" />';

		$_id_name = $field['id'] .'_id';

		$_id_meta = demo_xml_Meta_Box::get_data( $_id_name );

		// If there is no ID saved yet, try to get it from the url
		if ( $meta && ! $_id_meta ) {
			$_id_meta = demo_xml_Meta_Box::image_id_from_url( esc_url_raw( $meta ) );
		}

		echo '<input class="demo_xml_upload_file_id" type="hidden" id="', $_id_name, '" name="', $_id_name, '" value="', $_id_meta, '" />',
		self::desc( true ),
		'<div id="', $field['id'], '_status" class="demo_xml_media_status">';
			if ( ! empty( $meta ) ) {

				if ( self::is_valid_img_ext( $meta ) ) {
					echo '<div class="img_status">';
					echo '<img style="max-width: 350px; width: 100%; height: auto;" src="', $meta, '" alt="" />';
					echo '<p><a href="#" class="demo_xml_remove_file_button" rel="', $field['id'], '">'. __( 'Remove Image', 'demo_xml_l10n' ) .'</a></p>';
					echo '</div>';
				} else {
					// $file_ext = self::get_file_ext( $meta );
					$parts = explode( '/', $meta );
					for ( $i = 0; $i < count( $parts ); ++$i ) {
						$title = $parts[$i];
					}
					echo __( 'File:', 'demo_xml_l10n' ), ' <strong>', $title, '</strong>&nbsp;&nbsp;&nbsp; (<a href="', $meta, '" target="_blank" rel="external">'. __( 'Download', 'demo_xml_l10n' ) .'</a> / <a href="#" class="demo_xml_remove_file_button" rel="', $field['id'], '">'. __( 'Remove', 'demo_xml_l10n' ) .'</a>)';
				}
			}
		echo '</div>';
	}

	public static function oembed( $field, $meta, $object_id, $object_type ) {
		echo '<input class="demo_xml_oembed regular-text" type="text" name="', $field['id'], '" id="', $field['id'], '" value="', self::esc( $meta ), '" data-objectid="', $object_id ,'" data-objecttype="', $object_type ,'" />', self::desc( true );
		echo '<p class="cmb-spinner spinner" style="display:none;"><img src="'. admin_url( '/images/wpspin_light.gif' ) .'" alt="spinner"/></p>';
		echo '<div id="', $field['id'], '_status" class="demo_xml_media_status ui-helper-clearfix embed_wrap">';

			if ( $meta != '' )
				echo demo_xml_Meta_Box_ajax::get_oembed( $meta, $object_id, array(
					'object_type' => $object_type,
					'oembed_args' => array( 'width' => '640' ),
					'field_id'    => $field['id'],
				) );

		echo '</div>';
	}

	public static function gallery($field, $meta, $object_id, $object_type) {

		// include our gallery scripts only when we need them
		wp_enqueue_media();
		wp_enqueue_script( 'proof_pixgallery' );

		// ensure the wordpress modal scripts even if an editor is not present
		wp_enqueue_script( 'jquery-ui-dialog', false, array('jquery'), false, true );
		wp_localize_script( 'proof_pixgallery', 'locals', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		// output html
		echo '<div id="proof_pixgallery">'.
			'<ul></ul>'.
			'<a class="open_proof_pixgallery" href="#" class="wp-gallery" >'.
			'<input type="hidden" name="', $field['id'], '[gallery]" id="pixgalleries" value="', isset($meta['gallery']) ? $meta['gallery'] : '', '" />'.
			'<input type="hidden" name="', $field['id'], '[random]" id="pixgalleries_random" value="', isset($meta['random']) ? $meta['random'] : '', '" />'.
			'<input type="hidden" name="', $field['id'], '[columns]" id="pixgalleries_columns" value="', isset($meta['columns']) ? $meta['columns'] : '', '" />'.
			'<i class="icon"></i>'.
			'</a>'.
			'</div>';
	}

	/**
	 * Deprecated methods. use demo_xml_Meta_Box_types::repeat( true/false ) to toggle repeatable
	 */

	public static function text_repeat( $field, $meta ) {
		self::repeat_text_field( $meta );
	}
	public static function text_small_repeat( $field, $meta ) {
		self::repeat_text_field( $meta, 'demo_xml_text_small' );
	}
	public static function text_medium_repeat( $field, $meta ) {
		self::repeat_text_field( $meta, 'demo_xml_text_medium' );
	}
	public static function text_email_repeat( $field, $meta ) {
		self::repeat_text_field( $meta, 'demo_xml_text_email demo_xml_text_medium', 'email' );
	}
	public static function text_url_repeat( $field, $meta ) {
		$val = self::esc( $meta );
		$protocols = isset( $field['protocols'] ) ? (array) $field['protocols'] : null;
		$val = $val ? esc_url( $val, $protocols ) : '';
		self::repeat_text_field( $val, 'demo_xml_text_url demo_xml_text_medium' );
	}
	public static function text_money_repeat( $field, $meta ) {
		self::repeat_text_field( $meta, 'demo_xml_text_money' );
	}


	/**
	 * Default fallback. Allows rendering fields via "demo_xml_render_$name" hook
	 * @since  1.0.0
	 * @param  string $name      Non-existent method name
	 * @param  array  $arguments All arguments passed to the method
	 */
	public function __call( $name, $arguments ) {
		list( $field, $meta, $object_id, $object_type ) = $arguments;
		// When a non-registered field is called, send it through an action.
		do_action( "demo_xml_render_$name", $field, $meta, $object_id, $object_type );
	}

}
