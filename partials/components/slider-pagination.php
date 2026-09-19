<?php
/**
 * @var string $style 'primary' | 'secondary'
 * @var string $modifier_class
 */

if (empty($style)) {
    $style = 'primary';
}
?>

<!--SLIDER PAGINATION-->
<div class="c-slider-pagination c-slider-pagination--<?php echo $style; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> js-slider-pagination"></div>
<!--end SLIDER PAGINATION-->
