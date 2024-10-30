<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; }

if ( file_exists( CreateMembers::core_dir() . 'admin/settings/tab-content/parts/woo/settings.php' ) ) {
	include_once CreateMembers::core_dir() . 'admin/settings/tab-content/parts/woo/settings.php';
}
