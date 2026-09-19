<?php

namespace App\bundles;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use bornfight\wpHelpers\bundles\AssetBundle;
use App\config\Config;

class Altrd8WpStarterAssets extends AssetBundle {
//    public $asyncCss = true;

	public array $js;
	public array $css;

	/**
	 * Block editor stylesheet (static/scss/editor.scss). Kept out of $css so
	 * it never gets enqueued on the front end by register()/enqueue_styles() —
	 * only get_editor_css() reads it.
	 *
	 * Deliberately never wp_enqueue_style()'d into wp-admin: that would load
	 * on the *admin document* (post list, settings screens, the whole editor
	 * chrome), not just the block editor's canvas iframe, and this stylesheet
	 * resets fonts/spacing site-wide — it would clobber core admin UI styles
	 * (e.g. admin font sizes) everywhere, not just inside the post content
	 * area. Instead its CSS text is inlined via the block_editor_settings_all
	 * filter (see get_editor_css()), which WordPress scopes to the editor
	 * canvas iframe only.
	 */
	public ?array $editor_css = null;

	public function __construct() {
		$manifest_path = get_template_directory() . '/static/dist/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			$this->set_js_data();
			$this->set_css_data();
			$this->set_editor_css_data();
			return;
		}

		$manifest_content = file_get_contents( $manifest_path );
		$manifest         = json_decode( $manifest_content, true );

		if ( ! is_array( $manifest ) ) {
			$this->set_js_data();
			$this->set_css_data();
			$this->set_editor_css_data();
			return;
		}

		$this->js  = array();
		$this->css = array();

		foreach ( $manifest as $key => $value ) {
			// Skip source maps
			if ( str_ends_with( $key, '.map' ) ) {
				continue;
			}

			// Block editor stylesheet — admin-only, handled separately below.
			if ( $key === 'editor.css' ) {
				$this->editor_css = array(
					'path'           => ltrim( str_replace( '/static/', '', $value ), '/' ),
					'timestamp_bust' => false,
				);
				continue;
			}

			if ( str_ends_with( $key, '.js' ) ) {
				$handle              = 'bwp-' . basename( $key, '.js' );
				$this->js[ $handle ] = array(
					'path'           => ltrim( str_replace( '/static/', '', $value ), '/' ),
					'timestamp_bust' => false,
				);
			} elseif ( str_ends_with( $key, '.css' ) ) {
				$handle               = 'bwp-' . basename( $key, '.css' );
				$this->css[ $handle ] = array(
					'path'           => ltrim( str_replace( '/static/', '', $value ), '/' ),
					'in_footer'      => false,
					'timestamp_bust' => false,
				);
			}
		}

		// Add localization for main bundle if it exists
		if ( isset( $this->js['bwp-bundle'] ) ) {
			$this->js['bwp-bundle']['localize'] = array(
				'object' => 'frontend_rest_object',
				'data'   => $this->get_localize_data(),
			);
		}
	}

	public function set_js_data(): void {
		$this->js = array(
			'AppVendor' => array(
				'path'     => 'dist/vendor.js',
				'version'  => 1.0,
				'localize' => array(
					'object' => 'frontend_rest_object',
					'data'   => $this->get_localize_data()
				),
				'timestamp_bust' => true
			),
			'AppBundle' => array(
				'path'    => 'dist/bundle.js',
				'version' => 1.0,
				'timestamp_bust' => true
			),
		);
	}

	public function set_css_data(): void {
		$this->css = array(
			'AppMainCSS' => array(
				'path'      => 'dist/style.css',
				'in_footer' => false,
				'version'   => 1.0,
				'timestamp_bust' => true
			),
		);
	}

	public function set_editor_css_data(): void {
		$this->editor_css = array(
			'path'           => 'dist/editor.css',
			'timestamp_bust' => true,
		);
	}

	public function get_localize_data(): array {
		return array(
			'rest_url' => trailingslashit(get_rest_url( null, Config::get_api_namespace() )),
		);
	}

	/**
	 * Compiled editor stylesheet's CSS text, ready to inline into the block
	 * editor canvas iframe via `block_editor_settings_all`'s `styles` array.
	 * Returns '' when the file hasn't been built yet.
	 */
	public static function get_editor_css(): string {
		$bundle = new static();

		if ( empty( $bundle->editor_css ) ) {
			return '';
		}

		$path = $bundle->get_base_path() . $bundle->editor_css['path'];

		if ( ! file_exists( $path ) ) {
			return '';
		}

		return self::rewrite_relative_editor_css_urls( file_get_contents( $path ) );
	}

	/**
	 * Rewrite relative `url()` references (fonts, background images) to
	 * absolute theme URLs.
	 *
	 * css-loader is configured with `url: false`, so these stay exactly as
	 * authored relative to static/dist/ (e.g. `url(../fonts/Roboto.woff2)`).
	 * That resolves fine for a linked <link> stylesheet, but this CSS is
	 * inlined as literal <style> text on the block editor page — relative
	 * URLs in an inline <style> resolve against the *document's* URL
	 * (/wp-admin/post.php), not the theme, and would 404. data:/http(s)://
	 * absolute/protocol-relative URLs are left untouched.
	 */
	private static function rewrite_relative_editor_css_urls( string $css ): string {
		return preg_replace_callback(
			'/url\((["\']?)(?!data:|https?:\/\/|\/\/|\/)([^"\')]+)\1\)/i',
			function ( array $matches ): string {
				$quote         = $matches[1];
				$relative_path = $matches[2];
				$absolute_url  = INCLUDE_URL . self::resolve_relative_asset_path( 'static/dist', $relative_path );

				return 'url(' . $quote . esc_url( $absolute_url ) . $quote . ')';
			},
			$css
		) ?? $css;
	}

	/**
	 * Resolve a `../`-relative path against a base directory (both relative
	 * to the theme root), returning a theme-root-relative path (leading `/`).
	 */
	private static function resolve_relative_asset_path( string $base_dir, string $relative_path ): string {
		$parts = array();

		foreach ( explode( '/', $base_dir . '/' . $relative_path ) as $segment ) {
			if ( $segment === '' || $segment === '.' ) {
				continue;
			}

			if ( $segment === '..' ) {
				array_pop( $parts );
				continue;
			}

			$parts[] = $segment;
		}

		return '/' . implode( '/', $parts );
	}
}
