<?php

namespace App\blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use App\helpers\CtaHelper;
use App\interfaces\BlockInterface;

class IntroBlock implements BlockInterface {
	use BlockSettings;

	public function get_settings(): array {
		return array(
			'name'            => 'intro-block',
			'title'           => 'Intro Block',
			'description'     => 'Eyebrow, title, rich text and CTAs intro section.',
			'category'        => 'custom-blocks',
			'icon'            => 'align-center',
			// Live editor preview — ACF PRO AJAX-refreshes render_callback on every field change.
			'mode'            => 'preview',
			'keywords'        => array( 'intro', 'hero', 'section' ),
			'post_types'      => array( 'post', 'page' ),
			'render_callback' => array( $this, 'get_view' ),
			// Page-template slugs this block may be inserted on; ['*'] = every template.
			'templates'       => array( '*' ),
		);
	}

	public function get_view( array $block ): void {
		get_partial( 'blocks/intro-block', array_merge(
			array(
				'eyebrow' => get_field( 'eyebrow' ) ?: '',
				'title'   => get_field( 'title' ) ?: '',
				'text'    => get_field( 'text' ) ?: '',
				'ctas'    => CtaHelper::normalize_rows( get_field( 'ctas' ) ),
			),
			$this->get_section_wrapper(),
			$this->get_section_id(),
			$this->get_color_scheme(),
			$this->get_section_borders(),
			$this->get_bg_gradient()
		) );
	}
}
