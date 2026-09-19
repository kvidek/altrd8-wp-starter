<?php

namespace App\optimization;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\optimization\BaseWPPluginsOptimization;

class WPPlugins extends BaseWPPluginsOptimization {
	public function init(): void {
		$this->deactivate_acfe_options( array(
			'remove_acfe_module_author',
			'remove_acfe_dynamic_block_types',
			'remove_acfe_dynamic_dynamic_forms',
			'remove_acfe_dynamic_post_type',
			'remove_acfe_dynamic_taxonomies',
			'remove_acfe_dynamic_options_page',
			'remove_acfe_multi_language_support',
			'remove_acfe_module_options',
			'remove_acfe_module_taxonomies_enhancements',
		) );

		// $this->deactivate_cf7_options( array(
		// 	'remove_autop' => true
		// ) );

		// add_action( 'wp_enqueue_scripts', function() {
		// 	wp_dequeue_style( 'wpml-blocks' );
		// 	wp_dequeue_style( 'contact-form-7' );
		// 	wp_dequeue_style( 'cky-style-inline' );
		// }, 20 );

		// wpml-legacy-horizontal-list-0-css
		// wpml-menu-item-0-css
		//define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
	}
}
