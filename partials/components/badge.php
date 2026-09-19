<?php
/**
 * @var string $label
 * @var string $style 'primary' | 'secondary' | 'tertiary'
 * @var string $size 'small' | 'large'
 * @var string $icon
 * @var string $icon_position 'left' | 'right'
 * @var string $typography
 * @var string $modifier_class
 */

if (empty($style)) {
    $style = 'primary';
}

if (empty($size)) {
    $size = 'small';
}

if (empty($icon_position)) {
    $icon_position = 'right';
}
?>

<?php if (!empty($label)) { ?>
    <!--BADGE-->
    <span class="c-badge c-badge--<?php echo $style; ?> c-badge--<?php echo $size; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? $typography : 'u-b2 u-uppercase'; ?>">
        <?php if (!empty($icon) && $icon_position === 'left') { ?><?php echo get_icon($icon); ?><?php } ?>
        <?php echo $label; ?>
        <?php if (!empty($icon) && $icon_position === 'right') { ?><?php echo get_icon($icon); ?><?php } ?>
    </span>
    <!--end BADGE-->
<?php } ?>
