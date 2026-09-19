<?php

namespace App\adminMenus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\services\Service;
use WP_Block_Editor_Context;

class CustomAdminMenus extends Service {
	public function get_namespace(): string {
		return __NAMESPACE__ . '\\menus\\';
	}

	public function get_pattern(): string {
		return trailingslashit( get_stylesheet_directory() ) . 'app/adminMenus/menus';
	}
}
