<?php
/**
 * BG GRADIENT
 *
 * Soft decorative background-gradient blob. Absolutely positioned, so the
 * containing element must have `position: relative` (every `.o-section` does
 * already) — see static/scss/components/common/_components.bg-gradient.scss.
 *
 * @var bool   $enabled
 * @var string $color                 | 'off-light' || 'tint' || 'dark'
 * @var string $horizontal_alignment  | 'left' || 'center' || 'right'
 * @var string $vertical_alignment    | 'top' || 'center' || 'bottom'
 * @var string $size                  | 'small' || 'medium' || 'large'
 */

if ( empty( $enabled ) ) {
	return;
}

if ( empty( $color ) ) {
	$color = 'tint';
}

if ( empty( $horizontal_alignment ) ) {
	$horizontal_alignment = 'right';
}

if ( empty( $vertical_alignment ) ) {
	$vertical_alignment = 'top';
}

if ( empty( $size ) ) {
	$size = 'medium';
}

$classes = array(
	'c-bg-gradient',
	'c-bg-gradient--' . $color,
	'c-bg-gradient--h-' . $horizontal_alignment,
	'c-bg-gradient--v-' . $vertical_alignment,
	'c-bg-gradient--' . $size,
);
?>
<span class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-hidden="true"></span>
