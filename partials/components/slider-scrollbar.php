<?php
/**
 * @var string $style 'primary' | 'secondary'
 * @var string $modifier_class
 */

if (empty($style)) {
    $style = 'primary';
}
?>

<!--SLIDER SCROLLBAR-->
<div class="c-slider-scrollbar c-slider-scrollbar--<?php echo $style; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> js-slider-scrollbar"></div>
<!--end SLIDER SCROLLBAR-->
