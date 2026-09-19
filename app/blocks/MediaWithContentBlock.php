<?php

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use App\helpers\CtaHelper;
use App\helpers\VideoHelper;
use App\interfaces\BlockInterface;

class MediaWithContentBlock implements BlockInterface {
	use BlockSettings;

	public function get_settings(): array {
		return array(
			'name'            => 'media-with-content-block',
			'title'           => 'Media with Content Block',
			'description'     => 'Two-column section pairing a 4:3 image or video with eyebrow, title, text and CTAs.',
			'category'        => 'custom-blocks',
			'icon'            => 'align-pull-left',
			// Live editor preview — ACF PRO AJAX-refreshes render_callback on every field change.
			'mode'            => 'preview',
			'keywords'        => array( 'media', 'image', 'video', 'content' ),
			'post_types'      => array( 'post', 'page' ),
			'render_callback' => array( $this, 'get_view' ),
			// Page-template slugs this block may be inserted on; ['*'] = every template.
			'templates'       => array( '*' ),
		);
	}

	public function get_view( array $block ): void {
		$media_type = get_field( 'media_type' );
		if ( ! in_array( $media_type, array( 'image', 'video' ), true ) ) {
			$media_type = 'image';
		}

		$title = get_field( 'title' ) ?: '';

		get_partial( 'blocks/media-with-content-block', array_merge(
			array(
				'eyebrow'    => get_field( 'eyebrow' ) ?: '',
				'title'      => $title,
				'text'       => get_field( 'text' ) ?: '',
				'ctas'       => CtaHelper::normalize_rows( get_field( 'ctas' ) ),
				'media_type' => $media_type,
				// No separate alt-text field: get_responsive_image() always uses the
				// attachment's own registered alt text for the image+sizes form.
				'image'      => (int) ( get_field( 'image' ) ?: 0 ),
				'video'      => VideoHelper::normalize( get_field( 'media_video' ), $title ),
			),
			$this->get_section_wrapper(),
			$this->get_section_id(),
			$this->get_color_scheme(),
			$this->get_section_borders(),
			$this->get_alignment(),
			$this->get_highlight_content()
		) );
	}
}
