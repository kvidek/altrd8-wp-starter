<?php
/**
 * @var string $background_color
 * @var string $spinner_style 'primary' | 'secondary'
 * @var string $modifier_class
 */

if (empty($spinner_style)) {
    $spinner_style = 'primary';
}
?>

<!--CONTENT LOADER-->
<span class="c-content-loader <?php echo !empty($background_color) ? esc_attr($background_color) : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> js-content-loader">
    <?php get_partial('components/spinner', array(
        'style' => $spinner_style,
        'small' => false,
    )); ?>
</span>
<!--end CONTENT LOADER-->
