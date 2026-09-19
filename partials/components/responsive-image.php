<?php
/*
  RESPONSIVE IMAGE

  Required:
    - $urls: {Array}

  Optional:
    - $alt: {String} | default: 'Image'
    - $aspect_ratio: {String} | default: '1-1' -> '1-1' || '2-1' || '1-2' || '3-1' || '1-3' || '3-2' || '2-3' || '4-3' || '3-4' || '16-9' || '9-16' || 'auto' || 'adopt'
    - $object_fit: {String} | default: 'cover' -> 'cover' || 'contain'
    - $object_position: {String} | default: 'center' -> 'center' || 'top' || 'bottom' || 'left' || 'right'
    - $is_background: {Boolean} | default: false - used with aspect_ratio: 'adopt' to make media fill the first relative parent element
    - $lazy: {Boolean} | default: true - lazy load media using vanilla lazy load lib (https://github.com/verlok/vanilla-lazyload), not to be used for above the fold media or when using priority option
    - $native_lazy: {Boolean} | default: false - lazy load media natively with browser-level media lazy loading, not to be used for above the fold media or when using priority option
    - $priority: {Boolean} | default: false - set media fetch priority to high, all lazy loading options must be disabled, used with above the fold media which should not be lazy loaded
    - $animate: {Boolean} | default: true - show/hide media loader for lazy loaded media
    - $width: {Number} | if aspect ratio is set to 'auto' use this option to specify image width
    - $height: {Number} | if aspect ratio is set to 'auto' use this option to specify image height
    - $loader_bg: {String} | used for changing the media loader background if it needs to be synced with section background
    - $modifier_class: {String} | general modifier class rendered on the main component wrapper

  Usage:
    <?php echo get_responsive_image(array(
        'urls' => array(
            'widescreen' => 'https://picsum.photos/id/237/2000/2000',
            'widescreen_retina' => 'https://picsum.photos/id/237/2400/2400',
            'desktop' => 'https://picsum.photos/id/236/1600/1600',
            'desktop_retina' => 'https://picsum.photos/id/236/1800/1800',
            'tablet' => 'https://picsum.photos/id/235/1000/1000',
            'tablet_retina' => 'https://picsum.photos/id/235/1200/1200',
            'mobile' => 'https://picsum.photos/id/234/600/600',
            'mobile_retina' => 'https://picsum.photos/id/234/800/800',
        ),
        'alt' => 'Image',
        'aspect_ratio' => '1-1',
        'object_fit' => 'cover',
        'object_position' => 'center',
        'is_background' => false,
        'lazy' => true,
        'native_lazy' => false,
        'priority' => false,
        'animate' => true,
        'width' => '',
        'height' => '',
        'loader_bg' => '',
        'modifier_class' => '',
    )); ?>
*/

/**
 * @var array  $urls
 * @var string $alt
 * @var string $aspect_ratio
 * @var string $object_fit
 * @var string $object_position
 * @var bool   $is_background
 * @var bool   $lazy
 * @var bool   $native_lazy
 * @var bool   $priority
 * @var bool   $animate
 * @var number $width
 * @var number $height
 * @var string $loader_bg
 * @var string $modifier_class
 * @var bool   $show_sources
 */
if ( empty( $urls['widescreen'] ) ) {
    $urls['widescreen'] = $urls['desktop'];
}

if ( empty( $urls['widescreen_retina'] ) ) {
    $urls['widescreen_retina'] = $urls['desktop_retina'];
}

if ( empty( $urls['tablet'] ) ) {
    $urls['tablet'] = $urls['desktop'];
}

if ( empty( $urls['tablet_retina'] ) ) {
    $urls['tablet_retina'] = $urls['desktop_retina'];
}

if ( empty( $urls['mobile'] ) ) {
    $urls['mobile'] = $urls['desktop'];
}

if ( empty( $urls['mobile_retina'] ) ) {
    $urls['mobile_retina'] = $urls['desktop_retina'];
}

if ( ! isset( $is_background ) ) {
    $is_background = false;
}

if ( empty( $aspect_ratio ) ) {
    $aspect_ratio = '1-1';
}

if ( ! isset( $lazy ) ) {
    $lazy = true;
}

if ( ! isset( $native_lazy ) ) {
    $native_lazy = false;
}

if ( ! isset( $priority ) ) {
    $priority = false;
}

if ( ! isset( $animate ) ) {
    $animate = true;
}

if ( ! isset( $show_sources ) ) {
	$show_sources = true;
}
?>

<?php if ( ! empty( $urls ) && ! empty( $urls['desktop'] ) && ! empty( $urls['desktop_retina'] ) ) { ?>
    <!--RESPONSIVE IMAGE-->
    <figure class="c-responsive-media <?php echo $is_background ? 'c-responsive-media--background' : ''; ?> <?php echo ! empty( $modifier_class ) ? esc_attr( $modifier_class ) : ''; ?>">
        <picture class="c-responsive-media__inner c-responsive-media__inner--<?php echo $aspect_ratio; ?>">
			<?php if ( $show_sources ) { ?>
                <source media="(min-width: 2000px)"
				        <?php echo $lazy ? 'data-' : ''; ?>srcset="<?php echo esc_url( $urls['widescreen'] ); ?> 1x, <?php echo esc_url( $urls['widescreen_retina'] ); ?> 2x"/>
                <source media="(min-width: 1141px)"
				        <?php echo $lazy ? 'data-' : ''; ?>srcset="<?php echo esc_url( $urls['desktop'] ); ?> 1x, <?php echo esc_url( $urls['desktop_retina'] ?? '' ); ?> 2x"/>
                <source media="(min-width: 641px)"
				        <?php echo $lazy ? 'data-' : ''; ?>srcset="<?php echo esc_url( $urls['tablet'] ); ?> 1x, <?php echo esc_url( $urls['tablet_retina'] ?? '' ); ?> 2x"/>
                <source media="(max-width: 640px)"
				        <?php echo $lazy ? 'data-' : ''; ?>srcset="<?php echo esc_url( $urls['mobile'] ); ?> 1x, <?php echo esc_url( $urls['mobile_retina'] ?? '' ); ?> 2x"/>
			<?php } ?>
            <img alt="<?php echo ! empty( $alt ) ? esc_attr( $alt ) : 'Image'; ?>"
                 class="c-responsive-media__img <?php echo ! empty( $object_fit ) ? 'c-responsive-media__img--' . esc_attr( $object_fit ) : ''; ?> <?php echo ! empty( $object_position ) ? 'c-responsive-media__img--' . esc_attr( $object_position ) : ''; ?> <?php echo $lazy ? 'js-lazy-load' : ''; ?> js-responsive-image"
                 <?php echo $lazy ? 'data-' : ''; ?>src="<?php echo esc_url( $urls['desktop'] ); ?>"
                 <?php if ( ! empty( $width ) ) { ?>width="<?php echo esc_attr( $width ); ?>" <?php } ?>
                 <?php if ( ! empty( $height ) ) { ?>height="<?php echo esc_attr( $height ); ?>" <?php } ?>
                <?php echo $priority ? 'fetchpriority="high"' : ''; ?>
                <?php echo $native_lazy && ! $lazy ? 'loading="lazy"' : ''; ?>/>
            <?php if ( $lazy && $animate ) { ?>
                <?php get_partial( 'components/media-loader', array(
                    'background_color' => ! empty( $loader_bg ) ? esc_attr( $loader_bg ) : '',
                ) ); ?>
            <?php } ?>
        </picture>
    </figure>
    <!--end RESPONSIVE IMAGE-->
<?php } ?>
