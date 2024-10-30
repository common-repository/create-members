<?php

namespace Membership;

use CreateMembers;

class Wrapper {

	private static $instance;

	/**
	 * __construct function
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Load autoload method.
		Autoloader::run();
		// pro & others menu
		add_filter( 'plugin_action_links_' . CreateMembers::plugins_basename(), array( $this, 'add_action_links' ) );

		// Core files
		\Membership\Core\Core::instance()->init();
		// Enqueue Assets
		\Membership\Base\Enqueue::instance()->init();
	}

		/**
		 * Add required links
		 *
		 * @param [type] $actions
		 * @return array
		 */
	public function add_action_links( $actions ) {
		$this->custom_css();
		$actions[] = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=um-settings' ) ) . '">' .
		esc_html__( 'Settings', 'create-members' ) . '</a>';
		if ( ! class_exists( 'CreateMembersPro' ) ) {
			$actions[] = '<a href="https://woooplugin.com/ultimate-membership/" class="membership-go-pro" target="_blank">' . esc_html__( 'Go To Premium', 'filter-plus' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Custom css
	 *
	 * @param string $template
	 * @return void
	 */
	public function custom_css() {
		global $custom_css;
			$custom_css = '
				.membership-go-pro {
					color: #086808;
					font-weight: bold;
				}
			';

		wp_register_style( 'membership-go-pro', false );
		wp_enqueue_style( 'membership-go-pro' );
		wp_add_inline_style( 'membership-go-pro', $custom_css );
	}

	/**
	 * Load License module
	 */
	public function init() {
		// load license
	}


	/**
	 * Singleton Instance
	 *
	 * @return Wrapper
	 */
	public static function instance() {

		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
