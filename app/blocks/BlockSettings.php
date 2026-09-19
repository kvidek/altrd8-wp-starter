<?php

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Shared reader for the Settings-tab ACF clone groups (spacing, color scheme,
 * section borders, section ID, alignment). This project's equivalent of
 * Villa Argentina's BlockComposer base, adapted for plain block classes
 * (no View Composer layer here) — `use BlockSettings;` in a block class
 * implementing BlockInterface.
 */
trait BlockSettings {
	/**
	 * Read a sub-field from a prefixed Settings-tab clone group.
	 */
	private function get_cloned_setting( string $group_name, string $sub_field, mixed $default = null ): mixed {
		$group = get_field( $group_name );

		if ( is_array( $group ) && array_key_exists( $sub_field, $group ) && $group[ $sub_field ] !== null && $group[ $sub_field ] !== '' ) {
			return $group[ $sub_field ];
		}

		return $default;
	}

	/**
	 * Cloned `group_altrd8_wp_starter_spacing`. Every block clones this.
	 */
	private function get_section_wrapper( string $default_top = 'medium', string $default_bottom = 'medium' ): array {
		return array(
			'padding_top'    => $this->get_cloned_setting( 'spacing', 'padding_top', $default_top ),
			'padding_bottom' => $this->get_cloned_setting( 'spacing', 'padding_bottom', $default_bottom ),
		);
	}

	/**
	 * Cloned `group_altrd8_wp_starter_color_scheme`, when a block uses it.
	 */
	private function get_color_scheme( string $default = 'light' ): array {
		return array(
			'color_scheme' => $this->get_cloned_setting( 'color_scheme', 'color_scheme', $default ),
		);
	}

	/**
	 * Cloned `group_altrd8_wp_starter_section_borders`, when a block uses it.
	 */
	private function get_section_borders(): array {
		return array(
			'border_top'    => filter_var( $this->get_cloned_setting( 'section_borders', 'border_top', false ), FILTER_VALIDATE_BOOLEAN ),
			'border_bottom' => filter_var( $this->get_cloned_setting( 'section_borders', 'border_bottom', false ), FILTER_VALIDATE_BOOLEAN ),
		);
	}

	/**
	 * Cloned `group_altrd8_wp_starter_section_id`, on every block that is
	 * its own page section.
	 */
	private function get_section_id(): array {
		$id = $this->get_cloned_setting( 'section_id', 'section_id', '' );

		return array(
			'section_id' => is_string( $id ) && $id !== '' ? sanitize_title( $id ) : '',
		);
	}

	/**
	 * Cloned `group_altrd8_wp_starter_alignment`, when a block uses it.
	 */
	private function get_alignment( string $default = 'right' ): array {
		$alignment = $this->get_cloned_setting( 'alignment', 'alignment', $default );

		return array(
			'alignment' => in_array( $alignment, array( 'left', 'right' ), true ) ? $alignment : $default,
		);
	}

	/**
	 * Plain block-level `true_false` field (not a shared clone) — read
	 * directly rather than via get_cloned_setting().
	 */
	private function get_highlight_content(): array {
		return array(
			'highlight_content' => filter_var( get_field( 'highlight_content' ), FILTER_VALIDATE_BOOLEAN ),
		);
	}

	/**
	 * Cloned `group_altrd8_wp_starter_component_bg_gradient`, when a
	 * block uses it. Nested under a single 'bg_gradient' key (rather than
	 * flattened, like get_section_wrapper()'s 'padding_top' etc.) so its
	 * generic sub-keys ('enabled', 'color', 'size', ...) can never collide
	 * with an unrelated field a block happens to also name that way.
	 *
	 * Pass the returned array straight through:
	 * get_partial('components/bg-gradient', $bg_gradient)
	 */
	private function get_bg_gradient(): array {
		$horizontal = $this->get_cloned_setting( 'bg_gradient', 'horizontal_alignment', 'right' );
		$vertical   = $this->get_cloned_setting( 'bg_gradient', 'vertical_alignment', 'top' );
		$size       = $this->get_cloned_setting( 'bg_gradient', 'size', 'medium' );
		$color      = $this->get_cloned_setting( 'bg_gradient', 'color', 'tint' );

		return array(
			'bg_gradient' => array(
				'enabled'              => filter_var( $this->get_cloned_setting( 'bg_gradient', 'enabled', false ), FILTER_VALIDATE_BOOLEAN ),
				'color'                => in_array( $color, array( 'off-light', 'tint', 'dark' ), true ) ? $color : 'tint',
				'horizontal_alignment' => in_array( $horizontal, array( 'left', 'center', 'right' ), true ) ? $horizontal : 'right',
				'vertical_alignment'   => in_array( $vertical, array( 'top', 'center', 'bottom' ), true ) ? $vertical : 'top',
				'size'                 => in_array( $size, array( 'small', 'medium', 'large' ), true ) ? $size : 'medium',
			),
		);
	}
}
