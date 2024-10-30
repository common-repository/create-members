<?php

namespace Membership\Core\Modules\Members;

defined( 'ABSPATH' ) || exit;

use Membership\Core\Models\Members as MembersModel;
use Membership\Core\Models\Plans as PlansModel;
use Membership\Utils\Helper;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/inclueds/class-wp-list-table.php';
}

class Table extends \WP_List_Table {

	public $singular_name;
	public $plural_name;
	public $id      = '';
	public $plan_id = '';
	public $columns = array();

	/**
	 * Show list
	 */
	function __construct( $all_data_of_table ) {

		$this->singular_name = $all_data_of_table['singular_name'];
		$this->plural_name   = $all_data_of_table['plural_name'];
		$this->columns       = $all_data_of_table['columns'];
		$this->plan_id       = $all_data_of_table['plan_id'];

		parent::__construct(
			array(
				'singular' => $this->singular_name,
				'plural'   => $this->plural_name,
				'ajax'     => true,
			)
		);
	}

	/**
	 * User column
	 */
	protected function column_user_name( $item ) {
		$_wpnonce = esc_attr( wp_create_nonce( 'delete-member-nonce' ) );
		$actions  = array(
			'delete'    => sprintf(
				'<a href="?page=%s&action=%s&id=%s&_wpnonce=%s" aria-label="%s" role="button">%s</a>',
				'um-members',
				'delete',
				$item['ID'],
				$_wpnonce,
				__( 'Delete', 'create-members' ),
				__( 'Delete', 'create-members' )
			),
			'edit'      => sprintf(
				'<a href="?page=%s&member=%s&id=%s" aria-label="%s" role="button">%s</a>',
				'um-members',
				'update_member',
				$item['ID'],
				__( 'Edit Member', 'create-members' ),
				__( 'Edit Member', 'create-members' )
			),
			'edit_user' => sprintf(
				'<a href="user-edit.php?user_id=%s" target="_blank" aria-label="%s" role="button">%s</a>',
				$item['member_user'],
				__( 'Edit User', 'create-members' ),
				__( 'Edit User', 'create-members' )
			),
		);
		return $item['user_name'] . $this->row_actions( $actions );
	}

	/**
	 * Get column header function
	 */
	public function get_columns() {
		return $this->columns;
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="bulk-delete-members[]" value="%1$s" />', intval( $item['ID'] ) );
	}

	/**
	 * Delete data
	 */
	public function process_bulk_action() {
		$action = $this->current_action();

		if ( 'delete' === $action &&
		isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			$nonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( ! wp_verify_nonce( $nonce, 'delete-member-nonce' ) ) {
				wp_die( __( 'Security check failed!', 'create-members' ) );
			}
			$id = $_GET['id'];
			wp_delete_post( $id, true );
		}

		if ( ! empty( $_POST['plan_id'] ) ) {
			$plan_id = esc_sql( $_POST['plan_id'] );
			wp_redirect(
				esc_url_raw(
					add_query_arg(
						array(
							'page' => 'um-members',
							'plan' => $plan_id,
						)
					)
				)
			);
		}
		if ( ! empty( $_POST['bulk-delete-members'] ) && 'trash' === $action ) {
			$delete_ids = esc_sql( $_POST['bulk-delete-members'] );
			foreach ( $delete_ids as $did ) {
				$user_id = get_post_meta( $did, 'member_user', true );
				update_user_meta( $user_id, MEM_PLAN_DETAILS, '' );
				update_user_meta( $user_id, NEW_MEM_MAIL_SENT, '' );
				wp_delete_post( $did, true );
			}

			wp_redirect( esc_url_raw( add_query_arg( array( 'delete' => 'member-delete' ) ) ) );

			exit;
		}
	}

	/**
	 * Get Bulk options
	 */
	public function get_bulk_actions() {
		$actions          = array();
		$actions['trash'] = esc_html__( 'Move to Trash', 'create-members' );

		return $actions;
	}

	/**
	 * Sortable column function
	 */
	public function get_sortable_columns() {
		unset( $this->columns['cb'] );
		unset( $this->columns['action'] );

		return $this->columns;
	}

	/**
	 * Display all row function
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'new_mail_status':
				return MembersModel::status_col( $item, 'new_mail_status' );
			case 'next_payment':
				$html             = '';
				$um_subscriptions = get_user_meta( $item['member_user'], 'um_subscription', true );
				if ( ! empty( $um_subscriptions ) ) {
					$html .= '<div class="center-align">' . date( 'd-m-Y', strtotime( $um_subscriptions['next_payment_date'] ) ) . '</div>';
					$html .= '<div class="center-align"><a href="' . esc_url( admin_url() . 'admin.php?page=wc-orders&action=edit&id=' . $um_subscriptions['order_id'] ) . '" target="_blank">' . esc_html__( 'Order Details', 'create-members' ) . '</a></div>';
				}
				return $html;
			case 'order_info':
				$html = '<div class="center-align">';
				foreach ( $item['order_info'] as $key => $value ) {
					if ( $key == 'orders_count' ) {
						$html .= '<div>' . esc_html__( 'Orders:' ) . $value . '</div>';
					} elseif ( $key == 'revenue' ) {
						$html .= '<div>' . esc_html__( 'Total Spend:' ) . $value . '</div>';
					} elseif ( $key == 'total_refund' ) {
						$html .= '<div>' . esc_html__( 'Refund:' ) . $value . '</div>';
					} elseif ( $key == 'refund_amount' ) {
						$html .= '<div>' . esc_html__( 'Refund Amount:' ) . $value . '</div>';
					}

					$html .= '</div>';
				}
				return $html;
			case 'status':
				return PlansModel::status_col( $item, 'status' );
			case 'start_date':
				return MembersModel::format_date_col( $item[ $column_name ] );
			case 'end_date':
				return MembersModel::membership_end_date_text( $item[ $column_name ] );
			case $column_name:
				return $item[ $column_name ];
			default:
				isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
				break;
		}
	}

	public function extra_tablenav( $which ) {
		$plans   = PlansModel::get_all_plans( -1, false, true );
		$submit  = array( 'id' => 'filter-by-plan' );
		$plan_id = ! empty( $_GET['plan'] ) ? intval( $_GET['plan'] ) : '';
		if ( ! Helper::is_pro_active() ) {
			$submit['disabled'] = 'disabled';
		}
		?>
			<div class="alignleft actions bulkactions">
				<input type="hidden" name="plan_id" class="plan_id" value=""/>
				<select name="member_plan_id" class="member_plan_id">
					<?php
					if ( count( $plans ) > 0 ) {
						?>
							<option value=""><?php esc_html_e( 'Select Subscription', 'create-members' ); ?></option>
						<?php
						foreach ( $plans as $key => $value ) {
							$selected = $plan_id == $key ? 'selected' : '';
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php
						}
					} else {
						?>
							<option value=""><?php esc_html_e( 'No Plan found', 'create-members' ); ?></option>
							<?php
					}

					?>
				</select>
			</div>
		<?php
		submit_button( esc_html__( 'Filter By Plan', 'create-members' ) . Helper::pro_text(), '', 'filter-by-plan', false, $submit );
		submit_button( esc_html__( 'Export CSV', 'create-members' ) . Helper::pro_text(), '', 'export-members', false, array( 'id' => 'export-members' ) );
	}

	/**
	 * Main query and show function
	 */
	public function preparing_items() {
		$per_page              = 20;
		$column                = $this->get_columns();
		$hidden                = array();
		$meta_data             = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $column, $hidden, $sortable );
		$current_page          = $this->get_pagenum();
		$offset                = ( $current_page - 1 ) * $per_page;

		$this->process_bulk_action();

		if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
			$args['orderby'] = sanitize_key( $_REQUEST['orderby'] );
			$args['order']   = sanitize_key( $_REQUEST['order'] );
		}

		$args['limit']  = $per_page;
		$args['offset'] = $offset;

		if ( ! empty( $this->plan_id ) ) {
			$meta_data = array(
				array(
					'key'     => 'member_plan_id',
					'value'   => $this->plan_id,
					'compare' => '=',
				),
			);
		}

		$get_data = MembersModel::instance()->get_all_data( $args['limit'], 'table', $meta_data );
		if ( ! empty( $_POST['export-members'] ) ) {
			$export = new \Membership\Core\Modules\Members\Exporter();
			$export->export( $get_data, 'csv' );
		}
		$this->set_pagination_args(
			array(
				'total_items' => count( (array) MembersModel::instance()->get_all_data( -1, false, $meta_data ) ),
				'per_page'    => $per_page,
			)
		);

		$this->items = $get_data;
	}
}

