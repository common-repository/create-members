<?php

use Membership\Utils\Helper;

$args = array(
	'label'   => esc_html__( 'Enable Discount On Product/Category', 'create-members' ),
	'id'      => 'product_discount',
	'disable' => Helper::is_pro_active() ? false : true,
	'checked' => $product_discount,
);

membership_checkbox_field( $args );

$args = array(
	'label'       => esc_html__( 'Discount Label', 'create-members' ),
	'placeholder' => esc_html__( 'Enter discount label', 'create-members' ),
	'field_type'  => 'text',
	'id'          => 'discount_label',
	'disable'     => Helper::is_pro_active() ? false : true,
	'value'       => $discount_label,
);
membership_number_input_field( $args );

$args = array(
	'label'    => esc_html__( 'Discount In', 'create-members' ),
	'id'       => 'discount_in',
	'type'     => 'random',
	'disable'  => Helper::is_pro_active() ? false : true,
	'options'  => array(
		'product'  => esc_html__( 'Product', 'create-members' ),
		'category' => esc_html__( 'Category', 'create-members' ),
	),
	'selected' => $discount_in,
);

membership_select_field( $args );
$product_block  = ( $discount_in == 'product' || $discount_in == '' ) ? '' : ' d-none';
$category_block = $discount_in == 'category' ? '' : ' d-none';

// Apply discount in products
$args = array(
	'label'           => esc_html__( 'Products', 'create-members' ),
	'id'              => 'filter_by_products',
	'type'            => 'random',
	'selected'        => $filter_by_products,
	'select_type'     => 'multiple',
	'condition_class' => 'product' . $product_block,
	'options'         => Helper::get_products(),
	'disable'         => Helper::is_pro_active() ? false : true,
);
membership_select_field( $args );

// Apply discount in category
$args = array(
	'label'           => esc_html__( 'Categories', 'create-members' ),
	'id'              => 'filter_by_category',
	'select_type'     => 'multiple',
	'selected'        => $filter_by_category,
	'type'            => 'random',
	'condition_class' => 'category' . $category_block,
	'disable'         => Helper::is_pro_active() ? false : true,
	'options'         => Helper::get_categories(),
);

membership_select_field( $args );

// Apply discount by
$args = array(
	'label'    => esc_html__( 'Discount Type', 'create-members' ),
	'id'       => 'discount_type',
	'type'     => 'random',
	'selected' => $discount_type,
	'disable'  => Helper::is_pro_active() ? false : true,
	'options'  => array(
		'fixed_product'   => esc_html__( 'Fixed Discount', 'create-members' ),
		'percent_product' => esc_html__( 'Percentage Discount', 'create-members' ),
	),
);
membership_select_field( $args );

// discount in fixed or percentage
$args = array(
	'label'             => esc_html__( 'Discount:', 'create-members' ),
	'disable'           => Helper::is_pro_active() ? false : true,
	'field_type'        => 'number',
	'id'                => 'discount_number',
	'value'             => $discount_number,
	'extra_label'       => '',
	'extra_label_class' => 'discount_number_label',
);
membership_number_input_field( $args );
