<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.storeprose.com
 * @since      1.0.0
 *
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ps_Hpacb
 * @subpackage Ps_Hpacb/includes
 * @author     Store Prose <hello@pluginstory.com>
 */
class Ps_Hpacb {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ps_Hpacb_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The settings that's responsible for rendering the settings page of
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ps_Hpacb_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $settings;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PS_HPACB_VERSION' ) ) {
			$this->version = PS_HPACB_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ps-hpacb';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ps_Hpacb_Loader. Orchestrates the hooks of the plugin.
	 * - Ps_Hpacb_i18n. Defines internationalization functionality.
	 * - Ps_Hpacb_Admin. Defines all hooks for the admin area.
	 * - Ps_Hpacb_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ps-hpacb-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ps-hpacb-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-ps-hpacb-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-ps-hpacb-public.php';

		/**
		 * The class responsible for defining the plugin settings
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-ps-hpacb-settings.php';

		$this->loader   = new Ps_Hpacb_Loader();
		$this->settings = new Ps_Hpacb_Settings( 'Catalog Mode', 'ps_hpacb', __FILE__ );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ps_Hpacb_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ps_Hpacb_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Ps_Hpacb_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'ps_hpacb_admin_init' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'ps_hpacb_enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Ps_Hpacb_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_public, 'ps_hpacb_public_init' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ps_Hpacb_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
