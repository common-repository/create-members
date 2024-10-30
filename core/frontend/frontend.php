<?php

namespace Membership\Core\Frontend;

defined( 'ABSPATH' ) || die();

use Membership\Core\Models\Members as MemberModel;
use Membership\Core\Models\Plans as PlanModel;
use Membership\Utils\Helper;
use Membership\Utils\Singleton;
use WP_User;

/**
 * Base Class
 *
 * @since 1.0.0
 */
class Frontend {

	use Singleton;

	/**
	 * Initialize all modules.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		extract( Helper::instance()->get_settings() );

		if ( $is_enable_membership == '' ) {
			return;
		}

		add_action( 'woocommerce_single_product_summary', array( $this, 'non_member_offer' ), 5 );
		add_action( 'woocommerce_shop_loop_item_title', array( $this, 'non_member_offer' ), 10 );
		add_filter( 'woocommerce_product_is_visible', array( $this, 'product_visible_by_membership' ), 10, 2 );
		add_filter( 'woocommerce_package_rates', array( $this, 'free_shipping_for_member' ), 100 );
		add_action( 'template_redirect', array( $this, 'hide_page_for_members' ) );
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'members_cart_discount' ), 20, 1 );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'send_email_customer' ), 20, 4 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'update_member_status_update' ), 10, 3 );
	}

	/**
	 * Create user
	 */
	private function create_user( $order_id, $package_id = null ) {
		$order        = wc_get_order( $order_id );
		$order_email  = $order->get_billing_email();
		$email_exists = email_exists( $order_email );
		$user_exists  = username_exists( $order_email );
		$user_id      = null;

		if ( $user_exists == false && $email_exists == false ) {
			$random_password = wp_generate_password();
			$first_name      = $order->get_billing_first_name();
			$last_name       = $order->get_billing_last_name();
			$role            = 'customer';

			$user_id = wp_insert_user(
				array(
					'user_email' => $order_email,
					'user_login' => $order_email,
					'user_pass'  => $random_password,
					'first_name' => $first_name,
					'last_name'  => $last_name,
					'role'       => $role,
				)
			);

			update_user_meta( $user_id, 'guest', 'yes' );
			update_user_meta( $user_id, 'wp_capabilities', array( 'customer' => true ) );
			wc_update_new_customer_past_orders( $user_id );

			// Get WP_User
			$user      = new WP_User( intval( $user_id ) );
			$reset_key = get_password_reset_key( $user );

			// Send the WC_email Customer Reset Password
			$wc_emails = WC()->mailer()->get_emails();
			$wc_emails['WC_Email_Customer_Reset_Password']->trigger( $user->user_login, $reset_key );

		} else {
			$user_id = get_current_user_id();
		}

		if ( ! empty( $user_id ) ) {
			$is_user_exist = get_posts(
				array(
					'post_type'      => MEMBERSHIP_MEMBER,
					'posts_per_page' => -1,
					'meta_query'     => array(
						array(
							'key'     => 'member_user',
							'value'   => $user_id,
							'compare' => '=',
						),
					),
				)
			);

			if ( ! empty( $is_user_exist ) ) {
				return;
			}

			if ( ! empty( $package_id ) ) {
				$params['member_user']    = $order->get_customer_id();
				$params['member_plan_id'] = $package_id;
				$params['status']         = 'yes';
				\Membership\Base\Actions::instance()->save_member( $params, false );
			} else {
				// create member
				MemberModel::create_membership( $order, $user_id );
			}
		}
	}


	/**
	 * Send Email to customer
	 */
	public function send_email_customer( $order, $sent_to_admin, $plain_text, $email ) {
		if ( empty( $this->get_customer_user_id( $order ) ) ) {
			return;
		}

		$user_id      = $this->get_customer_user_id( $order );
		$is_mail_sent = get_user_meta( $user_id, NEW_MEM_MAIL_SENT, true );
		$plan_details = MemberModel::mem_plan_details( $user_id );

		if ( $is_mail_sent == '' && $plan_details !== '' ) {
			echo MemberModel::new_member_mail_body( $plan_details['plan_id'], '' );
			update_user_meta( $user_id, NEW_MEM_MAIL_SENT, true );
		}
	}

	/**
	 * Assign / Update Membership Plan
	 *
	 * @param mixed $order_id
	 * @param mixed $old_order_status
	 * @param mixed $new_order_status
	 * @return void
	 */
	public function update_member_status_update( $order_id, $old_order_status, $new_order_status ) {
		$accepted_status = array( 'processing', 'completed' );
		$order           = wc_get_order( $order_id );
		$package_id      = '';
		foreach ( $order->get_items() as $item_id => $item ) {
			if ( ! is_null( $item->get_meta( '_package_id', true ) ) ) {
				$package_id = $item->get_meta( '_package_id', true );
				break;
			}
		}

		if ( ! empty( $package_id ) && in_array( $new_order_status, $accepted_status ) ) {
			$this->create_user( $order_id, $package_id );
		}
	}

	/**
	 * add COD charge
	 *
	 * @return void
	 */
	public function members_cart_discount( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		$discount = PlanModel::conditional_product( get_current_user_id(), 'cart_discount' );

		if ( empty( $cart ) || empty( $discount['cart_discount'] )
		|| empty( $discount['cart_amount'] ) ) {
			return;
		}

		$dis_amount = '';
		if ( $discount['cart_discount_type'] == 'percentage' ) {
			$dis_amount = ( (float) $discount['cart_discount'] * $cart->get_subtotal() ) / 100;
		} elseif ( $discount['cart_discount_type'] == 'fix' ) {
			$dis_amount = (float) $discount['cart_discount'];
		}

		if ( ! empty( $dis_amount ) && $cart->get_subtotal() >= (float) $discount['cart_amount'] ) {
			$label = esc_html__( 'Membership Discount', 'create-members' );
			$cart->add_fee( $label, -$dis_amount );
		}
	}

	/**
	 * Hide pages for non members
	 *
	 * @param [type] $query
	 */
	public function hide_page_for_members() {
		$content_to_hide = $this->hide_product_content( 'restrict_contents' );
		if ( empty( $content_to_hide ) || empty( $content_to_hide['hide_prod_ids'] ) ) {
			return;
		}
		if ( is_page( $content_to_hide['hide_prod_ids'] ) ) {
			extract( Helper::instance()->get_settings() );
			$redirect = $redirect_content == '' ? home_url() : get_permalink( $redirect_content );
			wp_redirect( $redirect );
			exit;
		}
	}

	/**
	 * Free shipping for member
	 *
	 * @param [type] $rates
	 */
	public function free_shipping_for_member( $rates ) {
		$free_shipping = PlanModel::conditional_product( get_current_user_id(), 'free_shipping' );
		$is_free_ship  = ! empty( $free_shipping['free_shipping'] ) && $free_shipping['free_shipping'] == 'yes' ? true : false;
		if ( ! $is_free_ship ) {
			return $rates;
		}

		foreach ( $rates as $rate_key => $rate ) {
			if ( 'free_shipping' !== $rate->method_id ) {
				$rates[ $rate_key ]->cost = 0;
				break;
			}
		}

		return $rates;
	}

	public function hide_product_content( $col ) {
		$to_hide = array();
		if ( is_user_logged_in() ) {
			$to_hide = PlanModel::conditional_product( get_current_user_id(), $col );
		} else {
			$to_hide = PlanModel::conditional_product( null, $col );
		}

		return $to_hide;
	}

	/**
	 * Hide product by membership
	 *
	 * @param [type] $visible
	 * @param [type] $product_id
	 */
	public function product_visible_by_membership( $visible, $product_id ) {
		$products_to_hide = array();
		if ( is_user_logged_in() ) {
			$products_to_hide = PlanModel::conditional_product( get_current_user_id(), 'restrict_products' );
		} else {
			$products_to_hide = PlanModel::conditional_product( null, 'restrict_products' );
		}
		if ( is_array( $products_to_hide ) &&
		! empty( $products_to_hide['hide_prod_ids'] ) &&
		in_array( $product_id, $products_to_hide['hide_prod_ids'], true ) ) {
			$visible = false;
		}

		return $visible;
	}

	/**
	 * Non member offer
	 */
	public function non_member_offer() {
		extract( Helper::instance()->get_settings() );
		$is_non_mem = MemberModel::is_non_member();
		if ( $non_member_offer == 'yes' && $is_non_mem ) {
			$price_to_hide = $this->hide_product_content( 'restrict_prices' );
			if ( empty( $price_to_hide['hide_prod_ids'] ) ||
			in_array( get_the_ID(), $price_to_hide['hide_prod_ids'], true ) == false ) {
				$msg = '<div class="non_member_msg">' . $non_member_msg . '</div>';
				echo Helper::kses( $msg );
			}
		}
	}

	public function get_customer_user_id( $order ) {
		global $post;

		if ( ! is_a( $order, 'WC_Order' ) ) {
			$order_id = $post->ID;
			$order    = wc_get_order( $order_id );
		} else {
			$order_id = $order->get_id();
		}

		$user_id = $order->get_user_id();

		return $user_id;
	}
}
