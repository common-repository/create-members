<?php

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Helper;

echo Helper::kses( $message );
