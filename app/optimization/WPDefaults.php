<?php

namespace App\optimization;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\optimization\BaseWPDefaultsOptimization;

class WPDefaults extends BaseWPDefaultsOptimization {
	public function init() : void {
		// $this->deactivate_gutenberg_scripts();
		// $this->deactivate_wp_comments();
		// $this->deactivate_wp_embeds();
		// $this->deactivate_wp_emoji();
		// $this->deactivate_wp_js_dependencies( array(
		// 	'remove_jquery_migrate'       => true,
		// 	'remove_jquery_migrate_admin' => true,
		// 	'remove_jquery_on_front'      => true,
		// ) );
		// $this->deactivate_wp_posts();

		// Add CDN Jquery
		// $this->add_jquery_cdn();

		// add_action( 'wp_enqueue_scripts', function() {
		// 	wp_dequeue_style( 'classic-theme-styles' );
		// 	wp_dequeue_style( 'global-styles' );
		// }, 20 );

		// Disable Native Gutenberg Features
		// add_action( 'after_setup_theme', [ $this, 'gutenberg_removals' ] );


		// disable user enumaration
		// add_filter( 'rest_pre_dispatch', function ( $result, $server, $request ) {
        //     $namespace = $request->get_route();

        //     // Block user-related endpoints
        //     if ( strpos( $namespace, '/wp/v2/users' ) === 0 ) {
        //         return new \WP_Error( 'rest_forbidden', __( 'Access to this endpoint is forbidden.' ), array( 'status' => 403 ) );
        //     }

        //     return $result;
        // }, 10, 3 );
	}

	/**
	 * Disable Native Gutenberg Features
	 */
	public function gutenberg_removals(): void {
		add_theme_support( 'disable-custom-font-sizes' );
		add_theme_support( 'editor-font-sizes', array() );
		add_theme_support( 'disable-custom-colors' );
		add_theme_support( 'disable-custom-gradients' );
		add_theme_support( 'editor-color-palette', array() );
		add_theme_support( 'editor-gradient-presets', array() );
	}
}
