<?php

namespace Membership\Core\Models;

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Helper;
use Membership\Utils\Singleton;

class Modules {

	use Singleton;

	public function init() {
		add_action( 'init', array( $this, 'save_modules' ) );
	}

	/**
	 * Undocumented function
	 */
	public function save_modules() {
		if ( ! empty( $_POST['save_modules'] ) && 'um-modules' == $_POST['save_modules'] ) {
			$modules                = Helper::get_modules_key();
			$modules['wp_modules']  = ! empty( $_POST['wp_modules'] ) ? sanitize_text_field( $_POST['wp_modules'] ) : 'no';
			$modules['woo_modules'] = ! empty( $_POST['woo_modules'] ) ? sanitize_text_field( $_POST['woo_modules'] ) : 'no';
			update_option( MODULES_SAVINGS, $modules );
		}
	}
}
