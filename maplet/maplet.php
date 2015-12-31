<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://ingmmo.com
 * @since             1.0.0
 * @package           Maplet
 *
 * @wordpress-plugin
 * Plugin Name:       maplet
 * Plugin URI:        http://ingmmo.com/wp/plugins/maplet
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Marco Montanari
 * Author URI:        http://ingmmo.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       maplet
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-maplet-activator.php
 */
function activate_maplet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-maplet-activator.php';
	Maplet_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-maplet-deactivator.php
 */
function deactivate_maplet() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-maplet-deactivator.php';
	Maplet_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_maplet' );
register_deactivation_hook( __FILE__, 'deactivate_maplet' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-maplet.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_maplet() {

	$plugin = new Maplet();
	$plugin->run();

}
run_maplet();
