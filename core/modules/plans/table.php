<?php

namespace Membership\Core\Modules\Plans;

defined( 'ABSPATH' ) || exit;

use Membership\Core\Models\Plans as PlansModel;
use Membership\Utils\Helper;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/inclueds/class-wp-list-table.php';
}


class Table extends \WP_List_Table {

	public $singular_name;
	public $plural_name;
	public $id      = '';
	public $columns = array();

	/**
	 * Show list
	 */
	function __construct( $all_data_of_table ) {

		$this->singular_name = $all_data_of_table['singular_name'];
		$this->plural_name   = $all_data_of_table['plural_name'];
		$this->columns       = $all_data_of_table['columns'];

		parent::__construct(
			array(
				'singular' => $this->singular_name,
				'plural'   => $this->plural_name,
				'ajax'     => true,
			)
		);
	}

	protected function column_plan_name( $item ) {
		$_wpnonce = esc_attr( wp_create_nonce( 'delete-plan-nonce' ) );
		$actions  = array(
			'delete' => sprintf(
				'<a href="?page=%s&action=%s&id=%s&_wpnonce=%s" aria-label="%s" role="button">%s</a>',
				'um-plans',
				'delete',
				$item['ID'],
				$_wpnonce,
				__( 'Delete', 'create-members' ),
				__( 'Delete', 'create-members' )
			),
			'edit'   => sprintf(
				'<a href="?page=%s&plan=%s&plan_id=%s" aria-label="%s" role="button">%s</a>',
				'um-plans',
				'update_plan',
				$item['ID'],
				__( 'Edit', 'create-members' ),
				__( 'Edit', 'create-members' )
			),
		);

		return $item['plan_name'] . $this->row_actions( $actions );
	}

	
	protected function column_price( $item ) {
		return Helper::level_price($item);
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
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete-plans[]" value="%1$s" />',
			$item['ID']
		);
	}

	public function extra_tablenav( $which ) {
		if ( ! Helper::is_pro_active() ) {
			return;
		}
		$modules = Helper::membership_modules();
		$submit  = array( 'id' => 'filter-by-modules' );

		?>
			<div class="alignleft actions bulkactions">
				<input type="hidden" name="modules_type" class="modules_type" value=""/>
				<select name="filter_modules_type" class="filter_modules_type">
					<?php
					if ( count( $modules ) > 0 ) {
						$modules_type = ! empty( $_POST['modules_type'] ) ? $_POST['modules_type'] : '';
						?>
							<option value=""><?php esc_html_e( 'Select Modules Type', 'create-members' ); ?></option>
						<?php
						foreach ( $modules as $key => $value ) {
							$selected = $modules_type == $key ? 'selected' : '';
							?>
								<option  <?php echo esc_attr( $selected ); ?> value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value ); ?></option>
							<?php
						}
					} else {
						?>
							<option value=""><?php esc_html_e( 'No modules found', 'create-members' ); ?></option>
							<?php
					}

					?>
				</select>
			</div>
		<?php
		submit_button( esc_html__( 'Filter By Modules', 'create-members' ), '', 'filter-by-modules', false, $submit );
		submit_button( esc_html__( 'Export CSV', 'create-members' ) . Helper::pro_text(), '', 'export-plans', false, array( 'id' => 'export-plans' ) );
	}

	/**
	 * Delete data
	 */
	public function process_bulk_action() {
		$action = $this->current_action();

		if ( 'delete' === $action &&
		isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			$nonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_SPECIAL_CHARS );
			if ( ! wp_verify_nonce( $nonce, 'delete-plan-nonce' ) ) {
				wp_die( __( 'Security check failed!', 'create-members' ) );
			}
			$id = $_GET['id'];
			wp_delete_post( $id, true );
		}

		if ( 'trash' === $action ) {
			$delete_ids = esc_sql( $_POST['bulk-delete-plans'] );
			foreach ( $delete_ids as $did ) {
				wp_delete_post( $did, true );
			}

			wp_redirect( esc_url_raw( add_query_arg( array( 'delete' => 'plan-delete' ) ) ) );

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
		unset( $this->columns['actions'] );

		return $this->columns;
	}

	/**
	 * Display all row function
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case $column_name:
				return $item[ $column_name ];
			default:
				isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
				break;
		}
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
		if ( ! empty( $_POST['modules_type'] ) ) {
			$meta_data = array(
				array(
					'key'     => 'level_type',
					'value'   => $_POST['modules_type'],
					'compare' => '=',
				),
			);
		}

		$get_data = PlansModel::get_all_plans( $args['limit'], true, false, $meta_data );
		if ( ! empty( $_POST['export-plans'] ) ) {
			$export = new \Membership\Core\Modules\Plans\Exporter();
			$export->export( $get_data, 'csv' );
		}
		$this->set_pagination_args(
			array(
				'total_items' => count( (array) PlansModel::get_all_plans( -1, false, false, $meta_data ) ),
				'per_page'    => $per_page,
			)
		);

		$this->items = $get_data;
	}
}
