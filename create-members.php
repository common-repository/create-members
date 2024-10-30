<?php
/**
 * Plugin Name:       Create Membership
 * Plugin URI:        https://woooplugin.com/ultimate-membership
 * Description:       Restrict content, manage member subscriptions, Offer discount to Capture and keep users engaged with your business.
 * Version:           1.0.44
 * Requires at least: 5.2
 * Requires PHP:      7.3
 * Author:            Woooplugin
 * Author URI:        https://woooplugin.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       create-members
 * Domain Path:       /languages
 *
 * @package Membership
 * @category Core
 * @author Membership
 * @version 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The Main Plugin Requirements Checker
 *
 * @since 1.0.0
 */
final class CreateMembers {
	private static $instance;

	/**
	 * Current  Version
	 *
	 * @return string
	 */
	public static function get_version() {
		if( ! function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data( __FILE__ );
		return ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
	}

	/**
	 * Singleton Instance
	 *
	 * @return CreateMembers
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Setup Plugin Requirements
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->define_constants();

		// Load modules
		add_action( 'plugins_loaded', array( $this, 'initialize_modules' ), 999 );
	}

	/**
	 * Define Plugin Constants
	 */
	public function define_constants() {
		define( 'SUBSCRIPTION_PRODUCT_TYPE', 'ultimate_member_plan' );
		define( 'MODULES_SAVINGS', 'ultimate_membership_modules' );
		define( 'MEMBER_CANCEL_STATUS', 'cancel' );
		define( 'MEMBERSHIP_MEMBER', 'ultimate_member' );
		define( 'MEMBER_PLAN', 'ultimate_member_plan' );
		define( 'MEM_PLAN_DETAILS', 'member_plan_details' );
		define( 'NEW_MEM_MAIL_SENT', 'ultimate_customer_mail_sent' );
		define( 'UM_ORDERS', 'um_subscription_orders' );
		define( 'SUBSCRIPTION_PAGE', admin_url() . 'admin.php?page=um-plans' );
		define( 'MEMBER_PAGE', admin_url() . 'admin.php?page=um-members' );
	}

	/**
	 * Initialize Modules
	 *
	 * @since 1.1.0
	 */
	public function initialize_modules() {
		do_action( 'create-members/before_load' );
		$this->load_text_domain();
		require_once plugin_dir_path( __FILE__ ) . 'autoloader.php';
		require_once plugin_dir_path( __FILE__ ) . 'wrapper.php';

		// required plugin check
		$this->required_plugin();
		// Load Plugin modules and classes
		\Membership\Wrapper::instance()->init();
		do_action( 'create-members/after_load' );
	}

	/**
	 * Check required plugin and throw notice
	 *
	 * @return void
	 */
	public function required_plugin() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugins = array();

		foreach ( $plugins as $key => $value ) {
			if ( ! is_plugin_active( $value['slug'] ) ) {
				add_action( 'admin_notices', array( $this, 'plugin_notice' ) );
			}
		}
	}

	public function plugin_notice() {
		return esc_html__( 'Active required Plugin', 'create-members' );
	}

	/**
	 * Load Localization Files
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'create-members', false, self::plugin_dir() . 'languages/' );
	}

	/**
	 * Assets Directory Url
	 */
	public static function assets_url() {
		return trailingslashit( self::plugin_url() . 'assets' );
	}

	/**
	 * Build Directory Url
	 */
	public static function build_url() {
		return trailingslashit( self::plugin_url() . 'build' );
	}

	/**
	 * Build Directory Url
	 */
	public static function lib_url() {
		return trailingslashit( self::plugin_url() . 'lib' );
	}

	/**
	 * Assets Folder Directory Path
	 *
	 * @since 1.0.0
	 */
	public static function assets_dir() {
		return trailingslashit( self::plugin_dir() . 'assets' );
	}

	/**
	 * Plugin Core File Directory Url
	 *
	 * @since 1.0.0
	 */
	public static function core_url() {
		return trailingslashit( self::plugin_url() . 'core' );
	}

	/**
	 * Plugin Core File Directory Path
	 *
	 * @since 1.0.0
	 */
	public static function core_dir() {
		return trailingslashit( self::plugin_dir() . 'core' );
	}

	/**
	 * Plugin Template File Directory Path
	 *
	 * @since 1.0.0
	 */
	public static function template_dir() {
		return trailingslashit( self::plugin_dir() . 'core/templates' );
	}

	/**
	 * Plugin Modules File Directory Path
	 */
	public static function modules_dir() {
		return trailingslashit( self::plugin_dir() . 'core/modules' );
	}


	/**
	 * Plugin Url
	 *
	 * @since 1.0.0
	 */
	public static function plugin_url() {
		return trailingslashit( plugin_dir_url( self::plugin_file() ) );
	}

	/**
	 * Plugin Directory Path
	 *
	 * @since 1.0.0
	 */
	public static function plugin_dir() {
		return trailingslashit( plugin_dir_path( self::plugin_file() ) );
	}

	/**
	 * Plugins Basename
	 *
	 * @since 1.0.0
	 */
	public static function plugins_basename() {
		return plugin_basename( self::plugin_file() );
	}

	/**
	 * Plugin File
	 *
	 * @since 1.0.0
	 */
	public static function plugin_file() {
		return __FILE__;
	}

	/**
	 * Assets Folder Directory Path
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function base_dir() {
		return trailingslashit( self::plugin_dir() . 'base' );
	}
}

// Initiate Plugin
CreateMembers::get_instance();
