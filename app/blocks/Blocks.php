<?php

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use bornfight\wpHelpers\blocks\BaseBlocks;
use WP_Block_Editor_Context;

final class Blocks extends BaseBlocks {
	public function get_blocks(): array {
		return array(
			'test-block',
			'intro-block',
			'media-with-content-block',
		);
	}

	public function get_namespace() : string {
		return 'App\blocks\\';
	}

	protected function get_default_blocks(): array {
		return array(
			'core/heading',
			'core/paragraph',
			'core/list',
			'core/list-item',
			'core/image',
			'core/quote',
			'core/buttons',
			'core/button',
			'core/block',
		);
	}

	/**
	 * Restrict ACF blocks to the page templates they declare via their
	 * `templates` settings key (defaults to ['*'] = every template).
	 * Overrides BaseBlocks' flat allow-list with per-template scoping.
	 *
	 * @param bool|array $allowed_block_types
	 * @param WP_Block_Editor_Context $block_editor_context
	 *
	 * @return bool|array
	 */
	public function filter_allowed_blocked_types( bool|array $allowed_block_types, WP_Block_Editor_Context $block_editor_context ): bool|array {
		$post     = $block_editor_context->post ?? null;
		$template = $post ? get_page_template_slug( $post->ID ) : '';

		$blocks = $this->get_default_blocks();

		foreach ( $this->get_blocks() as $block ) {
			$class          = $this->get_namespace() . str_replace( '-', '', ucwords( $block, '-' ) );
			$class_instance = new $class();

			if ( ! method_exists( $class_instance, 'get_settings' ) ) {
				continue;
			}

			$templates = $class_instance->get_settings()['templates'] ?? array( '*' );

			if ( in_array( '*', $templates, true ) || in_array( $template, $templates, true ) ) {
				$blocks[] = 'acf/' . $block;
			}
		}

		return $blocks;
	}

	/**
	 * Overrides BaseBlocks' "Bornfight" category with a "Custom Blocks" one.
	 *
	 * @param array $block_categories
	 * @param WP_Block_Editor_Context $block_editor_context
	 *
	 * @return array
	 */
	public function filter_block_categories( array $block_categories, WP_Block_Editor_Context $block_editor_context ): array {
		if ( ! empty( $block_editor_context->post ) ) {
			$block_categories[] = array(
				'slug'  => 'custom-blocks',
				'title' => 'Custom Blocks',
				'icon'  => null,
			);
		}

		return $block_categories;
	}
}