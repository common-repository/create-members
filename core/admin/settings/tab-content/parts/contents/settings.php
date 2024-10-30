<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

use Membership\Utils\Helper;
?>
<h2><?php esc_html_e( 'Page Link', 'create-members' ); ?></h2>
<?php
$args = array(
	'label'       => esc_html__( 'Subscription Page Link', 'create-members' ),
	'placeholder' => esc_html__( 'Enter Subscription Page Link', 'create-members' ),
	'docs'        => esc_html__( 'Redirect to the page to Purchase Subscription', 'create-members' ),
	'field_type'  => 'text',
	'id'          => 'subscription_page',
	'value'       => $subscription_page,
	'disable'     => Helper::is_pro_active() ? false : true,
);
membership_number_input_field( $args );
?>
<h2><?php esc_html_e( 'Message Settings', 'create-members' ); ?></h2>
<?php
$args = array(
	'label'       => esc_html__( 'Message for Restrict Content', 'create-members' ),
	'placeholder' => esc_html__( 'Enter Message', 'create-members' ),
	'docs'        => esc_html__( 'Message will be Shown to Non member and Logged Out user when access to the Content', 'create-members' ),
	'id'          => 'non_member_text',
	'value'       => $non_member_text,
	'cols'        => '50%',
	'disable'     => Helper::is_pro_active() ? false : true,

);
membership_text_area( $args );
?>
<h2><?php esc_html_e( 'Content Settings', 'create-members' ); ?></h2>
<?php
$args = array(
	'label'           => esc_html__( 'Filter searches and archives', 'create-members' ),
	'id'              => 'filter_queries',
	'type'            => 'random',
	'select_type'     => 'single',
	'condition_class' => '',
	'selected'        => $filter_queries,
	'options'         => array(
		'yes' => esc_html__( 'Yes - Only Members will See Restricted Posts/Pages in Searches and Archives.', 'create-members' ),
		'no'  => esc_html__( 'No - Non-Members will See Restricted Posts/Pages in Searches and Archives.', 'create-members' ),
	),
	'disable'         => Helper::is_pro_active() ? false : true,
);

membership_select_field( $args );

