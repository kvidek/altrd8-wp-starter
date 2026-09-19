<?php

namespace App\config;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MenuConfig {
	const HEADER_MENU_LOCATION = 'header-menu';
	const FOOTER_MENU_LOCATION = 'footer-menu';

	public static function get_menus(): array {
		return array(
			self::HEADER_MENU_LOCATION => 'Header Menu',
			self::FOOTER_MENU_LOCATION => 'Footer Menu'
		);
	}
}
