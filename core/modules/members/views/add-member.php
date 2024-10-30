<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Membership\Core\Models\Plans as PlansModel;
use Membership\Core\Models\Members as MembersModel;
use Membership\Utils\Helper;

if ( file_exists( CreateMembers::base_dir() . 'input-fields.php' ) ) {
	include_once CreateMembers::base_dir() . 'input-fields.php';
}

$id      = ( isset( $_GET['id'] ) ) ? intval( $_GET['id'] ) : '';
$members = MembersModel::get_single_data( $id );

extract( $members );

?>

<div class="form-section">
	<div class="mt-2 mb-2 center-align content-header">
		<?php
			$title = $id == '' ? esc_html__( 'Add New Member', 'create-members' ) :
			get_avatar( $member_user, 96 ) . '<div class="ml-1">' . esc_html__( 'Edit', 'create-members' ) . ' ' . $user_name . '</div>';
		?>
		<div class="circle center-align title mr-1"><?php echo Helper::kses( $title ); ?></div>
		<a href="<?php echo esc_url( admin_url() . 'admin.php?page=um-members' ); ?>" target="_self">
			<button class="button button-primary add-new-plan"><?php esc_html_e( 'View Members', 'create-members' ); ?></button>
		</a>
	</div>
	<form id="membership-settings" class="add-member">
		<?php

			$args = array(
				'label'   => esc_html__( 'Enable Member', 'create-members' ),
				'id'      => 'status',
				'checked' => $status,
			);

			membership_checkbox_field( $args );

			$args = array(
				'label'      => '',
				'field_type' => 'hidden',
				'id'         => 'ID',
				'value'      => $ID,
			);
			membership_number_input_field( $args );

			$args = array(
				'label'      => '',
				'field_type' => 'hidden',
				'id'         => 'membership_members',
				'value'      => 'membership_members',
			);
			membership_number_input_field( $args );

			$docs = '';
			if ( empty( MembersModel::user_list() ) ) {
				$docs = esc_html( 'If user not exist, please create ' ) . ' ' .
				'<a href="' . esc_url( admin_url() . '/user-new.php' ) . '" target="_self">' . esc_html__( 'New User', 'create-members' ) . '</a>';
			} else {
				$docs = esc_html( 'Select User to assign a Subscription' );
			}

			$args = array(
				'label'           => esc_html__( 'Select User', 'create-members' ),
				'id'              => 'member_user',
				'docs'            => $docs,
				'condition_class' => '',
				'select_type'     => 'single',
				'type'            => 'random',
				'selected'        => $member_user,
				'options'         => MembersModel::user_list(),
			);
			membership_select_field( $args );

			$args = array(
				'label'           => esc_html__( 'Select Subscription', 'create-members' ),
				'id'              => 'member_plan_id',
				'docs'            => PlansModel::is_plan_exist(),
				'condition_class' => '',
				'select_type'     => 'multiple',
				'type'            => 'random',
				'selected'        => $member_plan_id,
				'options'         => PlansModel::get_all_plans( -1, false, true ),
			);

			membership_select_field( $args );

			$args = array(
				'label'       => esc_html__( 'Member Note', 'create-members' ),
				'placeholder' => esc_html__( 'Enter Member Note', 'create-members' ),
				'docs'        => esc_html__( 'Member Notes are Private and only Visible to other Users with Membership Management Capabilities', 'create-members' ),
				'id'          => 'notes',
				'value'       => $notes,
				'cols'        => 29,
			);
			membership_text_area( $args );

			$args = array(
				'label'              => esc_html__( 'Expire Date', 'create-members' ),
				'placeholder'        => esc_html__( 'Enter Expire Date', 'create-members' ),
				'docs'               => esc_html__( 'Subscription Default End date will be replaced with this Expire Date. Reset Expire Date to Set Default Subscription End Date.', 'create-members' ),
				'docs1'              => esc_html__( 'NOTE: Expire Date will not work if Recurring subscription applied at the Subscription Level.', 'create-members' ),
				'id'                 => 'expire_date',
				'extra_label_class1' => 'reset_expire',
				'extra_label_text1'  => esc_html__( 'Reset', 'create-members' ),
				'value'              => $expire_date,
			);
			membership_number_input_field( $args );
			?>
			<h2 class="mt-2 mb-1"><?php esc_html_e( 'Other Information', 'create-members' ); ?></h2>
			<?php
			if ( $id !== '' ) {
				$um_subscriptions = get_user_meta( $member_user, 'um_subscription', true );
				if ( ! empty( $um_subscriptions ) ) {
					echo '<div class="center-align mb-2">' .
						esc_html__( 'Next Payment Date', 'create-members' ) . ':' .
						date( 'd-m-Y', strtotime( $um_subscriptions['next_payment_date'] ) ) .
					'</div>';
				}
				$args = array(
					'label'   => esc_html__( 'Order Details', 'create-members' ),
					'txt'     => esc_html__( 'View Order Details', 'create-members' ),
					'class'   => 'action-link',
					'id'      => 'member-orders',
					'url'     => esc_url( admin_url() . 'admin.php?page=wc-orders&s=' . $user_email ),
					'disable' => '',
				);
				membership_anchor_field( $args );
				$args = array(
					'label'   => esc_html__( 'Subscription Details', 'create-members' ),
					'txt'     => esc_html__( 'View Subscription Details', 'create-members' ),
					'class'   => 'action-link',
					'id'      => 'member-orders',
					'url'     => esc_url( admin_url() . 'admin.php?page=um-plans&plan=update_plan&plan_id=' . $member_plan_id ),
					'disable' => '',
				);
				membership_anchor_field( $args );
			}
			?>
		<button type="submit" class="button button-primary admin-button mt-3"><?php esc_html_e( 'Save Changes', 'create-members' ); ?></button>
	</form>
</div>
