<?php

use bornfight\wpHelpers\core\PartialFinder;
use App\modules\Modules;
use App\helpers\ImageHelper;

function is_frontend_request() {
	return ! is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() && ! defined( 'REST_REQUEST' );
}

function is_rest_request() {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

	$rest_prefix = rest_get_url_prefix();
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';

	return str_contains( $request_uri, $rest_prefix );
}

function bu( $url ) {
	$clean = trim( $url );

	return INCLUDE_URL . '/static/' . $clean;
}

function au( $url ) {
	$clean = trim( $url );

	return get_template_directory() . '/static/' . $clean;
}

function get_partial( $partial, $data = null, $return = false, $folder = PartialFinder::PARTIAL_FOLDER ) {
	return PartialFinder::get_instance()->get_partial( $partial, $data, $return, $folder );
}

function get_slice_partial( string $partial, array $data = array(), bool $return = false ) {
	return get_partial( $partial, $data, $return, 'slice/partials' );
}

function get_icon( string $name ): string {
	return get_partial( 'icons/icon-' . $name, array(), true, 'static' );
}

function get_content( int $id = 0 ) {
	global $post;

	if ( empty( $id ) ) {
		$id = $post->ID;
	}

	return apply_filters( 'the_content', get_post_field( 'post_content', $id ) );
}

/**
 * BWP module factory function
 * Get module instance by name
 * Return instance or echo view
 *
 * @param string $name module_name with underscore
 * @param array $data Data for module
 * @param boolean $return Return instance
 * @return void
 */
function get_module( string $name, $data, $return = false ) {

	$module = array_merge(
		array(
			'settings' => array( 'show' => true ),
		),
		$data
	);

	$class = 'App\modules\\' . str_replace( '_', '', ucwords( $name, '_' ) );

	$obj = new $class( $module );
	if ( $return ) {
		return $obj;
	}

	echo $obj->get_view();
}

/**
 * Flexible layout using ACF
 * Render modules in order. Index inherited from ACF.
 *
 * Example:
 * get_modules_partial( ACFProvider::get_instance()->get_field( 'header_modules' ) ?? array() );
 * get_modules_partial( ACFProvider::get_instance()->get_field( 'modules' ) ?? array() );
 *
 * @param null|array|boolean $modules
 * @return void
 */
function get_modules_partial( null|array|bool $modules ): void {
	if ( ! empty( $modules ) && is_array( $modules ) ) {
		foreach ( $modules as $index => $module ) {
			$module['index'] = $index;

			echo Modules::get_module( $module )->get_view();
		}
	}
}

/**
 * Static layout
 * Render modules in order. Generate index.
 *
 * Example:
 * get_static_modules_partial( ACFProvider::get_instance()->get_field( 'modules' ) ?? array() );
 * get_static_modules_partial( ACFProvider::get_instance()->get_archive_field( 'modules', NewsPostType::SLUG ) ?? array() );
 *
 * @param null|array|boolean $modules
 * @return void
 */
function get_static_modules_partial( null|array|bool $modules ): void {
	if ( ! empty( $modules ) && is_array( $modules ) ) {
		$i = 0;
		foreach ( $modules as $index => $module ) {
			$module['index'] = $i++;
			echo Modules::get_module( $module, $index )->get_view();
		}
	}
}


/**
 * Look for sizes in /app/config/Config.php
 *
 * @param   array  $args
 *
 * Example when image url is used:
 *
 * echo get_responsive_image( array(
 *     'urls'            => array(
 *         'widescreen'        => $image['url'],
 *         'widescreen_retina' => $image['url'],
 *         'desktop'           => $image['url'],
 *         'desktop_retina'    => $image['url'],
 *         'tablet'            => $image['url'],
 *         'tablet_retina'     => $image['url'],
 *         'mobile'            => $image['url'],
 *         'mobile_retina'     => $image['url'],
 *     ),
 *     'alt'             => 'Image',
 *     'aspect_ratio'    => '1-1',
 *     'object_fit'      => 'cover',
 *     'object_position' => 'center',
 *     'is_background'   => false,
 *     'lazy'            => true,
 *     'native_lazy'     => false,
 *     'priority'        => false,
 *     'animate'         => true,
 *     'width'           => '',
 *     'height'          => '',
 *     'loader_bg'       => '',
 *     'modifier_class'  => '',
 *     'show_sources'    => true,
 * ) );
 *
 * Example when image id is used:
 *
 * echo get_responsive_image( array(
 *     'image'           => $image_id, // image id
 *     'sizes'           => array(
 *         'widescreen'        => 'image_1920',
 *         'widescreen_retina' => 'image_2880',
 *         'desktop'           => 'image_1200',
 *         'desktop_retina'    => 'image_1440',
 *         'tablet'            => 'image_800',
 *         'tablet_retina'     => 'image_900',
 *         'mobile'            => 'image_600',
 *         'mobile_retina'     => 'image_700',
 *     ),
 *     'alt'             => 'Image',
 *     'aspect_ratio'    => '1-1',
 *     'object_fit'      => 'cover',
 *     'object_position' => 'center',
 *     'is_background'   => false,
 *     'lazy'            => true,
 *     'native_lazy'     => false,
 *     'priority'        => false,
 *     'animate'         => true,
 *     'width'           => '',
 *     'height'          => '',
 *     'loader_bg'       => '',
 *     'modifier_class'  => '',
 *     'show_sources'    => true,
 * ) );
 *
 * Example when multiple image id is used:
 *
 * echo get_responsive_image( array(
 *     'image'           => $image_id, // image id
 *     'sizes'           => array(
 *         'widescreen'        => 'image_1920',
 *         'widescreen_retina' => 'image_2880',
 *         'desktop'           => 'image_1200',
 *         'desktop_retina'    => 'image_1440',
 *         'tablet'            => array( $tablet_image_id, 'image_800' ),
 *         'tablet_retina'     => array( $tablet_image_id, 'image_900' ),
 *         'mobile'            => array( $mobile_image_id, 'image_600' ),
 *         'mobile_retina'     => array( $mobile_image_id, 'image_700' ),
 *     ),
 *     'alt'             => 'Image',
 *     'aspect_ratio'    => '1-1',
 *     'object_fit'      => 'cover',
 *     'object_position' => 'center',
 *     'is_background'   => false,
 *     'lazy'            => true,
 *     'native_lazy'     => false,
 *     'priority'        => false,
 *     'animate'         => true,
 *     'width'           => '',
 *     'height'          => '',
 *     'loader_bg'       => '',
 *     'modifier_class'  => '',
 *     'show_sources'    => true,
 * ) );
 */
function get_responsive_image( array $args ): string {
	// vanilla-lazyload (the JS that swaps data-src -> src) is part of the
	// frontend bundle and never loads in wp-admin/REST context (e.g. the ACF
	// block editor's live preview, which renders this via a block-renderer
	// REST call) — lazy would leave <img> with no src at all there, showing
	// as a broken image. Force eager loading outside real frontend requests.
	if ( ! is_frontend_request() ) {
		$args['lazy']        = false;
		$args['native_lazy'] = false;
	}

	if ( empty( $args['image'] ) && ! empty( $args['urls'] ) ) {
		return get_partial( 'components/responsive-image', $args, true );
	}

	if ( is_int( $args['image'] ) && ! empty( $args['sizes'] ) ) {
		$image_helper = new ImageHelper();
		$args['urls'] = array();

		foreach ( $args['sizes'] as $key => $size ) {
			if ( is_array( $size ) && ! empty( $size[0] ) && ! empty( $size[1] ) ) {
				$args['urls'][ $key ] = $image_helper->get_image_by_size_name( $size[0], $size[1] );
			} elseif ( is_string( $size ) ) {
				$args['urls'][ $key ] = $image_helper->get_image_by_size_name( $args['image'], $size );
			}
		}

		$args['alt'] = $image_helper->get_attachment_alt_text( $args['image'] );

		//check if image is .svg/.gif and hide sources
		if ( ! isset( $args['show_sources'] ) ) {
			$args['show_sources'] = ! ( ( get_post_mime_type( $args['image'] ) === 'image/svg+xml' ) || ( get_post_mime_type( $args['image'] ) === 'image/gif' ) );
		}

		//if image is gif return original image, and don't show sources
		if ( get_post_mime_type( $args['image'] ) === 'image/gif' ) {
			//get image url from image id
			$args['urls']['desktop'] = wp_get_attachment_image_url( $args['image'], 'full' );
		}

		return get_partial( 'components/responsive-image', $args, true );
	}

	return '';
}

/**
 * Render responsive video partial.
 *
 * Example with static URL:
 * echo get_responsive_video( array(
 *  'qhd_video_url'       => 'https://', // $module['video']['qhd_video_url'],
 *  'qhd_video_poster'    => 'https://', // $module['video']['qhd_video_poster'],
 *  'fhd_video_url'       => 'https://', // $module['video']['fhd_video_url'],
 *  'fhd_video_poster'    => 'https://', // $module['video']['fhd_video_poster'],
 *  'hd_video_url'        => 'https://', // $module['video']['hd_video_url'],
 *  'hd_video_poster'     => 'https://', // $module['video']['hd_video_poster'],
 *  'sd_video_url'        => 'https://', // $module['video']['sd_video_url'],
 *  'sd_video_poster'     => 'https://', // $module['video']['sd_video_poster'],
 *  'alt'                 => 'Video',
 *  'autoplay'            => false,
 *  'muted'               => false,
 *  'loop'                => false,
 *  'plays_inline'        => true,
 *  'controls'            => true,
 *  'preload'             => 'auto',
 *  'aspect_ratio'        => '16-9',
 *  'object_fit'          => 'cover',
 *  'object_position'     => 'center',
 *  'is_background'       => false,
 *  'lazy'                => true,
 *  'native_lazy'         => false,
 *  'priority'            => false,
 *  'animate'             => true,
 *  'loader_bg'           => '',
 *  'scroll_trigger'      => false,
 *  'play_button'         => false,
 *  'modifier_class'      => '',
 * ) );
 *
 * Example with poster image by id and size:
 * Look for sizes in /app/config/Config.php
 * echo get_responsive_video( array(
 *  'qhd_video_url'       => 'https://', // $module['video']['qhd_video_url'],
 *  'qhd_video_poster'    => array( $module['video']['qhd_video_poster'], 'image_2880' ),
 *  'fhd_video_url'       => 'https://', // $module['video']['fhd_video_url'],
 *  'fhd_video_poster'    => array( $module['video']['fhd_video_poster'], 'image_1440' ),
 *  'hd_video_url'        => 'https://', // $module['video']['hd_video_url'],
 *  'hd_video_poster'     => array( $module['video']['hd_video_poster'], 'image_900' ),
 *  'sd_video_url'        => 'https://', // $module['video']['sd_video_url'],
 *  'sd_video_poster'     => array( $module['video']['sd_video_poster'], 'image_600' ),
 *  'alt'                 => 'Video',
 *  'autoplay'            => false,
 *  'muted'               => false,
 *  'loop'                => false,
 *  'plays_inline'        => true,
 *  'controls'            => true,
 *  'preload'             => 'auto',
 *  'aspect_ratio'        => '16-9',
 *  'object_fit'          => 'cover',
 *  'object_position'     => 'center',
 *  'is_background'       => false,
 *  'lazy'                => true,
 *  'native_lazy'         => false,
 *  'priority'            => false,
 *  'animate'             => true,
 *  'loader_bg'           => '',
 *  'scroll_trigger'      => false,
 *  'play_button'         => false,
 *  'modifier_class'      => '',
 * ) );
 *
 * @param array $args
 * @return string
 */
function get_responsive_video( array $args ): string {
	// Same reasoning as get_responsive_image(): the lazy-load JS never runs
	// in wp-admin/REST context (e.g. the ACF block editor's live preview),
	// so lazy would leave <video>/<source> with no src at all there.
	if ( ! is_frontend_request() ) {
		$args['lazy']        = false;
		$args['native_lazy'] = false;
	}

	$defaults = array();

	// Fetch qhd poster URL by ID
	if ( isset( $args['qhd_video_poster'] ) && is_array( $args['qhd_video_poster'] ) && count( $args['qhd_video_poster'] ) == 2 ) {
		$image_helper             = new ImageHelper();
		$args['qhd_video_poster'] = $image_helper->get_image_by_size_name( $args['qhd_video_poster'][0], $args['qhd_video_poster'][1] );
	}

	// Fetch fhd poster URL by ID
	if ( isset( $args['fhd_video_poster'] ) && is_array( $args['fhd_video_poster'] ) && count( $args['fhd_video_poster'] ) == 2 ) {
		$image_helper             = new ImageHelper();
		$args['fhd_video_poster'] = $image_helper->get_image_by_size_name( $args['fhd_video_poster'][0], $args['fhd_video_poster'][1] );
	}

	// Fetch hd poster URL by ID
	if ( isset( $args['hd_video_poster'] ) && is_array( $args['hd_video_poster'] ) && count( $args['hd_video_poster'] ) == 2 ) {
		$image_helper            = new ImageHelper();
		$args['hd_video_poster'] = $image_helper->get_image_by_size_name( $args['hd_video_poster'][0], $args['hd_video_poster'][1] );
	}

	// Fetch sd poster URL by ID
	if ( isset( $args['sd_video_poster'] ) && is_array( $args['sd_video_poster'] ) && count( $args['sd_video_poster'] ) == 2 ) {
		$image_helper            = new ImageHelper();
		$args['sd_video_poster'] = $image_helper->get_image_by_size_name( $args['sd_video_poster'][0], $args['sd_video_poster'][1] );
	}

	$args = array_merge( $defaults, $args );

	return get_partial( 'components/responsive-video', $args, true );
}
