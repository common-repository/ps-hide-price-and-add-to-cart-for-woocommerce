<?php
/**
 * Plugin settings Class
 *
 * @link       https://www.storeprose.com
 * @since      1.0.0
 *
 * @package    Ps_Hpacb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * PS_HPACB_Settings
 */
class PS_HPACB_Settings {
	/**
	 * Dir
	 *
	 * @var mixed
	 */
	private $dir;
	/**
	 * File
	 *
	 * @var mixed
	 */
	private $file;
	/**
	 * Plugin_name
	 *
	 * @var mixed
	 */
	private $plugin_name;
	/**
	 * Plugin_slug
	 *
	 * @var mixed
	 */
	private $plugin_slug;
	/**
	 * Textdomain
	 *
	 * @var mixed
	 */
	private $textdomain;
	/**
	 * Options
	 *
	 * @var mixed
	 */
	private $options;
	/**
	 * Settings
	 *
	 * @var mixed
	 */
	private $settings;

	/**
	 * Method __construct
	 *
	 * @param $plugin_name $plugin_name passed as parameter.
	 * @param $plugin_slug $plugin_slug passed as parameter.
	 * @param file        $file passed as parameter.
	 *
	 * @return void
	 */
	public function __construct( $plugin_name, $plugin_slug, $file ) {
		$this->file        = $file;
		$this->plugin_slug = $plugin_slug;
		$this->plugin_name = $plugin_name;
		$this->textdomain  = str_replace( '_', '-', $plugin_slug );

		// Initialise settings.
		add_action( 'admin_init', array( $this, 'init' ) );

		// Add settings page to menu.
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		// Add settings link to plugins page.
		$plugin_link_name = 'plugin_action_links_ps-hide-price-and-add-to-cart-for-woocommerce/ps-hpacb.php';
		add_filter( $plugin_link_name, array( $this, 'add_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'ps_hpacb_add_plugin_description' ), 10, 2 );
	}

	/**
	 * Initialise settings
	 *
	 * @return void
	 */
	public function init() {
		$this->settings = $this->settings_fields();
		$this->options  = $this->get_options();
		$this->register_settings();
	}

	/**
	 * Add settings page to admin menu
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$page = add_submenu_page( 'woocommerce', $this->plugin_name, $this->plugin_name, 'manage_options', $this->plugin_slug, array( $this, 'settings_page' ) );
	}

	/**
	 * Method ps_hpacb_add_plugin_description
	 *
	 * @param $links $links text.
	 * @param file  $file text.
	 *
	 * @return array
	 */
	public function ps_hpacb_add_plugin_description( $links, $file ) {

		if ( strpos( $file, 'ps-hpacb.php' ) !== false ) {
			$review_link   = '<a href="https://wordpress.org/support/plugin/ps-hide-price-and-add-to-cart-for-woocommerce/reviews/#new-post" target="_blank"><span class="dashicons dashicons-welcome-write-blog"></span>Write a Review</a>';
			$donation_link = '<a href="' . esc_url( 'https://ko-fi.com/storeprose' ) . '" style="color:#e76f51;font-weight:bold" target="_blank"><span class="dashicons dashicons-heart"></span>' . __( 'Donate', 'ps-hide-price-and-add-to-cart-for-woocommerce' ) . '</a>';
			array_push( $links, $review_link );
			array_push( $links, $donation_link );
		}

		return $links;
	}

	/**
	 * Add settings link to plugin list table
	 *
	 * @param  array $links Existing links.
	 * @return array        Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=' . $this->plugin_slug . '">' . __( 'Settings', 'ps-hide-price-and-add-to-cart-for-woocommerce' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields
	 *
	 * @return array Fields to be displayed on settings page.
	 */
	private function settings_fields() {

		$settings['general'] = array(
			'title'       => __( 'General', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'description' => __( 'Global settings that impact the plugin functionality.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'fields'      => array(
				array(
					'id'          => 'product_selection',
					'label'       => __( 'Allow product override', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Selecting this checkbox will allow selective product exclusion.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				),
			),
		);

		$settings['price'] = array(
			'title'       => __( 'Price Options', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'description' => __( 'Configure the options to hide product prices.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'fields'      => array(
				array(
					'id'          => 'guest_hp',
					'label'       => __( 'Hide for Guest Users', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Select this checkbox to hide product prices for users who are not logged in.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				),
				array(
					'id'          => 'users_hp',
					'label'       => __( 'Hide for Registered Users', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Select this checkbox to hide product prices for users who are logged in.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
				array(
					'id'          => 'hide_sale',
					'label'       => __( 'Hide Sale Badge', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Select this checkbox to hide the sale badge if price is not displayed.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				),
				array(
					'id'          => 'price_text',
					'label'       => __( 'Replacement text', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Replace the price with any custom text. Leave it blank to show no text.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'text',
					'default'     => '',
				),

			),
		);

		$settings['buy_button'] = array(
			'title'       => __( 'Add to Cart Options', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'description' => __( 'Configure the options to hide the Add to cart button.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'fields'      => array(
				array(
					'id'          => 'guest_hb',
					'label'       => __( 'Hide for Guest Users', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Select this checkbox to hide the Add to Cart button for users who are not logged in.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				),
				array(
					'id'          => 'users_hb',
					'label'       => __( 'Hide for Registered Users', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Select this checkbox to hide the Add to Cart button for users who are logged in.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => '',
				),
			),
		);

		$settings['advanced'] = array(
			'title'       => __( 'Advanced', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'description' => __( 'Please modify these options only if you know what your are doing. If in doubt, please get in touch with our support.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
			'fields'      => array(
				array(
					'id'          => 'price_css',
					'label'       => __( 'Price CSS', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Use the developer tools of your browser to find the conflicting CSS selectors.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'          => 'btn_css',
					'label'       => __( 'Add to Cart Button CSS', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'Use the developer tools of your browser to find the conflicting CSS selectors.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'          => 'delete_options',
					'label'       => __( 'Delete all plugin data when uninstalled.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'description' => __( 'If you select this checkbox the plugin settings will be removed during uninstallation.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => 'on',
				),
			),
		);

		$settings = apply_filters( 'plugin_settings_fields', $settings );

		return $settings;
	}


	/**
	 * Options getter
	 *
	 * @return array Options, either saved or default ones.
	 */
	public function get_options() {
		$options = get_option( $this->plugin_slug );

		if ( ! $options && is_array( $this->settings ) ) {
			$options = array();
			foreach ( $this->settings as $section => $data ) {
				foreach ( $data['fields'] as $field ) {
					$options[ $field['id'] ] = $field['default'];
				}
			}

			add_option( $this->plugin_slug, $options );
		}

		return $options;
	}

	/**
	 * Register plugin settings
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( is_array( $this->settings ) ) {

			register_setting( $this->plugin_slug, $this->plugin_slug, array( $this, 'validate_fields' ) );

			foreach ( $this->settings as $section => $data ) {

				// Add section to page.
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->plugin_slug );

				foreach ( $data['fields'] as $field ) {

					// Add field to page.
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), $this->plugin_slug, $section, array( 'field' => $field ) );
				}
			}
		}
	}

	/**
	 * Method settings_section
	 *
	 * @param $section $section render sections.
	 *
	 * @return void
	 */
	public function settings_section( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo wp_kses_post( $html );
	}

	/**
	 * Method safe_tags
	 *
	 * @return array.
	 */
	public function safe_tags() {

		$allowed_tags = array(
			'input' => array(
				'id'          => array(),
				'type'        => array(),
				'name'        => array(),
				'placeholder' => array(),
				'value'       => array(),
				'checked'     => array(),
			),
			'label' => array(
				'class' => array(),
			),
			'span'  => array(
				'class' => array(),
			),

		);

		return $allowed_tags;
	}

	/**
	 * Generate HTML for displaying fields
	 *
	 * @param  array $args Field data.
	 * @return void
	 */
	public function display_field( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->plugin_slug . '[' . $field['id'] . ']';

		$data = ( isset( $this->options[ $field['id'] ] ) ) ? $this->options[ $field['id'] ] : '';

		switch ( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['default'] ) . '" value="' . $data . '"/>' . "\n";
				break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' === $data ) {
					$checked = 'checked="checked"';
				}

				$html .= '<label class="switch"><input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/><span class="slider round"></span></label>' . "\n";

				// $html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( is_array( $data ) && in_array( $k, $data, true ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k === $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k === $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, $data, true ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
				break;

		}
		$allowed_html = $this->safe_tags();

		echo wp_kses( $html, $allowed_html );
	}


	/**
	 * Method sanitize_checkbox
	 *
	 * @param $input $input input.
	 *
	 * @return number
	 */
	public function sanitize_checkbox( $input ) {
		return ( 'on' === $input ) ? 'on' : '';
	}
	/**
	 * Validate individual settings field
	 *
	 * @param  array $data Inputted value.
	 * @return array       Validated value.
	 */
	public function validate_fields( $data ) {

		if ( isset( $data['guest_hp'] ) ) {
			$data['guest_hp'] = $this->sanitize_checkbox( $data['guest_hp'] );
		}

		if ( isset( $data['guest_hb'] ) ) {
			$data['guest_hb'] = $this->sanitize_checkbox( $data['guest_hb'] );
		}

		if ( isset( $data['users_hp'] ) ) {
			$data['users_hp'] = $this->sanitize_checkbox( $data['users_hp'] );
		}

		if ( isset( $data['users_hb'] ) ) {
			$data['users_hb'] = $this->sanitize_checkbox( $data['users_hb'] );
		}

		if ( isset( $data['css_selectors'] ) ) {
			$data['css_selectors'] = sanitize_text_field( $data['css_selectors'] );
		}

		if ( isset( $data['price_text'] ) ) {
			$data['price_text'] = sanitize_text_field( $data['price_text'] );
			$data['price_text'] = esc_html( $data['price_text'] );
		}

		return $data;
	}

	/**
	 * Load settings page content
	 *
	 * @return void
	 */
	public function settings_page() {
		// Build page HTML output
		// If you don't need tabbed navigation just strip out everything between the <!-- Tab navigation --> tags.
		?>
		<div class="wrap" id="<?php echo wp_kses_post( $this->plugin_slug ); ?>">
			<h2><?php esc_html_e( 'Catalog Mode for WooCommerce', 'ps-hide-price-and-add-to-cart-for-woocommerce' ); ?></h2>
			<p><?php esc_html_e( 'Configure the plugin functionality using these options.', 'ps-hide-price-and-add-to-cart-for-woocommerce' ); ?></p>

		<!-- Tab navigation starts -->
		<h2 class="nav-tab-wrapper settings-tabs hide-if-no-js">
			<?php
			foreach ( $this->settings as $section => $data ) {
				echo wp_kses_post( '<a href="#' . $section . '" class="nav-tab">' . $data['title'] . '</a>' );
			}
			?>
		</h2>
		<?php $this->do_script_for_tabbed_nav(); ?>
		<!-- Tab navigation ends -->

		<form action="options.php" method="POST">
			<?php settings_fields( $this->plugin_slug ); ?>
			<div class="settings-container">
			<?php do_settings_sections( $this->plugin_slug ); ?>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>
		<?php
	}

	/**
	 * Print jQuery script for tabbed navigation
	 *
	 * @return void
	 */
	private function do_script_for_tabbed_nav() {
		// Very simple jQuery logic for the tabbed navigation.
		// Delete this function if you don't need it.
		// If you have other JS assets you may merge this there.
		?>
		<script>
		jQuery(document).ready(function($) {
			var headings = jQuery('.settings-container > h2, .settings-container > h3');
			var paragraphs  = jQuery('.settings-container > p');
			var tables = jQuery('.settings-container > table');
			var triggers = jQuery('.settings-tabs a');

			triggers.each(function(i){
				triggers.eq(i).on('click', function(e){
					e.preventDefault();
					triggers.removeClass('nav-tab-active');
					headings.hide();
					paragraphs.hide();
					tables.hide();

					triggers.eq(i).addClass('nav-tab-active');
					headings.eq(i).show();
					paragraphs.eq(i).show();
					tables.eq(i).show();
				});
			})

			triggers.eq(0).click();
		});
		</script>
		<?php
	}
}
