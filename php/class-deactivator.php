<?php
/**
 * Deactivator Class.
 *
 * @since   0.1.0
 *
 * @package Chiron_Sirio
 */

namespace Chiron_Sirio;

/**
 * Class Deactivator
 *
 * Carry out actions when the plugin is deactivated.
 *
 * @since   0.1.0
 *
 * @package Chiron_Sirio
 */
class Deactivator {

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
	 * Constructor.
	 *
	 * @since   0.1.0
	 */
	public function __construct() {
		$this->plugin_root   = PLUGIN_NAME_ROOT;
		$this->plugin_name   = PLUGIN_NAME_NAME;
		$this->plugin_slug   = PLUGIN_NAME_SLUG;
		$this->plugin_prefix = PLUGIN_NAME_PREFIX;
	}

	/**
	 * Unleash Hell.
	 *
	 * @since   0.1.0
	 */
	public function run() {
		// Register the deactivation callback.
		register_deactivation_hook( $this->plugin_root, array( $this, 'deactivate' ) );
	}

	/**
	 * Deactivate the plugin.
	 *
	 * @since   0.1.0
	 */
	public function deactivate() {
		// Set a transient to confirm activation.
		set_transient( $this->plugin_prefix . '_deactivated', true, 10 );
	}
}
