<?php
/**
 * @var string $background_color
 * @var string $modifier_class
 */
?>

<!--MEDIA LOADER-->
<span class="c-media-loader <?php echo !empty($background_color) ? 'c-media-loader--' . esc_attr($background_color) : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> js-media-loader"></span>
<!--end MEDIA LOADER-->
