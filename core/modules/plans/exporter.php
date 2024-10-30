<?php

namespace Membership\Core\Modules\Plans;

use Membership\Base\Exporter_Factory;

class Exporter {
	/**
	 * Store file name
	 *
	 * @var string
	 */
	private $file_name = 'subscription-report';

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
			'plan_name' => esc_html__( 'Name', 'create-members' ),
			'durations' => esc_html__( 'Durations', 'create-members' ),
			'price'     => esc_html__( 'Price', 'create-members' ),
			'status'    => esc_html__( 'Status', 'create-members' ),
		);
	}
}
