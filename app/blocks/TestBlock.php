<?php

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\blocks\BaseBlocks;
use App\interfaces\BlockInterface;

class TestBlock implements BlockInterface{
	public function get_settings(): array {
		return array(
			'name'            => 'test-block',
			'title'           => 'Test Block',
			'description'     => 'Test Block',
			'category'        => 'custom-blocks',
			'icon'            => null,
			// 'preview' makes ACF PRO live-refresh this block's render_callback output
			// in the editor whenever a field changes, without saving/reloading.
			'mode'            => 'preview',
			'keywords'        => array( 'test' ),
			'post_types'      => array( 'post', 'page' ),
			'example'         => array(
				'attributes' => array(
					'mode' => 'preview',
				)
			),
			'render_callback' => array( $this, 'get_view' ),
			// Not a native ACF/WP block arg — read back by App\blocks\Blocks::filter_allowed_blocked_types()
			// to restrict which page templates this block can be inserted on. ['*'] = every template.
			'templates'       => array( '*' ),
		);
	}

	public function get_view( array $block ): void {

		get_partial('blocks/test', array());
	}
}