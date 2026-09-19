<?php

namespace App\helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * This class helps to build custom server-side filters.
 * Use class to build dynmaic URLs.
 * 
 * 1. fetch data from requested URL
 * $current_page  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
 * $category_name = ( get_query_var( 'category_name' ) ) ? get_query_var( 'category_name' ) : null;
 * $blog_order    = ( ! empty( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : null;
 * 
 * 2. init class
 * $archive_url_helper = new ArchiveUrlHelper( get_the_permalink( ACFProvider::get_instance()->get_option_field( 'pages_-_blog' ) ), '%base_url%/%category_key%/%category_value%/%page_key%/%page_value%/?%order_key%=%order_value%#blog-filter' );
 * 
 * 3. build pagination URL
 * $pagination_base_url = $archive_url_helper->get_url( array(
 *    'category' => $category_name,
 *    'page'     => '%page_num%',
 *    'order'    => $blog_order,
 * ) );
 * 
 * 4. build inital filter URL
 * $filter_all_url = $archive_url_helper->get_url( array(
 *     'category' => '',
 *     'page'     => '',
 *     'order'    => '',
 *  ) );
 */
class ArchiveHelper
{
    private $base_url = '';

    private string $url_structure = '%base_url%/%category_key%/%category_value%/%page_key%/%page_value%/?%order_key%=%order_value%#filter';

    public function __construct( $base_url = '', $url_structure = '' ) {

        if( ! empty ( $base_url ) ) {
            $this->base_url = rtrim( $base_url, '/' );
        }

        if( ! empty ( $url_structure ) ) {
            $this->url_structure   = $url_structure;
        }
    } 

    public function get_url( $params = array() ): string {

        $final_url_str = '';

        $url_values = array();

        $url_values['base_url'] = $this->base_url;

        foreach( $params as $param => $value  ) {
            if( ! empty( $value ) || $value === 0  || $value === '0' ) {
                $url_values[$param . '_value'] = $value;
                $url_values[$param . '_key']   = $param;
            } else {
                $url_values[$param . '_key']   = '';
                $url_values[$param . '_value'] = '';
            }
        }

        // all keys in $this->url_parts are wrapped in %$this->url_parts%
        $url_values_tokenized = $this->tokenize_path_values( $url_values );

        // parse url
        $structure_arr = parse_url( $this->url_structure );

        // rebuild URL path part
        if( ! empty ( $structure_arr['path'] )  ) {

            $path_vars = array();

            $ex = explode( '/', $structure_arr['path'] );

            foreach( $ex as $token ) {
                if( ! empty( $url_values_tokenized[ $token ] ) ) {
                    $path_vars[] = $url_values_tokenized[ $token ];
                }
            }

            if( ! empty( $path_vars ) ) {
                $structure_arr['path'] = trailingslashit( implode( '/', $path_vars ) );
            } else {
                unset( $structure_arr['path'] );
            }
        }   

        // rebuild URL query part
        if( ! empty ( $structure_arr['query'] ) ) {

            $query_vars = array();

            parse_str( $structure_arr['query'], $query_arr );

            foreach( $query_arr as $left => $right ) {
                if( ! empty( $url_values_tokenized[ $right ] ) ) {
                    $query_vars[ $url_values_tokenized[ $left ] ] = $url_values_tokenized[ $right ];
                }
            }

            if( ! empty( $query_vars ) ) {
                $structure_arr['query'] = http_build_query( $query_vars );
            } else {
                unset( $structure_arr['query'] );
            }

        }

        $final_url_str = $this->unparse_url( $structure_arr );

        return esc_url( $final_url_str ) ;
    }

    private function tokenize_path_values( $pairs = array() ): array {
        $arr = array();

        foreach( $pairs as $left => $right ) {
            $key         = '%' . $left . '%';
            $arr[ $key ] = $right;
        }

        return $arr;
    }

    private function unparse_url( $parsed_url ) {

        $scheme   = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] . '://' : '';

        $host     = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';

        $port     = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';

        $user     = isset( $parsed_url['user'] ) ? $parsed_url['user'] : '';

        $pass     = isset( $parsed_url['pass'] ) ? ':' . $parsed_url['pass'] : '';

        $pass     = ( $user || $pass ) ? "{$pass}@" : '';

        $path     = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';

        $query    = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';

        $fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';

        return "{$scheme}{$user}{$pass}{$host}{$port}{$path}{$query}{$fragment}";

    }
}
