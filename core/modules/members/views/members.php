<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( file_exists( CreateMembers::base_dir() . 'input-fields.php' ) ) {
	include_once CreateMembers::base_dir() . 'input-fields.php';
}
?>
<div class="mt-2 content-header">
	<div class="title mr-1"><?php esc_html_e( 'Members', 'create-members' ); ?></div>
	<a href="<?php echo esc_url( admin_url() . 'admin.php?page=um-members&member=new-member' ); ?>" target="_self">
		<button class="button button-primary add-new-member"><?php esc_html_e( 'New Member', 'create-members' ); ?></button>
	</a>
</div>
<div class="view-plans">
<?php

	/**
	 * Shows Rules
	 */
	$columns = array(
		'cb'              => '<input name="bulk-delete[]" type="checkbox" />',
		'user_name'       => esc_html__( 'User', 'create-members' ),
		'status'          => esc_html__( 'Membership Status', 'create-members' ),
		'plan_name'       => esc_html__( 'Subscription', 'create-members' ),
		'next_payment'    => esc_html__( 'Next Payment Date', 'create-members' ),
		'order_info'      => esc_html__( 'Order Information', 'create-members' ),
		'start_date'      => esc_html__( 'Start Date', 'create-members' ),
		'end_date'        => esc_html__( 'End Date', 'create-members' ),
		'new_mail_status' => esc_html__( 'New Member Mail', 'create-members' ),
	);

	$lists = array(
		'singular_name' => esc_html__( 'All Member', 'create-members' ),
		'plural_name'   => esc_html__( 'All Members', 'create-members' ),
		'columns'       => $columns,
		'plan_id'       => ! empty( $_GET['plan'] ) ? $_GET['plan'] : '',
	);

	?>
	<div class="report-list">
		<form method="POST">
			<?php
				$table = new \Membership\Core\Modules\Members\Table( $lists );
				$table->preparing_items();
				$table->display();
			?>
		</form>
	</div>
</div>
