<?php

namespace App\acf;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use App\config\ACFConfig;

class ACFDefaults {
	public function init(): void {
		// Create module - thumbnails folder inside static
		// add_filter( 'acfe/flexible/thumbnail', array( $this, 'add_thumbnails_on_module' ), 10, 3 );

		// Add custom styles for ACF tooltip
		// add_action( 'admin_head', array( $this, 'multi_column_layout_thumbnails_style' ) );

		// Add custom styles for ACF module title
		// add_action( 'admin_head', array( $this, 'module_title_thumbnail_style' ) );

		// Add thumbnail to layout title
		// add_filter( 'acf/fields/flexible_content/layout_title', array( $this, 'add_thumbnail_to_layout_title' ), 10, 4 );

		// add minimal toolbar
		// add_filter( 'acf/fields/wysiwyg/toolbars', array( $this, 'add_minimal_toolbar' ) );
	}

	public function add_thumbnails_on_module( $thumbnail, $field, $layout ) {
		$filename = str_replace( '-', '_', $layout['name'] );
		$filename = 'module-thumbnails/' . $filename . '.png';
		return bu( $filename );
	}

	public function add_thumbnail_to_layout_title( $title, $field, $layout, $i ) {
		$title = '';

		// Display thumbnail image from module-thumbnails folder
		$filename      = str_replace( '-', '_', $layout['name'] );
		$thumbnail_url = bu( 'module-thumbnails/' . $filename . '.png' );

		$title .= '<span class="acf-layout-thumbnail"><img src="' . esc_url( $thumbnail_url ) . '" height="60px" /></span>';
		$title .= '<span>' . esc_html( $layout['label'] ) . '</span>';

		return $title;
	}

	public function multi_column_layout_thumbnails_style() {
		echo '<style>
             body .acf-tooltip {
                 max-width: 100%;
                 transform: translateX(50%);
             }
             body .acf-tooltip ul { 
                 display: grid;
                 grid-template-columns: repeat(4, 1fr);
             }
             @media (max-width: 1440px) {
                 body .acf-tooltip ul {
                     grid-template-columns: repeat(3, 1fr);
                 }
             }
         </style>';
	}

	public function module_title_thumbnail_style() {
		echo '<style>
             body .acf-fc-layout-title {
                display: flex;
                align-items: center;
                gap: 10px;
             }
             body .acf-layout-thumbnail {
                margin: 0;
                border: 1px solid #ccd0d4;
                margin-top: -10px;
                transform: translateY(1px);
             }
             body .acf-layout-thumbnail img {
                width: auto;
                height: 60px;
                display: block;
             }
             body .acf-fc-layout-original-title {
                display: flex;
                align-items: center;
                gap: 10px;
             }
        </style>';
	}

	public function add_minimal_toolbar( $toolbars ) {
		$toolbars['Minimal']    = array();
		$toolbars['Minimal'][1] = array(
			'bold',
			'italic',
			'underline',
			'link',
		);

		return $toolbars;
	}
}
