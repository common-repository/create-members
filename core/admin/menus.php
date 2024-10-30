<?php

/**
 * Menu class
 */
namespace Membership\Core\Admin;

defined( 'ABSPATH' ) || die();

use Membership\Utils\Singleton;

/**
 * Class Menu
 */
class Menus {

	use Singleton;

	private $capability  = 'read';
	private $parent_slug = 'ultimate-membership';
	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'query_vars', array( $this, 'add_query_var' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}

	/**
	 * Add query param
	 *
	 * @param [type] $vars
	 * @return array
	 */
	public function add_query_var( $vars ) {
		$vars[] = 'page';
		$vars[] = 'plan';

		return $vars;
	}

	/**
	 * Register admin menu
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		$capability = $this->capability;

		// Add main page
		if ( empty( $GLOBALS['admin_page_hooks'][ $this->parent_slug ] ) ) {
			$logo = file_get_contents(  \CreateMembers::assets_url() . 'images/logo.svg' );

			add_menu_page(
				esc_html__( 'Membership', 'create-members' ),
				esc_html__( 'Membership', 'create-members' ),
				$capability,
				$this->parent_slug,
				array( $this, 'settings_view' ),
				'data:image/svg+xml;base64,' . base64_encode($logo),
				10
			);
		}

		// Add submenu pages
		if ( count( $this->sub_menu_pages() ) > 0 ) {
			foreach ( $this->sub_menu_pages() as $key => $value ) {
				add_submenu_page(
					$value['parent_slug'],
					$value['page_title'],
					$value['menu_title'],
					$value['capability'],
					$value['menu_slug'],
					$value['cb_function'],
					$value['position']
				);
			}
		}

		if ( ! empty( $GLOBALS['admin_page_hooks'][ $this->parent_slug ] ) ) {
			unset( $GLOBALS['submenu']['ultimate-membership'][0] );
		}
	}

	/**
	 * Create menu page
	 *
	 * @param [type] $cb_function
	 */
	public function sub_menu_pages() {
		$sub_pages = array(
			array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => esc_html__( 'Settings', 'create-members' ),
				'menu_title'  => esc_html__( 'Settings', 'create-members' ),
				'capability'  => $this->capability,
				'menu_slug'   => 'um-settings',
				'cb_function' => array( $this, 'settings_view' ),
				'position'    => 11,
			),
			array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => esc_html__( 'Subscriptions', 'create-members' ),
				'menu_title'  => esc_html__( 'Subscriptions', 'create-members' ),
				'capability'  => $this->capability,
				'menu_slug'   => 'um-plans',
				'cb_function' => array( $this, 'modules_pages' ),
				'position'    => 11,
			),
			array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => esc_html__( 'Members', 'create-members' ),
				'menu_title'  => esc_html__( 'Members', 'create-members' ),
				'capability'  => $this->capability,
				'menu_slug'   => 'um-members',
				'cb_function' => array( $this, 'modules_pages' ),
				'position'    => 11,
			),
			array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => esc_html__( 'Reports', 'create-members' ),
				'menu_title'  => esc_html__( 'Reports', 'create-members' ),
				'capability'  => $this->capability,
				'menu_slug'   => 'um-reports',
				'cb_function' => array( $this, 'modules_pages' ),
				'position'    => 11,
			),
			array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => esc_html__( 'Modules', 'create-members' ),
				'menu_title'  => esc_html__( 'Modules', 'create-members' ),
				'capability'  => $this->capability,
				'menu_slug'   => 'um-modules',
				'cb_function' => array( $this, 'modules_pages' ),
				'position'    => 11,
			),
		);

		if ( ! class_exists( 'CreateMembersPro' ) ) {
			$premium_link = array(
				'parent_slug' => $this->parent_slug,
				'page_title'  => '',
				'menu_title'  => esc_html__( 'Upgrade To Premium', 'create-members' ),
				'capability'  => $this->capability,
				'menu_slug'   => 'https://woooplugin.com/ultimate-membership/',
				'cb_function' => null,
				'position'    => 11,
			);

			array_push( $sub_pages, $premium_link );
		}

		return $sub_pages;
	}

	/**
	 * Modules pages view
	 */
	public function modules_pages() {
		$url_part     = '';
		$current_page = ( isset( $_GET['page'] ) ) ? sanitize_text_field( $_GET['page'] ) : 'um-settings';
		$plan         = ( isset( $_GET['plan'] ) ) ? sanitize_text_field( $_GET['plan'] ) : '';
		if ( file_exists( \CreateMembers::core_dir() . 'admin/header.php' ) ) {
			require_once \CreateMembers::core_dir() . 'admin/header.php';
		}

		?>
		<div class="wrap">
			<?php
			switch ( $current_page ) {
				case 'um-modules':
					$url_part = 'modules.php';
					break;
				case 'um-reports':
					$url_part = 'reports/reports.php';
					break;
				case 'um-members':
					$member = ( isset( $_GET['member'] ) ) ? sanitize_text_field( $_GET['member'] ) : '';
					if ( $member == 'new-member' || $member == 'update_member' ) {
						$url_part = 'members/views/add-member.php';
					} else {
						$url_part = 'members/views/members.php';
					}
					break;
				case 'um-plans':
					$plan = ( isset( $_GET['plan'] ) ) ? sanitize_text_field( $_GET['plan'] ) : '';
					if ( $plan == 'new-plan' || $plan == 'update_plan' ) {
						$url_part = 'plans/add-plan.php';
					} else {
						$url_part = 'plans/plans.php';
					}
					break;
				default:
					break;
			}

			if ( file_exists( \CreateMembers::modules_dir() . $url_part ) ) {
				include_once \CreateMembers::modules_dir() . $url_part;
			}
			?>
		</div>
		<?php
	}

	/**
	 * Admin view
	 */
	public function settings_view() {
		if ( file_exists( \CreateMembers::core_dir() . 'admin/header.php' ) ) {
			require_once \CreateMembers::core_dir() . 'admin/header.php';
		}
		?>
		<div class="wrap">
			<?php
			if ( file_exists( \CreateMembers::core_dir() . 'admin/settings.php' ) ) {
				include_once \CreateMembers::core_dir() . 'admin/settings.php';
			}
			?>
		</div>
		<?php
	}
}
