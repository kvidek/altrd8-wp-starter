<?php

namespace App\modules;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use App\interfaces\ControllerInterface;

final class Modules {
	public static function get_module( array $module, string $module_name = '' ): ControllerInterface {

		if( ! empty( $module['acf_fc_layout'] ) ) {
			$module_name = $module['acf_fc_layout'];
		}

		$class = 'App\modules\\' . str_replace( '_', '', ucwords( $module_name, '_' ) );

		return new $class( $module );
	}
}