<?php
/**
 * @var string $style 'primary' | 'secondary' | 'tertiary'
 * @var string $modifier_class
 */

if (empty($style)) {
    $style = 'primary';
}
?>

<!--SLIDER NAVIGATION-->
<div class="c-slider-navigation <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> js-slider-navigation">
    <button class="c-slider-navigation__button c-slider-navigation__button--<?php echo $style; ?> js-slider-prev">
        <?php echo get_icon('arrow-left'); ?>
    </button>
    <button class="c-slider-navigation__button c-slider-navigation__button--<?php echo $style; ?> js-slider-next">
        <?php echo get_icon('arrow-right'); ?>
    </button>
</div>
<!--end SLIDER NAVIGATION-->
