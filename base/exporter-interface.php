<?php
/**
 * Exporter Interface
 */
namespace Membership\Base;

/**
 * Exporter interface
 */
interface Exporter_Interface {
	/**
	 * Export data
	 *
	 * @return void
	 */
	public function export( $data, $columns = array(), $file_name = '' );
}
