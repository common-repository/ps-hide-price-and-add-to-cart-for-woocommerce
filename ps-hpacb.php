<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.storeprose.com
 * @since             1.0.0
 * @package           Ps_Hpacb
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Catalog Mode
 * Plugin URI:        https://www.storeprose.com
 * Description:       WooCommerce Catalog Mode is the most efficient WooCommerce plugin to transform your shop into an online catalog by hiding the price and disabling sales.
 * Version:           1.3.7
 * Author:            Store Prose
 * Author URI:        https://www.storeprose.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ps-hide-price-and-add-to-cart-for-woocommerce
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
define( 'PS_HPACB_VERSION', '1.3.7' );




/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ps-hpacb-activator.php
 */
function activate_ps_hpacb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ps-hpacb-activator.php';

		Ps_Hpacb_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ps-hpacb-deactivator.php
 */
function deactivate_ps_hpacb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ps-hpacb-deactivator.php';
	Ps_Hpacb_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ps_hpacb' );
register_deactivation_hook( __FILE__, 'deactivate_ps_hpacb' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ps-hpacb.php';

require plugin_dir_path( __FILE__ ) . 'admin/class-ps-hpacb-review.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ps_hpacb() {

	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		$plugin = new Ps_Hpacb();
		$review = new Ps_Hpacb_Review();
		$plugin->run();
	}
}

run_ps_hpacb();
