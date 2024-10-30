<?php

	defined( 'ABSPATH' ) || die();

	use Membership\Core\Models\Plans as PlansModel;

if ( file_exists( \CreateMembers::base_dir() . 'input-fields.php' ) ) {
	include_once \CreateMembers::base_dir() . 'input-fields.php';
}

	$settings = \Membership\Utils\Helper::get_settings();
	extract( $settings );


	$plans     = PlansModel::get_all_plans( -1, false );
	$plan_desc = '';
if ( count( $plans ) == 0 ) {
	$plan_desc = esc_html__( 'Create New plan from ' ) .
	'<a href="' . esc_url( admin_url() . '?page=um-plans' ) . '"
        target="_blank" >' . esc_html__( 'Plans', 'create-members' ) . '</a>';
}

	$tabs = array(
		'settings'    => esc_html__( 'General Settings', 'create-members' ),
		'content'     => esc_html__( 'Content Settings', 'create-members' ),
		'email'       => esc_html__( 'Email', 'create-members' ),
		'short-codes' => esc_html__( 'Shortcodes', 'create-members' ),
	);

	$tab_content = array( 'settings', 'content', 'email', 'short-codes' );
	?>
<div class="settings_message d-none"></div>
<form id="membership-settings" class="membership-settings-form">
	<div class="content-header title-wrap">
		<div class="title"><?php esc_html_e( 'Settings', 'create-members' ); ?></div>
	</div>
	<div class="content-wrapper">
		<div class="settings_tab">
			<ul class="settings_tab_pan">
				<?php foreach ( $tabs as $key => $value ) { ?>
					<li data-item="<?php echo esc_attr( $key ); ?>"><?php echo( $value ); ?></li>
				<?php } ?>
			</ul>
			<div class="tab-content">
				<?php
				foreach ( $tab_content as $key => $value ) {
					$active = $value == 'settings' ? 'active tab-wrapper' : 'tab-wrapper';
					?>
				<div id="<?php echo esc_attr( $value ); ?>" class="<?php echo esc_attr( $active ); ?>">
					<?php
					if ( file_exists( \CreateMembers::core_dir() . 'admin/settings/tab-content/' . $value . '.php' ) ) {
						include_once \CreateMembers::core_dir() . 'admin/settings/tab-content/' . $value . '.php';
					}
					?>
				</div>
				<?php } ?>
			</div>	
		</div>
		<input type="hidden" id="" name="membership_settings" value="membership_settings"/>
		<button type="submit" class="button button-primary admin-button"><?php esc_html_e( 'Save Changes', 'create-members' ); ?></button>
	</div>
</form>