<?php
/**
 * Enqueues Class.
 *
 * @since   0.1.0
 *
 * @package Chiron_Sirio
 */

namespace Chiron_Sirio;

/**
 * Class Enqueues
 *
 * Enqueues JS and CSS dependencies.
 *
 * @since   0.1.0
 *
 * @package Chiron_Sirio
 */
class Enqueues {

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
	 * Debug mode status
	 *
	 * @var     bool
	 * @access  private
	 * @since   0.1.0
	 */
	private $debug_mode;

	/**
	 * Asset Suffix
	 *
	 * @var     string
	 * @access  private
	 * @since   0.1.0
	 */
	private $asset_suffix;

	/**
	 * Constructor.
	 *
	 * @since   0.1.0
	 */
	public function __construct() {
		$this->plugin_root   = PLUGIN_NAME_ROOT;
		$this->plugin_name   = PLUGIN_NAME_NAME;
		$this->plugin_slug   = PLUGIN_NAME_SLUG;
		$this->plugin_prefix = PLUGIN_NAME_PREFIX;

		// Determine whether we're in debug mode, and what the
		// asset suffix should be.
		$this->debug_mode   = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? true : false;
		$this->asset_suffix = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min';
	}

	/**
	 * Unleash Hell.
	 *
	 * @since   0.1.0
	 */
	public function run() {
		// Enqueue Front-end JS.
		add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue_scripts' ), 10 );
	}

	/**
	 * Enqueue Public Scripts.
	 *
	 * @since   0.1.0
	 */
	public function public_enqueue_scripts() {

		$do_public_enqueue     = apply_filters( $this->plugin_prefix . 'do_public_enqueue', true );
		$do_public_js_enqueue  = apply_filters( $this->plugin_prefix . 'do_public_js_enqueue', true );

		// Public JS.
		if ( $do_public_enqueue && $do_public_js_enqueue ) {
			$public_js_url  = 'https://api.sirio.chiron.ai/api/v1/profiling';
			$public_js_path = dirname( $this->plugin_root ) . '/assets/dist/js/' . $this->plugin_slug . '-public' . $this->asset_suffix . '.js';

			wp_enqueue_script(
				$this->plugin_slug . '-public-js',
				$public_js_url,
				array( 'jquery' ),
				filemtime( $public_js_path ),
				true
			);
		}
	}



}
