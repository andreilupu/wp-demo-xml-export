<?php
/**
 * DemoXml.
 *
 * @package   DemoXml
 * @author    Pixelgrade <contact@pixelgrade.com>
 * @license   GPL-2.0+
 * @link      http://pixelgrade.com
 * @copyright 2014 Pixelgrade
 */

/**
 * Plugin class.
 *
 * @package DemoXml
 * @author    Pixelgrade <contact@pixelgrade.com>
 */
class DemoXmlPlugin {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @const   string
	 */
	protected $version = '1.0.3';
	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'demo_xml';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Path to the plugin.
	 *
	 * @since    1.0.0
	 * @var      string
	 */
	protected $plugin_basepath = null;

	public $display_admin_menu = false;

	protected $config;

	protected static $number_of_images;

	protected static $wxr_version;

	// list of ids already imported
	protected static $imported_posts = array();

	public static $attachment_replacers = array();

	protected static $ignored_attachments = array();

	protected static $featured_image_replacers = array();

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	protected function __construct() {

		$this->plugin_basepath = plugin_dir_path( __FILE__ );
		$this->config          = self::config();
		self::$wxr_version     = 1.2;
		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'admin_init', array( $this, 'wpgrade_init_plugin' ) );

		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'demo_xml.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );


		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		add_filter( 'the_content_export', array( $this, 'replace_the_content_urls' ), 10, 1 );
		add_filter( 'the_content_export', array( $this, 'replace_gallery_shortcodes_ids' ), 10, 1 );

		add_filter( 'wxr_export_post_meta_value', array( $this, 'replace_metadata_by_id' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'call_demo_export' ) );

		/**
		 * Ajax Callbacks
		 */
		add_action( 'wp_ajax_pix_core_gallery_preview', array( &$this, 'ajax_pix_core_gallery_preview' ) );

		$this->register_export_api();
	}


	function register_export_api(){

		include_once( self::get_base_path() . '/class-Customify_Exporter.php' );

		$controller = new Customify_Exporter_Controller();

		$controller->init();
	}

	function call_demo_export() {
		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== 'demo_xml' || ! isset( $_POST['export_xml_submit'] ) ) {
			return;
		}

		$settings = get_option( 'demo_xml_settings' );

		if ( isset( $settings['enable_selective_export'] ) && ! empty( $settings['enable_selective_export'] ) ) {
			$this->config['enable_selective_export'] = $settings['enable_selective_export'];
		}

		if ( isset( $settings['display_on_post_types'] ) && ! empty( $settings['display_on_post_types'] ) ) {
			$this->config['display_on_post_types'] = $settings['display_on_post_types'];
		}

		if ( isset( $settings['demo_xml_replacers'] ) && ! empty( $settings['demo_xml_replacers'] ) ) {
			$this->config['replace_args']['replacers'] = explode( ',', $settings['demo_xml_replacers'] );
		}

		if ( isset( $settings['demo_xml_ignores'] ) && ! empty( $settings['demo_xml_ignores'] ) ) {
			$this->config['replace_args']['ignored_by_replace'] = explode( ',', $settings['demo_xml_ignores'] );
		}

		if ( isset( $settings['demo_xml_featured_images'] ) && ! empty( $settings['demo_xml_featured_images'] ) ) {
			$this->config['replace_args']['featured_image_replacers'] = explode( ',', $settings['demo_xml_featured_images'] );
		}

		if ( isset( $settings['demo_xml_meta_keys_replaced_by_id'] ) && ! empty( $settings['demo_xml_meta_keys_replaced_by_id'] ) ) {
			$this->config['replace_args']['replace_in_metadata']['by_id'] = array_keys( $settings['demo_xml_meta_keys_replaced_by_id'] );
		}

		if ( isset( $settings['demo_xml_meta_keys_replaced_by_url'] ) && ! empty( $settings['demo_xml_meta_keys_replaced_by_url'] ) ) {
			$this->config['replace_args']['replace_in_metadata']['by_url'] = explode( ',', $settings['demo_xml_meta_keys_replaced_by_url'] );
		}

		if ( isset( $this->config['replace_args'] ) ) {
			DemoXmlPlugin::demo_export( $this->config['replace_args'] );
			die();
		}

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function config() {
		// @TODO maybe check this
		return include 'plugin-config.php';
	}

	public function wpgrade_init_plugin() {
//		$this->plugin_textdomain();
//		$this->add_wpgrade_shortcodes_button();
//		$this->github_plugin_updater_init();
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

	}

	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0.0
	 *
	 * @param    boolean $network_wide True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, basename( dirname( __FILE__ ) ) . '/lang/' );
	}

	// create an ajax call which will return a preview to the current gallery
	function ajax_pix_core_gallery_preview() {
		$result = array( 'success' => false, 'output' => '' );

		if ( isset( $_REQUEST['attachments_ids'] ) ) {
			$ids = $_REQUEST['attachments_ids'];
		}
		if ( empty( $ids ) ) {
			echo json_encode( $result );
			exit;
		}

		$ids = explode( ',', $ids );

		foreach ( $ids as $id ) {
			$attach = wp_get_attachment_image_src( $id, 'thumbnail', false );

			$result["output"] .= '<li><img src="' . $attach[0] . '" /></li>';
		}
		$result["success"] = true;
		echo json_encode( $result );
		exit;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
			wp_localize_script( $this->plugin_slug . '-admin-script', 'locals',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' )
				)
			);
		}
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	function enqueue_styles() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			if ( ! wp_style_is( 'wpgrade-main-style' ) ) {
				wp_enqueue_style( 'demo_xml_inuit', plugins_url( 'css/inuit.css', __FILE__ ), array(), $this->version );
				wp_enqueue_style( 'demo_xml_magnific-popup', plugins_url( 'css/mangnific-popup.css', __FILE__ ), array(), $this->version );
			}
		}
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version, true );
			wp_localize_script( $this->plugin_slug . '-plugin-script', 'demo_xml', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_submenu_page(
			'tools.php',
			__( 'DemoXml', $this->plugin_slug ),
			__( 'DemoXml', $this->plugin_slug ),
			'edit_plugins',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 */
	function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 */
	function add_action_links( $links ) {
		return array_merge( array( 'settings' => '<a href="' . admin_url( 'tools.php?page=demo_xml' ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>' ), $links );
	}

	static function get_base_path() {
		return plugin_dir_path( __FILE__ );
	}

	/**===== Exporter methods ======= */

	/**
	 * Generates the WXR export file for download
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Filters defining what should be included in the export
	 */
	static function demo_export( $args = array() ) {
		global $wpdb, $post;

		$defaults = array(
			'content'    => 'all',
			'author'     => false,
			'category'   => false,
			'start_date' => false,
			'end_date'   => false,
			'status'     => false,
		);
		$args     = wp_parse_args( $args, $defaults );

		$replacers                = $args['replacers'];
		$featured_image_replacers = $args['featured_image_replacers'];
		$ignore                   = $args['ignored_by_replace'];

		$sitename = sanitize_key( get_bloginfo( 'name' ) );
		if ( ! empty( $sitename ) ) {
			$sitename .= '.';
		}
		$filename = $sitename . 'wordpress.' . date( 'Y-m-d' ) . '.xml';

		if ( 'all' != $args['content'] && post_type_exists( $args['content'] ) ) {
			$ptype = get_post_type_object( $args['content'] );
			if ( ! $ptype->can_export ) {
				$args['content'] = 'post';
			}

			$where = $wpdb->prepare( "{$wpdb->posts}.post_type = %s", $args['content'] );
		} else {
			$post_types = get_post_types( array( 'can_export' => true ) );
			$esses      = array_fill( 0, count( $post_types ), '%s' );
			$where      = $wpdb->prepare( "{$wpdb->posts}.post_type IN (" . implode( ',', $esses ) . ')', $post_types );
		}

		if ( $args['status'] && ( 'post' == $args['content'] || 'page' == $args['content'] ) ) {
			$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status = %s", $args['status'] );
		} else {
			$where .= " AND {$wpdb->posts}.post_status != 'auto-draft'";
		}

		$join = '';
		if ( $args['category'] && 'post' == $args['content'] ) {
			if ( $term = term_exists( $args['category'], 'category' ) ) {
				$join = "INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
				$where .= $wpdb->prepare( " AND {$wpdb->term_relationships}.term_taxonomy_id = %d", $term['term_taxonomy_id'] );
			}
		}

		if ( 'post' == $args['content'] || 'page' == $args['content'] ) {
			if ( $args['author'] ) {
				$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_author = %d", $args['author'] );
			}

			if ( $args['start_date'] ) {
				$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_date >= %s", date( 'Y-m-d', strtotime( $args['start_date'] ) ) );
			}

			if ( $args['end_date'] ) {
				$where .= $wpdb->prepare( " AND {$wpdb->posts}.post_date < %s", date( 'Y-m-d', strtotime( '+1 month', strtotime( $args['end_date'] ) ) ) );
			}
		}

		// Grab a snapshot of post IDs, just in case it changes during the export.
		$post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} $join WHERE $where" );

		self::display_header( $post_ids, $filename );

		self::display_terms( $args );
		// first lets import replacers
		self::display_replacers( $replacers );
		// first lets import ignored attachments by replace
		self::display_ignored( $ignore );
		self::display_featured_images( $featured_image_replacers );
		self::display_posts( $post_ids );
		self::display_footer();
	}

static function display_header( $post_ids, $filename ) {
	global $post;

	header( 'Content-Description: File Transfer' );
	header( 'Content-Disposition: attachment; filename=' . $filename );
	header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

	add_filter( 'wxr_export_skip_postmeta', array( 'DemoXmlPlugin', 'wxr_filter_postmeta' ), 10, 2 );

	echo '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . "\" ?>\n"; ?>
	<!-- This is a WordPress eXtended RSS file generated by WordPress as an export of your site. -->
	<!-- It contains information about your site's posts, pages, comments, categories, and other content. -->
	<!-- You may use this file to transfer that content from one site to another. -->
	<!-- This file is not intended to serve as a complete backup of your site. -->

	<!-- To import this information into a WordPress site follow these steps: -->
	<!-- 1. Log in to that site as an administrator. -->
	<!-- 2. Go to Tools: Import in the WordPress admin panel. -->
	<!-- 3. Install the "WordPress" importer from the list. -->
	<!-- 4. Activate & Run Importer. -->
	<!-- 5. Upload this file using the form provided on that page. -->
	<!-- 6. You will first be asked to map the authors in this export file to users -->
	<!--    on the site. For each author, you may choose to map to an -->
	<!--    existing user on the site or to create a new user. -->
	<!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->
	<!--    contained in this file into your site. -->
	<?php the_generator( 'export' ); ?>
	<rss version="2.0"
	xmlns:excerpt="http://wordpress.org/export/<?php echo self::$wxr_version; ?>/excerpt/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/<?php echo self::$wxr_version; ?>/"
	>
	<channel>
		<title><?php bloginfo_rss( 'name' ); ?></title>
		<link><?php bloginfo_rss( 'url' ); ?></link>
		<description><?php bloginfo_rss( 'description' ); ?></description>
		<pubDate><?php echo date( 'D, d M Y H:i:s +0000' ); ?></pubDate>
		<language><?php bloginfo_rss( 'language' ); ?></language>
		<wp:wxr_version><?php echo self::$wxr_version; ?></wp:wxr_version>
		<wp:base_site_url><?php echo self::wxr_site_url(); ?></wp:base_site_url>
		<wp:base_blog_url><?php bloginfo_rss( 'url' ); ?></wp:base_blog_url>
<?php
// @TODO make a checkbox which will tell us to import authors
//self::wxr_authors_list( $post_ids );
}

static function display_terms( $args ) {

	/*
	 * Get the requested terms ready, empty unless posts filtered by category
	 * or all content.
	 */
	$cats = $tags = $terms = array();
	if ( isset( $term ) && $term ) {
		$cat  = get_term( $term['term_id'], 'category' );
		$cats = array( $cat->term_id => $cat );
		unset( $term, $cat );
	} else if ( 'all' == $args['content'] ) {
		$categories = (array) get_categories( array( 'get' => 'all' ) );
		$tags       = (array) get_tags( array( 'get' => 'all' ) );

		$custom_taxonomies = get_taxonomies( array( '_builtin' => false ) );
		$custom_terms = array();
		foreach ( $custom_taxonomies as $key => $custom_tax ) {
			$custom_terms      = array_merge( $custom_terms, (array) get_terms( $custom_tax, array( 'get' => 'all' ) ) );
		}

		// Put categories in order with no child going before its parent.
		while ( $cat = array_shift( $categories ) ) {
			if ( $cat->parent == 0 || isset( $cats[ $cat->parent ] ) ) {
				$cats[ $cat->term_id ] = $cat;
			} else {
				$categories[] = $cat;
			}
		}

		// Put terms in order with no child going before its parent.
		while ( $t = array_shift( $custom_terms ) ) {
			if ( $t->parent == 0 || isset( $terms[ $t->parent ] ) ) {
				$terms[ $t->term_id ] = $t;
			} else {
				$custom_terms[] = $t;
			}
		}

		unset( $categories, $custom_taxonomies, $custom_terms );
	}

	echo "<!-- TERMS START -->\n";
	foreach ( $cats as $c ) : ?>
		<wp:category><wp:term_id><?php echo $c->term_id ?></wp:term_id><wp:category_nicename><?php echo $c->slug; ?></wp:category_nicename><wp:category_parent><?php echo $c->parent ? $cats[ $c->parent ]->slug : ''; ?></wp:category_parent><?php self::wxr_cat_name( $c ); ?><?php self::wxr_category_description( $c ); ?><?php self::wxr_term_meta( $c ); ?></wp:category>
<?php endforeach;

	foreach ( $tags as $t ) : ?>
		<wp:tag><wp:term_id><?php echo $t->term_id ?></wp:term_id><wp:tag_slug><?php echo $t->slug; ?></wp:tag_slug><?php self::wxr_tag_name( $t );?><?php self::wxr_tag_description( $t );?><?php self::wxr_term_meta( $t );  ?></wp:tag>
<?php endforeach;

	foreach ( $terms as $t ) : ?>
		<wp:term><wp:term_id><?php echo $t->term_id ?></wp:term_id><wp:term_taxonomy><?php echo $t->taxonomy; ?></wp:term_taxonomy><wp:term_slug><?php echo $t->slug; ?></wp:term_slug><wp:term_parent><?php echo $t->parent ? $terms[ $t->parent ]->slug : ''; ?></wp:term_parent><?php self::wxr_term_name( $t );?><?php self::wxr_term_description( $t );?><?php self::wxr_term_meta( $t ); ?></wp:term>
<?php endforeach;

	echo "<!-- TERMS END -->\n";
	if ( 'all' == $args['content'] ) {
		self::wxr_nav_menu_terms();
	}

	/** This action is documented in wp-includes/feed-rss2.php */ ?>

		<?php do_action( 'rss2_head' );?>

<?php }

static function display_replacers( $post_ids ) {
	global $wpdb, $post;

	if ( $post_ids ) {
		global $wp_query;

		echo "<!-- REPLACERS START -->\n";
		// Fake being in the loop.
		$wp_query->in_the_loop = true;

		// Fetch 20 posts at a time rather than loading the entire table into memory.
		while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
			$where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
			$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );

			// Begin Loop.
			foreach ( $posts as $post ) {
				ob_start();
				$new_post_id = self::display_item( $post, $is_attachment = true );
				array_push( self::$attachment_replacers, $new_post_id );
				echo( ob_get_clean() );
			}
		}

		echo "<!-- REPLACERS END -->\n";
	}
}

static function display_ignored( $post_ids ) {
	global $wpdb, $post;

	if ( $post_ids ) {
		global $wp_query;

		echo "<!-- IGNORED START -->\n";
		// Fake being in the loop.
		$wp_query->in_the_loop = true;

		// Fetch 20 posts at a time rather than loading the entire table into memory.
		while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
			$where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
			$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );

			// Begin Loop.
			foreach ( $posts as $post ) {
				ob_start();
				$new_post_id = self::display_item( $post, $is_attachment = true );
				array_push( self::$ignored_attachments, $new_post_id );
				echo( ob_get_clean() );
			}
		}

		echo "<!-- IGNORED END -->\n";
	}
}

	static function display_featured_images( $post_ids ) {
		global $wpdb, $post;

		if ( $post_ids ) {
			global $wp_query;

			echo "<!-- FEATURED IMAGES START -->\n";

			// Fake being in the loop.
			$wp_query->in_the_loop = true;

			// Fetch 20 posts at a time rather than loading the entire table into memory.
			while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
				$where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
				$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );

				// Begin Loop.
				foreach ( $posts as $post ) {
					ob_start();
					$new_post_id = self::display_item( $post, $is_attach = true );
					array_push( self::$featured_image_replacers, $new_post_id );
					echo( ob_get_clean() );
				}
			}

			echo "<!-- FEATURED IMAGES END -->\n";
		}
	}

	static function display_posts( $post_ids ) {
		global $wpdb, $post;

		if ( $post_ids ) {
			global $wp_query;

			echo "<!-- POSTS START -->\n";
			// Fake being in the loop.
			$wp_query->in_the_loop = true;

			// Fetch 20 posts at a time rather than loading the entire table into memory.
			while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
				$where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
				$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );

				// Begin Loop.
				foreach ( $posts as $post ) {

					// @TOOD make a multi select with ignored post types
					if ( get_post_type( $post ) === 'attachment' ||  get_post_type( $post ) === 'shop_order' ) {
						continue;
					}

					if ( get_post_status( $post->ID ) !== 'publish' ) {
						continue;
					}

					ob_start();
					$post_id = self::display_item( $post );
					echo( ob_get_clean() );
				}
			}

			echo "<!-- POSTS END -->\n";
		}
	}

	static function display_item( $post, $is_attachment = false) {
		global $wpdb;

		setup_postdata( $post );
		$is_sticky = is_sticky( $post->ID ) ? 1 : 0;

		if ( in_array( $post->ID, self::$imported_posts ) ) {
			return;
		}

		// we already imported any attachments needed
		if ( $is_attachment && $post->post_type !== 'attachment' ) {
			return;
		} elseif( ! $is_attachment && $post->post_type === 'attachment' ) {
			return;
		}

		$new_id = $post->ID;
		// in case this is an attachment let's make the id unique
		if ( $post->post_type === 'attachment' ) {
			$new_id = '9999' . $post->ID;
		}

		$postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );  ?>
		<item>
			<title><?php echo apply_filters( 'the_title_rss', $post->post_title ); ?></title>
			<link><?php the_permalink_rss() ?></link>
			<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true ), false ); ?></pubDate>
			<dc:creator><?php echo self::wxr_cdata( get_the_author_meta( 'login' ) ); ?></dc:creator>
			<guid isPermaLink="false"><?php the_guid(); ?></guid>
			<description></description>
			<content:encoded><?php
				/**
				 * Filter the post content used for WXR exports.
				 *
				 * @since 2.5.0
				 *
				 * @param string $post_content Content of the current post.
				 */
				echo self::wxr_cdata( apply_filters( 'the_content_export', $post->post_content ) );
				?></content:encoded>
			<excerpt:encoded><?php
				/**
				 * Filter the post excerpt used for WXR exports.
				 *
				 * @since 2.6.0
				 *
				 * @param string $post_excerpt Excerpt for the current post.
				 */
				echo self::wxr_cdata( apply_filters( 'the_excerpt_export', $post->post_excerpt ) );
				?></excerpt:encoded>
			<wp:post_id><?php echo $new_id; ?></wp:post_id>
			<wp:post_date><?php echo $post->post_date; ?></wp:post_date>
			<wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
			<wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
			<wp:ping_status><?php echo $post->ping_status; ?></wp:ping_status>
			<wp:post_name><?php echo $post->post_name; ?></wp:post_name>
			<wp:status><?php echo $post->post_status; ?></wp:status>
			<wp:post_parent><?php echo $post->post_parent; ?></wp:post_parent>
			<wp:menu_order><?php echo $post->menu_order; ?></wp:menu_order>
			<wp:post_type><?php echo $post->post_type; ?></wp:post_type>
			<wp:post_password><?php echo $post->post_password; ?></wp:post_password>
			<wp:is_sticky><?php echo $is_sticky; ?></wp:is_sticky>
<?php if ( $post->post_type == 'attachment' ) : ?>
			<wp:attachment_url><?php echo wp_get_attachment_url( $post->ID ); ?></wp:attachment_url>
<?php endif;

			// categories first
			self::wxr_post_taxonomy();

			foreach ( $postmeta as $meta ) :

				if ( $meta->meta_key === '_thumbnail_id' && ! empty( $meta->meta_value ) ) {
					$meta->meta_value = self::replace_featured_image( $new_id, $meta->meta_value );
				}

				/**
				 * Filter whether to selectively skip post meta used for WXR exports.
				 *
				 * Returning a truthy value to the filter will skip the current meta
				 * object from being exported.
				 *
				 * @since 3.3.0
				 *
				 * @param bool $skip Whether to skip the current post meta. Default false.
				 * @param string $meta_key Current meta key.
				 * @param object $meta Current meta object.
				 */
				if ( apply_filters( 'wxr_export_skip_postmeta', false, $meta->meta_key, $meta ) ) {
					continue;
				}

				$meta->meta_value = apply_filters( 'wxr_export_post_meta_value', $meta->meta_key, $meta->meta_value ); ?>
			<wp:postmeta>
				<wp:meta_key><?php echo $meta->meta_key; ?></wp:meta_key>
				<wp:meta_value><?php echo self::wxr_cdata( $meta->meta_value ); ?></wp:meta_value>
			</wp:postmeta>
<?php
			endforeach;

			$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = 1 'spam'", $post->ID ) );
			foreach ( $comments as $c ) : ?>
			<wp:comment>
				<wp:comment_id><?php echo $c->comment_ID; ?></wp:comment_id>
				<wp:comment_author><?php echo self::wxr_cdata( $c->comment_author ); ?></wp:comment_author>
				<wp:comment_author_email><?php echo $c->comment_author_email; ?></wp:comment_author_email>
				<wp:comment_author_url><?php echo esc_url_raw( $c->comment_author_url ); ?></wp:comment_author_url>
				<wp:comment_author_IP><?php echo $c->comment_author_IP; ?></wp:comment_author_IP>
				<wp:comment_date><?php echo $c->comment_date; ?></wp:comment_date>
				<wp:comment_date_gmt><?php echo $c->comment_date_gmt; ?></wp:comment_date_gmt>
				<wp:comment_content><?php echo self::wxr_cdata( $c->comment_content ) ?></wp:comment_content>
				<wp:comment_approved><?php echo $c->comment_approved; ?></wp:comment_approved>
				<wp:comment_type><?php echo $c->comment_type; ?></wp:comment_type>
				<wp:comment_parent><?php echo $c->comment_parent; ?></wp:comment_parent>
				<wp:comment_user_id><?php echo $c->user_id; ?></wp:comment_user_id>
<?php
				$c_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID ) );
				foreach ( $c_meta as $meta ) :
					/**
					 * Filter whether to selectively skip comment meta used for WXR exports.
					 *
					 * Returning a truthy value to the filter will skip the current meta
					 * object from being exported.
					 *
					 * @since 4.0.0
					 *
					 * @param bool $skip Whether to skip the current comment meta. Default false.
					 * @param string $meta_key Current meta key.
					 * @param object $meta Current meta object.
					 */
					if ( apply_filters( 'wxr_export_skip_commentmeta', false, $meta->meta_key, $meta ) ) {
						continue;
					} ?>
				<wp:commentmeta>
					<wp:meta_key><?php echo $meta->meta_key; ?></wp:meta_key>
					<wp:meta_value><?php echo self::wxr_cdata( $meta->meta_value ); ?></wp:meta_value>
				</wp:commentmeta>
<?php
				endforeach; ?>
			</wp:comment>
<?php
			endforeach; ?>
		</item>
<?php

		array_push( self::$imported_posts, $new_id );
		return $new_id;
	}

static function display_footer() { ?>
</channel>
</rss>
<?php }

	static function replace_featured_image( $post_id, $value ) {

		if ( ! empty( self::$featured_image_replacers ) ) {
			DemoXmlPlugin::$featured_image_replacers = DemoXmlPlugin::rotate_array( DemoXmlPlugin::$featured_image_replacers );
			return self::$featured_image_replacers[0];
		}

		return $value;
	}

	function replace_the_content_urls( $content ) {
		$reg_exUrl = "#((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?]))#i";
		$content   = preg_replace_callback( $reg_exUrl, array(
			$this,
			'replace_the_content_urls_pregmatch_callback'
		), $content );

		return $content;
	}

	function replace_the_content_urls_pregmatch_callback( $matches ) {

		if ( ! isset( DemoXmlPlugin::$attachment_replacers[0] ) ) {
			return false;
		}
		DemoXmlPlugin::$attachment_replacers = DemoXmlPlugin::rotate_array( DemoXmlPlugin::$attachment_replacers );

		$attach_id = DemoXmlPlugin::$attachment_replacers[0];
		$src       = wp_get_attachment_image_src( str_replace('9999', '', $attach_id), 'full' );
		if ( strpos( $matches[0], 'wp-content/uploads' ) > 0 ) {
			$matches[0] = $src[0];
		}

		if ( isset( $matches[0] ) ) {
			return $matches[0];
		}

		return false;
	}

	function replace_gallery_shortcodes_ids( $content ) {
		// pregmatch only ids attribute
		$pattern = '((\[gallery.*])?ids=\"(.*)\")';

		$content = preg_replace_callback( $pattern, array(
			$this,
			'replace_gallery_shortcodes_ids_pregmatch_callback'
		), $content );

		return $content;
	}

	function replace_gallery_shortcodes_ids_pregmatch_callback( $matches ) {

		if ( isset( $matches[2] ) && ! empty( $matches[2] ) ) {

			$replace_ids = array();
			$matches[2]  = explode( ',', $matches[2] );
			foreach ( $matches[2] as $key => $match ) {
				if ( isset( self::$attachment_replacers[0] ) ) {
					self::$attachment_replacers = self::rotate_array( self::$attachment_replacers );
					$replace_ids[ $key ] = self::$attachment_replacers[0];
				}
			}

			$replace_string = implode( ',', $replace_ids );

			return ' ids="' . $replace_string . '"';
		}
	}

	function replace_metadata_by_id( $meta_key, $meta_value ) {
	/**
	 * Some checks
	 */
	if ( ! empty( $meta_value ) && isset( $this->config['replace_args']['replace_in_metadata']['by_id'] ) && ! empty( $this->config['replace_args']['replace_in_metadata']['by_id'] ) && in_array( $meta_key, $this->config['replace_args']['replace_in_metadata']['by_id'] ) ) {

		// I know for sure this meta_value has an id or ids separated with commas
		$ids = explode( ',', $meta_value );

		// cache replacers
		$replacers = self::$attachment_replacers;
		$new_meta  = array();
		foreach ( $ids as $key => $id ) {
			// always get the first id, and after that shift the array
			if ( isset( $replacers[0] ) ) {
				$replacers = self::rotate_array( $replacers );
				$new_meta[ $key ] = $replacers[0];
			}
		}

		self::$attachment_replacers        = self::rotate_array( $replacers );
		$return_string = implode( ',', $new_meta );

		return $return_string;
	}

	return $meta_value;
	}

	/**
	 * Wrap given string in XML CDATA tag.
	 *
	 * @since 2.1.0
	 *
	 * @param string $str String to wrap in XML CDATA tag.
	 *
	 * @return string
	 */
	static function wxr_cdata( $str ) {
		if ( seems_utf8( $str ) == false ) {
			$str = utf8_encode( $str );
		}

		// $str = ent2ncr(esc_html($str));
		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

	/**
	 * Return the URL of the site
	 *
	 * @since 2.5.0
	 *
	 * @return string Site URL.
	 */
	static function wxr_site_url() {
		// Multisite: the base URL.
		if ( is_multisite() ) {
			return network_home_url();
		} // WordPress (single site): the blog URL.
		else {
			return get_bloginfo_rss( 'url' );
		}
	}

	/**
	 * Output a cat_name XML tag from a given category object
	 *
	 * @since 2.1.0
	 *
	 * @param object $category Category Object
	 */
	static function wxr_cat_name( $category ) {
		if ( empty( $category->name ) ) {
			return;
		}

		echo '<wp:cat_name>' . self::wxr_cdata( $category->name ) . '</wp:cat_name>';
	}

	/**
	 * Output a category_description XML tag from a given category object
	 *
	 * @since 2.1.0
	 *
	 * @param object $category Category Object
	 */
	static function wxr_category_description( $category ) {
		if ( empty( $category->description ) ) {
			return;
		}

		echo '<wp:category_description>' . self::wxr_cdata( $category->description ) . '</wp:category_description>';
	}

	/**
	 * Output a tag_name XML tag from a given tag object
	 *
	 * @since 2.3.0
	 *
	 * @param object $tag Tag Object
	 */
	static function wxr_tag_name( $tag ) {
		if ( empty( $tag->name ) ) {
			return;
		}

		echo '<wp:tag_name>' . self::wxr_cdata( $tag->name ) . '</wp:tag_name>';
	}

	/**
	 * Output a tag_description XML tag from a given tag object
	 *
	 * @since 2.3.0
	 *
	 * @param object $tag Tag Object
	 */
	static function wxr_tag_description( $tag ) {
		if ( empty( $tag->description ) ) {
			return;
		}

		echo '<wp:tag_description>' . self::wxr_cdata( $tag->description ) . '</wp:tag_description>';
	}

	/**
	 * Output a term_name XML tag from a given term object
	 *
	 * @since 2.9.0
	 *
	 * @param object $term Term Object
	 */
	static function wxr_term_name( $term ) {
		if ( empty( $term->name ) ) {
			return;
		}

		echo '<wp:term_name>' . self::wxr_cdata( $term->name ) . '</wp:term_name>';
	}

	/**
	 * Output a term_description XML tag from a given term object
	 *
	 * @since 2.9.0
	 *
	 * @param object $term Term Object
	 */
	static function wxr_term_description( $term ) {
		if ( empty( $term->description ) ) {
			return;
		}

		echo '<wp:term_description>' . self::wxr_cdata( $term->description ) . '</wp:term_description>';
	}

	/**
	 * Output a term_description XML tag from a given term object
	 *
	 * @since 2.9.0
	 *
	 * @param object $term Term Object
	 */
	function wxr_term_meta( $term ) {
		global $wpdb;

		$termmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE term_id = %d", $term->term_id ) );

		foreach ( $termmeta as $meta ) {
			/**
			 * Filter whether to selectively skip term meta used for WXR exports.
			 *
			 * Returning a truthy value to the filter will skip the current meta
			 * object from being exported.
			 *
			 * @since 4.4.0
			 *
			 * @param bool $skip Whether to skip the current term meta. Default false.
			 * @param string $meta_key Current meta key.
			 * @param object $meta Current meta object.
			 */
			if ( ! apply_filters( 'wxr_export_skip_termmeta', false, $meta->meta_key, $meta ) ) {
				if ( $meta->meta_key === 'pix_term_icon' && ! empty( $meta->meta_value ) ) {
					$meta->meta_value = '9999' . $meta->meta_value;
				}
				printf( "<wp:termmeta><wp:meta_key>%s</wp:meta_key><wp:meta_value>%s</wp:meta_value></wp:termmeta>", self::wxr_cdata( $meta->meta_key ), self::wxr_cdata( $meta->meta_value ) );
			}
		}
	}

	/**
	 * Output list of authors with posts
	 *
	 * @since 3.1.0
	 *
	 * @param array $post_ids Array of post IDs to filter the query by. Optional.
	 */
	static function wxr_authors_list( array $post_ids = null ) {
		global $wpdb;

		if ( ! empty( $post_ids ) ) {
			$post_ids = array_map( 'absint', $post_ids );
			$and      = 'AND ID IN ( ' . implode( ', ', $post_ids ) . ')';
		} else {
			$and = '';
		}

		$authors = array();
		$results = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft' $and" );
		foreach ( (array) $results as $result ) {
			$authors[] = get_userdata( $result->post_author );
		}

		$authors = array_filter( $authors );

		foreach ( $authors as $author ) {
			echo "\t\t<wp:author>";
			echo '<wp:author_id>' . $author->ID . '</wp:author_id>';
			echo '<wp:author_login>' . $author->user_login . '</wp:author_login>';
			echo '<wp:author_email>' . $author->user_email . '</wp:author_email>';
			echo '<wp:author_display_name>' . self::wxr_cdata( $author->display_name ) . '</wp:author_display_name>';
			echo '<wp:author_first_name>' . self::wxr_cdata( $author->user_firstname ) . '</wp:author_first_name>';
			echo '<wp:author_last_name>' . self::wxr_cdata( $author->user_lastname ) . '</wp:author_last_name>';
			echo "</wp:author>\n";
		}
	}

	/**
	 * Ouput all navigation menu terms
	 *
	 * @since 3.1.0
	 */
	static function wxr_nav_menu_terms() {
		$nav_menus = wp_get_nav_menus();
		if ( empty( $nav_menus ) || ! is_array( $nav_menus ) ) {
			return;
		}

		foreach ( $nav_menus as $menu ) {
			echo "\t\t<wp:term><wp:term_id>{$menu->term_id}</wp:term_id><wp:term_taxonomy>nav_menu</wp:term_taxonomy><wp:term_slug>{$menu->slug}</wp:term_slug>";
			self::wxr_term_name( $menu );
			echo "</wp:term>\n";
		}
	}

	/**
	 * Output list of taxonomy terms, in XML tag format, associated with a post
	 *
	 * @since 2.3.0
	 */
	static function wxr_post_taxonomy() {
		$post = get_post();

		$taxonomies = get_object_taxonomies( $post->post_type );
		if ( empty( $taxonomies ) ) {
			return;
		}
		$terms = wp_get_object_terms( $post->ID, $taxonomies );

		foreach ( (array) $terms as $term ) {
			echo "\t\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . self::wxr_cdata( $term->name ) . "</category>\n";
		}
	}

	static function wxr_filter_postmeta( $return_me, $meta_key ) {
		if ( '_edit_lock' == $meta_key ) {
			$return_me = true;
		}

		return $return_me;
	}

	static function rotate_array( &$arr ) {

		if ( is_array( $arr ) && ! empty( $arr ) ) {
			array_push( $arr, array_shift( $arr ) );
		}

		return $arr;
	}

}