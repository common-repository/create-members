<?php

namespace Membership\Base;

use Exception;
use Membership\Base\CSV_Exporter;

/**
 * Class Exporter
 */
class Exporter_Factory {
	public static function get_exporter( $format ) {
		switch ( $format ) {
			case 'csv':
				return new CSV_Exporter();

			default:
				throw new Exception( __( 'Unknown format', 'eventin' ) );
		}
	}
}
