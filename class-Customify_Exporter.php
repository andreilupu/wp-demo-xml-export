<?php

final class Customify_Exporter_Controller {

	protected $args;

	function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the oEmbed REST API route.
	 *
	 * @since 4.4.0
	 */
	public function register_routes() {

		register_rest_route( 'customify/1.0', 'discover', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'discover' ),
			),
		) );


		register_rest_route( 'customify/1.0', 'export', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'export' ),
				'args'     => array(
//					'action'      => array(
//						'required'          => true,
//						'sanitize_callback' => 'esc_url_raw',
//					),
//					'option_key'   => array(
//						'required'          => true,
//						'sanitize_callback' => 'esc_url_raw',
//					),
//					'step_id' => array(
//						'required'          => true,
//					),
				),
			),
		) );

	}

	/**
	 * Callback for the API endpoint.
	 *
	 * Returns the JSON object for the post.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|array oEmbed response data or WP_Error on failure.
	 */
	public function export( $request ) {

		return 'depricated';
		$result   = array();
		$settings = get_option( 'demo_xml_settings' );

		if ( ! isset( $settings['enable_rest_export'] ) || empty( $settings['enable_rest_export'] ) || ! $settings['enable_rest_export'] ) {
			wp_send_json_error( 'no api here' );
		}

		if ( isset( $settings['enable_rest_export_post_types'] ) && ! empty( $settings['enable_rest_export_post_types'] ) ) {
			$post_types = $settings['rest_types_export'];

			if ( ! empty( $post_types ) ) {

				foreach ( $post_types as $post_type => $val ) {

					if ( $val === 'on' ) {
						$args  = array(
							'post_type' => $post_type
						);
						$posts = $the_query = new WP_Query( $args );

						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							global $post;
							$result['post_types'][ $post_type ][ $post->ID ] = $post;

							/**
							 * @TODO export
							 * 'comment_status'
							 * (string) Whether the post can accept comments. Accepts 'open' or 'closed'. Default is the value of 'default_comment_status' option.
							 *
							 * Also export metadata
							 *
							 */
						}
					}
				}
			}
		}


		if ( isset( $settings['enable_rest_export_taxonomies'] ) && ! empty( $settings['enable_rest_export_taxonomies'] ) ) {
			$taxonomies = $settings['rest_taxes_export'];

			if ( ! empty( $taxonomies ) ) {

				foreach ( $taxonomies as $tax => $val ) {

					if ( $val === 'on' ) {
						$terms = get_terms( $tax );

						foreach ( $terms as $term ) {
							$result['taxonomies'][ $tax ][ $term->term_id ] = $term;

							/**
							 * @TODO export
							 * Also export metadata
							 *
							 */
						}
					}
				}
			}
		}

		/**
		 * @TODO Export wp_options
		 */

		wp_send_json_success( $result );

		// look for this step
		return $_POST;
	}


	/**
	 * Callback for the API endpoint.
	 *
	 * Returns the JSON object for the post.
	 *
	 * @since 4.4.0
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|array oEmbed response data or WP_Error on failure.
	 */
	public function discover( $request ) {

		$result   = array();
		$settings = get_option( 'demo_xml_settings' );

		if ( ! isset( $settings['enable_rest_export'] ) || empty( $settings['enable_rest_export'] ) || ! $settings['enable_rest_export'] ) {
			wp_send_json_error( 'no api here' );
		}

		if ( isset( $settings['rest_taxes_export'] ) && ! empty( $settings['rest_taxes_export'] ) ) {
			$taxonomies = $settings['rest_taxes_export'];

			if ( ! empty( $taxonomies ) ) {

				foreach ( $taxonomies as $tax => $val ) {

					if ( $val === 'on' ) {
						$terms = get_terms( $tax );

						$result['taxonomies'][ $tax ]['count'] = count( $terms );

						$result['taxonomies'][ $tax ]['labels'] = get_taxonomy_labels( get_taxonomy( $tax ) );
						foreach ( $terms as $term ) {
							$result['taxonomies'][ $tax ]['results'][ $term->term_id ] = $term;
						}
					}
				}
			}
		}

		if ( isset( $settings['rest_types_export'] ) && ! empty( $settings['rest_types_export'] ) ) {
			$post_types = $settings['rest_types_export'];

			if ( ! empty( $post_types ) ) {

				foreach ( $post_types as $post_type => $val ) {

					if ( $val === 'on' ) {
						$args      = array(
							'posts_per_page' => '-1',
							'post_type'      => $post_type
						);
						$the_query = new WP_Query( $args );

						$result['post_types'][ $post_type ]['count'] = $the_query->post_count;

						$labels = get_post_type_object( $post_type );

						$result['post_types'][ $post_type ]['labels'] = $labels->labels;

						while ( $the_query->have_posts() ) {
							$the_query->the_post();
							global $post;
							$result['post_types'][ $post_type ]['results'][ $post->ID ] = $post;
						}
					}
				}
			}
		}

		if ( isset( $settings['enable_rest_wp_options_export'] ) && ! empty( $settings['enable_rest_wp_options_export'] ) ) {
			$wp_options = $settings['select_wp_options_to_export'];

			$result['wp_options'] = array();
			foreach ( $wp_options as $option => $on ) {
				if ( 'on' === $on ) {
					$result['wp_options'][$option] = get_option( $option );
				}
			}

			$result['wp_options'];
		}

		wp_send_json_success( $result );

		// look for this step
		return $_POST;
	}

	protected function get_customify_field_data( $option_key, $step_id ) {

		global $pixcustomify_plugin;

		$options = $pixcustomify_plugin->get_options_configs();

		if ( ! isset( $options[ $option_key ] ) ) {
			wp_send_json_error( 'inexistent key' );
		}

		$option_config = $options[ $option_key ];

		if ( ! isset( $options[ $option_key ]['imports'] ) ) {
			wp_send_json_error( 'where is imports????' );
		}

		$imports = $options[ $option_key ]['imports'];

		if ( isset( $imports[ $step_id ] ) ) {
			return $imports[ $step_id ];
		}

		return false;
	}
}