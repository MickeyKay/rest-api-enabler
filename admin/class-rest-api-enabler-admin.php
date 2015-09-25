<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://wordpress.org/plugins/rest-api-enabler
 * @since      1.0.0
 *
 * @package    REST_API_Enabler
 * @subpackage REST_API_Enabler/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    REST_API_Enabler
 * @subpackage REST_API_Enabler/admin
 * @author     Mickey Kay Creative mickey@mickeykaycreative.com
 */
class REST_API_Enabler_Admin {

	/**
	 * The main plugin instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      REST_API_Enabler    $plugin    The main plugin instance.
	 */
	private $plugin;

	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_slug    The slug of this plugin.
	 */
	private $plugin_slug;

	/**
	 * The display name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The plugin display name.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The instance of this class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      REST_API_Enabler_Admin    $instance    The instance of this class.
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
     * @return    REST_API_Enabler_Admin    A single instance of this class.
     */
    public static function get_instance( $plugin ) {

        if ( null == self::$instance ) {
            self::$instance = new self( $plugin );
        }

        return self::$instance;

    }

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_slug       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
		$this->plugin_slug = $this->plugin->get( 'slug' );
		$this->plugin_name = $this->plugin->get( 'name' );
		$this->version = $this->plugin->get( 'version' );
		$this->options = $this->plugin->get( 'options' );

	}

	/**
	 * Add settings page.
	 *
	 * @since 1.0.0
	 */
	function add_settings_page() {

		$this->settings_page = add_options_page(
			__( 'REST API Enabler', 'rest-api-enabler'), // Page title
			__( 'REST API Enabler', 'rest-api-enabler'), // Menu title
			'manage_options', // Capability
			$this->plugin_slug, // Page ID
			array( $this, 'do_settings_page' ) // Callback
		);

	}

	/**
	 * Output contents of settings page.
	 *
	 * @since 1.0.0
	 */
	function do_settings_page() {
		?>
		<?php screen_icon();
		?>
		<div class="wrap <?php echo $this->plugin_slug; ?>-settings">
	        <h2><?php echo $this->plugin_name; ?></h2>
	        <?php if ( defined( 'REST_API_VERSION' ) ) { ?>
			<form action='options.php' method='post'>
				<?php
				settings_fields( $this->plugin_slug );
				do_settings_sections( $this->plugin_slug );
				submit_button();
				?>
			</form>
			<?php
			} else {
				echo '<p>' . sprintf( __( 'You need to install the %sWordPress REST API%s for this plugin to work.' ), '<a href="https://wordpress.org/plugins/rest-api/" target="_blank">', '</a>' ) . '</p>';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Add settings fields to the settings page.
	 *
	 * @since 1.0.0
	 */
	function add_settings_fields() {

		register_setting(
			$this->plugin_slug, // Option group
			$this->plugin_slug, // Option name
			array( $this, 'sanitize_setting' ) // Sanitization
		);

		// General settings section
		add_settings_section(
			'general', // Section ID
			__( 'Post Types', 'ultimate-lightbox' ), // Title
			null, // Callback
			$this->plugin_slug // Page
		);

		// Define args for fetching post types.
		$arg = array(

		);

		foreach( get_post_types( null, 'objects' ) as $post_type => $post_type_object ) {

			$id = "post_type_{$post_type}";

			add_settings_field(
				$id, // ID
				$post_type_object->labels->name,
				array( $this, 'render_post_type_settings' ), // Callback
				$this->plugin_slug, // Page
				'general', // Section
				array( // Args
					'id'               => $id,
					'post_type_object' => $post_type_object,
				)
			);

		}

	}

	function render_post_type_settings( $args ) {

		// Do post type checkbox.
		$args['secondary_id'] = 'enabled';
		$this->render_checkbox( $args );

		// Do post type REST base.
		$args['secondary_id'] = 'rest_base';

		// Get input markup.
		ob_start();
		$this->render_text_input( $args );
		$rest_base_output = ob_get_clean();
		printf( '&nbsp;&nbsp;&nbsp;<span class="rae-rest-base rae-hidden-opacity">%s: %s</span>', __( 'REST base', 'rest-api-enabler' ), $rest_base_output );

	}

	/**
	 * Checkbox settings field callback.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Args from add_settings_field().
	 */
	function render_checkbox( $args ) {

		// Set up option name and value.
		if ( isset( $args['secondary_id'] ) ) {
			$option_name = $this->get_option_name( $args['id'], $args['secondary_id'] );
			$option_value = $this->get_option_value( $args['id'], $args['secondary_id'] );
		} else {
			$option_name = $this->get_option_name( $args['id'] );
			$option_value = $this->get_option_value( $args['id'] );
		}

		$checked = isset( $option_value ) ? $option_value : null;

		// Get post type REST info.
		if ( isset ( $args['post_type_object'] ) ) {

			$post_type_object = $args['post_type_object'];
			$init_rest_base = isset( $post_type_object->rest_base ) ? $post_type_object->rest_base : '';

			// Get
			if ( isset( $option_value ) ) {
				$checked = $option_value;
			} elseif ( $init_rest_base ) {
				$checked = true;
			}

		}

		printf(
            '<label for="%s"><input type="checkbox" value="1" id="%s" name="%s" %s/> %s</label>',
            $option_name,
            $option_name,
            $option_name,
            checked( 1, $checked, false ),
            ! empty( $args['description'] ) ? $args['description'] : ''
        );

	}

	/**
	 * Text input settings field callback.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Args from add_settings_field().
	 */
	function render_text_input( $args ) {

		// Set up option name and value.
		if ( isset( $args['secondary_id'] ) ) {
			$option_name = $this->get_option_name( $args['id'], $args['secondary_id'] );
			$option_value = $this->get_option_value( $args['id'], $args['secondary_id'] );
		} else {
			$option_name = $this->get_option_name( $args['id'] );
			$option_value = $this->get_option_value( $args['id'] );
		}

		$value = $option_value;

		// Get post type REST info.
		if ( ! $value && isset ( $args['post_type_object'] ) ) {

			$post_type_object = $args['post_type_object'];
			$rest_base = isset( $post_type_object->rest_base ) ? $post_type_object->rest_base : '';

			// Auto-generate initial rest_base if not already set.
			if ( ! $rest_base ) {
				$rest_base = sanitize_title_with_dashes( $args['post_type_object']->labels->name );
			}

			$value = $rest_base;

		}

		printf(
            '%s<input type="text" value="%s" id="%s" name="%s" class="regular-text %s"/><br /><p class="description" for="%s">%s</p>',
            ! empty( $args['sub_heading'] ) ? '<b>' . $args['sub_heading'] . '</b><br />' : '',
            $value,
            $option_name,
            $option_name,
            ! empty( $args['class'] ) ? $args['class'] : '',
            $option_name,
            ! empty( $args['description'] ) ? $args['description'] : ''
        );

	}

	private function get_option_name( $option_id, $secondary_id = '' ) {
		if ( $secondary_id ) {
			return sprintf( '%s[%s][%s]', $this->plugin_slug, $option_id, $secondary_id );
		} else {
			return sprintf( '%s[%s]', $this->plugin_slug, $option_id );
		}
	}

	private function get_option_value( $option_id, $secondary_id = '' ) {

		if ( $secondary_id ) {
			return isset( $this->options[ $option_id ][ $secondary_id ] ) ? $this->options[ $option_id ][ $secondary_id ] : null;
		} else {
			return isset( $this->options[ $option_id ] ) ? $this->options[ $option_id ] : null;
		}

	}

	function sanitize_setting( $input ) {

		$new_input = array();

		// Save checkboxes as 1's or 0's, not null.
		foreach ( get_post_types() as $post_type_slug => $post_type_object ) {
			$new_input["post_type_{$post_type_slug}"]['enabled'] = isset( $input["post_type_{$post_type_slug}"]['enabled'] ) ? $input["post_type_{$post_type_slug}"]['enabled'] : 0;
			$new_input["post_type_{$post_type_slug}"]['rest_base'] = isset( $input["post_type_{$post_type_slug}"]['rest_base'] ) ? $input["post_type_{$post_type_slug}"]['rest_base'] : '';
		}

		return $new_input;

	}

	/**
	 * Register the stylesheets for the admin.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in REST_API_Enabler_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The REST_API_Enabler_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'css/rest-api-enabler-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the scripts for the admin.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_slug, plugin_dir_url( __FILE__ ) . 'js/rest-api-enabler-admin.js', array( 'jquery' ), $this->version, false );

	}

}
