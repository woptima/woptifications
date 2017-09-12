<?php
/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class woptifications_Admin {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'woptifications_options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'woptifitications_hooks';
	private $metabox_id_2 = 'woptifications_alerts';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = 'woptifications settings';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = 'woptifications_settings';

	/**
	 * Holds an instance of the object
	 *
	 * @var Myprefix_Admin
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	private function __construct() {
		// Set our title
		$this->title = __( 'Wopmist Options', 'woptimizr' );
	}

	/**
	 * Returns the running object
	 *
	 * @return Myprefix_Admin
	 **/
	public static function get_instance() {
		if( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}
		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_hooks' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_alert' ) );
	}


	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
    <div class="wrap cmb2-options-page <?php echo $this->key; ?>">
        <h2>
            <?php echo esc_html( get_admin_page_title() ); ?>
        </h2>
        <?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
        <?php cmb2_metabox_form( $this->metabox_id_2, $this->key ); ?>
    </div>
    <?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	

	/* CUSTOM FUNCTIONS */
	function populate_post_types() {
        $post_types=get_post_types(['public' => true, '_builtin' => true],'names');
        $ret = array();
        foreach ($post_types as $post_type) {
            $ret[$post_type] = $post_type;
        }
        return $ret;
    }
	
	/////////////////////////////
	/// COLORS
	/////////////////////////////

	function add_options_page_colors() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );
		
		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );
		
		$prefix = "wps_";

		$cmb->add_field( array(
			'name'    => 'Hooks settings',
			'desc'    => 'Select notifications hooks',
			'id'      => 'hooks',
			'type'    => 'multicheck_inline',
			'options' => array(
	                'post' => 'post publish',
	                'comment' => 'new comment',
	            ),
		) );

		$cmb->add_field( array(
			'name'    => 'Publish Post types',
			'desc'    => 'Select post types for post publish',
			'id'      => 'publish_post_types',
			'type'    => 'multicheck_inline',
			'options' => populate_post_types(),
		) );

	}
	
	/////////////////////////////
	/// GENERAL
	/////////////////////////////

	function add_options_page_menu() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id_2}", array( $this, 'settings_notices' ), 10, 2 );
		
		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id_2,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );
		
		$prefix = "wps_";
		
					
	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'woptifications' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Myprefix_Admin object
 * @since  0.1.0
 * @return Myprefix_Admin object
 */
function woptifications_admin() {
	return woptifications_Admin::get_instance();
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */

function woptifications_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( woptifications_admin()->key, $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( woptifications_admin()->key, $default );
	$val = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}

// Get it started
woptifications_admin();
