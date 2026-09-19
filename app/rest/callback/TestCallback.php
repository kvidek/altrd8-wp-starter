<?php
/**
 *
 * Plugin BF Advanced Images is dependency
 *
 * Plugin repos: https://github.com/bornfight/bf-advanced-images
 * Plugin page: https://wordpress.org/plugins/bf-advanced-images/
 *
 */
namespace App\rest\callback;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\rest\BaseCallback;
use WP_REST_Request;
use WP_REST_Response;

class TestCallback extends BaseCallback {
	public function rest_endpoint( WP_REST_Request $request ): WP_REST_Response {
		$query_params = $this->get_request_params( $request, array() );

		$html = '';
		$url  = $this->create_new_url( $query_params );

		$response_data = array(
			'html' => $html,
			'url'  => $url
		);

		return rest_ensure_response( $response_data );
	}
}
