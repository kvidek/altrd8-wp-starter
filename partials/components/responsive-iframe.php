<?php
/*
  RESPONSIVE IFRAME

  Required:
    - $iframe_html: {String}

  Optional:
    - $aspect_ratio: {String} | default: '16-9' -> '1-1' || '2-1' || '1-2' || '3-1' || '1-3' || '3-2' || '2-3' || '4-3' || '3-4' || '16-9' || '9-16' || 'auto' || 'adopt'
    - $is_background: {Boolean} | default: false - used with aspect_ratio: 'adopt' to make media fill the first relative parent element
    - $lazy: {Boolean} | default: true - lazy load media using vanilla lazy load lib (https://github.com/verlok/vanilla-lazyload), not to be used for above the fold media or when using priority option
    - $native_lazy: {Boolean} | default: false - lazy load media natively with browser-level media lazy loading, not to be used for above the fold media or when using priority option
    - $animate: {Boolean} | default: true - show/hide media loader for lazy loaded media
    - $loader_bg: {String} | used for changing the media loader background if it needs to be synced with section background
    - $modifier_class: {String} | general modifier class rendered on the main component wrapper

  Usage:
    <?php get_slice_partial('components/responsive-iframe', array(
        'iframe_html' => '<iframe src=""></iframe>',
        'aspect_ratio' => '16-9',
        'is_background' => false,
        'lazy' => true,
        'native_lazy' => false,
        'animate' => true,
        'loader_bg' => '',
        'modifier_class' => '',
    )); ?>
*/

/**
 * @var string $iframe_html
 * @var string $aspect_ratio
 * @var bool   $is_background
 * @var bool   $lazy
 * @var bool   $native_lazy
 * @var bool   $animate
 * @var string $loader_bg
 * @var string $modifier_class
 */
if ( empty( $aspect_ratio ) ) {
    $aspect_ratio = '16-9';
}

if ( ! isset( $is_background ) ) {
    $is_background = false;
}

if ( ! isset( $lazy ) ) {
    $lazy = true;
}

if ( ! isset( $native_lazy ) ) {
    $native_lazy = false;
}

if ( ! isset( $animate ) ) {
    $animate = true;
}
?>

<?php if ( ! empty( $iframe_html ) ) { ?>
    <?php $iframe_html = str_replace( 'loading="lazy"', '', $iframe_html ); ?>
    <!--RESPONSIVE IFRAME-->
    <div class="c-responsive-media <?php echo $is_background ? 'c-responsive-media--background' : ''; ?> <?php echo ! empty( $modifier_class ) ? esc_attr( $modifier_class ) : ''; ?>">
        <div class="c-responsive-media__inner c-responsive-media__inner--<?php echo $aspect_ratio; ?>">
            <?php if ( $lazy ) { ?>
                <?php echo str_replace( 'src="', 'class="js-lazy-load" data-src="', $iframe_html ); ?>
                <?php if ( $animate ) { ?>
                    <?php get_partial( 'components/media-loader', array(
                        'background_color' => ! empty( $loader_bg ) ? esc_attr( $loader_bg ) : '',
                    ) ); ?>
                <?php } ?>
            <?php } elseif ( $native_lazy && ! $lazy ) { ?>
                <?php echo str_replace( 'src="', 'loading="lazy" src="', $iframe_html ); ?>
            <?php } else { ?>
                <?php echo $iframe_html; ?>
            <?php } ?>
        </div>
    </div>
    <!--end RESPONSIVE IFRAME-->
<?php } ?>
