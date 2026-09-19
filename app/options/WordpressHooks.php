<?php

namespace App\options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use App\config\Config;
use App\config\MenuConfig;

class WordpressHooks {

	public function init(): void {
		add_action( 'init', array( $this, 'on_init' ) );
		add_filter( 'wp_image_editors', array( $this, 'wpb_image_editor_default_to_gd' ) );

		// CF fix
		add_action( 'init', array( $this, 'allow_rich_edit_cloud_front' ), 9 );
		remove_action( 'wp_head', 'wp_generator' );
		$this->add_theme_support_options();
		$this->remove_oembed_author_for_pages();
	}

	public function on_init(): void {
		if ( function_exists( 'bfai_register_image_sizes' ) ) {
			bfai_register_image_sizes( Config::get_image_sizes() );
		}

		add_filter( 'user_can_richedit', '__return_true' );

		register_nav_menus( MenuConfig::get_menus() );
	}

	public function add_theme_support_options(): void {
		add_theme_support( 'align-wide' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'editor-styles' );
	}

	public function wpb_image_editor_default_to_gd( $editors ): array {
		$gd_editor = 'WP_Image_Editor_GD';
		$editors   = array_diff( $editors, array( $gd_editor ) );
		array_unshift( $editors, $gd_editor );

		return $editors;
	}

	public function allow_rich_edit_cloud_front(): void {
		add_filter( 'user_can_richedit', '__return_true' );
	}

	public function remove_oembed_author_for_pages(): void {
		add_filter( 'oembed_response_data', function( $data, $post ) {
			if ( $post instanceof WP_Post && $post->post_type === 'page' ) {
				unset( $data['author_name'], $data['author_url'] );
			}
			return $data;
		}, 10, 2 );
	}
}
