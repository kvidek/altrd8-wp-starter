<?php
/**
 * INTRO BLOCK
 *
 * @var string $eyebrow
 * @var string $title
 * @var string $text
 * @var array  $ctas | normalized rows from App\helpers\CtaHelper, args for get_partial('components/button', ...)
 * @var string $padding_top    | 'large' || 'medium' || 'small' || 'none'
 * @var string $padding_bottom | 'large' || 'medium' || 'small' || 'none'
 * @var string $color_scheme   | 'light' || 'off-light' || 'dark' || 'off-dark'
 * @var bool   $border_top
 * @var bool   $border_bottom
 * @var string $section_id
 * @var array  $bg_gradient | from App\blocks\BlockSettings::get_bg_gradient(), args for get_partial('components/bg-gradient', ...)
 */

if ( empty( $title ) && empty( $eyebrow ) && empty( $text ) ) {
	return;
}

$padding_top_class    = $padding_top === 'none' ? '0' : $padding_top;
$padding_bottom_class = $padding_bottom === 'none' ? '0' : $padding_bottom;

$modifier_classes = array(
	'c-intro-block',
	'o-section',
	'u-color-scheme-' . $color_scheme,
	'u-pt-' . $padding_top_class,
	'u-pb-' . $padding_bottom_class,
);

if ( ! empty( $border_top ) ) {
	$modifier_classes[] = 'u-border-top';
}

if ( ! empty( $border_bottom ) ) {
	$modifier_classes[] = 'u-border-bottom';
}
?>
<!--INTRO BLOCK-->
<section class="<?php echo esc_attr( implode( ' ', $modifier_classes ) ); ?>"
	<?php if ( ! empty( $section_id ) ) { ?>id="<?php echo esc_attr( $section_id ); ?>"<?php } ?>>
	<?php get_partial( 'components/bg-gradient', $bg_gradient ); ?>
	<div class="o-container">
		<?php if ( ! empty( $eyebrow ) ) { ?>
			<!--eyebrow-->
			<p class="c-intro-block__eyebrow u-b2 u-uppercase">
				<?php echo esc_html( $eyebrow ); ?>
			</p>
			<!--end eyebrow-->
		<?php } ?>
		<?php if ( ! empty( $title ) ) { ?>
			<!--title-->
			<h2 class="c-intro-block__title u-a2 u-fw-700">
				<?php echo esc_html( $title ); ?>
			</h2>
			<!--end title-->
		<?php } ?>
		<?php if ( ! empty( $text ) ) { ?>
			<!--text-->
			<div class="c-intro-block__text u-b0 u-content-editor">
				<?php echo wp_kses_post( $text ); ?>
			</div>
			<!--end text-->
		<?php } ?>
		<?php if ( ! empty( $ctas ) ) { ?>
			<!--ctas-->
			<div class="c-intro-block__ctas">
				<?php foreach ( $ctas as $cta ) { ?>
					<?php get_partial( 'components/button', $cta ); ?>
				<?php } ?>
			</div>
			<!--end ctas-->
		<?php } ?>
	</div>
</section>
<!--end INTRO BLOCK-->
