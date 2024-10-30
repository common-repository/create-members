<?php

if ( ! defined( 'ABSPATH' ) ) { exit; }

$user_id = get_current_user_id();
if (empty($user_id)) {
	return;
}
?>
<h2 class="mt-3"><?php esc_html_e( 'Related orders', 'create-members' ); ?></h2>
<div class="related-orders">
	<?php
		$related_orders = get_user_meta( $user_id , UM_ORDERS , true );
		if (!empty($related_orders)) {
			?>
				<table id="um-table">
					<thead>
						<?php
							if (!empty($um_view_type) && $um_view_type == 'order') {
								?>
									<th><?php esc_html_e( 'Relation', 'create-members' ); ?></th>
								<?php
							}
						?>
						<th><?php esc_html_e( 'Order Date', 'create-members' ); ?></th>
						<th><?php esc_html_e( 'Date', 'create-members' ); ?></th>
						<th><?php esc_html_e( 'Status', 'create-members' ); ?></th>
						<th><?php esc_html_e( 'Total', 'create-members' ); ?></th>
					</thead>
					<tbody>
						<tr>
							<?php
								foreach ($related_orders as $key => $value) {
									$order = wc_get_order( $value['order_id'] );
									if (!empty($order)) {
										if (!empty($um_view_type) && $um_view_type == 'order') { ?>
											<td><?php echo $value['relationship'] == 'parent_order' ? esc_html__('Parent','create-members-pro') 
											: esc_html__('Child','create-members-pro'); ?></td>
										<?php } ?>
										<td><?php echo intval($order->get_id()); ?></td>
										<td><?php echo esc_html(date(get_option('date_format'),strtotime($order->get_date_created()))); ?></td>
										<td><?php echo esc_html($order->get_status()); ?></td>
										<td><?php echo wc_price($order->get_total()); ?></td>
									<?php
									}
								}
							?>
						</tr>
					</tbody>
				</table>
			<?php
		}
	?>
</div>