<?php

namespace App\helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Normalizes cloned `group_altrd8_wp_starter_component_video` ACF data
 * into args for the theme's `get_responsive_video()` global helper
 * (app/global-theme-functions.php).
 *
 * Ported from Villa Argentina's App\Helpers\VideoFieldNormalizer, adapted to
 * this project's named image-size registry (App\config\Config::get_image_sizes())
 * instead of on-the-fly arbitrary-dimension resizing.
 */
class VideoHelper {
	/** Poster sizes, from Config::get_image_sizes(). */
	private const POSTER_SIZES = array(
		'qhd' => 'image_1440',
		'fhd' => 'image_900',
		'hd'  => 'image_700',
		'sd'  => 'image_600',
	);

	/**
	 * @param mixed $video Cloned video component group from ACF
	 * @return array<string, mixed>
	 */
	public static function normalize( mixed $video, string $default_alt = '' ): array {
		if ( ! is_array( $video ) ) {
			return self::empty_props( $default_alt );
		}

		$source = $video['source'] ?? 'file';
		$alt    = is_string( $video['alt'] ?? null ) ? trim( $video['alt'] ) : '';

		if ( $source === 'url' ) {
			$qhd_url = self::string_url( $video['qhd_video_url'] ?? '' );
			$fhd_url = self::string_url( $video['fhd_video_url'] ?? '' );
			$hd_url  = self::string_url( $video['hd_video_url'] ?? '' );
			$sd_url  = self::string_url( $video['sd_video_url'] ?? '' );
		} else {
			$qhd_url = self::attachment_url( $video['qhd_video'] ?? 0 );
			$fhd_url = self::attachment_url( $video['fhd_video'] ?? 0 );
			$hd_url  = self::attachment_url( $video['hd_video'] ?? 0 );
			$sd_url  = self::attachment_url( $video['sd_video'] ?? 0 );
		}

		$poster_id = self::attachment_id( $video['poster'] ?? 0 );
		$posters   = $poster_id > 0 ? self::resolve_posters( $poster_id ) : array();
		$fhd_poster = $posters['fhd'] ?? '';

		if ( $fhd_url === '' || $fhd_poster === '' ) {
			return self::empty_props( $default_alt );
		}

		return array(
			'has_video'        => true,
			'qhd_video_url'    => $qhd_url,
			'qhd_video_poster' => $posters['qhd'] ?? '',
			'fhd_video_url'    => $fhd_url,
			'fhd_video_poster' => $fhd_poster,
			'hd_video_url'     => $hd_url,
			'hd_video_poster'  => $posters['hd'] ?? '',
			'sd_video_url'     => $sd_url,
			'sd_video_poster'  => $posters['sd'] ?? '',
			'alt'              => $alt !== '' ? $alt : ( $default_alt !== '' ? $default_alt : __( 'Video', 'altrd8-wp-starter' ) ),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function empty_props( string $default_alt = '' ): array {
		$alt = $default_alt !== '' ? $default_alt : __( 'Video', 'altrd8-wp-starter' );

		return array(
			'has_video'        => false,
			'qhd_video_url'    => '',
			'qhd_video_poster' => '',
			'fhd_video_url'    => '',
			'fhd_video_poster' => '',
			'hd_video_url'     => '',
			'hd_video_poster'  => '',
			'sd_video_url'     => '',
			'sd_video_poster'  => '',
			'alt'              => $alt,
		);
	}

	/**
	 * @return array<string, string>
	 */
	private static function resolve_posters( int $poster_id ): array {
		$image_helper = new ImageHelper();
		$posters      = array();

		foreach ( self::POSTER_SIZES as $breakpoint => $size_name ) {
			$posters[ $breakpoint ] = (string) $image_helper->get_image_by_size_name( $poster_id, $size_name );
		}

		return $posters;
	}

	private static function string_url( mixed $value ): string {
		return is_string( $value ) ? trim( $value ) : '';
	}

	private static function attachment_id( mixed $attachment ): int {
		if ( is_numeric( $attachment ) ) {
			return (int) $attachment;
		}

		if ( is_array( $attachment ) && isset( $attachment['ID'] ) ) {
			return (int) $attachment['ID'];
		}

		return 0;
	}

	private static function attachment_url( mixed $attachment ): string {
		$id = self::attachment_id( $attachment );

		if ( $id <= 0 ) {
			return '';
		}

		$url = wp_get_attachment_url( $id );

		return is_string( $url ) ? $url : '';
	}
}
