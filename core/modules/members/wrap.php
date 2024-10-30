<?php

namespace Membership\Core\Modules\Members;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Membership\Utils\Singleton;

class Wrap {

	use Singleton;

	public function init() {
		\Membership\Core\Modules\Members\Account::instance()->init();
	}
}
