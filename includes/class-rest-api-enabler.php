<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://wordpress.org/plugins/rest-api-enabler
 * @since      1.0.0
 *
 * @package    REST_API_Enabler
 * @subpackage REST_API_Enabler/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    REST_API_Enabler
 * @subpackage REST_API_Enabler/includes
 * @author     Mickey Kay Creative mickey@mickeykaycreative.com
 */
class REST_API_Enabler {

	/**
	 * The main plugin file.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_file    The main plugin file.
	 */
	protected $plugin_file;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      REST_API_Enabler_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $slug    The string used to uniquely identify this plugin.
	 */
	protected $slug;

	/**
	 * The display name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $name    The plugin display name.
	 */
	protected $name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The instance of this class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      REST_API_Enabler    $instance    The instance of this class.
	 */
	private static $instance = null;

	/**
     * Plugin options.
     *
     * @since  1.0.0
     *
     * @var    string
     */
    protected $options;

	/**
     * Creates or returns an instance of this class.
     *
     * @return    REST_API_Enabler    A single instance of this class.
     */
    public static function get_instance( $args = array() ) {

        if ( null == self::$instance ) {
            self::$instance = new self( $args );
        }

        return self::$instance;

    }

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $args ) {

		$this->plugin_file = $args['plugin_file'];

		$this->slug = 'rest-api-enabler';
		$this->name = __( 'REST API Enabler', 'rest-api-enabler' );
		$this->version = '1.0.0';
		$this->options = get_option( $this->slug );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		//$this->define_public_hooks();
		$this->define_shared_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - REST_API_Enabler_Loader. Orchestrates the hooks of the plugin.
	 * - REST_API_Enabler_i18n. Defines internationalization functionality.
	 * - REST_API_Enabler_Admin. Defines all hooks for the dashboard.
	 * - REST_API_Enabler_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rest-api-enabler-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rest-api-enabler-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rest-api-enabler-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rest-api-enabler-public.php';

		$this->loader = new REST_API_Enabler_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the REST_API_Enabler_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new REST_API_Enabler_i18n();
		$plugin_i18n->set_domain( $this->slug );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = REST_API_Enabler_Admin::get_instance( $this );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add settings page and fields.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'add_settings_fields' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = REST_API_Enabler_Public::get_instance( $this );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to both the admin and public-facing
	 * functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shared_hooks() {

		$plugin_shared = $this;

		// Enable REST API support for post types.
		$this->loader->add_action( 'init', $plugin_shared, 'add_rest_api_support', 15 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    REST_API_Enabler_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Get any plugin property.
	 *
	 * @since     1.0.0
	 * @return    mixed    The plugin property.
	 */
	public function get( $property = '' ) {
		return $this->$property;
	}

	/**
	 * Add custom REST API support to specificed post types.
	 *
	 * @since 1.0.0
	 */
	public function add_rest_api_support() {

		global $wp_post_types;

		$post_meta_enabled = $this->is_post_meta_enabled();

		// Loop over each post type, register support for the API, and add the response filter.
		foreach ( $wp_post_types as $post_type_slug => $post_type_object ) {

			// Add REST API support for post types as defined in the plugin settings.
			$this->add_rest_api_support_for_post_type( $post_type_slug );

			// Enable REST API support for post meta.
			if ( $post_meta_enabled ) {
				add_filter( "rest_prepare_{$post_type_slug}", array( $this, 'add_rest_api_support_for_post_meta' ), 10, 3 );
			}

		}

	}

	private function add_rest_api_support_for_post_type( $post_type_slug ) {

		global $wp_post_types;

		// If no option has been explicitly saved for this post type, then skip it and maintain the defaults.
		if ( ! isset( $this->options["post_type_{$post_type_slug}"]['show_in_rest'] ) ) {
			return;
		}

		// If show_in_rest option has been saved (1 or 0), then set REST API values for post type based on saved options.
		$option = $this->options["post_type_{$post_type_slug}"];
		$wp_post_types[ $post_type_slug ]->show_in_rest = $option['show_in_rest'];
		$wp_post_types[ $post_type_slug ]->rest_base = $option['rest_base'];
		$wp_post_types[ $post_type_slug ]->rest_controller_class = 'WP_REST_Posts_Controller';

	}

	/**
	 * Add post meta to REST API response.
	 *
	 * @since 1.0.0
	 *
	 * @param stdClass        $response Default API response object.
	 * @param WP_Post         $post     Current post.
	 * @param WP_REST_Request $request  Current API request.
	 *
	 * @return stdClass $response Updated response request.
	 */
	public function add_rest_api_support_for_post_meta( $response, $post, $request ) {

		// Get initial response data.
		$response_data = $response->get_data();

		// Get post meta based on method for inclusion/exclusion.
		$post_meta = get_post_custom( $post->ID );

		// Get post meta include/exclude setting.
		$show_post_meta = isset( $this->options['show_post_meta'] ) ? $this->options['show_post_meta'] : null;
		$post_meta_checked = isset( $this->options['post_meta_individual'] ) ? $this->options['post_meta_individual'] : null;

		// Get array of post meta based on include/exclude settings.
		switch ( $show_post_meta ) {

			case 'exclude':
				$response_post_meta = array_diff_key( $post_meta, $post_meta_checked );
				break;

			default: // Enabled
				$response_post_meta = array_intersect_key( $post_meta, $post_meta_checked );

		}

		// Add post meta to response data.
		$response_data = array_merge( $response_data, $response_post_meta );

		// Re-assemble response.
		$response->set_data( $response_data );

		return $response;

	}

	/**
	 * Check if any post meta is enabled for REST API inclusion.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether or not post meta support is enabled.
	 */
	private function is_post_meta_enabled() {

		$show_post_meta = isset( $this->options['show_post_meta'] ) ? $this->options['show_post_meta'] : null;
		$post_meta_checked = isset( $this->options['post_meta_individual'] ) ? $this->options['post_meta_individual'] : null;
		$no_post_meta_enabled = ( 'include' === $show_post_meta ) && ! isset( $post_meta_checked );
		$add_post_meta_support = $show_post_meta && ! $no_post_meta_enabled;

		return $add_post_meta_support;

	}

}
