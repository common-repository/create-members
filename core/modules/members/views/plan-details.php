<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use Membership\Core\Models\Plans as PlanModel;
use Membership\Core\Models\Members as MembersModel;
use Membership\Utils\Helper;

$plan_details = Membership\Core\Modules\Members\Account::instance()->get_plan_details();
if ( empty( $plan_details ) ) {
	return;
}
?>
<h2><?php esc_html_e( 'Membership Plan Details', 'create-members' ); ?></h2>
<div class="membership-plans">
	<div class="item">
		<label><?php esc_html_e( 'Membership Status:', 'create-members' ); ?></label>
		<div>
			<?php echo PlanModel::status_col( $plan_details, 'member_status' ); ?>
		</div>
	</div>
	<div class="item">
		<label><?php esc_html_e( 'Subscription Level:', 'create-members' ); ?></label>
		<div><?php echo esc_html( $plan_details['plan_name'] ); ?></div>
	</div>
	<div class="item">
		<label><?php esc_html_e( 'Price:', 'create-members' ); ?></label>
		<div><?php echo esc_html( Helper::level_price($plan_details)); ?></div>
	</div>
	<div class="item">
		<label><?php esc_html_e( 'Start Date:', 'create-members' ); ?></label>
		<div><?php echo MembersModel::format_date_col( $plan_details['start_date'] ); ?></div>
	</div>
	<?php	if ( $plan_details['recurring_subscription'] !== 'yes' ) { ?>
	<div class="item">
		<label><?php esc_html_e( 'End Date:', 'create-members' ); ?></label>
		<div><?php echo MembersModel::membership_end_date_text( $plan_details['end_date'] ); ?></div>
	</div>
	<?php }else{
		$recurring_details = get_user_meta($plan_details['member_user'], 'um_subscription' , true );
		$next_payment_date = !empty( $recurring_details ) && !empty( $recurring_details['next_payment_date'] ) ? $recurring_details['next_payment_date'] : '';
		?>
		<div class="item">
			<label><?php esc_html_e( 'Next Payment Date:', 'create-members' ); ?></label>
			<div><?php echo date_i18n( $next_payment_date ); ?></div>
		</div>
	<?php } ?>
</div>

<h2 class="mt-3"><?php esc_html_e( 'Membership Discount', 'create-members' ); ?></h2>
<div class="membership-plans">
	<div class="item">
		<label><?php esc_html_e( 'Free Shipping:', 'create-members' ); ?></label>
		<div><?php echo esc_html( $plan_details['free_shipping'] ); ?></div>
	</div>
	<?php if ( $plan_details['member_status'] !== MEMBER_CANCEL_STATUS ) : ?>
	<div class="item">
		<label><?php esc_html_e( 'Actions:', 'create-members' ); ?></label>
		<form method="post">
			<div class="d-inline">
			<?php echo wp_nonce_field( 'um-account-nonce', 'um-account-nonce', true, false ); ?>
				<button type="submit" name="cancel_membership" 
					value="cancel-membership"
					class="cancel-membership">
				<?php esc_html_e( 'Cancel Membership', 'create-members' ); ?>
				</button>
			<?php	if ( $plan_details['recurring_subscription'] == 'yes' ) { 
				$link = trailingslashit(wc_get_account_endpoint_url( get_option( 'woocommerce_myaccount_account_endpoint' ) )) . 'create-members-details/';
				?>
				<a href="<?php echo esc_url( $link .'?um_renew=true&um_renewal_early='.$plan_details['member_plan_id'] ) ?>">
				<?php esc_html_e( 'Renew Now', 'create-members' ); ?>
				</a>
			<?php } ?>
			</div>
		</form>
	</div>
	<?php endif; ?>
</div>

<?php
if (file_exists(\CreateMembers::modules_dir() . 'members/views/subscriptions/related-orders.php')) {
	include \CreateMembers::modules_dir() . 'members/views/subscriptions/related-orders.php';
}
?>