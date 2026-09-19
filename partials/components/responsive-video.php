<?php
/*
  RESPONSIVE VIDEO

  Required:
    - $fhd_video_url: {String}
    - $fhd_video_poster: {String}

  Optional:
    - $qhd_video_url: {String} | default: fhd_video_url
    - $qhd_video_poster: {String} | default: fhd_video_poster
    - $hd_video_url: {String} | default: fhd_video_url
    - $hd_video_poster: {String} | default: fhd_video_poster
    - $sd_video_url: {String} | default: fhd_video_url
    - $sd_video_poster: {String} | default: fhd_video_poster
    - $alt: {String} | default: 'Video'
    - $autoplay: {Boolean} | default: false - autoplay video, muted option is set to true to enable autoplay
    - $muted: {Boolean} | default: false - mute video, must be true to enable autoplay option
    - $loop: {Boolean} | default: false - play video in loop
    - $plays_inline: {Boolean} | default: true - enable/disable video playing in popup on mobile devices
    - $controls: {Boolean} | default: true - enable video controls
    - $preload: {String} | default: 'auto' -> 'auto' || 'metadata' || 'none' - use preload: 'none' for above the fold video
    - $aspect_ratio: {String} | default: '16-9' -> '1-1' || '2-1' || '1-2' || '3-1' || '1-3' || '3-2' || '2-3' || '4-3' || '3-4' || '16-9' || '9-16' || 'auto' || 'adopt'
    - $object_fit: {String} | default: 'cover' -> 'cover' || 'contain'
    - $object_position: {String} | default: 'center' -> 'center' || 'top' || 'bottom' || 'left' || 'right'
    - $is_background: {Boolean} | default: false - used with aspect_ratio: 'adopt' to make media fill the first relative parent element
    - $lazy: {Boolean} | default: true - lazy load media using vanilla lazy load lib (https://github.com/verlok/vanilla-lazyload), not to be used for above the fold media or when using priority option
    - $native_lazy: {Boolean} | default: false - lazy load media natively with browser-level media lazy loading, not to be used for above the fold media or when using priority option
    - $priority: {Boolean} | default: false - set media fetch priority to high, all lazy loading options must be disabled, used with above the fold media which should not be lazy loaded
    - $animate: {Boolean} | default: true - show/hide media loader for lazy loaded media
    - $loader_bg: {String} | used for changing the media loader background if it needs to be synced with section background
    - $scroll_trigger: {Boolean} | default: false - play video only when in viewport
    - $play_button: {Boolean} | default: false - show/hide play button, autoplay and scroll trigger options must be false to enable play button
    - $modifier_class: {String} | general modifier class rendered on the main component wrapper

  Usage:
    <?php echo get_responsive_video(array(
        'qhd_video_url' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
        'qhd_video_poster' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/images/Sintel.jpg',
        'fhd_video_url' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',
        'fhd_video_poster' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/images/SubaruOutbackOnStreetAndDirt.jpg',
        'hd_video_url' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
        'hd_video_poster' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/images/BigBuckBunny.jpg',
        'sd_video_url' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
        'sd_video_poster' => 'https://storage.googleapis.com/gtv-videos-bucket/sample/images/ElephantsDream.jpg',
        'alt' => 'Video',
        'autoplay' => false,
        'muted' => false,
        'loop' => false,
        'plays_inline' => true,
        'controls' => true,
        'preload' => 'auto',
        'aspect_ratio' => '16-9',
        'object_fit' => 'cover',
        'object_position' => 'center',
        'is_background' => false,
        'lazy' => true,
        'native_lazy' => false,
        'priority' => false,
        'animate' => true,
        'loader_bg' => '',
        'scroll_trigger' => false,
        'play_button' => false,
        'modifier_class' => '',
    )); ?>
*/

/**
 * @var string $qhd_video_url
 * @var string $qhd_video_poster
 * @var string $fhd_video_url
 * @var string $fhd_video_poster
 * @var string $hd_video_url
 * @var string $hd_video_poster
 * @var string $sd_video_url
 * @var string $sd_video_poster
 * @var string $alt
 * @var bool   $autoplay
 * @var bool   $muted
 * @var bool   $loop
 * @var bool   $plays_inline
 * @var bool   $controls
 * @var string $preload
 * @var string $aspect_ratio
 * @var string $object_fit
 * @var string $object_position
 * @var bool   $is_background
 * @var bool   $lazy
 * @var bool   $native_lazy
 * @var bool   $priority
 * @var bool   $animate
 * @var string $loader_bg
 * @var bool   $scroll_trigger
 * @var bool   $play_button
 * @var string $modifier_class
 */
if ( empty( $qhd_video_url ) ) {
    $qhd_video_url = $fhd_video_url;
}

if ( empty( $qhd_video_poster ) ) {
    $qhd_video_poster = $fhd_video_poster;
}

if ( empty( $hd_video_url ) ) {
    $hd_video_url = $fhd_video_url;
}

if ( empty( $hd_video_poster ) ) {
    $hd_video_poster = $fhd_video_poster;
}

if ( empty( $sd_video_url ) ) {
    $sd_video_url = $fhd_video_url;
}

if ( empty( $sd_video_poster ) ) {
    $sd_video_poster = $fhd_video_poster;
}

if ( ! isset( $autoplay ) ) {
    $autoplay = false;
}

if ( $autoplay ) {
    $muted = true;
} elseif ( ! isset( $muted ) ) {
    $muted = false;
}

if ( ! isset( $loop ) ) {
    $loop = false;
}

if ( ! isset( $plays_inline ) ) {
    $plays_inline = true;
}

if ( ! isset( $controls ) ) {
    $controls = true;
}

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

if ( ! isset( $priority ) ) {
    $priority = false;
}

if ( ! isset( $animate ) ) {
    $animate = true;
}

if ( ! isset( $scroll_trigger ) ) {
    $scroll_trigger = false;
}

if ( $scroll_trigger ) {
    $autoplay = false;
    $muted = true;
}

if ( ! isset( $play_button ) ) {
    $play_button = false;
}

if ( $play_button ) {
    $autoplay = false;
    $scroll_trigger = false;
}
?>

<?php if ( ! empty( $fhd_video_url ) && ! empty( $fhd_video_poster ) ) { ?>
    <!--RESPONSIVE VIDEO-->
    <div class="c-responsive-media <?php echo $is_background ? 'c-responsive-media--background' : ''; ?> <?php echo ! empty( $modifier_class ) ? esc_attr( $modifier_class ) : ''; ?> <?php echo $scroll_trigger ? 'js-video-on-scroll' : ''; ?> <?php echo $play_button ? 'js-video-play-button' : ''; ?>">
        <div class="c-responsive-media__inner c-responsive-media__inner--<?php echo $aspect_ratio; ?>">
            <video class="c-responsive-media__video <?php echo ! empty( $object_fit ) ? 'c-responsive-media__video--' . esc_attr( $object_fit ) : ''; ?> <?php echo ! empty( $object_position ) ? 'c-responsive-media__video--' . esc_attr( $object_position ) : ''; ?> <?php echo $lazy ? 'js-lazy-load' : ''; ?> js-responsive-video"
                   <?php if ( $autoplay ) { ?>autoplay<?php } ?>
                   <?php if ( $muted ) { ?>muted<?php } ?>
                   <?php if ( $loop ) { ?>loop<?php } ?>
                   <?php if ( $plays_inline ) { ?>playsinline<?php } ?>
                   <?php if ( $controls ) { ?>controls<?php } ?>
                   preload="<?php echo ! empty( $preload ) ? esc_attr( $preload ) : 'auto'; ?>"
                   <?php echo $lazy ? 'data-' : ''; ?>poster="<?php echo ! $lazy && ! $native_lazy ? $sd_video_poster : ''; ?>"
                   <?php if ( $priority ) { ?>fetchpriority="high"<?php } ?>
                   disablePictureInPicture
                   data-qhd-poster="<?php echo esc_attr( $qhd_video_poster ); ?>"
                   data-fhd-poster="<?php echo esc_attr( $fhd_video_poster ); ?>"
                   data-hd-poster="<?php echo esc_attr( $hd_video_poster ); ?>"
                   data-sd-poster="<?php echo esc_attr( $sd_video_poster ); ?>"
                   aria-label="<?php echo ! empty( $alt ) ? esc_attr( $alt ) : 'Video'; ?>"
                <?php echo $native_lazy && ! $lazy ? 'loading="lazy"' : ''; ?>>
                <source media="(min-width: 2000px)" type="video/mp4"
                        <?php echo $lazy ? 'data-' : ''; ?>src="<?php echo esc_url( $qhd_video_url ); ?>">
                <source media="(min-width: 1141px)" type="video/mp4"
                        <?php echo $lazy ? 'data-' : ''; ?>src="<?php echo esc_url( $fhd_video_url ); ?>">
                <source media="(min-width: 641px)" type="video/mp4"
                        <?php echo $lazy ? 'data-' : ''; ?>src="<?php echo esc_url( $hd_video_url ); ?>">
                <source media="(max-width: 640px)" type="video/mp4"
                        <?php echo $lazy ? 'data-' : ''; ?>src="<?php echo esc_url( $sd_video_url ); ?>">
            </video>
            <?php if ( $lazy && $animate ) { ?>
                <?php get_partial( 'components/media-loader', array(
                    'background_color' => ! empty( $loader_bg ) ? esc_attr( $loader_bg ) : '',
                ) ); ?>
            <?php } ?>
            <?php if ( $play_button && ! $autoplay && ! $scroll_trigger ) { ?>
                <!--MEDIA TRIGGER-->
                <button class="c-media-trigger js-video-play-button-trigger" type="button">
                    <?php echo get_icon( 'play' ); ?>
                </button>
                <!--end MEDIA TRIGGER-->
            <?php } ?>
        </div>
    </div>
    <!--end RESPONSIVE VIDEO-->
<?php } ?>
