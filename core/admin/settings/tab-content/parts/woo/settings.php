<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

use Membership\Utils\Helper;

$args = array(
	'label'   => esc_html__( 'Enable Membership Plan', 'create-members' ),
	'id'      => 'is_enable_membership',
	'checked' => ( $is_enable_membership == 'default' || $is_enable_membership == 'yes' ) ? 'yes' : '',
);

membership_checkbox_field( $args );

$non_member_offer_cond = $non_member_offer == 'yes' ? '' : 'd-none';

$args = array(
    'label'     => esc_html__('Membership Level Based On','create-members'),
    'id'        => 'plan_cost',
    'docs'      => esc_html__('Customer can be a member based on purchase a specific product or subscription package or based total cart amount. Note: Recurring Subscription will work only for "Purchase Subscription Package"','create-members'),
    'condition_class'   => '',
    'select_type'       => 'single',
    'type'              => 'random',
    'disable_key'       => array('subscription'),
    'selected'          => $plan_cost,
    'options'           => array(
        'subscription'  => esc_html__('Purchase Subscription Package','create-members'), 
        'amount'        => esc_html__('Purchase Amount','create-members'),
        'product'       => esc_html__('Purchase Specific Product','create-members') 
    )
);
membership_select_field( $args );
?>
<h1 class="font_bold font_18"><?php echo esc_html__( 'WooCommerce Membership Settings', 'create-members' ); ?></h1>
<div class="documentation mb-1"><i class="doc"><?php echo esc_html__( 'Set WooCommerce Membership settings to enable offer for the members', 'create-members' ); ?></i></div>
<?php

$args = array(
	'label'           => esc_html__( 'Hide Price Message', 'create-members' ),
	'placeholder'     => '',
	'docs'            => esc_html__( 'Message will be display to hide price product', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'hide_price_txt',
	'condition_class' => '',
	'value'           => $hide_price_txt,
	'disable'         => Helper::is_pro_active() ? false : true,
);
membership_number_input_field( $args );

$args = array(
	'label'   => esc_html__( 'Show Offer to Non-Member', 'create-members' ),
	'id'      => 'non_member_offer',
	'checked' => $non_member_offer,
	'disable' => Helper::is_pro_active() ? false : true,
);
membership_checkbox_field( $args );


$non_member_offer_cond = $non_member_offer == 'yes' ? '' : 'd-none';

$args = array(
	'label'           => esc_html__( 'Message for Non-Member', 'create-members' ),
	'placeholder'     => esc_html__( 'Enter Message for Non-Member', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'non_member_msg',
	'value'           => $non_member_msg,
	'disable'         => Helper::is_pro_active() ? false : true,
	'condition_class' => 'non_member_offer ' . $non_member_offer_cond,
);
membership_number_input_field( $args );


$args = array(
	'label'           => esc_html__( 'Re-Direct Page', 'create-members' ),
	'desc'            => esc_html__( 'Re-Direct to the Page if click to the Restrict page', 'create-members' ),
	'id'              => 'redirect_content',
	'type'            => 'random',
	'select_type'     => 'single',
	'condition_class' => '',
	'selected'        => $redirect_content,
	'options'         => Helper::get_content_pages(),
	'disable'         => Helper::is_pro_active() ? false : true,
);

membership_select_field( $args );

