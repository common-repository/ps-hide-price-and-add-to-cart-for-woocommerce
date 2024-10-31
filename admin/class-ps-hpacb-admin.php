<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.storeprose.com
 * @since      1.0.0
 *
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/admin
 * @author     Store Prose <hello@pluginstory.com>
 */
class Ps_Hpacb_Admin {

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
	 * The options name to be used in this plugin
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string      $option_name    Option name of this plugin
	 */
	private $option_name = 'ps_hpacb_settings';



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Method ps_hpacb_add_columns
	 *
	 * @return void
	 */
	public function ps_hpacb_add_columns() {
		add_filter( 'manage_edit-product_columns', array( $this, 'ps_hpacb_product_columns' ), 9999 );
		add_action( 'manage_product_posts_custom_column', array( $this, 'ps_hpacb_product_columns_value' ), 9999, 2 );
	}

	/**
	 * Method ps_hpacb_product_columns
	 *
	 * @param $columns $columns columns.
	 *
	 * @return array
	 */
	public function ps_hpacb_product_columns( $columns ) {
		unset( $columns['date'] );
		unset( $columns['product_tag'] );
		$columns['ps_hpacb_price'] = 'Hide Price';
		$columns['ps_hpacb_btn']   = 'Hide Buy Button';
		return $columns;
	}

	/**
	 * Method ps_hpacb_product_columns_value
	 *
	 * @param column     $column column.
	 * @param $product_id $product_id product id.
	 *
	 * @return void
	 */
	public function ps_hpacb_product_columns_value( $column, $product_id ) {
		$price_column   = 'ps_hpacb_price';
		$buy_btn_column = 'ps_hpacb_btn';

		if ( $column === $price_column || $column === $buy_btn_column ) {
			$checked_value  = 'on';
			$term_type      = 'Product';
			$style          = 'cursor:pointer;';
			$class          = 'ps_hpacb dashicons ';
			$product        = wc_get_product( $product_id );
			$product_meta   = $product->get_meta( '_' . $column );
			$product_option = ( $product_meta === $checked_value ) ? 'off' : 'on';

			$title  = ( $product_option === $checked_value ) ? 'Hidden' : 'Visible';
			$class .= ( $product_option === $checked_value ) ? 'dashicons-yes-alt' : 'dashicons-no-alt';
			$style .= ( $product_option === $checked_value ) ? 'color:green;' : 'color:red;';
			echo '<a class="' . esc_html( $class ) . '" style="' . esc_html( $style ) . '" ps_hpacb_term="' . esc_html( $term_type ) . '" title ="' . esc_html( $title ) . '" id="' . esc_html( $column ) . esc_html( $product_id ) . '"ps_hpacb_term_id="' . esc_html( $product_id ) . '" ps_hpacb_term_option="' . esc_html( $column ) . '"/>';

		}
	}

	/**
	 * Method ps_hpacb_admin_init
	 *
	 * @return void
	 */
	public function ps_hpacb_admin_init() {
		$options       = get_option( 'ps_hpacb' );
		$checked_value = 'on';
		if ( isset( $options['product_selection'] ) && $options['product_selection'] === $checked_value ) {
			$this->ps_hpacb_add_columns();
			add_action( 'wp_ajax_ps_hpacb_update_meta', array( $this, 'ps_hpacb_update_meta' ) );
			add_action( 'wp_ajax_nopriv_ps_hpacb_update_meta', array( $this, 'ps_hpacb_update_meta' ) );
		}
		$this->migrate_1_3_0_options();
	}



	/**
	 * Method migrate_1_3_0_options
	 *
	 * @return void
	 */
	public function migrate_1_3_0_options() {
		$option_names = array( 'ps_hpacb_price', 'ps_hpacb_btn' );
		foreach ( $option_names as $option ) {
			$option_name = $option . '_prod';
			$products    = get_option( $option_name );
			if ( $products ) {
				foreach ( $products as $product_id ) {
					$id_ex   = explode( $option, $product_id );
					$id      = $id_ex[1];
					$product = wc_get_product( $id );
					$product->update_meta_data( '_' . $option, 'on' );
					$product->save_meta_data();
				}
				delete_option( $option_name );
			}
		}
	}

	/**
	 * Method enqueue_scripts
	 *
	 * @return void
	 */
	public function ps_hpacb_enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ps-hpacb-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ps-hpacb.css', array(), $this->version, 'all' );
		wp_localize_script(
			$this->plugin_name,
			'ps_hpacb_ajax',
			array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ps_hpacb_ajax' ),
			)
		);
	}

	/**
	 * Method ps_hpacb_toggle
	 *
	 * @return void
	 */
	public function ps_hpacb_update_meta() {
		header( 'Access-Control-Allow-Origin: *' );

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'ps_hpacb_ajax' ) ) {

			$term_id     = isset( $_POST['term_id'] ) ? sanitize_text_field( wp_unslash( $_POST['term_id'] ) ) : 'X';
			$term_type   = isset( $_POST['term_type'] ) ? sanitize_text_field( wp_unslash( $_POST['term_type'] ) ) : 'X';
			$visibility  = isset( $_POST['visibility'] ) ? sanitize_text_field( wp_unslash( $_POST['visibility'] ) ) : 'X';
			$term_option = isset( $_POST['term_option'] ) ? sanitize_text_field( wp_unslash( $_POST['term_option'] ) ) : 'X';
			$object_id   = isset( $_POST['object_id'] ) ? sanitize_text_field( wp_unslash( $_POST['object_id'] ) ) : 'X';

			$term_type_product = 'Product';
			$term_type_hidden  = 'Hidden';
			$respond           = false;
			$user_choice       = ( $visibility === $term_type_hidden ) ? 'on' : 'off';

			$response = array(
				'id'        => esc_html( $term_id ),
				'do_action' => ( $visibility === $term_type_hidden ? 'Show' : 'Hide' ),
			);

			if ( $term_type === $term_type_product ) {
				$product = wc_get_product( $object_id );
				$product->update_meta_data( '_' . $term_option, $user_choice );
				$product->save_meta_data();
				$respond = true;
			}
			if ( $respond ) {
				wp_send_json( $response );
			}
		}
		die();
	}
}
