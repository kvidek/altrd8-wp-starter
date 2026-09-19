<?php
/**
 * @var float $opacity
 * @var string $modifier_class
 */

if (empty($opacity)) {
    $opacity = 1;
}
?>

<!--MEDIA OVERLAY-->
<span class="c-media-overlay <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> js-media-overlay"
      style="opacity: <?php echo $opacity; ?>"></span>
<!--end MEDIA OVERLAY-->
