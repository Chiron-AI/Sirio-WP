<?php
/**
 * Notices Class.
 *
 * @since   0.1.0
 *
 * @package Chiron_Sirio
 */

namespace Chiron_Sirio;

/**
 * Class Notices
 *
 * Generates various plugin notices, including on activation.
 *
 * @since   0.1.0
 *
 * @package Chiron_Sirio
 */
class Notices {

	/**
	 * Path to the root plugin file.
	 *
	 * @var     string
	 * @access  private
	 * @since   0.1.0
	 */
	private $plugin_root;

	/**
	 * Plugin name.
	 *
	 * @var     string
	 * @access  private
	 * @since   0.1.0
	 */
	private $plugin_name;

	/**
	 * Plugin slug.
	 *
	 * @var     string
	 * @access  private
	 * @since   0.1.0
	 */
	private $plugin_slug;

	/**
	 * Plugin prefix.
	 *
	 * @var     string
	 * @access  private
	 * @since   0.1.0
	 */
	private $plugin_prefix;

	/**
	 * Minimum PHP Version.
	 *
	 * @var     string
	 * @access  private
	 * @since   0.1.0
	 */
	private $min_php_ver;

	/**
	 * Constructor
	 *
	 * @since   0.1.0
	 */
	public function __construct() {
		$this->plugin_root   = PLUGIN_NAME_ROOT;
		$this->plugin_name   = PLUGIN_NAME_NAME;
		$this->plugin_slug   = PLUGIN_NAME_SLUG;
		$this->plugin_prefix = PLUGIN_NAME_PREFIX;
		$this->min_php_ver   = '5.6';
	}

	/**
	 * Unleash Hell.
	 *
	 * @since   0.1.0
	 */
	public function run() {
		// Hook in specific functionality such as adding notices etc.
		add_action( 'admin_notices', array( $this, 'display_activation_notices' ), 10 );

		// Display warning if using unsupported PHP version.
		add_action( 'admin_notices', array( $this, 'display_php_version_warning_notice' ), 15 );
	}

	/**
	 * Display notice(s) on plugin activation.
	 *
	 * @since   0.1.0
	 */
	public function display_activation_notices() {

		// Does the activation transient exist?
		if ( ! empty( get_transient( $this->plugin_prefix . '_activated' ) ) ) {

			$activation_notices = array();

			// Add a successful activation notice.
			/* translators: 1 is the name of the plugin */
			$activation_text = sprintf(
				/* translators: 1 is the name of the plugin */
				__( '%s has been successfully activated.', 'chiron-sirio' ),
				$this->plugin_name
			);
			$activation_notice    = apply_filters( $this->plugin_prefix . '_activation_notice', $activation_text );
			$activation_notices[] = $activation_notice;

			// Have we got any notices to display?
			if ( ! empty( $activation_notices ) ) {

				// Loop through the array and generate the notices.
				foreach ( $activation_notices as $notice ) {
					echo '<div class="updated notice is-dismissible"><p>' . esc_html( $notice ) . '</p></div>';
				}
			}

			// Delete the activated/deactivated transients.
			delete_transient( $this->plugin_prefix . '_activated' );
			delete_transient( $this->plugin_prefix . '_deactivated' );
		}
	}

	/**
	 * Display warning if running an unsupported version of PHP
	 *
	 * @since   0.1.0
	 */
	public function display_php_version_warning_notice() {

		if ( version_compare( phpversion(), $this->min_php_ver, '<' ) ) {

			$php_version_notice = sprintf(
				/* translators: 1 is the minimum PHP version, 2 is the name of the plugin. */
				__( 'Your web-server is running an un-supported version of PHP. Please upgrade to version %1$s  or higher to avoid potential issues with %2$s and other WordPress plugins.', 'chiron-sirio' ),
				$this->min_php_ver,
				$this->plugin_name
			);
			echo '<div class="error notice notice-warning"><p>' . esc_html( $php_version_notice ) . '</p></div>';
		}

	}
}
