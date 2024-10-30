<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Membership\Utils\Helper;
?>
<h2 class="mt-2 mb-0"><?php esc_html_e( 'Billing Details:', 'create-members' ); ?></h2>
<p class="block-desc mb-2"><?php esc_html_e( 'Set Initial payment to become a member and Recurring payments, if applicable, begin one cycle after the initial payment.', 'create-members' ); ?></p>
<?php
	$args = array(
		'label'       => esc_html__( 'Initial Payment', 'create-members' ),
		'docs'        => esc_html__( 'Subscription Level Price. 0 for free', 'create-members' ),
		'placeholder' => esc_html__( 'Enter Subscription Price', 'create-members' ),
		'field_type'  => 'number',
		'number_attr' => 'step=any',
		'id'          => 'subscription_price',
		'value'       => $subscription_price,
	);
	membership_number_input_field( $args );

	$args = array(
		'label'   => esc_html__( 'Recurring Subscription', 'create-members' ),
		'id'      => 'recurring_subscription',
		'disable' => Helper::is_pro_active() ? false : true,
		'checked' => $recurring_subscription,
	);
	membership_checkbox_field( $args );

	$recurring_cond    = $recurring_subscription == 'yes' ? '' : 'd-none';
	$recurring_section = $recurring_subscription == 'yes' ? 'd-none' : '';

	?>
<div class="input-group-field bg-white recurring_subscription  <?php echo esc_attr( $recurring_cond ); ?>">
	<div class="form-label"><?php esc_html_e( 'Billing Amount', 'create-members' ); ?></div>
	<div class="input-group-wrapper input-group-billing">
	<?php
		$args = array(
			'label'             => esc_html__( 'Amount', 'create-members' ),
			'number_attr'       => 'step=any',
			'docs'              => esc_html__( 'The amount to be billed one cycle after the initial payment.', 'create-members' ),
			'wrapper_class'     => 'group-block',
			'placeholder'       => '',
			'field_type'        => 'number',
			'id'                => '_subscription_price',
			'extra_label_text1' => Helper::currency_symbol(),
			'condition_class'   => '',
			'disable'           => Helper::is_pro_active() ? false : true,
			'value'             => $_subscription_price,
		);
		membership_number_input_field( $args );

		$args = array(
			'label'           => esc_html__( 'Length', 'create-members' ),
			'docs'            => '',
			'wrapper_class'   => 'group-block',
			'placeholder'     => '',
			'field_type'      => 'number',
			'id'              => '_subscription_period_interval',
			'condition_class' => '',
			'disable'         => Helper::is_pro_active() ? false : true,
			'value'           => $_subscription_period_interval,
		);
		membership_number_input_field( $args );
		$args = array(
			'label'           => esc_html__( 'Type', 'create-members' ),
			'wrapper_class'   => 'group-block',
			'type'            => 'random',
			'select_type'     => 'single',
			'selected'        => $_subscription_period,
			'id'              => '_subscription_period',
			'options'         => Helper::subscription_period(),
			'disable'         => Helper::is_pro_active() ? false : true,
			'condition_class' => '',
		);
		membership_select_field( $args );
		?>
	</div>    
</div>
<?php
	$args = array(
		'label'             => esc_html__( 'Stop Renewing After', 'create-members' ),
		'extra_label_text1' => esc_html__( 'Month(s)', 'create-members' ),
		'docs'              => esc_html__( 'Automatically stop renewing after this length of time. Leave blank to do not stop.', 'create-members' ),
		'placeholder'       => '',
		'field_type'        => 'number',
		'id'                => '_subscription_length',
		'condition_class'   => 'recurring_subscription ' . $recurring_cond,
		'value'             => $_subscription_length,
		'disable'           => Helper::is_pro_active() ? false : true,
	);
	membership_number_input_field( $args );
	?>
<div class="recurring_section <?php echo esc_attr( $recurring_section ); ?>">
	<h2 class="mt-2 mb-0"><?php esc_html_e( 'Expiration Settings:', 'create-members' ); ?></h2>
	<p class="block-desc mb-2"><?php esc_html_e( 'Set Subscription duration when membership access expires', 'create-members' ); ?></p>
	<div class="input-group-field bg-white">
		<div class="form-label"><?php esc_html_e( 'Duration', 'create-members' ); ?></div>
		<div class="input-group-wrapper input-group-2">
		<?php
			$args = array(
				'label'           => esc_html__( 'Length', 'create-members' ),
				'docs'            => esc_html__( 'Enter 0 for Unlimited', 'create-members' ),
				'wrapper_class'   => 'group-block',
				'placeholder'     => '',
				'field_type'      => 'number',
				'id'              => 'duration',
				'condition_class' => '',
				'value'           => $duration,
			);
			membership_number_input_field( $args );

			$args = array(
				'label'           => esc_html__( 'Type', 'create-members' ),
				'wrapper_class'   => 'group-block',
				'type'            => 'random',
				'select_type'     => 'single',
				'selected'        => $period,
				'id'              => 'period',
				'options'         => array(
					'day'   => esc_html__( 'Day(s)', 'create-members' ),
					'month' => esc_html__( 'Month(s)', 'create-members' ),
					'year'  => esc_html__( 'Year(s)', 'create-members' ),
				),
				'condition_class' => '',
			);
			membership_select_field( $args );
			?>
		</div>    
	</div>
</div>