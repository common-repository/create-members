<?php

namespace Membership\Base;

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Singleton;
use Membership\Utils\Helper;
use Membership\Core\Models\Members as MemberModel;
use Membership\Core\Models\Plans;
use Membership\Core\Modules\Members\Emails\Send_Email;

class Actions {

	use Singleton;

	/**
	 * Initialize
	 */
	public function init() {
		$callback = array( 'save_membership_settings', 'resend_email' );
		if ( ! empty( $callback ) ) {
			foreach ( $callback as $key => $value ) {
				add_action( 'wp_ajax_' . $value, array( $this, $value ) );
				add_action( 'wp_ajax_nopriv_' . $value, array( $this, $value ) );
			}
		}
	}

	/**
	 * Save settings
	 */
	public function save_membership_settings() {
		Helper::instance()->verify_nonce( 'ult_mem_nonce', sanitize_key( $_POST['ult_mem_nonce'] ) );
		$post_data = filter_input_array( INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS );
		$params    = ! empty( $post_data['params'] ) ? $post_data['params'] : array();

		$message = '';
		if ( empty( $params['status'] ) ) {
			$params['status'] = 'no';
		}

		if ( ! empty( $params['membership_settings'] ) && $params['membership_settings'] == 'membership_settings' ) {
			$settings_key = Helper::get_settings_key();
			foreach ( $settings_key as $key => $value ) {
				$settings_key[ $key ] = ! empty( $params[ $key ] ) ? $params[ $key ] : '';
			}
			$this->send_email_all_members( $params );
			update_option( 'membership_settings', $settings_key );
			$message = esc_html__( 'Settings Save Successfully', 'create-members' );
		} elseif ( ! empty( $params['membership_plans'] ) && $params['membership_plans'] == 'membership_plans' ) {
			$title = ! empty( $params['plan_name'] ) ? $params['plan_name'] : esc_html__( 'Membership Plan', 'create-members' );
			$this->insert_membership_data( $params, $title, MEMBER_PLAN );
			$message = esc_html__( 'Membership Plan Added Successfully', 'create-members' );
		} elseif ( ! empty( $params['membership_members'] ) && $params['membership_members'] == 'membership_members' ) {
			if ( ! empty( $params['member_user'] ) ) {
				$this->save_member( $params );
				$message = esc_html__( 'Member Added Successfully', 'create-members' );
			} else {
				$message = esc_html__( 'User Missing', 'create-members' );
			}
		}

		wp_send_json_success( array( 'message' => $message ) );

		wp_die();
	}

	/**
	 * Send Message to all members
	 *
	 * @param [type] $params
	 */
	private function send_email_all_members( $params ) {
		if ( ! empty( $params['members_message'] ) && ! empty( $params['message_plan'] ) ) {
			$meta_data      = array(
				'key'     => 'member_plan',
				'value'   => is_array( $params['message_plan'] ) ? $params['message_plan'] : array( $params['message_plan'] ),
				'compare' => 'IN',
			);
			$recipient_data = MemberModel::instance()->get_all_data( -1, false, $meta_data );
			if ( empty( $recipient_data ) ) {
				return;
			}

			foreach ( $recipient_data as $key => $value ) {
				$value['mail_type'] = 'all_members';
				$args               = array(
					'data'      => $value,
					'recipient' => $value['user_email'],
					'subject'   => $params['members_email_subject'],
					'title'     => $params['members_email_title'],
					'message'   => $params['members_message'],
				);
				$obj                = new Send_Email( $args );
				$obj->send();
			}
		}
	}

	/**
	 * Insert member
	 *
	 * @param [type] $params
	 */
	public function save_member( $params, $mail = true ) {
		$member    = esc_html__( 'Member', 'create-members' );
		$title     = ! empty( $params['member_user'] ) ? $member . ' ' . $params['member_user'] : $member;
		$member_id = $this->insert_membership_data( $params, $title, MEMBERSHIP_MEMBER );

		if ( ! empty( $member_id ) ) {
			MemberModel::save_member_data( $params['member_user'], $params['member_plan_id'], $member_id );
			if ( $mail ) {
				MemberModel::new_member_mail( $params['member_user'], $params['member_plan_id'] );
			}
		}
	}

	/**
	 * insert plan,member
	 */
	private function insert_membership_data( $params, $title, $post_type ) {
		$id = null;
		$ID = ! empty( $params['ID'] ) ? $params['ID'] : '';
		if ( $ID == '' ) {
			$id = wp_insert_post(
				array(
					'post_title'   => $title,
					'post_type'    => $post_type,
					'post_content' => '',
					'post_status'  => 'publish',
				)
			);
		} else {
			$post_update = array(
				'ID'         => $ID,
				'post_title' => $title,
			);
			wp_update_post( $post_update );
			$id = $ID;
		}

		if ( ! empty( $id ) ) {
			if ( $post_type == MEMBER_PLAN && ( !empty( $params['subscription_price'] )
			 && $params['subscription_price'] !== '' ) ) {
				Plans::set_package_price( $id, $params['subscription_price'] );
			} elseif ( $post_type == MEMBERSHIP_MEMBER ) {
				MemberModel::membership_end_date( $id, $params['member_plan_id'] );
			}

			foreach ( $params as $key => $value ) {
				update_post_meta( $id, $key, $value );
			}
		}

		return $id;
	}

	/**
	 * Resend email
	 */
	public function resend_email() {
		Helper::instance()->verify_nonce( 'ult_mem_nonce', sanitize_key( $_POST['ult_mem_nonce'] ) );
		$post_data     = filter_input_array( INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS );
		$re_send_email = ! empty( $post_data['re_send_email'] ) ? $post_data['re_send_email'] : 're_send_email';
		$user_id       = ! empty( $post_data['user_id'] ) ? $post_data['user_id'] : '';
		$plan_id       = ! empty( $post_data['plan_id'] ) ? $post_data['plan_id'] : '';
		$txt           = esc_html__( 'Sent', 'create-members' );

		if ( $re_send_email == 'new-mem-resend' ) {
			$result = MemberModel::new_member_mail( $user_id, $plan_id );
			$txt    = $result == true ? esc_html__( 'Sent', 'create-members' ) : esc_html__( 'Re-Send', 'create-members' );
		}

		wp_send_json_success( array( 'txt' => $txt ) );
	}
}
