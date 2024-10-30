<?php

namespace Membership\Base;

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Helper;
use Membership\Utils\Singleton;
use Membership\Core\Models\Plans as PlansModel;

/**
 * Enqueue all css and js file class
 */
class Enqueue {

	use Singleton;


	/**
	 * Main calling function
	 */
	public function init() {
		// backend asset
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_assets' ) );
		// frontend asset
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_assets' ) );
	}


	/**
	 * all admin js files function
	 */
	public function admin_get_scripts() {
		$script_arr = array(
			'um-chart'                => array(
				'src'     => \CreateMembers::assets_url() . 'js/um-chart.js',
				'version' => \CreateMembers::get_version(),
				'deps'    => array( 'jquery' ),
			),
			'um-admin-js'             => array(
				'src'     => \CreateMembers::assets_url() . 'js/admin.js',
				'version' => \CreateMembers::get_version(),
				'deps'    => array( 'jquery', 'ultimate-member-select2', 'um-flat-picker', 'um-chart' ),
			),
			'ultimate-member-select2' => array(
				'src'     => \CreateMembers::assets_url() . 'js/select2.js',
				'version' => \CreateMembers::get_version(),
				'deps'    => array( 'jquery' ),
			),
			'um-flat-picker'          => array(
				'src'     => \CreateMembers::assets_url() . 'js/um-flatpickr.min.js',
				'version' => \CreateMembers::get_version(),
				'deps'    => array( 'jquery' ),
			),
		);

		return $script_arr;
	}

	/**
	 * all admin css files function
	 *
	 * @param array
	 */
	public function admin_get_styles() {
		return array(
			'ultimate-member-admin'   => array(
				'src'     => \CreateMembers::assets_url() . 'css/admin.css',
				'version' => \CreateMembers::get_version(),
			),
			'ultimate-member-select2' => array(
				'src'     => \CreateMembers::assets_url() . 'css/select2.css',
				'version' => \CreateMembers::get_version(),
			),
			'ultimate-flatpickr'      => array(
				'src'     => \CreateMembers::assets_url() . 'css/member-flatpickr.min.css',
				'version' => \CreateMembers::get_version(),
			),
		);
	}

	/**
	 * Enqueue admin js and css function
	 *
	 * @param  $var
	 */
	public function admin_enqueue_assets() {
		$screen = get_current_screen();
		$pages  = Helper::admin_unique_id();
	
		// load js in specific pages
		if ( is_admin() && ( in_array( $screen->id, $pages ) ) ) {

			foreach ( $this->admin_get_scripts() as $key => $value ) {
				$deps    = ! empty( $value['deps'] ) ? $value['deps'] : false;
				$version = ! empty( $value['version'] ) ? $value['version'] : false;
				wp_enqueue_script( $key, $value['src'], $deps, $version, true );
			}

			// css

			foreach ( $this->admin_get_styles() as $key => $value ) {
				$deps    = isset( $value['deps'] ) ? $value['deps'] : false;
				$version = ! empty( $value['version'] ) ? $value['version'] : false;
				wp_enqueue_style( $key, $value['src'], $deps, $version, 'all' );
			}

			extract( Helper::instance()->get_settings() );
			// localize for admin
			$form_data                      = array();
			$form_data['ajax_url']          = admin_url( 'admin-ajax.php' );
			$form_data['currency']          = class_exists( 'WooCommerce' ) ? get_woocommerce_currency_symbol() : '';
			$form_data['is_pro_active']     = class_exists( 'CreateMembersPro' ) ? true : false;
			$form_data['plan_cost']         = $plan_cost;
			$form_data['ult_mem_nonce']     = wp_create_nonce( 'ult_mem_nonce' );
			$form_data['subscription_page'] = SUBSCRIPTION_PAGE;
			$form_data['member_page']       = MEMBER_PAGE;
			wp_localize_script( 'um-admin-js', 'membership_admin', $form_data );
		}
	}



	/**
	 * all js files function
	 */
	public function frontend_get_scripts() {

		return array();
	}

	/**
	 * all css files function
	 */
	public function frontend_get_styles() {
		$enqueue = array(
			'ultimate-member-public' => array(
				'src'     => \CreateMembers::assets_url() . 'css/public.css',
				'version' => \CreateMembers::get_version(),
			),
		);

		return $enqueue;
	}

	/**
	 * Enqueue admin js and css function
	 */
	public function frontend_enqueue_assets() {
		// js
		$scripts = $this->frontend_get_scripts();

		foreach ( $scripts as $key => $value ) {
			$deps    = isset( $value['deps'] ) ? $value['deps'] : false;
			$version = ! empty( $value['version'] ) ? $value['version'] : false;
			wp_enqueue_script( $key, $value['src'], $deps, $version, true );
		}

		// css
		$styles = $this->frontend_get_styles();

		foreach ( $styles as $key => $value ) {
			$deps    = isset( $value['deps'] ) ? $value['deps'] : false;
			$version = ! empty( $value['version'] ) ? $value['version'] : false;
			wp_enqueue_style( $key, $value['src'], $deps, $version, 'all' );
		}
	}
}
