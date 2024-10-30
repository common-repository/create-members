<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Membership\Core\Models\Plans;
use Membership\Utils\Helper;

if ( file_exists( CreateMembers::base_dir() . 'input-fields.php' ) ) {
	include_once CreateMembers::base_dir() . 'input-fields.php';
}

extract( Helper::instance()->get_settings() );
$plan_id = ( isset( $_GET['plan_id'] ) ) ? intval( $_GET['plan_id'] ) : '';
$plan    = Plans::get_plan( $plan_id );
extract( $plan );

$hide_woo_modules = Helper::hide_block( 'woo_modules' );

?>
<div class="form-section">
	<div class="mt-2 mb-2 content-header">
		<?php
			$title = $plan_id == '' ? esc_html__( 'Add New Subscription', 'create-members' ) : esc_html__( 'Edit', 'create-members' ) . ' ' . $plan_name;
		?>
		<div class="title mr-1"><?php echo esc_html( $title ); ?></div>
		<a href="<?php echo esc_url( admin_url() . 'admin.php?page=um-plans' ); ?>" target="_self">
			<button class="button button-primary add-new-plan"><?php esc_html_e( 'View Subscriptions', 'create-members' ); ?></button>
		</a>
	</div>
	<form id="membership-settings" class="membership-plan new-plan">
		<?php
			$args = array(
				'label'    => esc_html__( 'Create Subscription Level for', 'create-members' ),
				'id'       => 'level_type',
				'type'     => 'random',
				'options'  => Helper::membership_modules(),
				'selected' => $level_type,
			);
			membership_select_field( $args );

			$args = array(
				'label'   => esc_html__( 'Status', 'create-members' ),
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
				'id'         => 'membership_plans',
				'value'      => 'membership_plans',
			);
			membership_number_input_field( $args );

			$args = array(
				'label'       => esc_html__( 'Name', 'create-members' ),
				'placeholder' => esc_html__( 'Enter Subscription Level Name', 'create-members' ),
				'docs'        => esc_html__( 'Subscription Name will be Shown in Registration Page', 'create-members' ),
				'field_type'  => 'text',
				'id'          => 'plan_name',
				'value'       => $plan_name,
			);
			membership_number_input_field( $args );

			$args = array(
				'label'       => esc_html__( 'Description', 'create-members' ),
				'placeholder' => esc_html__( 'Enter Subscription Level Description', 'create-members' ),
				'docs'        => esc_html__( 'Subscription Description will be Shown in Registration Page', 'create-members' ),
				'id'          => 'description',
				'value'       => $description,
				'cols'        => 29,
			);
			membership_text_area( $args );
			?>
			<div class="woo_modules <?php echo esc_attr( $hide_woo_modules ); ?>">
				<?php
					if ( $plan_cost == 'product' ) {
						$args = array(
							'label'           => esc_html__( 'Select Products', 'create-members' ),
							'docs'            => esc_html__( 'Customer will be member in this  plan after purchasing the product', 'create-members' ),
							'id'              => 'plan_product',
							'type'            => 'random',
							'select_type'     => 'single',
							'selected'        => $plan_product,
							'condition_class' => '',
							'options'         => Helper::get_products(),
						);
						membership_select_field( $args );
					} elseif ( $plan_cost == 'amount' ) {
						$args = array(
							'label'       => esc_html__( 'Total Order Amount', 'create-members' ),
							'docs'        => esc_html__( 'Customer will be member in this  plan with this total order amount', 'create-members' ),
							'placeholder' => esc_html__( 'Enter Order Amount', 'create-members' ),
							'field_type'  => 'number',
							'id'          => 'price',
							'value'       => $price,
						);
						membership_number_input_field( $args );
					}
					$plan_cost_cond = (
						( $level_type == 'woo_modules' && $plan_cost == 'subscription' ) ||
						$level_type == 'wp_modules' ) ? '' : 'd-none';
				?>
			</div>

			<?php
			// billing details
			if ( file_exists( CreateMembers::modules_dir() . 'plans/types/billing.php' )
			&& $plan_cost == 'subscription' ) {
				include_once CreateMembers::modules_dir() . 'plans/types/billing.php';
			}

			// WooCommerce membership modules
			if ( file_exists( CreateMembers::modules_dir() . 'plans/types/woo-modules.php' ) ) {
				include_once CreateMembers::modules_dir() . 'plans/types/woo-modules.php';
			}
			// WordPress membership modules
			if ( file_exists( CreateMembers::modules_dir() . 'plans/types/wp-modules.php' ) ) {
				include_once CreateMembers::modules_dir() . 'plans/types/wp-modules.php';
			}

			?>

		<button type="submit" class="button button-primary plan-btn admin-button mt-1"><?php esc_html_e( 'Save Changes', 'create-members' ); ?></button>
	</form>
</div>
<?php


