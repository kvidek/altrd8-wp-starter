<?php
/**
 * MEDIA WITH CONTENT BLOCK
 *
 * @var string $eyebrow
 * @var string $title
 * @var string $text
 * @var array  $ctas       | normalized rows from App\helpers\CtaHelper
 * @var string $media_type | 'image' || 'video'
 * @var int    $image      | attachment ID, used when media_type === 'image'
 * @var array  $video      | normalized from App\helpers\VideoHelper, used when media_type === 'video'
 * @var string $padding_top    | 'large' || 'medium' || 'small' || 'none'
 * @var string $padding_bottom | 'large' || 'medium' || 'small' || 'none'
 * @var string $color_scheme   | 'light' || 'off-light' || 'dark' || 'off-dark'
 * @var bool   $border_top
 * @var bool   $border_bottom
 * @var string $alignment  | 'left' || 'right' — which side the media column sits on
 * @var bool   $highlight_content
 * @var string $section_id
 */

if ( empty( $title ) ) {
	return;
}

$has_image = $media_type === 'image' && ! empty( $image );
$has_video = $media_type === 'video' && ! empty( $video['has_video'] );
$has_media = $has_image || $has_video;

$padding_top_class    = $padding_top === 'none' ? '0' : $padding_top;
$padding_bottom_class = $padding_bottom === 'none' ? '0' : $padding_bottom;

$section_classes = array(
	'o-section',
	'o-section--full-width',
	'u-color-scheme-' . $color_scheme,
	'u-pt-' . $padding_top_class,
	'u-pb-' . $padding_bottom_class,
);

if ( ! empty( $border_top ) ) {
	$section_classes[] = 'u-border-top';
}

if ( ! empty( $border_bottom ) ) {
	$section_classes[] = 'u-border-bottom';
}

if ( ! empty( $highlight_content ) ) {
	$section_classes[] = 'o-section--highlight-content';
}

$module_classes = array(
	'c-media-with-content-block',
	'c-media-with-content-block--' . $alignment,
);
?>
<!--MEDIA WITH CONTENT BLOCK-->
<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>"
	<?php if ( ! empty( $section_id ) ) { ?>id="<?php echo esc_attr( $section_id ); ?>"<?php } ?>>
	<div class="<?php echo esc_attr( implode( ' ', $module_classes ) ); ?>">
		<!--content-->
		<div class="c-media-with-content-block__content">
			<?php if ( ! empty( $eyebrow ) ) { ?>
				<p class="c-media-with-content-block__eyebrow u-b1 u-uppercase u-fw-700">
					<?php echo esc_html( $eyebrow ); ?>
				</p>
			<?php } ?>
			<h2 class="c-media-with-content-block__title u-a3 u-fw-700">
				<?php echo esc_html( $title ); ?>
			</h2>
			<?php if ( ! empty( $text ) ) { ?>
				<div class="c-media-with-content-block__text u-b0 u-content-editor">
					<?php echo wp_kses_post( $text ); ?>
				</div>
			<?php } ?>
			<?php if ( ! empty( $ctas ) ) { ?>
				<div class="c-media-with-content-block__ctas">
					<?php foreach ( $ctas as $cta ) { ?>
						<?php get_partial( 'components/button', $cta ); ?>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<!--end content-->
		<?php if ( $has_media ) { ?>
			<!--media-->
			<div class="c-media-with-content-block__media">
				<?php
				if ( $has_video ) {
					echo get_responsive_video( array(
						'qhd_video_url'    => $video['qhd_video_url'],
						'qhd_video_poster' => $video['qhd_video_poster'],
						'fhd_video_url'    => $video['fhd_video_url'],
						'fhd_video_poster' => $video['fhd_video_poster'],
						'hd_video_url'     => $video['hd_video_url'],
						'hd_video_poster'  => $video['hd_video_poster'],
						'sd_video_url'     => $video['sd_video_url'],
						'sd_video_poster'  => $video['sd_video_poster'],
						'alt'              => $video['alt'],
						'aspect_ratio'     => '4-3',
						'object_fit'       => 'cover',
						'object_position'  => 'center',
						'autoplay'         => true,
						'muted'            => true,
						'loop'             => true,
						'controls'         => false,
						'modifier_class'   => 'c-media-with-content-block__video',
					) );
				} elseif ( $has_image ) {
					echo get_responsive_image( array(
						'image'           => $image,
						'sizes'           => array(
							'widescreen'        => 'image_1200',
							'widescreen_retina' => 'image_1920',
							'desktop'           => 'image_900',
							'desktop_retina'    => 'image_1440',
							'tablet'            => 'image_700',
							'tablet_retina'     => 'image_900',
							'mobile'            => 'image_600',
							'mobile_retina'     => 'image_800',
						),
						'aspect_ratio'    => '4-3',
						'object_fit'      => 'cover',
						'object_position' => 'center',
						'modifier_class'  => 'c-media-with-content-block__image',
					) );
				}
				?>
			</div>
			<!--end media-->
		<?php } ?>
	</div>
</section>
<!--end MEDIA WITH CONTENT BLOCK-->
