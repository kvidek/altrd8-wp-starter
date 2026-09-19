<?php

namespace App\helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PaginationHelper {

	private string $page_base;

	private string $url;

	public function __construct( $url = '', $page_base = 'page' ) {
		$this->page_base = $page_base;
		$this->url       = $url;
	}

	public function get_pagination_start_page( int $current_page ): int {
		if ( $current_page == 1 || $current_page == 2 ) {
			return 1;
		}

		return $current_page - 1;
	}

	public function get_pagination_end_page( int $current_page, int $max_pages ): int {
		if ( $current_page < ($max_pages - 3) ) {
			return $current_page + 2;
		}

		return $max_pages + 1;
	}

	public function get_page_url( $page_num, $url = '' ) {

		if ( empty( $url ) ) {
			$url = $this->url;
		}

		if ( false !== strpos( $url, $this->page_base . '/%page_num%' ) ) {

			$url_arr = parse_url( $url );

			if ( ! empty( $url_arr['path'] ) ) {
				if ( (int) $page_num > 1 ) {
					// replace %page_num% with page number
					$url_arr['path'] = strtr( $url_arr['path'], array( '%page_num%' => $page_num ) );
				} else {
					// remove page/%page_num% from URL
					$url_arr['path'] = strtr( $url_arr['path'], array( $this->page_base . '/%page_num%' => '' ) );
				}

				$url_arr['path'] = trailingslashit( $url_arr['path'] );
			}

			$url = $this->unparse_url( $url_arr );

		} else if ( false !== strpos( $url, $this->page_base . '=%page_num%' ) ) {

			$url_arr = parse_url( $url );
			if ( ! empty( $url_arr['query'] ) ) {
				if ( (int) $page_num > 1 ) {
					// replace %page_num% with page number
					$url_arr['query'] = strtr( $url_arr['query'], array( '%page_num%' => $page_num ) );
				} else {
					// remove page=%page_num% from URL
					$url_arr['query'] = strtr( $url_arr['query'], array( $this->page_base . '=%page_num%' => '' ) );
				}
			}

			// if query empty, remove ?
			if ( empty( $url_arr['query'] ) ) {
				unset( $url_arr['query'] );
			}

			$url = $this->unparse_url( $url_arr );

		} else {
			if ( (int) $page_num > 1 ) {
				// add page/2 to end of URL
				$url = trailingslashit( $this->url . $this->page_base . '/' . $page_num );
			} else {
				// do nothing
			}
		}

		return esc_url( $url );
	}

	private function unparse_url( $parsed_url ) {

		$scheme = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';

		$host = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';

		$port = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';

		$user = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';

		$pass = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';

		$pass = ( $user || $pass ) ? "{$pass}@" : '';

		$path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';

		$query = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';

		$fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

		return "{$scheme}{$user}{$pass}{$host}{$port}{$path}{$query}{$fragment}";

	}
}
