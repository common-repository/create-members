<?php

namespace Membership\Core\Models;

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Singleton;
use Membership\Core\Models\Plans as PlanModel;
use Membership\Core\Modules\Members\Emails\Send_Email;
use Membership\Utils\Helper;
use WC_Customer;

class Members {

	use Singleton;

	public static function get_all_data( $limit, $action = 'table', $meta_data = null ) {
		$meta_query = array();
		$args       = array(
			'post_type'      => MEMBERSHIP_MEMBER,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
		);

		if ( ! empty( $meta_data ) ) {
			foreach ( $meta_data as $key => $value ) {
				array_push( $meta_query, $value );
			}
			$args['meta_query'] = $meta_query;
		}
		$all_data = get_posts( $args );

		return self::get_data( $all_data, $action );
	}

	/**
	 * Get rules data
	 *
	 * @param [type] $all_plans
	 * @return array
	 */
	public static function get_data( $all_data, $action ) {
		$results = array();
		$reports = array(
			'revenue'       => 0,
			'refund_amount' => 0,
			'orders_count'  => 0,
		);
		if ( ! empty( $all_data ) ) {
			foreach ( $all_data as $key => $value ) {
				$single_member = self::get_single_data( $value->ID );
				if ( $action == 'table' ) {
					if ( ! empty( $single_member['plan_details'] ) ) {
						$single_member['plan_name'] = $single_member['plan_details']['plan_name'];
					} else {
						$single_member['plan_name'] = '';
					}
					$single_member['new_mail_status'] = get_user_meta( $single_member['member_user'], NEW_MEM_MAIL_SENT, true );
				} elseif ( $action == 'report' ) {
					$order_details             = $single_member['order_info']['orders_details'];
					$reports['orders_count']  += $order_details['orders_count'];
					$reports['revenue']       += $order_details['revenue'];
					$reports['refund_amount'] += $order_details['refund_amount'];
				}

				array_push( $results, $single_member );
			}
		}
		if ( $action == 'report' ) {
			return $reports;
		} else {
			return $results;
		}
	}

	/**
	 * Single membership details
	 */
	public static function get_single_data( $id ) {
		$arr = array(
			'ID'             => '',
			'status'         => 'yes',
			'notes'          => '',
			'member_user'    => '',
			'member_plan_id' => '',
			'user_email'     => '',
			'start_date'     => '',
			'end_date'       => '',
			'expire_date'    => '',
		);

		if ( ! empty( $id ) ) {
			$arr['ID']             = $id;
			$arr['member_user']    = get_post_meta( $id, 'member_user', true );
			$arr['order_info']     = self::get_user_orders( $arr['member_user'] );
			$arr['expire_date']    = get_post_meta( $id, 'expire_date', true );
			$arr['member_plan_id'] = get_post_meta( $id, 'member_plan_id', true );
			$arr['start_date']     = get_post_field( 'post_date', $id );
			$arr['notes']          = get_post_field( 'notes', $id );
			$arr['end_date']       = self::membership_end_date( $id, $arr['member_plan_id'] );
			$arr['status']         = self::membership_status( $id );
			$user_info             = get_user_by( 'id', $arr['member_user'] );
			$arr['user_name']      = ! empty( $user_info ) ? $user_info->display_name : '';
			$arr['user_email']     = ! empty( $user_info ) ? $user_info->user_email : '';
			$plan_details          = get_user_meta( $arr['member_user'], MEM_PLAN_DETAILS, true );
			$arr['plan_details']   = PlanModel::instance()->plan_expire_status( $plan_details );
		}

		return $arr;
	}

	public static function get_user_orders( $user_id ) {
		$result = array(
			'orders_details' => array(),
			'orders_count'   => 0,
			'revenue'        => 0,
			'total_refund'   => 0,
			'refund_amount'  => 0,
		);
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $result;
		}
		$result['orders_details'] = Reports::get_orders_sum_amount_for( $user_id, '' );
		extract( Reports::get_orders_sum_amount_for( $user_id, '' ) );
		$result['orders_count']  = $orders_count;
		$result['revenue']       = wc_price( $revenue );
		$result['total_refund']  = $total_refund;
		$result['refund_amount'] = wc_price( $refund_amount );

		return $result;
	}

	/**
	 * Get user list
	 *
	 * @return array
	 */
	public static function user_list() {
		$user_list = array();
		$args      = array( 'fields' => array( 'ID', 'display_name' ) );
		$users     = get_users( $args );

		foreach ( $users as $key => $user ) {
			$full_name              = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
			$user_list[ $user->ID ] = empty( $full_name ) || $full_name == ' ' ? $user->display_name : $full_name;
		}

		return $user_list;
	}

	public static function is_non_member() {
		$non_member = true;
		if ( ! is_user_logged_in() ) {
			return $non_member;
		} elseif ( ! empty( wp_get_current_user() ) ) {
				$user_id = wp_get_current_user()->ID;
				return get_user_meta( $user_id, MEM_PLAN_DETAILS, true ) == '' ? true : false;
		} else {
			return $non_member;
		}
	}

	/**
	 * New member email body
	 *
	 * @param [type] $plan_id
	 * @param [type] $mail_body
	 */
	public static function new_member_mail_body( $plan_id, $mail_body = '' ) {
		$plan_name = get_post_meta( $plan_id, 'plan_name', true );
		$user_link = '<a href="' . esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) . 'create-members-pro/' ) . '" target="_blank">' . esc_html__( 'Membership Details', 'create-members' ) . '</a>';

		$default_body = '<p class="email-p">
			' . esc_html__( 'Thank you for being our member! You will get offer details of your membership here.', 'create-members' ) . '
			' . $user_link . '
		</p>';

		$message_body = $mail_body !== '' ? $mail_body : $default_body;
		$message_body = Helper::add_template_tags( $plan_name, $mail_body );

		return '
		<h2 class="email-title">' . esc_html__( 'Congratulations you are ', 'create-members' ) . ' ' . esc_html( $plan_name ) . ' ' . esc_html__( 'Member', 'create-members' ) . '</h2>
		' . $message_body . '
		';
	}

	/**
	 * Send Email
	 *
	 * @param [type] $plan_id
	 */
	public static function new_member_mail( $user_id, $plan_id ) {

		if ( empty( get_userdata( $user_id ) ) ) {
			return;
		}
		extract( Helper::get_settings() );

		$recipient = get_userdata( $user_id )->user_email;
		$args      = array(
			'data'      => array( 'mail_type' => 'new_member' ),
			'recipient' => $recipient,
			'subject'   => $new_member_subject,
			'title'     => $new_member_title,
			'message'   => self::new_member_mail_body( $plan_id, $new_member_message ),
		);

		$obj    = new Send_Email( $args );
		$result = $obj->send();
		update_user_meta( $user_id, NEW_MEM_MAIL_SENT, $result );

		return $result;
	}

	/**
	 * Status column
	 *
	 * @param [type] $value
	 */
	public static function status_col( $value, $col_name = 'status' ) {
		$col = '';
		if ( $col_name == 'new_mail_status' ) {
			$class      = ! empty( $value[ $col_name ] ) && $value[ $col_name ] == true ? 'success' : 'new-mem-resend';
			$text       = ! empty( $value[ $col_name ] ) && $value[ $col_name ] == true ? esc_html__( 'Sent', 'create-members' ) : esc_html__( 'Re-Send', 'create-members' );
			$data_value = ! empty( $value[ $col_name ] ) && $value[ $col_name ] == true ? '' : ' data-id="new-mem-resend" data-user_id="' . esc_attr( $value['member_user'] ) . '"
			 data-plan_id="' . esc_attr( $value['member_plan_id'] ) . '" ';
			$col        = '<span ' . $data_value . ' class="tag ' . esc_attr( $class ) . '">' . esc_html( $text ) . '</span>';
		}

		return $col;
	}

	/**
	 * Check Membership Status
	 *
	 * @param mixed $id
	 * @return string
	 */
	public static function membership_status( $id ) {
		$status     = 'yes';
		$start_date = get_the_date( 'Y-m-d', $id );
		$end_date   = get_post_meta( $id, 'end_date', true );
		$status     = get_post_meta( $id, 'status', true );
		if ( $status == 'cancel' ) {
			$status = 'cancel';
			update_post_meta( $id, 'status', $status );
			return $status;
		}

		if ( $end_date == '' || $end_date == 'unlimited' ) {
			$status = 'yes';
		} elseif ( date( 'Y-m-d', strtotime( $end_date ) ) < date( 'Y-m-d' ) ) {
			$status = 'expire';
		}
		update_post_meta( $id, 'status', $status );

		return $status;
	}

	/**
	 * date column
	 *
	 * @param [type] $date
	 */
	public static function format_date_col( $date ) {
		if ( $date == '' ) {
			return '-';
		}
		$str_date = strtotime( $date );
		return date_i18n( get_option( 'date_format' ), $str_date );
	}

	/**
	 * expiration text column
	 *
	 * @param [type] $date
	 */
	public static function membership_end_date_text( $date ) {
		if ( $date == '' ) {
			return '-';
		} elseif ( $date == 'unlimited' ) {
			return esc_html__( 'Unlimited', 'create-members' );
		} else {
			$str_date = strtotime( $date );
			return date_i18n( get_option( 'date_format' ), $str_date );
		}
	}


	/**
	 * Status column
	 *
	 * @param [type] $value
	 */
	public static function create_membership( $order, $user_id = null ) {
		if ( empty( $order ) ) {
			return;
		}
		$member_user = empty( $user_id ) ? $order->get_customer_id() : $user_id;
		$member_plan = self::mem_plan_details( $member_user );

		if ( ! empty( $member_plan ) ) {
			return;
		}

		extract( Helper::instance()->get_settings() );
		$plans    = PlanModel::get_all_plans( -1, false );
		$filtered = array();

		if ( $plan_cost == 'amount' ) {
			$cost     = (float) $order->get_subtotal();
			$filtered = array_filter(
				$plans,
				function ( $element ) use ( $cost ) {
					return ( (float) $element['price'] <= $cost );
				}
			);
		} elseif ( $plan_cost == 'product' ) {
			$order_ids = array();
			foreach ( $order->get_items() as $item_id => $item ) {
				array_push( $order_ids, $item->get_product_id() );
			}

			$filtered = array_filter(
				$plans,
				function ( $element ) use ( $order_ids ) {
					return in_array( $element['plan_product'], $order_ids );
				}
			);

		}

		self::save_frontend_member( $member_user, $filtered );
	}

	/**
	 * Save frontend member
	 *
	 * @param [type] $user_id
	 * @param [type] $filtered
	 * @return void
	 */
	private static function save_frontend_member( $user_id, $filtered ) {
		$result = false;
		if ( ! empty( $filtered ) ) {
			$plan                     = reset( $filtered );
			$params                   = array();
			$params['member_user']    = $user_id;
			$params['member_plan_id'] = $plan['ID'];
			$params['status']         = 'yes';
			$result                   = \Membership\Base\Actions::instance()->save_member( $params, false );
		}

		return $result;
	}

	/**
	 * Save plan details in member
	 *
	 * @param mixed $user_id
	 * @param mixed $plan_id
	 * @param mixed $member_id
	 * @return void
	 */
	public static function save_member_data( $user_id, $plan_id, $member_id ) {
		$member_plan = array(
			'plan_id'   => $plan_id,
			'member_id' => $member_id,
		);
		update_user_meta( $user_id, MEM_PLAN_DETAILS, $member_plan );
	}

	public static function mem_plan_details( $user_id ) {
		return get_user_meta( $user_id, MEM_PLAN_DETAILS, true );
	}

	/**
	 * Membership end date
	 *
	 * @param mixed $member_id
	 * @param mixed $plan_id
	 * @return string
	 */
	public static function membership_end_date( $member_id, $plan_id ) {
		$end_date = '';
		if ( empty( $plan_id ) ) {
			return $end_date;
		}
		$duration = get_post_meta( $plan_id, 'duration', true );
		if ( $duration == '' ) {
			$end_date = 'unlimited';
		}
		$expire_date = get_post_meta( $member_id, 'expire_date', true );
		if ( $expire_date == '' && $duration !== '' ) {
			$period     = get_post_meta( $plan_id, 'period', true );
			$durations  = ' +' . $duration . ' ' . $period;
			$start_date = get_post_field( 'post_date', $member_id );
			$start_date = date( 'Y-m-d', strtotime( $start_date ) );
			$end_date   = date( 'Y-m-d', strtotime( $start_date . $durations ) );
		} elseif ( $expire_date !== '' ) {
			$end_date = $expire_date;
		}

		update_post_meta( $member_id, 'end_date', $end_date );

		return $end_date;
	}
}
