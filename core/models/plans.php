<?php

namespace Membership\Core\Models;
use MembershipPro\Core\Modules\RecurringLevel\UM_Subscription_Product;

defined( 'ABSPATH' ) || exit;

use Membership\Utils\Singleton;
use Membership\Core\Models\Members as MembersModel;
use Membership\Utils\Helper;

class Plans {

	use Singleton;

	public static function get_all_plans( $limit, $action = true, $select2 = false, $meta_data = array() ) {
		$args = array(
			'post_type'      => MEMBER_PLAN,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
		);

		if ( ! empty( $meta_data ) ) {
			$args['meta_query'] = $meta_data;
		}

		$all_plans = get_posts( $args );

		if ( $select2 ) {
			$select_arr = array();
			$data       = self::get_data( $all_plans, $action );
			foreach ( $data as $key => $value ) {
				if ( $value['status'] == 'yes' ) {
					$select_arr[ $value['ID'] ] = $value['plan_name'];
				}
			}
			return $select_arr;
		}
		return self::get_data( $all_plans, $action );
	}

	/**
	 * Get rules data
	 *
	 * @param [type] $all_plans
	 * @return array
	 */
	public static function get_data( $all_plans, $action ) {
		$results = array();
		if ( ! empty( $all_plans ) ) {
			foreach ( $all_plans as $key => $value ) {
				$single_plan = self::get_plan( $value->ID );
				if ( $action ) {
					$single_plan['actions']     = '
					<a class="action-link" href="' . esc_url( admin_url() . 'admin.php?page=um-members&plan=' . $value->ID ) . '">' . esc_html__( 'View Members', 'create-members' ) . '</a>
				';
					$single_plan['status']      = self::status_col( $single_plan );
					$single_plan['access_type'] = self::plan_access_col( $single_plan );
				}

				array_push( $results, $single_plan );
			}
		}

		return $results;
	}

	public static function plan_markup_data( $single_plan ) {
		$html = '';
		foreach ( $single_plan as $key => $value ) {
			$check_value = $value == '' ? '""' : $value;
			$plan_value  = is_array( $check_value ) ? implode( ',', $check_value ) : $check_value;
			$html       .= 'data-' . $key . '=' . $plan_value . ' ';
		}

		return $html;
	}

	public static function get_plan( $id ) {
		$rule_arr = array(
			'ID'                            => '',
			'plan_name'                     => '',
			'status'                        => 'yes',
			'price'                         => '',
			'cart_discount'                 => '',
			'level_type'                    => 'woo_modules',
			'plan_product'                  => '',
			'access_type'                   => '',
			'expire_date'                   => '',
			'restrict_products'             => array(),
			'description'                   => '',
			'restrict_prices'               => array(),
			'restrict_contents'             => array(),
			'cart_amount'                   => '',
			'cart_discount_type'            => 'fix',
			'product_discount'              => 'yes',
			'discount_label'                => array(),
			'discount_in'                   => 'product',
			'filter_by_products'            => '',
			'filter_by_category'            => array(),
			'discount_type'                 => 'fixed_product',
			'discount_number'               => '',
			'free_shipping'                 => '',
			'duration'                      => '',
			'period'                        => '',
			'subscription_price'            => '',
			'wp_hide_pages'                 => array(),
			'post_categories'               => array(),
			'single_post'                   => array(),
			'_subscription_length'          => 0,
			'_subscription_price'           => 0,
			'_subscription_period_interval' => 1,
			'_subscription_period'          => 'month',
			'recurring_subscription'        => 'no',
		);

		if ( ! empty( $id ) ) {
			$rule_arr['ID']                            = $id;
			$rule_arr['active_members']                = self::active_member_per_level( $id );
			$rule_arr['level_type']                    = get_post_meta( $id, 'level_type', true );
			$rule_arr['wp_hide_pages']                 = get_post_meta( $id, 'wp_hide_pages', true );
			$rule_arr['post_categories']               = get_post_meta( $id, 'post_categories', true );
			$rule_arr['plan_name']                     = get_post_meta( $id, 'plan_name', true );
			$rule_arr['recurring_level_txt']           = self::recurring_level_txt( $id );
			$rule_arr['description']                   = get_post_meta( $id, 'description', true );
			$rule_arr['status']                        = get_post_meta( $id, 'status', true );
			$rule_arr['price']                         = self::get_plan_price( $id );
			$rule_arr['subscription_price']            = get_post_meta( $id, 'subscription_price', true );
			$rule_arr['plan_product']                  = get_post_meta( $id, 'plan_product', true );
			$rule_arr['access_type']                   = get_post_meta( $id, 'access_type', true );
			$rule_arr['expire_date']                   = get_post_meta( $id, 'expire_date', true );
			$rule_arr['durations']                     = self::format_duration( $id );
			$rule_arr['duration']                      = get_post_meta( $id, 'duration', true );
			$rule_arr['period']                        = get_post_meta( $id, 'period', true );
			$rule_arr['restrict_products']             = get_post_meta( $id, 'restrict_products', true );
			$rule_arr['restrict_prices']               = get_post_meta( $id, 'restrict_prices', true );
			$rule_arr['restrict_contents']             = get_post_meta( $id, 'restrict_contents', true );
			$rule_arr['free_shipping']                 = get_post_meta( $id, 'free_shipping', true );
			$rule_arr['cart_amount']                   = get_post_meta( $id, 'cart_amount', true );
			$rule_arr['cart_discount']                 = get_post_meta( $id, 'cart_discount', true );
			$rule_arr['cart_discount_type']            = get_post_meta( $id, 'cart_discount_type', true );
			$rule_arr['product_discount']              = get_post_meta( $id, 'product_discount', true );
			$rule_arr['discount_label']                = get_post_meta( $id, 'discount_label', true );
			$rule_arr['discount_in']                   = get_post_meta( $id, 'discount_in', true );
			$rule_arr['filter_by_products']            = get_post_meta( $id, 'filter_by_products', true );
			$rule_arr['filter_by_category']            = get_post_meta( $id, 'filter_by_category', true );
			$rule_arr['discount_type']                 = get_post_meta( $id, 'discount_type', true );
			$rule_arr['discount_number']               = get_post_meta( $id, 'discount_number', true );
			$rule_arr['single_post']                   = get_post_meta( $id, 'single_post', true );
			$rule_arr['_subscription_length']          = get_post_meta( $id, '_subscription_length', true );
			$rule_arr['_subscription_price']           = get_post_meta( $id, '_subscription_price', true );
			$rule_arr['_subscription_period_interval'] = get_post_meta( $id, '_subscription_period_interval', true );
			$rule_arr['_subscription_period']          = get_post_meta( $id, '_subscription_period', true );
			$rule_arr['recurring_subscription']        = get_post_meta( $id, 'recurring_subscription', true );
		}

		return $rule_arr;
	}

	public static function recurring_level_txt( $id ) {
		$html      = '';
		$recurring = get_post_meta( $id, 'recurring_subscription', true );
		if ( $recurring == 'yes' ) {
			$html .= ' ' . esc_html__( 'then', 'create-members' );
			$html .= ' ' . get_post_meta( $id, '_subscription_price', true ) . Helper::currency_symbol();
			$html .= ' ' . esc_html__( 'per', 'create-members' );
			$html .= ' ' . get_post_meta( $id, '_subscription_period_interval', true );
			$html .= ' ' . get_post_meta( $id, '_subscription_period', true );
		}

		return $html;
	}

	public static function get_plan_price( $id ) {
		$level_type = get_post_meta( $id, 'level_type', true );
		$price      = '';

		if ( $level_type == '' || $level_type == 'woo_modules' ) {
			extract( Helper::get_settings() );
			switch ( $plan_cost ) {
				case 'amount':
					$price = get_post_meta( $id, 'price', true );
					break;
				case 'subscription':
					$price = get_post_meta( $id, 'subscription_price', true );
					break;
				case 'product':
					$product_id = get_post_meta( $id, 'plan_product', true );
					if ( ! empty( $product_id ) ) {
						$product = wc_get_product( $product_id );
						$price   = ! empty( $product ) ? $product->get_price() : '';
					}
					break;
				default:
					$price = get_post_meta( $id, 'price', true );
					break;
			}
		} else {
			$price = get_post_meta( $id, 'subscription_price', true );
		}

		return $price;
	}

	public static function is_plan_exist() {
		$plan_desc = '';
		$plans     = self::get_all_plans( -1, false );

		if ( count( $plans ) == 0 ) {
			$plan_desc = esc_html__( 'Create New plan from ' ) .
			'<a href="' . esc_url( admin_url() . '?page=um-plans' ) . '"
			target="_blank" >' . esc_html__( 'Plans', 'create-members' ) . '</a>';
		} else {
			$plan_desc = esc_html__( 'Assign Member to Active Subscription', 'create-members' );
		}

		return $plan_desc;
	}

	/**
	 * Status column
	 *
	 * @param [type] $value
	 */
	public static function status_col( $value, $col_name = 'status' ) {
		$col = '';
		if ( empty( $value[ $col_name ] ) ) {
			return $col;
		}

		if ( $col_name == 'new_mail_status' ) {
			$class = $value[ $col_name ] == true ? 'success' : 'error';
			$text  = $value[ $col_name ] == true ? esc_html__( 'Active', 'create-members' ) : esc_html__( 'Active', 'create-members' );
			$col   = '<span class="tag "' . esc_attr( $class ) . '"">' . esc_html( $text ) . '</span>';
		} elseif ( $value[ $col_name ] == 'yes' ) {
				$col = '<span class="tag success">' . esc_html__( 'Active', 'create-members' ) . '</span>';
		} elseif ( $value[ $col_name ] == 'cancel' ) {
			$col = '<span class="tag error">' . esc_html__( 'Cancel', 'create-members' ) . '</span>';
		} elseif ( $value[ $col_name ] == 'expire' ) {
			$col = '<span class="tag error">' . esc_html__( 'Expired', 'create-members' ) . '</span>';
		} else {
			$col = '<span class="tag error">' . esc_html__( 'InActive', 'create-members' ) . '</span>';
		}

		return $col;
	}

	/**
	 * Status column
	 *
	 * @param [type] $value
	 */
	public static function plan_status_col( $value ) {
		$cls = ! empty( $value['plan_status'] ) && $value['plan_status'] == 'expired' ? 'error' : 'success';
		$col = '<div class="tag ' . $cls . '">' . $value['plan_status_text'] . '</div>';

		return $col;
	}

	/**
	 * Access Type column
	 *
	 * @param [type] $value
	 */
	public static function plan_access_col( $value ) {
		$col = '';

		if ( $value['access_type'] == 'limited' ) {
			$col = esc_html__( 'Limited:', 'create-members' )
			. '<span class="ml-1 plan_expire_date">' . $value['expire_date'] . '</span>';
		} else {
			$col = esc_html__( 'Lifetime', 'create-members' );
		}

		return $col;
	}

	public static function plan_expire_status( $plan_details ) {
		$member_plan = array();
		if ( ! empty( $plan_details ) ) {
			$member_plan = self::get_plan( $plan_details['plan_id'] );
			if ( ! empty( $member_plan ) && $member_plan['access_type'] == 'limited' ) {
				$today       = date( 'Y-m-d', strtotime( date( 'Y-m-d' ) ) );
				$expire_date = date( 'Y-m-d', strtotime( $member_plan['expire_date'] ) );
				if ( $expire_date < $today ) {
					$member_plan['plan_status']      = 'expired';
					$member_plan['plan_status_text'] = esc_html__( 'Expired', 'create-members' );
				} else {
					$member_plan['plan_status']      = 'active';
					$member_plan['plan_status_text'] = esc_html__( 'Active', 'create-members' );
				}
			} else {
				$member_plan['plan_status']      = 'active';
				$member_plan['plan_status_text'] = esc_html__( 'Active', 'create-members' );
			}
			$member_plan['member_status'] = get_post_meta( $plan_details['member_id'], 'status', true );
			$member_plan['member_id']     = $plan_details['member_id'];

		}

		return $member_plan;
	}

	public static function conditional_product( $user_id = null, $col = '' ) {
		$hide_result = array();
		$result      = array(
			'hide_prod_ids'      => $hide_result,
			'hide_price_txt'     => '',
			'free_shipping'      => '',
			'cart_discount'      => '',
			'cart_discount_type' => '',
			'cart_amount'        => '',
			'plan_data'          => '',
		);

		extract( Helper::instance()->get_settings() );
		$plans = self::get_all_plans( -1, false, false );

		if ( empty( $plans ) ) {
			return $plans;
		}
		foreach ( $plans as $key => $value ) {
			$plan_id    = $value['ID'];
			$status     = get_post_meta( $plan_id, 'status', true );
			$level_type = get_post_meta( $plan_id, 'level_type', true );
			if ( $level_type == 'woo_modules' && $status == 'yes' ) {
				if ( ! empty( $user_id ) ) {
					$meta_data = array(
						array(
							'key'     => 'member_user',
							'value'   => $user_id,
							'compare' => '=',
						),
						array(
							'key'     => 'member_plan_id',
							'value'   => $plan_id,
							'compare' => '=',
						),
					);
					$is_exist  = MembersModel::instance()->get_all_data( -1, false, $meta_data );
					if ( empty( $is_exist ) && $col !== '' ) {
						$hide_result = self::restrict_product_by_plan( $plan_id, $col, $hide_result );
					} else {
						$result['plan_data']          = self::get_plan( $plan_id );
						$result['free_shipping']      = get_post_meta( $plan_id, 'free_shipping', true );
						$result['cart_discount']      = get_post_meta( $plan_id, 'cart_discount', true );
						$result['cart_discount_type'] = get_post_meta( $plan_id, 'cart_discount_type', true );
						$result['cart_amount']        = get_post_meta( $plan_id, 'cart_amount', true );
					}
				} else {
					$hide_result = $col !== '' ? self::restrict_product_by_plan( $plan_id, $col, $hide_result ) : array();
				}
			}
		}

		$result['hide_prod_ids']  = $hide_result;
		$result['hide_price_txt'] = $hide_price_txt;

		return $result;
	}

	private static function restrict_product_by_plan( $plan_id, $col, $hide_result ) {
		$not_restrict = array( 'free_shipping', 'cart_discount' );
		if ( in_array( $col, $not_restrict ) ) {
			return $hide_result;
		}

		$products = get_post_meta( $plan_id, $col, true );

		if ( ! empty( $products ) ) {
			foreach ( $products as $key => $product_id ) {
				array_push( $hide_result, (int) $product_id );
			}
		}

		return $hide_result;
	}

	public static function get_plans_by_meta( $post_type, $data = array(), $limit = -1, $date_query = null ) {
		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
		);
		if ( ! empty( $date_query ) ) {
			$args['date_query'] = $date_query;
		}
		if ( ! empty( $data ) ) {
			$args['meta_query'] = array( $data );
		}

		return get_posts( $args );
	}

	public static function active_member_per_level( $plan_id ) {
		$meta = array(
			'relation' => 'AND',
			array(
				'key'     => 'member_plan_id',
				'value'   => $plan_id,
				'compare' => '=',
			),
			array(
				'key'     => 'status',
				'value'   => 'yes',
				'compare' => '=',
			),
		);

		$get_data = self::get_plans_by_meta( MEMBERSHIP_MEMBER, $meta, -1 );

		return count( $get_data );
	}



	/**
	 * All modules for membership
	 *
	 * @return array
	 */
	public static function packages_by_modules( $modules = '', $type = 'select' ) {
		$meta = array();
		$data = array();

		if ( ! class_exists( 'WooCommerce' ) ) {
			return $data;
		}
		if ( $modules !== '' ) {
			$meta = array(
				'key'     => 'level_type',
				'value'   => $modules,
				'compare' => '=',
			);
		}
		
		$packages = self::get_plans_by_meta( MEMBER_PLAN, $meta, -1 );

		if ( empty( $packages ) ) {
			return $data;
		}
		extract( Helper::instance()->get_settings() );
		foreach ( $packages as $key => $value ) {
			$id = $value->ID;
			if ($plan_cost == 'product') {
				$product_id = get_post_meta( $id, 'plan_product', true );
				if ( ! empty( $product_id ) ) {
					$product = wc_get_product( $product_id );
					if ( $type == 'select' ) {
						if (class_exists('CreateMembersPro')) {
							$price   = \MembershipPro\Core\Modules\RecurringLevel\Includes\Subscriptions_Product::get_price_string( $product , array( 'price' => $product->get_price() ) );
						}else{
							$price   = wc_price( $product->get_price() );
						}
						$data[ $id ] = $product->get_name().' ' . $price;
					}else{
						$data[ $key ]['id']          = $id;
						$data[ $key ]['plan_name']   = get_post_meta( $id, 'plan_name', true );
						$data[ $key ]['description'] = get_post_meta( $id, 'description', true );
						$data[ $key ]['price']       = $product->get_price();
					}
				}
			} 
			else {
				if ( get_post_meta( $id, 'status', true ) == 'yes' ) {
					if ( $type == 'select' ) {
						$recurring = get_post_meta( $id, 'recurring_subscription', true );
						$subscription_price =  get_post_meta( $id, 'subscription_price', true ) ;
						if ( $recurring == '' ) {
							$data[ $id ] = get_post_meta( $id, 'plan_name', true ) .
							' - ' . $subscription_price .
							' - ' . self::format_duration( $id );
						} else {
							$data[ $id ] = get_post_meta( $id, 'plan_name', true ) .
							' - ' . $subscription_price .
							' ' . esc_html__( 'now', 'create-members' ) .
							self::recurring_level_txt( $id );
						}
					} else {
						$data[ $key ]['id']          = $id;
						$data[ $key ]['plan_name']   = get_post_meta( $id, 'plan_name', true );
						$data[ $key ]['description'] = get_post_meta( $id, 'description', true );
						$data[ $key ]['price']       = get_post_meta( $id, 'subscription_price', true );
					}
				}
			}
		}

		return $data;
	}


	/**
	 * Format duration
	 *
	 * @param mixed $id
	 * @return mixed
	 */
	public static function format_duration( $id ) {
		return get_post_meta( $id, 'duration', true ) == '' ?
		esc_html__( 'Unlimited', 'create-members' )
		: get_post_meta( $id, 'duration', true ) . ' ' . self::format_period( $id );
	}

	/**
	 * Format duration type
	 *
	 * @param mixed $id
	 * @return mixed
	 */
	public static function format_period( $id ) {
		$duration = get_post_meta( $id, 'duration', true );
		$period   = get_post_meta( $id, 'period', true );
		$result   = $duration == '' ? '' : $period . '(s)';

		return $result;
	}

	/**
	 * Set package price
	 */
	public static function set_package_price( $id, $price ) {
		update_post_meta( $id, '_regular_price', $price );
		update_post_meta( $id, '_price', $price );
		update_post_meta( $id, '_price', $price );
	}

	/**
	 * Set package price
	 */
	public static function membership_duration( $id ) {
		$status   = false;
		$duration = get_post_meta( $id, 'duration', true );
		if ( $duration == '' ) {
			return $status;
		}
	}
}
