<?php

namespace Membership\Core;

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Singleton;
use Membership\Core\Modules\Members;
/**
 * Base Class
 *
 * @since 1.0.0
 */
class Core {

	use Singleton;

	/**
	 * Initialize all modules.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		if ( is_admin() ) {
			\Membership\Core\Admin\Hooks::instance()->init();
			\Membership\Core\Models\Modules::instance()->init();
			// Load admin menus
			\Membership\Core\Admin\Menus::instance()->init();
			// Ajax submit
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				\Membership\Base\Actions::instance()->init();
			}
		} else {
			\Membership\Core\Frontend\Frontend::instance()->init();
		}

		Members\Wrap::instance()->init();
	}
}
