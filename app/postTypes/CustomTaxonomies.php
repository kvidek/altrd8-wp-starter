<?php
namespace App\postTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\services\Service;


class CustomTaxonomies extends Service {
	public function get_namespace(): string {
		return __NAMESPACE__ . '\\taxonomies\\';
	}

	public function get_pattern(): string {
		return trailingslashit( get_stylesheet_directory() ) . 'app/postTypes/taxonomies';
	}
}
