<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Membership\Utils\Helper;

$hide_woo_block = $level_type == 'woo_modules' ? '' : 'd-none';
?>
<div class="woo_modules <?php echo esc_attr( $hide_woo_block ); ?>">
	<h2 class="mt-2 mb-0"><?php esc_html_e( 'Product Discount Section:', 'create-members' ); ?></h2>
	<p class="block-desc mb-2"><?php esc_html_e( 'Offer Discount to specific product or categories into Membership Plan', 'create-members' ); ?></p>
	<?php
	if ( file_exists( \CreateMembers::modules_dir() . 'plans/discounts.php' ) ) {
		include_once \CreateMembers::modules_dir() . 'plans/discounts.php';
	}
	?>
	<h2 class="mt-2 mb-0"><?php esc_html_e( 'Restrict Product/Price Section:', 'create-members' ); ?></h2>
	<p class="block-desc mb-2"><?php esc_html_e( 'ONLY MEMBERS can buy these products, view prices and can access the pages', 'create-members' ); ?></p>
	<?php
		$args = array(
			'label'           => esc_html__( 'Show Products to this Subscription', 'create-members' ),
			'id'              => 'restrict_products',
			'type'            => 'random',
			'selected'        => $restrict_products,
			'select_type'     => 'multiple',
			'condition_class' => '',
			'options'         => Helper::get_products(),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );

		$args = array(
			'label'           => esc_html__( 'Show Prices of the product', 'create-members' ),
			'id'              => 'restrict_prices',
			'type'            => 'random',
			'selected'        => $restrict_prices,
			'select_type'     => 'multiple',
			'condition_class' => '',
			'options'         => Helper::get_products(),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );

		$args = array(
			'label'           => esc_html__( 'Show Pages', 'create-members' ),
			'id'              => 'restrict_contents',
			'type'            => 'random',
			'selected'        => $restrict_contents,
			'select_type'     => 'multiple',
			'condition_class' => '',
			'options'         => Helper::get_content_pages(),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );

		?>

	<h2 class="mt-2 mb-0"><?php esc_html_e( 'Cart/Shipping Discount Section:', 'create-members' ); ?></h2>
	<p class="block-desc mb-2"><?php esc_html_e( 'Offer Discount on total spend into specific Membership Plan', 'create-members' ); ?></p>
	<?php
		$args = array(
			'label'   => esc_html__( 'Free Shipping', 'create-members' ),
			'id'      => 'free_shipping',
			'checked' => $free_shipping,
		);
		membership_checkbox_field( $args );
		?>
	<div class="input-group-field bg-white">
		<div class="form-label"><?php esc_html_e( 'Cart Discount', 'create-members' ); ?></div>
		<div class="input-group-wrapper input-group-3">
		<?php
			$args = array(
				'label'           => esc_html__( 'Cart Amount', 'create-members' ),
				'wrapper_class'   => 'group-block',
				'placeholder'     => '',
				'field_type'      => 'text',
				'id'              => 'cart_amount',
				'condition_class' => '',
				'value'           => $cart_amount,
			);
			membership_number_input_field( $args );

			$currency = class_exists( 'WooCommerce' ) ? get_woocommerce_currency_symbol() : '';
			$args     = array(
				'label'           => esc_html__( 'Discount Type', 'create-members' ),
				'id'              => 'cart_discount_type',
				'wrapper_class'   => 'group-block',
				'type'            => 'random',
				'selected'        => $cart_discount_type,
				'select_type'     => 'single',
				'condition_class' => '',
				'options'         => array(
					'percentage' => esc_html__( 'Percentage(%)', 'create-members' ),
					'fix'        => esc_html__( 'Fix Discount', 'create-members' ) . '(' . $currency . ')',
				),
			);
			membership_select_field( $args );

			$args = array(
				'label'           => esc_html__( 'Discount Amount', 'create-members' ),
				'wrapper_class'   => 'group-block',
				'placeholder'     => '',
				'field_type'      => 'text',
				'id'              => 'cart_discount',
				'condition_class' => '',
				'value'           => $cart_discount,
			);
			membership_number_input_field( $args );
			?>
		</div>    
	</div>
</div>