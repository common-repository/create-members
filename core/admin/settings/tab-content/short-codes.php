<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

use Membership\Utils\Helper;

// subscription registration form
?>
<div class="input-group-field bg-white">
	<div class="form-label"><?php esc_html_e( 'Registration Form', 'create-members' ); ?></div>
	<div class="input-group-wrapper shortcode-block input-group-auto-4" data-name="reg_form">
	<?php
		$args = array(
			'wrapper_class'   => 'shortcode_value group-block',
			'label'           => esc_html__( 'Select Style', 'create-members' ),
			'desc'            => esc_html__( 'Select subscription form style', 'create-members' ),
			'id'              => 'template',
			'type'            => 'template',
			'selected'        => '',
			'select_type'     => 'single',
			'data_label'      => 'template',
			'condition_class' => '',
			'options'         => array(1,2,3),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );
		$args = array(
			'wrapper_class'   => 'shortcode_value group-block',
			'label'           => esc_html__( 'Select Modules', 'create-members' ),
			'desc'            => esc_html__( 'Selected module will be displayed in the registration form', 'create-members' ),
			'id'              => 'short_code_module',
			'type'            => 'random',
			'selected'        => '',
			'select_type'     => 'multiple',
			'data_label'      => 'modules',
			'condition_class' => '',
			'options'         => Helper::membership_modules(),
			'disable'         => Helper::is_pro_active() ? false : true,
		);
		membership_select_field( $args );

		$args = array(
			'label'         => esc_html__( 'Copy the Shortcode', 'create-members' ),
			'docs'          => esc_html__( 'Place the shortcode in any pages to show subscription form', 'create-members' ),
			'field_type'    => 'text',
			'wrapper_class' => 'group-block',
			'id'            => 'full_input',
			'input_class'   => 'full_input',
			'value'         => "[reg_form modules='']",
			'disable'       => Helper::is_pro_active() ? false : true,
		);
		membership_number_input_field( $args );

		$args = array(
			'btn_txt'       => esc_html__( 'Copy', 'create-members' ),
			'field_type'    => 'button',
			'btn_class'     => 'button button-primary',
			'wrapper_class' => 'generate-block group-block center-align',
			'id'            => 'reg-form',
			'disable'       => Helper::is_pro_active() ? false : true,
		);
		membership_btn_field( $args );
		?>
	</div>    
</div>

