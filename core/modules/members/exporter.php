<?php

namespace Membership\Core\Modules\Members;

use Membership\Base\Exporter_Factory;

class Exporter {
	/**
	 * Store file name
	 *
	 * @var string
	 */
	private $file_name = 'member-report';

	/**
	 * Store event data
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Export event data
	 *
	 * @return void
	 */
	public function export( $data, $format ) {
		$this->data = $data;

		$rows      = $this->prepare_data();
		$columns   = $this->get_columns();
		$file_name = $this->file_name;

		$exporter = Exporter_Factory::get_exporter( $format );

		$exporter->export( $rows, $columns, $file_name );
	}

	/**
	 * Prepare data to export
	 *
	 * @return  array
	 */
	private function prepare_data() {
		return $this->data;
	}

	/**
	 * Get columns
	 *
	 * @return  array
	 */
	private function get_columns() {
		return array(
			'user_name'  => esc_html__( 'User', 'create-members' ),
			'status'     => esc_html__( 'Membership Status', 'create-members' ),
			'plan_name'  => esc_html__( 'Subscription', 'create-members' ),
			'order_info' => esc_html__( 'Order Information', 'create-members' ),
			'start_date' => esc_html__( 'Start Date', 'create-members' ),
			'end_date'   => esc_html__( 'End Date', 'create-members' ),
		);
	}
}
