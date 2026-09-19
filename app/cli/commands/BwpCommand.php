<?php

namespace App\cli\commands;

use WP;
use WP_CLI;
use WP_Query;
use bornfight\wpHelpers\cli\attributes\Attr;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

#[Attr('bwp', 'BWP CLI commands')]
class BwpCommand {
	/**
	 * Example CLI command
	 * run wp bwp example
	 * @param mixed $args 
	 * @param mixed $assoc_args 
	 * @return void 
	 */
	public function example( $args, $assoc_args ) {
		set_time_limit( 0 );

		ini_set( 'display_errors', 1 );
		error_reporting( E_ALL );

		WP_CLI::log( 'This is an example BWP CLI command.' );
	}
}
