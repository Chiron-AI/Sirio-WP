<?php
/**
 * Settings Class.
 *
 * @since   0.0.1
 *
 * @package Chiron_Sirio
 */

namespace Chiron_Sirio;

/**
 * Class Settings
 *
 * @since   0.0.1
 *
 * @package Chiron_Sirio
 */
class Settings {

	/**
	 * Path to the root plugin file.
	 *
	 * @var     string
	 * @since   0.0.1
	 */
	private $plugin_root;

	/**
	 * Plugin name.
	 *
	 * @var     string
	 * @since   0.0.1
	 */
	private $plugin_name;

	/**
	 * Plugin slug.
	 *
	 * @var     string
	 * @since   0.0.1
	 */
	private $plugin_slug;

	/**
	 * Plugin prefix.
	 *
	 * @var     string
	 * @since   0.0.1
	 */
	private $plugin_prefix;

	/**
	 * Constructor.
	 *
	 * @since   0.0.1
	 */
	public function __construct() {
		$this->plugin_root   = PLUGIN_NAME_ROOT;
		$this->plugin_name   = PLUGIN_NAME_NAME;
		$this->plugin_slug   = PLUGIN_NAME_SLUG;
		$this->plugin_prefix = PLUGIN_NAME_PREFIX;
	}

	/**
	 * Do Work
	 *
	 * @since   0.0.1
	 */
	public function run() {
		// @codingStandardsIgnoreStart
		//add_action( 'admin_init', array( $this, 'init_settings_page' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		// Add WooCommerce Navigation Bar
		add_action('admin_menu', array( $this, 'add_navigation_bar'));
		add_action( 'plugin_action_links_' . plugin_basename( $this->plugin_root ) , array( $this, 'add_settings_link' ) );
		// add Shop Manager capability to save options
		add_action('option_page_capability_' . $this->plugin_prefix, array( $this,'option_page_capability'));
		// @codingStandardsIgnoreEnd
	}




	/**
	 * Add the settings page.
	 *
	 * @since   0.0.1
	 */
	public function add_settings_page() {


		add_submenu_page(
				'woocommerce',
				esc_html__( 'Settings', 'chiron-sirio' ),
				esc_html__( 'Chiron Sirio', 'chiron-sirio' ),
				$this->get_allowed_capability(),
				$this->plugin_prefix,
				array( $this, 'render_settings_page' )
		);
	}
	
	/**
	* Include the new Navigation Bar the Admin page.
	*/
	public function add_navigation_bar() {
		if ( function_exists( 'wc_admin_connect_page' ) ) {
			wc_admin_connect_page(
				array(
					'id' => $this->plugin_prefix,
					'screen_id' => 'wp_page_sirio',
					'title'     => esc_html__( 'Chiron Sirio', 'chiron-sirio'  ),
				)
			);
		}
	}
	
	/**
	 * Setta i campi per il form di configurazione,
	 * se attivato recurring viene sovrascritte dalla classe figlia
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			
			'enabled' => array(
				'title' => __('Enable/Disable', 'sirio'),
				'type' => 'checkbox',
				'label' => __("Enable Sirio Module.", 'woocommerce-sirio'),
				'default' => 'no'
			),
			
		);
	}
	
	/**
	* Render the settings page.
	*
	* @since   0.0.1
	*/
	public function init_settings_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Sirio', 'chiron-sirio' ); ?></h2>
		</div>
  
		<?php
       // $this->init_form_fields();
	}

	/**
	* Add 'Settings' action on installed plugin list.
	*
	* @param array $links An array of plugin action links.
	*
	* @since   0.0.1
	*/
	public function add_settings_link( $links ) {
		$new_item2 = '<a target="_blank" href="https://www.chiron.ai" target="_blank">by <span style="background-color: #4067af; color: white; font-weight: bold; padding: 1px 8px;">Chiron</span></a>';
		$new_item1 = '<a href="' . admin_url('admin.php?page=' . $this->plugin_prefix ) . '">' . esc_html__( 'Settings', 'chiron-sirio' ) . '</a>';
		array_unshift($links, $new_item2, $new_item1);
		return $links;
	}

	/**
	* Check if current user can view options pages/ save plugin options
	*/
	public function option_page_capability() {
		return $this->get_allowed_capability();
	}
	
	/**
	* Get allowe capability
	*/
	public function get_allowed_capability() {
		if (current_user_can('manage_woocommerce')) {
			return 'manage_woocommerce';
		}
		return 'manage_options';
	}
}
