<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress or ClassicPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://racmanuel.dev/
 * @since             1.0.0
 * @package           Tools_For_Devs
 *
 * @wordpress-plugin
 * Plugin Name:       Tools for Developers
 * Plugin URI:        https://plugin.com/tools-for-devs-uri/
 * Description:       Make a shortcodes with tools for wordpress developers
 * Version:           1.0.0
 * Author:            racmanuel
 * Requires at least: 6.9
 * Requires PHP:      7.4
 * Tested up to:      6.9
 * Author URI:        https://racmanuel.dev//
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tools-for-devs
 * Domain Path:       /languages
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
