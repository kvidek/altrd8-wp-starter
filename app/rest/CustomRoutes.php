<?php
/**
 *
 * Plugin BF Advanced Images is dependency
 *
 * Plugin repos: https://github.com/bornfight/bf-advanced-images
 * Plugin page: https://wordpress.org/plugins/bf-advanced-images/
 *
 */
namespace App\rest;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\services\Service;

class CustomRoutes extends Service {
	public function get_namespace(): string {
		return __NAMESPACE__ . '\\routes\\';
	}

	public function get_pattern(): string {
		return trailingslashit( get_stylesheet_directory() ) . 'app/rest/routes';
	}
}
