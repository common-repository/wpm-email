<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              hqcservices.org
 * @since             1.0.0
 * @package           WPM_Email
 *
 * @wordpress-plugin
 * Plugin Name:       WPM-Email
 * Plugin URI:        hqcservices.org/plugins
 * Description:       Addition to WP-Members plugin that sends an email to the Membership Administrator when members change specific fields of their profile.
 * Version:           1.0.0
 * Author:            Giles Wheatley
 * Author URI:        hqcservices.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpm-email
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPN_MAIL_PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpm-email-activator.php
 */
function activate_wpm_email() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpm-email-activator.php';
	WPM_Email_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpm-email-deactivator.php
 */
function deactivate_wpm_email() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpm-email-deactivator.php';
	WPM_Email_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpm_email' );
register_deactivation_hook( __FILE__, 'deactivate_wpm_email' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpm-email.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpm_email() {
	$plugin = new WPM_Email();
	$plugin->run();

}
run_wpm_email();
