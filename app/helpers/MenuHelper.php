<?php

namespace App\helpers;

use bornfight\wpHelpers\core\ACFProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MenuHelper
{
    private $grouped_menu_items = array();
    private $current_menu_items = array();

    public function get_nav_menu_items_by_location( $location, $args = array() ): ?array
    {

        $locations = get_nav_menu_locations();

        $location = ! empty( $locations[ $location ] ) ? $locations[ $location ] : '';

        $object = wp_get_nav_menu_object( $location );

        if ( false === $object ) {
            return array();
        }

        $menu_items = wp_get_nav_menu_items( $object->name, $args );

        if ( false === $menu_items ) {
            return array();
        }

        return $menu_items;
    }

    /**
     * This is obsolete, please use create_menu_hierarchy_with_current
     *
     * @param [type] $location
     */
    public function menu_hierarchy_create( $location ): array
    {

        $menu_locations = get_nav_menu_locations();

        if ( empty( $menu_locations[ $location ] ) ) {
            return array();
        }

        $menu = wp_get_nav_menu_object( $menu_locations[ $location ] );

        $menu_items = wp_get_nav_menu_items( $menu->term_id );

        if ( empty( $menu_items ) ) {
            return array();
        }

        $new_menu_array = array();

        foreach ( (array) $menu_items as $key => $menu_item ) {
            $new_menu_array[ $menu_item->menu_item_parent ][] = $menu_item;
        }

        $new_menu_array1 = array();

        foreach ( (array) $menu_items as $key => $menu_item ) {
            if ( isset( $new_menu_array[ $menu_item->ID ] ) ) {
                $menu_item->sub = $new_menu_array[ $menu_item->ID ];
                if ( 0 === (int) $menu_item->menu_item_parent ) {
                    $new_menu_array1[] = $menu_item;
                }
            }
        }

        return array_splice( $new_menu_array[0], 0, 15, $new_menu_array1 );
    }

    public function get_current_path(): string
    {
        global $wp;
        $url = home_url( $wp->request );

        $arr = parse_url( $url );
        if ( ! empty( $arr['path'] ) ) {
            return rtrim( $arr['path'], '/' );
        }

        return '';
    }

    public function get_menu_item_path( $url )
    {
        $arr = parse_url( $url );
        if ( ! empty( $arr['path'] ) ) {
            return rtrim( $arr['path'], '/' );
        }

        return '';
    }

    public function get_home_path()
    {
        $url = get_home_url();

        $arr = parse_url( $url );
        if ( ! empty( $arr['path'] ) ) {
            return rtrim( $arr['path'], '/' );
        }

        return '';
    }

    /**
     * Create menu hierarchy and mark current items
     * It will mark parent items as current if any of its children is current
     * @param string $location Menu location
     * @param array $args Optional arguments
     *  - aliases: array of path => alias to match current path with menu item path
     *              e.g. array( '/services' => '/our-services' ) will match
     *              current path '/our-services/xyz' with menu item path '/services'
     * @return array Hierarchical menu items with 'current' key added to each item
     */
    public function create_menu_hierarchy_with_current( $location, $args = array() ): array
    {

        $this->grouped_menu_items = array(); // gropued items by parent
        $this->current_menu_items = array(); // array of current elements

        $menu_items_arr  = array(); // menu items of array
        $menu_items_tree = array(); // hiearhical array

        $current_path = $this->get_current_path();

        $menu_locations = get_nav_menu_locations();

        if ( empty( $menu_locations[ $location ] ) ) {
            return array();
        }

        $menu = wp_get_nav_menu_object( $menu_locations[ $location ] );

        $menu_items = wp_get_nav_menu_items( $menu->term_id );

        // cast to array
        foreach ( (array) $menu_items as $menu_item ) {
            $menu_items_arr[] = $menu_item->to_array();
        }

        // group by parent
        foreach ( $menu_items_arr as $menu_item ) {

            
            $menu_item_path = $this->get_menu_item_path( $menu_item['url'] );

            // default state is false
            $menu_item['current'] = false;

            if( $this->get_home_path() == $current_path ) {

                // if homepage then only one match
                if ( $current_path === $menu_item_path ) {
                    $menu_item['current'] = true;
                }

            } else {

                // if not homepage then do partial match
                // multiple menu items can be current

                if ( strpos( $menu_item_path, $current_path ) !== false ) {
                    $menu_item['current'] = true;
                }

                // match alias
                if ( ! empty( $args['aliases'] ) ) {
                    foreach ( $args['aliases'] as $path => $alias ) {
                        if ( $path === $menu_item_path && strpos( $current_path, $alias ) !== false ) {
                            $menu_item['current'] = true;
                        }
                    }
                }
            }


            // push current to array and index by parent
            if ( $menu_item['current'] === true ) {
                $this->current_menu_items[ $menu_item['menu_item_parent'] ] = true;
            }

            if ( ! isset( $this->grouped_menu_items[ $menu_item['menu_item_parent'] ] ) ) {
                $this->grouped_menu_items[ $menu_item['menu_item_parent'] ] = array();
            }

            $this->grouped_menu_items[ $menu_item['menu_item_parent'] ][] = $menu_item;
        }

        // start recursion from root element
        if ( ! empty( $this->grouped_menu_items[0] ) ) {
            $menu_items_tree = $this->do_submenu_recursion( $this->grouped_menu_items[0] );
        }

        return $menu_items_tree;
    }

    private function do_submenu_recursion( $items = array() ): array
    {
        $arr = array();

        foreach ( $items as $menu_item ) {

            if ( ! empty( $this->current_menu_items[ $menu_item['ID'] ] ) ) {
                $menu_item['current'] = true;
            }

            if ( ! empty( $this->grouped_menu_items[ $menu_item['ID'] ] ) ) {
                $menu_item['sub'] = $this->do_submenu_recursion( $this->grouped_menu_items[ $menu_item['ID'] ] );
            }

            $arr[] = $menu_item;
        }

        return $arr;
    }

    public function get_is_active_class( \WP_Post $item, array $args ): string
    {
        if ( empty( $args['post_type'] ) || empty( $args['page_id'] ) ) {
            return '';
        }
        if ( 'page' === $args['post_type'] && (int) $item->object_id === $args['page_id'] ) {
            return 'is-active';
        }

        if ( $item->object !== 'page' && $args['post_type'] === $item->object ) {
            return 'is-active';
        }

        return '';
    }

    public function get_field( $field, $location ): array
    {
        $menu_locations = get_nav_menu_locations();

        if ( empty( $menu_locations[ $location ] ) ) {
            return array();
        }

        $menu = wp_get_nav_menu_object( $menu_locations[ $location ] );

        return ACFProvider::get_instance()->get_field( $field, 'term_' . $menu->term_id );
    }
}
