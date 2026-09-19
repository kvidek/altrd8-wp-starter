<?php

namespace App\config;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Config {
	public static function get_api_namespace(): string {
		return 'bornfight/v1';
	}

	public static function get_image_sizes(): array {
		return array(
			'image_200'  => array( 200, 0 ),
			'image_480'  => array( 480, 0 ),
			'image_600'  => array( 600, 0 ),
			'image_700'  => array( 700, 0 ),
			'image_800'  => array( 800, 0 ),
			'image_900'  => array( 900, 0 ),
			'image_1200' => array( 1200, 0 ),
			'image_1440' => array( 1440, 0 ),
			'image_1920' => array( 1920, 0 ),
			'image_2880' => array( 2880, 0 ),
		);
	}

	public static function get_date_format(): string {
		return 'd M Y';
	}
}
