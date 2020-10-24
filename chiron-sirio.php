<?php
/**
 * Chiron Sirio
 *
 * @link https://github.com/Chiron-AI/Sirio-WP
 *
 * @since 0.0.1
 *
 * @package Chiron_Sirio
 *
 * Plugin Name:  Sirio
 * Plugin URI:   https://github.com/Chiron-AI/Sirio-WP
 * Description:  Sirio wordpress module by Chiron.
 * Version:      0.0.1
 * Contributors: chiron
 * Author:       Chiron <sirio@chiron.ai>
 * Author URI:   https://chiron.ai/sirio
 * License:      GPL-3.0+
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:  chiron-sirio
 * Woo: 6547407:dfd97055a8d3ea627a6fe0afe700324b
 */



/**
 * Abort if this file is called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Constants.
 */
define( 'PLUGIN_NAME_ROOT', __FILE__ );
define( 'PLUGIN_NAME_NAME', 'Chiron Sirio' );
define( 'PLUGIN_NAME_SLUG', 'chiron-sirio' );
define( 'PLUGIN_NAME_PREFIX', 'chiron_sirio' );

/**
 * Classes (Comment out as appropriate).
 */
require_once 'php/class-activator.php';
require_once 'php/class-customizer.php';
require_once 'php/class-deactivator.php';
require_once 'php/class-enqueues.php';
require_once 'php/class-helpers.php';
require_once 'php/class-notices.php';
require_once 'php/class-settings.php';
require_once 'php/class-uninstaller.php';

/**
 * Namespaces (Comment out as appropriate).
 */
use Chiron_Sirio\Activator;
use Chiron_Sirio\Customizer;
use Chiron_Sirio\Deactivator;
use Chiron_Sirio\Enqueues;
use Chiron_Sirio\Helpers;
use Chiron_Sirio\Notices;
use Chiron_Sirio\Settings;
use Chiron_Sirio\Uninstaller;

/**
 * Instances (Comment out as appropriate).
 */
$activator   = new Activator();
$customizer  = new Customizer();
$deactivator = new Deactivator();
$enqueues    = new Enqueues();
$notices     = new Notices();
$settings    = new Settings();
$uninstaller = new Uninstaller();

/**
 * Textdomain.
 *
 * First parameter must be a string, not a constant.
 */
load_plugin_textdomain(
	'chiron-sirio',
	false,
	PLUGIN_NAME_ROOT . '/languages'
);



/**
 * Unleash Hell  (Comment out as appropriate).
 *
 * No need for a main controller; just run all the things.
 */
$activator->run();
//$customizer->run();
//$deactivator->run();
$enqueues->run();
//$notices->run();
$settings->run();
$uninstaller->run();
