<?php

namespace App\helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Normalizes seamlessly-cloned `group_altrd8_wp_starter_component_cta`
 * repeater rows into the args expected by `get_partial('components/button', ...)`.
 *
 * Settings drive optional behaviours wired to existing front-end hooks:
 * - open_modal → 'modal_id' (Modal.js, data-modal-open — already supported by
 *   partials/components/button.php)
 * - scroll_to  → url rewritten to '#target_id'
 * - download   → 'download' => true (file URL comes from the Link field)
 *
 * Ported from Villa Argentina's App\Helpers\CtaNormalizer.
 */
class CtaHelper {
	/** @var string[] */
	public const BUTTON_STYLES = array( 'primary', 'secondary', 'tertiary' );

	/** @var string[] */
	public const ACTIONS = array( 'none', 'open_modal', 'scroll_to', 'download' );

	/**
	 * @param mixed $rows
	 * @return array<int, array<string, mixed>>
	 */
	public static function normalize_rows( mixed $rows, string $default_style = 'primary' ): array {
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$ctas = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$ctas[] = self::normalize_row( $row, $default_style );
		}

		return $ctas;
	}

	/**
	 * @param array<string, mixed> $row
	 * @return array{label: string, url: string, type: string, style: string, new_tab: bool, download: bool, modal_id: string|null}
	 */
	public static function normalize_row( array $row, string $default_style = 'primary' ): array {
		$link   = is_array( $row['link'] ?? null ) ? $row['link'] : array();
		$url    = is_string( $link['url'] ?? null ) ? $link['url'] : '';
		$title  = is_string( $link['title'] ?? null ) ? $link['title'] : '';
		$target = is_string( $link['target'] ?? null ) ? $link['target'] : '';

		$style = $row['button_style'] ?? $default_style;
		if ( ! is_string( $style ) || ! in_array( $style, self::BUTTON_STYLES, true ) ) {
			$style = $default_style;
		}

		$settings  = is_array( $row['settings'] ?? null ) ? $row['settings'] : array();
		$action    = (string) ( $settings['action'] ?? 'none' );
		if ( ! in_array( $action, self::ACTIONS, true ) ) {
			$action = 'none';
		}
		$target_id = self::sanitize_dom_id( (string) ( $settings['target_id'] ?? '' ) );

		$modal_id = null;
		$download = false;

		if ( $action === 'scroll_to' && $target_id !== '' ) {
			$url = '#' . $target_id;
		}

		if ( $action === 'open_modal' && $target_id !== '' ) {
			$modal_id = $target_id;
			// Keep a clickable link; Modal.js preventDefault()s when the dialog exists.
			if ( $url === '' ) {
				$url = '#';
			}
		}

		if ( $action === 'download' ) {
			$download = true;
		}

		return array(
			'id'       => self::sanitize_dom_id( (string) ( $settings['button_id'] ?? '' ) ) ?: null,
			'label'    => $title,
			'url'      => $url,
			'type'     => 'link',
			'style'    => $style,
			'new_tab'  => $target === '_blank',
			'download' => $download,
			'modal_id' => $modal_id,
		);
	}

	/**
	 * Strip characters that are unsafe in HTML ids / CSS #selectors.
	 */
	public static function sanitize_dom_id( string $value ): string {
		return preg_replace( '/[^A-Za-z0-9_-]/', '', $value ) ?? '';
	}
}
