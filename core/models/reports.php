<?php

namespace Membership\Core\Models;

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Singleton;
use Membership\Core\Models\Plans as PlanModel;
use Membership\Core\Models\Members as MembersModel;
use Membership\Utils\Helper;

class Reports {

	use Singleton;

	/**
	 * Membership Summary
	 */
	public static function membership_summary() {
		$active_plan      = PlanModel::get_plans_by_meta(
			MEMBER_PLAN,
			array(
				'key'     => 'status',
				'value'   => 'yes',
				'compare' => '=',
			)
		);
		$inactive_plan    = PlanModel::get_plans_by_meta(
			MEMBER_PLAN,
			array(
				'key'     => 'status',
				'value'   => 'yes',
				'compare' => '!=',
			)
		);
		$active_members   = PlanModel::get_plans_by_meta(
			MEMBERSHIP_MEMBER,
			array(
				'key'     => 'status',
				'value'   => 'yes',
				'compare' => '=',
			)
		);
		$inactive_members = PlanModel::get_plans_by_meta(
			MEMBERSHIP_MEMBER,
			array(
				'relation' => 'OR',
				array(
					'key'     => 'status',
					'value'   => 'null',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'status',
					'value'   => MEMBER_CANCEL_STATUS,
					'compare' => '=',
				),
			)
		);

		$subscription_report = self::subscription_report();
		$members             = self::get_format_member_ids();
		$today_sales_report  = self::get_orders_sum_amount_for( $members, 'today' );
		$month_sales_report  = self::get_orders_sum_amount_for( $members, '1 month' );
		$year_sales_report   = self::get_orders_sum_amount_for( $members, '1 year' );
		$sales_report        = array(
			array(
				'label'   => esc_html__( 'Today', 'create-members' ),
				'sales'   => $today_sales_report['orders_count'],
				'revenue' => wc_price( $today_sales_report['revenue'] ),
				'refund'  => wc_price( $today_sales_report['refund_amount'] ),
			),
			array(
				'label'   => esc_html__( 'This Month', 'create-members' ),
				'sales'   => $month_sales_report['orders_count'],
				'revenue' => wc_price( $month_sales_report['revenue'] ),
				'refund'  => wc_price( $month_sales_report['refund_amount'] ),
			),
			array(
				'label'   => esc_html__( 'This Year', 'create-members' ),
				'sales'   => $year_sales_report['orders_count'],
				'revenue' => wc_price( $year_sales_report['revenue'] ),
				'refund'  => wc_price( $year_sales_report['refund_amount'] ),
			),
		);

		$members_per_level = array(
			'level'          => array(),
			'active_members' => array(),
		);
		$plans_by_month    = array(
			'key'     => 'status',
			'value'   => 'yes',
			'compare' => '=',
		);
		$members_level     = PlanModel::get_all_plans( -1, false, false, $plans_by_month );

		foreach ( $members_level as $key => $value ) {
			array_push( $members_per_level['level'], $value['plan_name'] . '(' . $value['active_members'] . ')' );
			array_push( $members_per_level['active_members'], $value['active_members'] );
		}

		$get_data = MembersModel::instance()->get_all_data( -1, 'report' );

		$summary = array(
			'total_plan'          => count( (array) PlanModel::get_all_plans( -1, false ) ),
			'total_members'       => count( (array) MembersModel::instance()->get_all_data( -1, false ) ),
			'orders_count'        => $get_data['orders_count'],
			'total_sales'         => wc_price( $get_data['revenue'] ),
			'total_refunds'       => wc_price( $get_data['refund_amount'] ),
			'active_plan'         => count( $active_plan ),
			'inactive_plan'       => count( $inactive_plan ),
			'active_members'      => count( $active_members ),
			'inactive_members'    => count( $inactive_members ),
			'subscription_report' => $subscription_report,
			'sales_report'        => $sales_report,
			'members_per_level'   => $members_per_level,
			'bar_lines'           => self::get_month_wise_revenue_refund(),
		);

		return $summary;
	}

	public static function get_format_member_ids() {
		$members = self::get_members_id();
		if ( ! empty( $members ) ) {
			$members = implode( "','", $members );
		} else {
			$members = '';
		}
		return $members;
	}

	public static function subscription_report() {
		return array(
			array(
				'label'  => esc_html__( 'Today', 'create-members' ),
				'signup' => self::members_report_by_period( 'today' ),
				'cancel' => self::members_report_by_period(
					'today',
					array(
						array(
							'key'     => 'status',
							'value'   => MEMBER_CANCEL_STATUS,
							'compare' => '=',
						),
					)
				),
				'expire' => self::members_report_by_period(
					'today',
					array(
						array(
							'key'     => 'status',
							'value'   => 'expire',
							'compare' => '=',
						),
					)
				),
			),
			array(
				'label'  => esc_html__( 'This Month', 'create-members' ),
				'signup' => self::members_report_by_period( 'month' ),
				'cancel' => self::members_report_by_period(
					'month',
					array(
						array(
							'key'     => 'status',
							'value'   => MEMBER_CANCEL_STATUS,
							'compare' => '=',
						),
					)
				),
				'expire' => self::members_report_by_period(
					'month',
					array(
						array(
							'key'     => 'status',
							'value'   => 'expire',
							'compare' => '=',
						),
					)
				),
			),
			array(
				'label'  => esc_html__( 'This Year', 'create-members' ),
				'signup' => self::members_report_by_period( 'year' ),
				'cancel' => self::members_report_by_period(
					'year',
					array(
						array(
							'key'     => 'status',
							'value'   => MEMBER_CANCEL_STATUS,
							'compare' => '=',
						),
					)
				),
				'expire' => self::members_report_by_period(
					'year',
					array(
						array(
							'key'     => 'status',
							'value'   => 'expire',
							'compare' => '=',
						),
					)
				),
			),
		);
	}

	public static function members_report_by_period( $period, $meta = null ) {
		$args = array(
			'column'    => 'post_date',
			'inclusive' => true,
		);
		if ( $period == 'today' ) {
			$args['after'] = $period;
		} elseif ( $period == 'month' ) {
			$args['date_query'] = array(
				array(
					'month' => date( 'm' ),
				),
			);
		} elseif ( $period == 'year' ) {
			$args['date_query'] = array(
				array( 'year' => date( 'Y' ) ),
			);
		}

		return count(
			PlanModel::get_plans_by_meta( MEMBERSHIP_MEMBER, $meta, - 1, $args )
		);
	}
	public static function get_orders_sum_amount_for( $user_id, $period = '', $status = array( 'wc-completed', 'wc-processing' ) ) {
		global $wpdb;
		$date       = '';
		$orders_sql = '';
		$status     = implode( ',', $status );
		if ( $period !== '' ) {
			$date = $period == 'today' ? date( 'Y-m-d' ) : date( 'Y-m-d H:i:s', strtotime( '- ' . $period ) );
		}
		$orders_sql = 'AND date_created_gmt >= "' . $date . '"';

		$user_id = "'" . $user_id . "'";
		$orders  = $wpdb->get_row(
			" SELECT 
			SUM(total_amount) AS revenue,
			COUNT(total_amount) AS orders_count
			FROM {$wpdb->prefix}wc_orders
			WHERE status IN ('wc-completed','wc-processing') AND customer_id IN ($user_id) $orders_sql
		",
			ARRAY_A
		);

		$orders_sql = 'AND o.date_created_gmt >= "' . $date . '"';
		$refund     = $wpdb->get_row(
			"
			SELECT 
			SUM(r.total_amount) AS refund_amount,
			COUNT(r.total_amount) AS total_refund
			FROM {$wpdb->prefix}wc_orders r
			LEFT JOIN {$wpdb->prefix}wc_orders o ON r.parent_order_id = o.id
			WHERE r.type = 'shop_order_refund' AND r.status IN ('wc-completed','wc-processing')
			AND o.customer_id IN ($user_id) $orders_sql
		",
			ARRAY_A
		);

		return array(
			'total_refund'  => $refund['total_refund'],
			'refund_amount' => abs( $refund['refund_amount'] ),
			'revenue'       => ! empty( $orders['revenue'] ) ? $orders['revenue'] : 0,
			'orders_count'  => ! empty( $orders['orders_count'] ) ? $orders['orders_count'] : 0,
		);
	}

	/**
	 * Get all members id's
	 *
	 * @return array
	 */
	public static function get_members_id() {
		$ids     = array();
		$members = MembersModel::instance()->get_all_data( -1, false );
		foreach ( $members as $key => $value ) {
			array_push( $ids, $value['member_user'] );
		}

		return $ids;
	}
	public static function get_month_wise_revenue_refund() {
		$months       = array(
			'January'   => esc_html__( 'January', 'create-members' ),
			'February'  => esc_html__( 'February', 'create-members' ),
			'March'     => esc_html__( 'March', 'create-members' ),
			'April'     => esc_html__( 'April', 'create-members' ),
			'May'       => esc_html__( 'May', 'create-members' ),
			'June'      => esc_html__( 'June', 'create-members' ),
			'July'      => esc_html__( 'July', 'create-members' ),
			'August'    => esc_html__( 'August', 'create-members' ),
			'September' => esc_html__( 'September', 'create-members' ),
			'October'   => esc_html__( 'October', 'create-members' ),
			'November'  => esc_html__( 'November', 'create-members' ),
			'December'  => esc_html__( 'December', 'create-members' ),
		);
		$results      = array(
			'month'   => array_keys( $months ),
			'revenue' => array(),
			'refund'  => array(),
		);
		$order_month  = array();
		$refund_month = array();

		global $wpdb;
		$members = self::get_format_member_ids();

		$orders = $wpdb->get_results(
			$wpdb->prepare(
				"
        SELECT 
        MONTHNAME(date_created_gmt) AS month,
        SUM(total_amount) AS revenue
        FROM {$wpdb->prefix}wc_orders
        WHERE status IN ('wc-completed','wc-processing') 
        AND customer_id IN (%d) 
        GROUP BY month
        ",
				$members
			),
			ARRAY_A
		);

		$refunds = $wpdb->get_results(
			$wpdb->prepare(
				"
        SELECT 
        MONTHNAME(r.date_created_gmt) AS month,
        SUM(r.total_amount) AS refund_amount
        FROM {$wpdb->prefix}wc_orders r
        LEFT JOIN {$wpdb->prefix}wc_orders o ON r.parent_order_id = o.id
        WHERE r.type = 'shop_order_refund' AND r.status IN ('wc-completed','wc-processing')
        AND o.customer_id IN (%d) 
        GROUP BY month
        ",
				$members
			),
			ARRAY_A
		);

		foreach ( $orders as $value ) {
			$order_month[ $value['month'] ] = $value['revenue'];
		}
		foreach ( $refunds as $value ) {
			$refund_month[ $value['month'] ] = $value['refund_amount'];
		}

		foreach ( $months as $month ) {
			if ( ! empty( $order_month[ $month ] ) ) {
				array_push( $results['revenue'], abs( $order_month[ $month ] ) );
			} else {
				array_push( $results['revenue'], 0 );
			}

			if ( ! empty( $refund_month[ $month ] ) ) {
				array_push( $results['refund'], abs( $refund_month[ $month ] ) );
			} else {
				array_push( $results['refund'], 0 );
			}
		}

		return $results;
	}
}
