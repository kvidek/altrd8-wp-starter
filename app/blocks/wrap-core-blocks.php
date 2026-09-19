<?php

/**
 * Wrap rendered core blocks in theme layout markup.
 *
 * Each qualifying top-level core block (dropped directly into a page's
 * content — not inside an ACF block, which brings its own section wrapper
 * via App\blocks\BlockSettings::get_section_wrapper()) gets its own
 * o-section/o-container wrapper so the u-content-editor rhythm applies to it.
 *
 * Spacing: native block padding presets (registered below via
 * wp_theme_json_data_theme) map to u-pt-* / u-pb-* on the block element
 * itself, matching static/scss/utilities/_utilities.spacing.scss.
 *
 * Ported from Villa Argentina's app/Blocks/wrap-core-blocks.php. This stack
 * has no theme.json file on disk and no Luge/motion library, so the reveal
 * attribute is dropped and the spacing preset list matches this project's
 * four-step scale (no "xl" step exists here).
 */

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Core blocks that get their own o-section/o-container wrapper. Kept in
 * sync with App\blocks\Blocks::get_default_blocks() (minus core/list-item
 * and core/button, which are inner blocks of core/list and core/buttons
 * respectively and are skipped via the nesting-depth check below, same as
 * any other block nested inside another).
 */
const CORE_BLOCKS_WITH_SECTION_WRAPPER = array(
	'core/heading',
	'core/paragraph',
	'core/list',
	'core/image',
	'core/quote',
	'core/buttons',
	'core/block',
);

/**
 * Spacing preset slugs aligned with _utilities.spacing.scss (no "xl" step in this project).
 */
const CORE_BLOCK_SPACING_SLUGS = array(
	'none',
	'small',
	'medium',
	'large',
);

const CORE_BLOCK_DEFAULT_PADDING = 'none';

/**
 * Render depth for nested core blocks (e.g. list-item inside list).
 */
const CORE_BLOCK_WRAPPER_DEPTH_KEY = 'altrd8_wp_starter_core_block_wrapper_depth';

/**
 * Mark the next render_block call as nested (inner block of a parent).
 */
add_filter(
	'pre_render_block',
	function ( $pre_render, $parsed_block, $parent_block ) {
		if ( $parent_block instanceof \WP_Block ) {
			$GLOBALS[ CORE_BLOCK_WRAPPER_DEPTH_KEY ] = ( $GLOBALS[ CORE_BLOCK_WRAPPER_DEPTH_KEY ] ?? 0 ) + 1;
		}

		return $pre_render;
	},
	10,
	3
);

/**
 * Enable padding controls on wrapped core blocks (native block spacing UI).
 *
 * Core paragraph/heading ship with padding support but hide the control via
 * `__experimentalDefaultControls.padding` = false — merge and expose padding.
 */
add_filter(
	'register_block_type_args',
	function ( $args, $block_type ) {
		if ( ! in_array( $block_type, CORE_BLOCKS_WITH_SECTION_WRAPPER, true ) ) {
			return $args;
		}

		$existing                 = is_array( $args['supports']['spacing'] ?? null ) ? $args['supports']['spacing'] : array();
		$default_controls         = is_array( $existing['__experimentalDefaultControls'] ?? null ) ? $existing['__experimentalDefaultControls'] : array();
		$stable_default_controls  = is_array( $existing['defaultControls'] ?? null ) ? $existing['defaultControls'] : array();

		$args['supports']['spacing'] = array_merge(
			$existing,
			array(
				// Vertical padding only (no horizontal controls in Dimensions).
				'padding'                        => array( 'top', 'bottom' ),
				'__experimentalDefaultControls'  => array_merge(
					$default_controls,
					array( 'padding' => true )
				),
				'defaultControls'                => array_merge(
					$stable_default_controls,
					array( 'padding' => true )
				),
			)
		);

		return $args;
	},
	20,
	2
);

/**
 * Register spacing presets for the block editor (matches theme utility scale).
 * Works without a theme.json file on disk — WordPress builds a default
 * WP_Theme_JSON_Data object this filter can still merge into.
 */
add_filter(
	'wp_theme_json_data_theme',
	function ( $theme_json ) {
		$data            = $theme_json->get_data();
		$data['version'] = $data['version'] ?? 2;

		$data['settings']['spacing']['padding']             = true;
		$data['settings']['spacing']['defaultSpacingSizes']  = false;

		$data['settings']['spacing']['spacingSizes'] = array(
			array(
				'name' => __( 'None', 'altrd8-wp-starter' ),
				'slug' => 'none',
				'size' => '0',
			),
			array(
				'name' => __( 'Small', 'altrd8-wp-starter' ),
				'slug' => 'small',
				'size' => 'var(--s-60)',
			),
			array(
				'name' => __( 'Medium', 'altrd8-wp-starter' ),
				'slug' => 'medium',
				'size' => 'var(--s-90)',
			),
			array(
				'name' => __( 'Large', 'altrd8-wp-starter' ),
				'slug' => 'large',
				'size' => 'var(--s-125)',
			),
		);

		$data['settings']['spacing']['customSpacingSize'] = false;

		return $theme_json->update_with( $data );
	},
	100
);

/**
 * Map block spacing attributes to padding utility classes.
 *
 * Returns an empty list when both sides are the default, so untouched blocks
 * stay class-free.
 */
function core_block_padding_classes( array $block ): array {
	$spacing = $block['attrs']['style']['spacing'] ?? array();
	$padding = is_array( $spacing['padding'] ?? null ) ? $spacing['padding'] : array();

	$top_raw    = $padding['top'] ?? ( $spacing['paddingTop'] ?? null );
	$bottom_raw = $padding['bottom'] ?? ( $spacing['paddingBottom'] ?? null );

	$padding_top    = normalize_core_block_spacing_slug( $top_raw ) ?? CORE_BLOCK_DEFAULT_PADDING;
	$padding_bottom = normalize_core_block_spacing_slug( $bottom_raw ) ?? CORE_BLOCK_DEFAULT_PADDING;

	if ( $padding_top === CORE_BLOCK_DEFAULT_PADDING && $padding_bottom === CORE_BLOCK_DEFAULT_PADDING ) {
		return array();
	}

	return array(
		'u-pt-' . $padding_top,
		'u-pb-' . $padding_bottom,
	);
}

/**
 * Move the block's padding utilities onto its own root tag.
 */
function apply_core_block_padding( string $html, array $block ): string {
	$classes = core_block_padding_classes( $block );

	if ( $classes === array() ) {
		return $html;
	}

	$processor = new \WP_HTML_Tag_Processor( $html );

	if ( ! $processor->next_tag() ) {
		return $html;
	}

	foreach ( $classes as $class ) {
		$processor->add_class( $class );
	}

	return $processor->get_updated_html();
}

/**
 * Paragraphs used as manual spacers — the content rhythm handles that now.
 */
function is_empty_core_paragraph( string $html ): bool {
	if ( preg_match( '/<(?:img|iframe|video|audio)\b/i', $html ) ) {
		return false;
	}

	$text = str_replace( array( '&nbsp;', "\xc2\xa0" ), ' ', wp_strip_all_tags( $html ) );

	return trim( $text ) === '';
}

/**
 * Parse preset value (var:preset|spacing|medium) or plain slug.
 */
function normalize_core_block_spacing_slug( mixed $value ): ?string {
	if ( ! is_string( $value ) || $value === '' ) {
		return null;
	}

	if ( preg_match( '/var:preset\|spacing\|([a-z0-9-]+)/', $value, $matches ) ) {
		$value = $matches[1];
	}

	if ( preg_match( '/--wp--preset--spacing--([a-z0-9-]+)/', $value, $matches ) ) {
		$value = $matches[1];
	}

	if ( ! in_array( $value, CORE_BLOCK_SPACING_SLUGS, true ) ) {
		return null;
	}

	return $value;
}

/**
 * Remove padding applied to the inner block so spacing lives on o-section only.
 */
function strip_core_block_inner_spacing( string $html ): string {
	$html = preg_replace(
		'/\s*has-(?:none|small|medium|large)-padding-(?:top|bottom|left|right)\b/',
		'',
		$html
	);

	$html = preg_replace_callback(
		'/\s*style="([^"]*)"/i',
		function ( array $matches ): string {
			$style = preg_replace( '/\s*padding-(?:top|bottom|left|right)\s*:[^;"]+;?/i', '', $matches[1] );
			$style = trim( $style, " \t\n\r\0\x0B;" );

			return $style === '' ? '' : ' style="' . esc_attr( $style ) . '"';
		},
		$html
	);

	return $html;
}

add_filter(
	'render_block',
	function ( string $block_content, array $block, $block_instance = null ): string {
		$nested_depth = $GLOBALS[ CORE_BLOCK_WRAPPER_DEPTH_KEY ] ?? 0;

		if ( $nested_depth > 0 ) {
			$GLOBALS[ CORE_BLOCK_WRAPPER_DEPTH_KEY ] = $nested_depth - 1;

			return $block_content;
		}

		$block_name = $block['blockName'] ?? '';

		if ( $block_content === '' || ! str_starts_with( $block_name, 'core/' ) ) {
			return $block_content;
		}

		if ( ! in_array( $block_name, CORE_BLOCKS_WITH_SECTION_WRAPPER, true ) ) {
			return $block_content;
		}

		if ( $block_name === 'core/paragraph' && is_empty_core_paragraph( $block_content ) ) {
			return '';
		}

		$inner_content = apply_core_block_padding(
			strip_core_block_inner_spacing( $block_content ),
			$block
		);

		$block_slug = str_replace( 'core/', '', $block_name );

		return '<section class="o-section c-core-block-wrapper">'
			. '<div class="o-container c-core-block c-core-block--' . esc_attr( $block_slug ) . ' u-content-editor">'
			. $inner_content
			. '</div></section>';
	},
	10,
	3
);
