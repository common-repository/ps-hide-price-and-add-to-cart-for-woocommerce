<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.storeprose.com
 * @since      1.0.0
 *
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/public
 * @author     Store Prose <hello@pluginstory.com>
 */
class Ps_Hpacb_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Checked_value
	 *
	 * @var string
	 */
	private $checked_value = 'on';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}


	/**
	 * Method ps_hpacb_hide_price_css
	 *
	 * @return void
	 */
	public function ps_hpacb_hide_price_css() {
		$options = get_option( 'ps_hpacb' );
		if ( isset( $options['price_css'] ) && strlen( $options['price_css'] ) !== 0 ) {
			$css_selectors = $options['price_css'];
			echo '<style>' . esc_html( $css_selectors ) . ' {display: none !important;}</style>';
		}
	}

	/**
	 * Method ps_hpacb_hide_btn_css
	 *
	 * @return void
	 */
	public function ps_hpacb_hide_btn_css() {
		$options = get_option( 'ps_hpacb' );
		if ( isset( $options['btn_css'] ) && strlen( $options['btn_css'] ) !== 0 ) {
			$css_selectors = $options['btn_css'];
			echo '<style>' . esc_html( $css_selectors ) . ' {display: none !important;}</style>';
		}
	}


	/**
	 * Method ps_hpacb_is_purchaseable
	 *
	 * @param $is_purchasable $is_purchasable boolean.
	 * @param product        $product product.
	 *
	 * @return bool
	 */
	public function ps_hpacb_is_purchaseable( $is_purchasable, $product ) {
		$product_excluded = $this->is_product_excluded( $product, 'B' );
		if ( $product_excluded ) {
			return $is_purchasable;
		}
		return false;
	}
	/**
	 * Method ps_hpacb_hide_buy_button
	 * Does the magic to hide buy button.
	 *
	 * @return void
	 */
	public function ps_hpacb_hide_buy_button() {
		$options = get_option( 'ps_hpacb' );

		$hide_cart_btn_for_users  = ( isset( $options['users_hb'] ) && $options['users_hb'] === $this->checked_value ) ? true : false;
		$hide_cart_btn_for_guests = ( isset( $options['guest_hb'] ) && $options['guest_hb'] === $this->checked_value ) ? true : false;

		$is_user_logged_in = is_user_logged_in();
		if ( ( $hide_cart_btn_for_guests && ! $is_user_logged_in ) || ( $hide_cart_btn_for_users && $is_user_logged_in ) ) {
			add_filter( 'woocommerce_is_purchasable', array( $this, 'ps_hpacb_is_purchaseable' ), 10, 2 );

			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'ps_hpacb_remove_btn_main' ), 25, 2 );
			add_action( 'wp', array( $this, 'ps_hpacb_remove_btn_hook' ) );
			add_action( 'wp_print_scripts', array( $this, 'ps_hpacb_hide_btn_css' ) );
		}
	}

	/**
	 * Method ps_hpacb_remove_btn_hook
	 *
	 * @return void
	 */
	public function ps_hpacb_remove_btn_hook() {
		if ( ! is_product() ) {
			return;
		}
		$product          = wc_get_product( get_queried_object_id() );
		$product_excluded = $this->is_product_excluded( $product, 'B' );
		if ( ! $product_excluded ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
	}

	/**
	 * Method ps_hpacb_remove_btn_main
	 *
	 * @param $add_to_cart_html $add_to_cart_html button.
	 * @param product          $product product.
	 *
	 * @return text
	 */
	public function ps_hpacb_remove_btn_main( $add_to_cart_html, $product ) {
		$product_excluded = $this->is_product_excluded( $product, 'B' );
		if ( $product_excluded ) {
			return $add_to_cart_html;
		}
		return '';
	}

	/**
	 * Method is_product_excluded
	 *
	 * @param product   $product product.
	 * @param term_type $term_type type.
	 *
	 * @return bool
	 */
	public function is_product_excluded( $product, $term_type ) {
		$options = get_option( 'ps_hpacb' );
		if ( ! isset( $options['product_selection'] ) || $options['product_selection'] !== $this->checked_value ) {
			return false;
		}

		$term_type_price  = 'P';
		$meta_name        = ( $term_type === $term_type_price ) ? 'ps_hpacb_price' : 'ps_hpacb_btn';
		$product_excluded = false;

		$product_meta = $product->get_meta( '_' . $meta_name );
		if ( $product_meta && $product_meta === $this->checked_value ) {
			$product_excluded = true;
		}

		return $product_excluded;
	}

	/**
	 * Method ps_hpacb_hide_sale
	 *
	 * @param $on_sale $on_sale boolean.
	 * @param $product $product product.
	 *
	 * @return bool
	 */
	public function ps_hpacb_hide_sale( $on_sale, $product ) {
		$product_excluded = $this->is_product_excluded( $product, 'P' );
		if ( $product_excluded ) {
			return $on_sale;
		}
		return false;
	}

	/**
	 * Method ps_hpacb_remove_price
	 *
	 * @param price   $price text.
	 * @param $product $product product.
	 *
	 * @return text
	 */
	public function ps_hpacb_remove_price( $price, $product ) {
		$product_excluded = $this->is_product_excluded( $product, 'P' );
		if ( $product_excluded ) {
			return $price;
		}

		$options    = get_option( 'ps_hpacb' );
		$price_text = ( isset( $options['price_text'] ) ) ? $options['price_text'] : '';
		return $price_text;
	}

	/**
	 * Method ps_hpacb_hide_price
	 * Does the magic to hide price.
	 *
	 * @return void
	 */
	public function ps_hpacb_hide_price() {
		$options = get_option( 'ps_hpacb' );

		$hide_price_for_guests = ( isset( $options['guest_hp'] ) && $options['guest_hp'] === $this->checked_value ) ? true : false;
		$hide_price_for_users  = ( isset( $options['users_hp'] ) && $options['users_hp'] === $this->checked_value ) ? true : false;
		$is_user_logged_in     = is_user_logged_in();
		$hide_sale             = ( isset( $options['hide_sale'] ) && $options['hide_sale'] === $this->checked_value ) ? true : false;

		if ( ( $hide_price_for_guests && ! $is_user_logged_in ) || ( $hide_price_for_users && $is_user_logged_in ) ) {
			add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'ps_hpacb_remove_price' ), 9999, 2 );
			add_filter( 'woocommerce_variable_price_html', array( $this, 'ps_hpacb_remove_price' ), 9999, 2 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'ps_hpacb_remove_price' ), 9999, 2 );
			add_action( 'wp_print_scripts', array( $this, 'ps_hpacb_hide_price_css' ) );
			if ( $hide_sale ) {
				add_filter( 'woocommerce_product_is_on_sale', array( $this, 'ps_hpacb_hide_sale' ), 9999, 2 );
			}
		}
	}

	/**
	 * Method ps_hpacb_public_init
	 *
	 * @return void
	 */
	public function ps_hpacb_public_init() {
		$this->ps_hpacb_hide_price();
		$this->ps_hpacb_hide_buy_button();
	}
}
