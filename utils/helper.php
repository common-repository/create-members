<?php

namespace Membership\Utils;

use Membership\Core\Models\Plans;

defined( 'ABSPATH' ) || exit;

/**
 * Helper function
 */
class Helper {

	use Singleton;

	/**
	 * Html markup validation
	 */
	public static function kses( $raw ) {
		$allowed_tags = array(
			'svg'                           => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true, // <= Must be lower case!
			),
			'g'                             => array( 'fill' => true ),
			'title'                         => array( 'title' => true ),
			'path'                          => array(
				'd'    => true,
				'fill' => true,
			),
			'circle'                        => array(
				'opacity' => true,
				'cx'      => true,
				'cy'      => true,
				'r'       => true,
				'fill'    => true,
			),
			'a'                             => array(
				'class'  => array(),
				'href'   => array(),
				'rel'    => array(),
				'title'  => array(),
				'target' => array(),
			),
			'button'                        => array(
				'type'      => array(),
				'name'      => array(),
				'class'     => array(),
				'id'        => array(),
				'data-name' => array(),
			),
			'input'                         => array(
				'value'       => array(),
				'type'        => array(),
				'size'        => array(),
				'name'        => array(),
				'checked'     => array(),
				'placeholder' => array(),
				'id'          => array(),
				'class'       => array(),
				'data-label'  => array(),
				'step'        => array(),
			),

			'select'                        => array(
				'value'       => array(),
				'type'        => array(),
				'size'        => array(),
				'name'        => array(),
				'placeholder' => array(),
				'id'          => array(),
				'class'       => array(),
				'multiple'    => array(),
				'data-option' => array(),
			),
			'option'                        => array(
				'selected' => array(),
				'value'    => array(),
				'disabled' => array(),
			),
			'textarea'                      => array(
				'value'       => array(),
				'type'        => array(),
				'size'        => array(),
				'name'        => array(),
				'rows'        => array(),
				'cols'        => array(),
				'placeholder' => array(),
				'id'          => array(),
				'class'       => array(),
			),
			'abbr'                          => array(
				'title' => array(),
			),
			'b'                             => array(),
			'blockquote'                    => array(
				'cite' => array(),
			),
			'cite'                          => array(
				'title' => array(),
			),
			'code'                          => array(),
			'del'                           => array(
				'datetime' => array(),
				'title'    => array(),
			),
			'dd'                            => array(),
			'div'                           => array(
				'data'  => array(),
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'dl'                            => array(),
			'dt'                            => array(),
			'em'                            => array(),
			'h1'                            => array(
				'class' => array(),
			),
			'h2'                            => array(
				'class' => array(),
			),
			'h3'                            => array(
				'class' => array(),
			),
			'h4'                            => array(
				'class' => array(),
			),
			'h5'                            => array(
				'class' => array(),
			),
			'h6'                            => array(
				'class' => array(),
			),
			'i'                             => array(
				'class' => array(),
			),
			'img'                           => array(
				'alt'    => array(),
				'class'  => array(),
				'height' => array(),
				'src'    => array(),
				'width'  => array(),
			),
			'li'                            => array(
				'class' => array(),
			),
			'ol'                            => array(
				'class' => array(),
			),
			'p'                             => array(
				'class' => array(),
			),
			'q'                             => array(
				'cite'  => array(),
				'title' => array(),
			),
			'span'                          => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'small'                         => array(
				'class' => array(),
				'title' => array(),
				'style' => array(),
			),
			'iframe'                        => array(
				'width'       => array(),
				'height'      => array(),
				'scrolling'   => array(),
				'frameborder' => array(),
				'allow'       => array(),
				'src'         => array(),
			),
			'strike'                        => array(),
			'br'                            => array(),
			'strong'                        => array(),
			'data-wow-duration'             => array(),
			'data-wow-delay'                => array(),
			'data-wallpaper-options'        => array(),
			'data-stellar-background-ratio' => array(),
			'ul'                            => array(
				'class' => array(),
			),
			'label'                         => array(
				'class' => array(),
				'for'   => array(),
			),
		);

		if ( function_exists( 'wp_kses' ) ) { // WP is here
			return wp_kses( $raw, $allowed_tags );
		} else {
			return $raw;
		}
	}

	/**
	 * Auto generate classname from path.
	 */
	public static function make_classname( $dirname ) {
		$dirname    = pathinfo( $dirname, PATHINFO_FILENAME );
		$class_name = explode( '-', $dirname );
		$class_name = array_map( 'ucfirst', $class_name );
		$class_name = implode( '_', $class_name );

		return $class_name;
	}

	/**
	 * Show Notices
	 */
	public static function push( $notice ) {

		$defaults = array(
			'id'               => '',
			'type'             => 'info',
			'show_if'          => true,
			'message'          => '',
			'class'            => 'active-notice',
			'dismissible'      => false,
			'btn'              => array(),
			'dismissible-meta' => 'user',
			'dismissible-time' => WEEK_IN_SECONDS,
			'data'             => '',
		);

		$notice = wp_parse_args( $notice, $defaults );

		$classes = array( 'notice', 'notice' );

		$classes[] = $notice['class'];

		if ( isset( $notice['type'] ) ) {
			$classes[] = 'notice-' . $notice['type'];
		}

		// Is notice dismissible?
		if ( true === $notice['dismissible'] ) {
			$classes[] = 'is-dismissible';

			// Dismissable time.
			$notice['data'] = ' dismissible-time=' . esc_attr( $notice['dismissible-time'] ) . ' ';
		}

		// Notice ID.
		$notice_id    = 'sites-notice-id-' . $notice['id'];
		$notice['id'] = $notice_id;

		if ( ! isset( $notice['id'] ) ) {
			$notice_id    = 'sites-notice-id-' . $notice['id'];
			$notice['id'] = $notice_id;
		} else {
			$notice_id = $notice['id'];
		}

		$notice['classes'] = implode( ' ', $classes );

		// User meta.
		$notice['data'] .= ' dismissible-meta=' . esc_attr( $notice['dismissible-meta'] ) . ' ';

		if ( 'user' === $notice['dismissible-meta'] ) {
			$expired = get_user_meta( get_current_user_id(), $notice_id, true );
		} elseif ( 'transient' === $notice['dismissible-meta'] ) {
			$expired = get_transient( $notice_id );
		}

		// Notice visible after transient expire.
		if ( isset( $notice['show_if'] ) ) {
			if ( true === $notice['show_if'] ) {
				// Is transient expired?
				if ( false === $expired || empty( $expired ) ) {
					self::markup( $notice );
				}
			}
		} else {
			self::markup( $notice );
		}
	}

	/**
	 * Markup Notice.
	 */
	public static function markup( $notice = array() ) {
		?>
		<div id="<?php echo esc_attr( $notice['id'] ); ?>" class="<?php echo esc_attr( $notice['classes'] ); ?>" <?php echo esc_html( $notice['data'] ); ?>>
			<p>
				<?php echo esc_html( $notice['message'] ); ?>
			</p>

			<?php if ( ! empty( $notice['btn'] ) ) : ?>
				<p>
					<a href="<?php echo esc_url( $notice['btn']['url'] ); ?>" class="button-primary"><?php echo esc_html( $notice['btn']['label'] ); ?></a>
				</p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Admin pages array
	 *
	 * @return array
	 */
	public static function admin_unique_id() {
		$admin_pages = array(
			'edit-category',
			'edit-post_tag',
			'membership_page_um-settings',
			'membership_page_um-reports',
			'membership_page_um-modules',
			'membership_page_um-plans',
			'membership_page_um-members',
			'membership_page_um-license',
			'woocommerce_page_wc-orders'
		);

		return $admin_pages;
	}

	/**
	 * Settings option
	 *
	 * @return array
	 */
	public static function get_settings_key() {
		$non_member_text = sprintf( __( 'This content is for {package} members only.<br /><a href="%s">Join Now</a>', 'create-members' ), '{package_url}' );

		$settings_key = array(
			'non_member_text'      => $non_member_text,
			'subscription_page'    => '',
			'filter_queries'       => 'no',
			'is_enable_membership' => 'default',
			'plan_cost'            => 'amount',
			'notification_type'    => 'all_members',
			'non_member_offer'     => '',
			'non_member_msg'       => '',
			'redirect_content'     => '',
			'new_member_subject'   => esc_html__( 'New Member', 'create-members' ),
			'new_member_title'     => esc_html__( 'New Member', 'create-members' ),
			'new_member_message'   => '',
			'cancel_subject'       => esc_html__( 'Cancelled Membership', 'create-members' ),
			'cancel_title'         => esc_html__( 'Cancelled Membership', 'create-members' ),
			'cancel_message'       => '',
			'hide_price_txt'       => '',
		);

		return $settings_key;
	}

	/**
	 * Admin settings
	 */
	public static function get_settings() {
		$settings     = array();
		$get_settings = get_option( 'membership_settings', true );
		$settings_key = self::get_settings_key();
		foreach ( $settings_key as $key => $value ) {
			$settings[ $key ] = ! empty( $get_settings[ $key ] ) ? $get_settings[ $key ] : $value;
		}

		return $settings;
	}



	public static function get_modules_key() {
		return array(
			'wp_modules'  => 'yes',
			'woo_modules' => 'yes',
		);
	}
	public static function currency_symbol() {
		return class_exists( 'WooCommerce' ) ? get_woocommerce_currency_symbol() : '';
	}

	public static function subscription_period() {
		$length = array(
				'day'   => esc_html__( 'Day(s)', 'create-members' ),
				'month' => esc_html__( 'Month(s)', 'create-members' ),
				'year'  => esc_html__( 'Year(s)', 'create-members' ),
		);
		return $length;
	}

	/**
	 * Admin settings
	 */
	public static function get_modules() {
		$modules     = array();
		$get_modules = get_option( MODULES_SAVINGS, true );
		$modules_key = self::get_modules_key();
		foreach ( $modules_key as $key => $value ) {
			$modules[ $key ] = ! empty( $get_modules[ $key ] ) ? $get_modules[ $key ] : $value;
		}

		return $modules;
	}

	/**
	 * Get categories
	 *
	 * @return array
	 */
	public static function get_categories( $type = 'product_cat' ) {
		$cat_arr = array();
		if ( $type == 'product_cat' && ! class_exists( 'WooCommerce' ) ) {
			return $cat_arr;
		}
		$categories = get_categories(
			array(
				'taxonomy'   => $type,
				'hide_empty' => 0,
				'order'      => 'DESC',
			)
		);

		foreach ( $categories as $key => $value ) {
			if ( $value->slug !== 'uncategorized' ) {
				$cat_arr[ $value->term_id ] = $value->name;
			}
		}

		return $cat_arr;
	}

	public static function get_all_post( $post_type = 'post' ) {
		$all_posts = array();
		$posts     = get_posts(
			array(
				'post_type' => $post_type,
				'limit'     => -1,
				'status'    => 'publish',
			)
		);
		if ( empty( $posts ) ) {
			return $all_posts;}

		foreach ( $posts as $key => $value ) {
			$all_posts[ $value->ID ] = $value->post_title;
		}
		return $all_posts;
	}

	/**
	 * woo products array
	 *
	 * @return array
	 */
	public static function get_products() {
		$products_arr = array();

		if ( ! class_exists( 'WooCommerce' ) ) {
			return $products_arr;
		}
		$products = wc_get_products(
			array(
				'limit'  => -1,
				'status' => 'publish',
			)
		);

		$is_pro_active = class_exists( 'CreateMembers' );
		foreach ( $products as $key => $value ) {
			if ( $is_pro_active !== '' &&
			( $value->get_type() !== 'variable' && $value->get_type() !== 'grouped' )
			) {
				$products_arr[ $value->get_id() ] = $value->get_name();
			} elseif ( 1 == $is_pro_active ) {
				$products_arr[ $value->get_id() ] = $value->get_name();
			}
		}

		return $products_arr;
	}

	/**
	 * wp pages array
	 *
	 * @return array
	 */
	public static function get_content_pages( $item = 'post_title' ) {
		$pages = get_pages( array( 'parent' => 0 ) );
		$ids   = wp_list_pluck( $pages, $item );

		return $ids;
	}

	/**
	 * check nonce
	 */
	public function verify_nonce( $nonce_index, $nonce_value ) {
		if ( is_null( $nonce_index ) || ! wp_verify_nonce( $nonce_value, $nonce_index ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security check failed', 'create-members' ),
					'code'    => 401,
				)
			);
		}
	}

	/**
	 * Get Path of template
	 */
	public static function um_get_template( $dir, $path ) {
		switch ( $dir ) {
			case 'modules_dir':
				if ( file_exists( \CreateMembers::modules_dir() . $path ) ) {
					include \CreateMembers::modules_dir() . $path;
				}
				break;

			default:
				break;
		}
	}

	/**
	 * Get Path of template
	 */
	public static function is_pro_active() {
		return class_exists( 'CreateMembersPro' ) ? true : false;
	}

	/**
	 * Pro Text
	 */
	public static function pro_text() {
		$pro = '';
		if ( ! self::is_pro_active() ) {
			$pro = ' ' . '(' . esc_html__( 'Pro', 'create-members' ) . ')';
		}

		return $pro;
	}

	/**
	 * Email body with template tags
	 *
	 * @param [type] $values
	 * @param [type] $content
	 * @return string
	 */
	public static function add_template_tags( $values, $content ) {
		$tags = array( '{plan_name}' );

		return str_replace( $tags, $values, $content );
	}

	/**
	 * Product  with discount
	 */
	public static function discount_product( $product_id, $filter_by_products ) {
		if ( is_array( $filter_by_products ) && in_array( $product_id, $filter_by_products ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Product  with discount
	 */
	public static function discount_category( $product_id, $filter_by_categories ) {
		$product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );

		if ( is_array( $filter_by_categories ) && count( array_intersect( $product_cats_ids, $filter_by_categories ) ) > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * All modules for membership
	 *
	 * @return array
	 */
	public static function membership_modules() {
		$options = array(
			'woo_modules' => esc_html__( 'WooCommerce Membership', 'create-members' ),
			'wp_modules'  => esc_html__( 'WordPress Membership', 'create-members' ),
		);
		if ( ! class_exists( 'CreateMembersPro' ) ) {
			return $options;
		}

		$modules = self::get_modules();
		foreach ( $modules as $key => $value ) {
			if ( $value == 'no' ) {
				unset( $options[ $key ] );
			}
		}

		return $options;
	}

	public static function hide_block( $module_name ) {
		$modules = array_keys( self::membership_modules() );
		return ( ! in_array( $module_name, $modules ) ) ? 'd-none' : '';
	}

	/**
	 * Check an associative array or not
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function is_associative_array( $array ) {
		return is_array( $array ) && count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
	}

	public static function level_price($level) {
		$price = '';
		if (!empty($level['price'])) {
			$price = $level['price'] . ' ' . esc_html__( 'now', 'create-members' )
			. $level['recurring_level_txt'];
		}

		return $price;
	}
}
