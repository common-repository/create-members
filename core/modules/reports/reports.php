<?php

	defined( 'ABSPATH' ) || exit;

	use Membership\Core\Models\Reports;

	$summary = Reports::membership_summary();
	extract( $summary );

if ( file_exists( CreateMembers::modules_dir() . 'reports/parts/summary.php' ) ) {
	include_once CreateMembers::modules_dir() . 'reports/parts/summary.php';
}

if ( file_exists( CreateMembers::modules_dir() . 'reports/parts/tables.php' ) ) {
	include_once CreateMembers::modules_dir() . 'reports/parts/tables.php';
}

if ( file_exists( CreateMembers::modules_dir() . 'reports/parts/visual-report.php' ) ) {
	include_once CreateMembers::modules_dir() . 'reports/parts/visual-report.php';
}
