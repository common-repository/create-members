<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;} ?>

<div class="mt-2 content-header">
	<div class="title mr-1"><?php esc_html_e( 'Subscriptions', 'create-members' ); ?></div>
	<a href="<?php echo esc_url( admin_url() . 'admin.php?page=um-plans&plan=new-plan' ); ?>" target="_self">
		<button class="button button-primary add-new-plan"><?php esc_html_e( 'Add New Subscription', 'create-members' ); ?></button>
	</a>
</div>
<div class="view-plans">
<?php

	/**
	 * Shows Rules
	 */
	$columns = array(
		'cb'             => '<input name="bulk-delete[]" type="checkbox" />',
		'plan_name'      => esc_html__( 'Name', 'create-members' ),
		'active_members' => esc_html__( 'Active Members', 'create-members' ),
		'durations'      => esc_html__( 'Durations', 'create-members' ),
		'price'          => esc_html__( 'Price', 'create-members' ),
		'status'         => esc_html__( 'Status', 'create-members' ),
		'actions'        => esc_html__( 'Actions', 'create-members' ),
	);

	$lists = array(
		'singular_name' => esc_html__( 'Subscriptions', 'create-members' ),
		'plural_name'   => esc_html__( 'Subscriptions', 'create-members' ),
		'columns'       => $columns,
	);

	?>
	<div class="report-list">
		<form method="POST">
			<?php
				$table = new \Membership\Core\Modules\Plans\Table( $lists );
				$table->preparing_items();
				$table->display();
			?>
		</form>
	</div>
</div>
