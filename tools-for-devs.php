<?php
/**
 * Plugin Name:       Tools for Developers
 * Plugin URI:        https://racmanuel.dev/plugins/tools-for-developers
 * Description:       A collection of practical generators and utilities for WordPress developers. Generate SQL, REST API code, and database boilerplate safely.
 * Version:           1.0.0
 * Requires at least: 6.9
 * Requires PHP:      7.4
 * Tested up to:      6.9
 * Author:            racmanuel
 * Author URI:        https://racmanuel.dev/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tools-for-devs
 * Domain Path:       /languages
 *
 * @package Tools_For_Devs
 */

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the
 * Plugins admin screen. This file also loads all dependencies, registers
 * activation hooks, and initializes the plugin.
 *
 * @since 1.0.0
 * @link  https://racmanuel.dev/
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TOOLS_FOR_DEVS_VERSION', '1.0.0' );

/**
 * Define the Plugin basename
 */
define( 'TOOLS_FOR_DEVS_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * This action is documented in includes/class-tools-for-devs-activator.php
 * Full security checks are performed inside the class.
 */
function tools_for_devs_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tools-for-devs-activator.php';
	Tools_For_Devs_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * This action is documented in includes/class-tools-for-devs-deactivator.php
 * Full security checks are performed inside the class.
 */
function tools_for_devs_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tools-for-devs-deactivator.php';
	Tools_For_Devs_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'tools_for_devs_activate' );
register_deactivation_hook( __FILE__, 'tools_for_devs_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tools-for-devs.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Generally you will want to hook this function, instead of callign it globally.
 * However since the purpose of your plugin is not known until you write it, we include the function globally.
 *
 * @since    1.0.0
 */
function tools_for_devs_run() {

	$plugin = new Tools_For_Devs();
	$plugin->run();

}
tools_for_devs_run();
