<?php

namespace App\config;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACFConfig {
	public static function get_modules() : array {
		return array(
			'test'
		);
	}
}
