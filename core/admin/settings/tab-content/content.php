<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; } ?>

<h1 class="font_bold font_18"><?php echo esc_html__( 'WordPress Membership Settings', 'create-members' ); ?></h1>
<div class="documentation mb-1"><i class="doc"><?php echo esc_html__( 'Set WordPress Membership settings to restrict contents', 'create-members' ); ?></i></div>

<?php
if ( file_exists( CreateMembers::core_dir() . 'admin/settings/tab-content/parts/contents/settings.php' ) ) {
	include_once CreateMembers::core_dir() . 'admin/settings/tab-content/parts/contents/settings.php';
}
