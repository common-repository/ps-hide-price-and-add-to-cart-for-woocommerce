<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.storeprose.com
 * @since      1.0.0
 *
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/includes
 * @author     Store Prose <hello@pluginstory.com>
 */
class Ps_Hpacb_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ps-hide-price-and-add-to-cart-for-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
