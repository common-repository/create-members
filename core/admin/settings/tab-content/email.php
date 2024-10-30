<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

use Membership\Core\Models\Plans as PlansModel;

$args = array(
	'label'           => esc_html__( 'Notification Type', 'create-members' ),
	'id'              => 'notification_type',
	'condition_class' => '',
	'select_type'     => 'single',
	'type'            => 'random',
	'selected'        => '',
	'disable_key'     => array( 'new_member', 'cancel_member' ),
	'options'         => array(
		'all_members'   => esc_html__( 'Email to All Members', 'create-members' ),
		'new_member'    => esc_html__( 'Email to New Member', 'create-members' ),
		'cancel_member' => esc_html__( 'Email to Cancel Member', 'create-members' ),
	),
);

membership_select_field( $args );

$args = array(
	'label'       => esc_html__( 'Email Template Tags', 'create-members' ),
	'description' => '
    <ul class="input-desc">
        <li><strong>{plan_name}</strong>' . ' ' . esc_html__( 'Membership Plan name will be added in the template', 'create-members' ) . '</li>
    </ul>
    ',
);
membership_desc_block( $args );


$args = array(
	'label'           => esc_html__( 'Select Plan', 'create-members' ),
	'id'              => 'message_plan',
	'docs'            => $plan_desc . ' ' . esc_html__( 'Send email to all members of selected Plan', 'create-members' ),
	'condition_class' => 'all_members',
	'select_type'     => 'multiple',
	'type'            => 'random',
	'selected'        => '',
	'options'         => PlansModel::get_all_plans( -1, false, true ),
);

membership_select_field( $args );

// All members
$args = array(
	'label'           => esc_html__( 'Subject', 'create-members' ),
	'placeholder'     => esc_html__( 'Enter Subject of the Email', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'members_email_subject',
	'value'           => '',
	'condition_class' => 'all_members',
);
membership_number_input_field( $args );

$args = array(
	'label'           => esc_html__( 'Title', 'create-members' ),
	'placeholder'     => esc_html__( 'Enter Title of the Email', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'members_email_title',
	'value'           => '',
	'condition_class' => 'all_members',
);
membership_number_input_field( $args );

$args = array(
	'label'           => esc_html__( 'Message', 'create-members' ),
	'id'              => 'Message',
	'settings'        => array(
		'textarea_name' => 'members_message',
		'editor_height' => 180,
		'textarea_rows' => 20,
	),
	'condition_class' => 'all_members',
);
membership_wp_editor( $args );

// new member
$args = array(
	'label'           => esc_html__( 'Subject', 'create-members' ),
	'placeholder'     => esc_html__( 'Enter Subject of the Email', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'new_member_subject',
	'value'           => $new_member_subject,
	'condition_class' => 'd-none new_member',
);
membership_number_input_field( $args );

$args = array(
	'label'           => esc_html__( 'Title', 'create-members' ),
	'placeholder'     => esc_html__( 'Enter Title of the Email', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'new_member_title',
	'value'           => $new_member_title,
	'condition_class' => 'd-none new_member',
);
membership_number_input_field( $args );

$args = array(
	'label'           => esc_html__( 'Message', 'create-members' ),
	'id'              => 'new_member_message',
	'settings'        => array(
		'textarea_name' => 'new_member_message',
		'editor_height' => 180,
		'textarea_rows' => 20,
	),
	'value'           => html_entity_decode( $new_member_message ),
	'condition_class' => 'd-none new_member',
);
membership_wp_editor( $args );

// cancel member
$args = array(
	'label'           => esc_html__( 'Subject', 'create-members' ),
	'placeholder'     => esc_html__( 'Enter Subject of the Email', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'cancel_subject',
	'value'           => $cancel_subject,
	'condition_class' => 'd-none cancel_member',
);
membership_number_input_field( $args );

$args = array(
	'label'           => esc_html__( 'Title', 'create-members' ),
	'placeholder'     => esc_html__( 'Enter Title of the Email', 'create-members' ),
	'field_type'      => 'text',
	'id'              => 'cancel_title',
	'value'           => $cancel_title,
	'condition_class' => 'd-none cancel_member',
);
membership_number_input_field( $args );

$args = array(
	'label'           => esc_html__( 'Message', 'create-members' ),
	'id'              => 'cancel_message',
	'value'           => html_entity_decode( $cancel_message ),
	'settings'        => array(
		'textarea_name' => 'cancel_message',
		'editor_height' => 180,
		'textarea_rows' => 20,
	),
	'condition_class' => 'd-none cancel_member',
);
membership_wp_editor( $args );
