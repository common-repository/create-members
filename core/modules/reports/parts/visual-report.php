<?php defined( 'ABSPATH' ) || exit; ?>

<h3 class="content-header mr_p_5"><?php esc_html_e( 'Visual Report', 'create-members' ); ?></h3>
<div class="report-section boxes">
	<div class="box">
		<h4 class="text-center"><?php esc_html_e( 'Active Membership Per Level', 'create-members' ); ?></h4>
		<canvas id="doughnut_chart"></canvas>
	</div>
	<div class="box">
		<h4 class="text-center"><?php esc_html_e( 'Revenues and Refunds', 'create-members' ); ?></h4>
		<canvas id="line_chart"></canvas>
	</div>
</div>
<script>
	(function ($) {
		$(document).ready(function () {
			/**
			 * Reports
			 */
			let ctx_line = $('#line_chart');
			new Chart(ctx_line, {
				data: {
					datasets: [
					{
						type: 'bar',
						label: '<?php esc_html_e( 'Revenue', 'create-members' ); ?>',
						data: <?php echo json_encode( $bar_lines['revenue'] ); ?>
					}, 
					{
						type: 'line',
						label: '<?php esc_html_e( 'Refund', 'create-members' ); ?>',
						data: <?php echo json_encode( $bar_lines['refund'] ); ?>,
					}],
					labels: <?php echo json_encode( $bar_lines['month'] ); ?>
				},
				options: {
					responsive: true,
				},
			});

			// doughnut
			var doughnut_chart = document.getElementById("doughnut_chart");
			new Chart(doughnut_chart, 
			{
				type: 'doughnut',
				data: {
					datasets: [
					{
						data: <?php echo json_encode( $members_per_level['active_members'] ); ?>,
						backgroundColor: [
						'rgb(255, 99, 132)',
						'rgb(255, 159, 64)',
						'rgb(255, 205, 86)',
						'rgb(75, 192, 192)',
						'rgb(54, 162, 235)',
						],
					},
					],
					labels: <?php echo json_encode( $members_per_level['level'] ); ?>,
				}
			});
		});
	})(jQuery);
</script>