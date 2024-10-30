<?php

use Membership\Utils\Helper;

defined( 'ABSPATH' ) || exit; ?>

<h3 class="content-header mr_p_5"><?php esc_html_e( 'States', 'create-members' ); ?></h3>
<div class="report-section boxes">
	<div class="box">
		<h3 class="content-header mr_p_5"><?php esc_html_e( 'Membership States', 'create-members' ); ?></h3>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Period', 'create-members' ); ?></th>
					<th><?php esc_html_e( 'Signup', 'create-members' ); ?></th>
					<th><?php esc_html_e( 'Cancellations', 'create-members' ); ?></th>
					<th><?php esc_html_e( 'Expiration', 'create-members' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $subscription_report as $key => $value ) {
					?>
					<tr>
					<td><?php echo esc_html( $value['label'] ); ?></td>
					<td><?php echo esc_html( $value['signup'] ); ?></td>
					<td><?php echo esc_html( $value['cancel'] ); ?></td>
					<td><?php echo esc_html( $value['expire'] ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="box">
		<h3 class="content-header mr_p_5"><?php esc_html_e( 'Sales and Revenue', 'create-members' ); ?></h3>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Period', 'create-members' ); ?></th>
					<th><?php esc_html_e( 'Sales', 'create-members' ); ?></th>
					<th><?php esc_html_e( 'Revenue', 'create-members' ); ?></th>
					<th><?php esc_html_e( 'Refund', 'create-members' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $sales_report as $key => $value ) {
					?>
					<tr>
					<td><?php echo esc_html( $value['label'] ); ?></td>
					<td><?php echo esc_html( $value['sales'] ); ?></td>
					<td><?php echo Helper::kses( $value['revenue'] ); ?></td>
					<td><?php echo Helper::kses( $value['refund'] ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
