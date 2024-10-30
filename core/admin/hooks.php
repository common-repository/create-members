<?php

namespace Membership\Core\Admin;

defined( 'ABSPATH' ) || exit;

use Membership\Core\Models\Plans;
use Membership\Utils\Helper;
use Membership\Utils\Singleton;

class Hooks {
	use Singleton;

	public function init() {
		add_action( 'post_tag_edit_form_fields', array( $this, 'subscription_packages' ), 10, 1 );
		add_action( 'category_edit_form_fields', array( $this, 'subscription_packages' ), 10, 1 );
		add_action( 'category_add_form_fields', array( $this, 'subscription_packages' ), 10, 1 );
		add_action( 'post_tag_add_form_fields', array( $this, 'subscription_packages' ), 10, 1 );
		add_action( 'edited_category', array( $this, 'save_custom_fields' ) );
		add_action( 'edited_post_tag', array( $this, 'save_custom_fields' ) );
		add_action( 'created_category', array( $this, 'save_custom_fields' ) );
		add_action( 'created_post_tag', array( $this, 'save_custom_fields' ) );
	}

	/**
	 * Add fields in Category
	 */
	public function subscription_packages( $tag ) {
		$package_id = '';
		if ( ! empty( $tag->term_id ) ) {
			$package_id = get_term_meta( $tag->term_id, 'membership_packages', true );
		}
		$ids = is_array( $package_id ) ? implode( ',', $package_id ) : $package_id;
		?> 
		<tr class="form-field">
			<th scope="row" valign="top"><label for="packages"><?php esc_html_e( 'Membership Package', 'create-members' ); ?></label></th>
			<td>
				<select name="membership_packages[]" id="packages" 
				data-packages="<?php echo esc_attr( $ids ); ?>">
					<?php
						$packages = Plans::packages_by_modules( 'wp_modules' );
						$disabled = Helper::is_pro_active() == '' ? 'disabled' : '';
					foreach ( $packages as $key => $value ) {
						?>
							<option <?php echo esc_attr( $disabled ); ?>
							value="<?php echo esc_html( $key ); ?>"><?php echo Helper::kses( $value ) . Helper::pro_text(); ?></option>
							<?php
					}
					?>
				</select>
  
				<div class="desc"><?php _e( 'Select Membership Package', 'create-members' ); ?></div>
			</td>
		</tr>
		<?php
	}

	/**
	 * save fields in Category
	 */
	public function save_custom_fields( $term_id ) {
		$package_ids = isset( $_POST['membership_packages'] ) ? $_POST['membership_packages'] : '';
		update_term_meta( $term_id, 'membership_packages', $package_ids );
	}
}