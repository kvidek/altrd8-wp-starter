<?php
/**
 * @var string $label
 * @var string $url
 * @var string $type 'link' | 'button' | 'span'
 * @var string $style 'primary' | 'secondary' | 'tertiary'
 * @var boolean $bordered
 * @var boolean $transparent
 * @var boolean $small
 * @var boolean $full_width
 * @var boolean $download
 * @var boolean $new_tab
 * @var boolean $submit
 * @var boolean $disabled
 * @var boolean $loader
 * @var string $loader_style 'primary' | 'secondary'
 * @var string $icon
 * @var string $icon_position 'left' | 'right'
 * @var string $modal_id
 * @var string $id HTML id attribute
 * @var string $typography
 * @var string $data_attribute
 * @var string $modifier_class
 */

if (empty($type)) {
    $type = 'link';
}

if (empty($style)) {
    $style = 'primary';
}

if (!isset($bordered)) {
    $bordered = false;
}

if (!isset($transparent)) {
    $transparent = false;
}

if (!isset($small)) {
    $small = false;
}

if (!isset($full_width)) {
    $full_width = false;
}

if (!isset($download)) {
    $download = false;
}

if (!isset($new_tab)) {
    $new_tab = false;
}

if (!isset($submit)) {
    $submit = false;
}

if (!isset($disabled)) {
    $disabled = false;
}

if (!isset($loader)) {
    $loader = false;
}

if (empty($loader_style)) {
    $loader_style = 'primary';
}

if (empty($icon_position)) {
    $icon_position = 'right';
}

$modal = !empty($modal_id) ? sprintf('data-modal-open="%s"', esc_attr($modal_id)) : '';
?>

<?php if (!empty($label)) { ?>
    <!--BUTTON-->
    <?php if ($type === 'link') { ?>
        <a href="<?php echo !empty($url) ? $url : '#'; ?>"
           class="c-button c-button--<?php echo $style; ?> <?php echo $bordered ? 'c-button--bordered' : ''; ?> <?php echo $transparent ? 'c-button--transparent' : ''; ?> <?php echo $small ? 'c-button--small' : ''; ?> <?php echo $full_width ? 'c-button--full' : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? $typography : 'u-b0 u-fw-500'; ?>" <?php echo !empty($id) ? 'id="' . esc_attr($id) . '"' : ''; ?> <?php echo $modal; ?> <?php echo $download ? 'download' : ''; ?> <?php echo $new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> <?php echo !empty($data_attribute) ? $data_attribute : ''; ?>>
            <?php if (!empty($icon) && $icon_position === 'left') { ?><?php echo get_icon($icon); ?><?php } ?>
            <?php echo $label; ?>
            <?php if (!empty($icon) && $icon_position === 'right') { ?><?php echo get_icon($icon); ?><?php } ?>
        </a>
    <?php } ?>
    <?php if ($type === 'button') { ?>
        <button class="c-button c-button--<?php echo $style; ?> <?php echo $bordered ? 'c-button--bordered' : ''; ?> <?php echo $transparent ? 'c-button--transparent' : ''; ?> <?php echo $small ? 'c-button--small' : ''; ?> <?php echo $full_width ? 'c-button--full' : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? $typography : 'u-b0 u-fw-500'; ?>" <?php echo !empty($id) ? 'id="' . esc_attr($id) . '"' : ''; ?> <?php echo $submit ? 'type="submit"' : ''; ?> <?php echo $disabled ? 'disabled' : ''; ?> <?php echo $modal; ?> <?php echo !empty($data_attribute) ? $data_attribute : ''; ?>>
            <?php if (!empty($icon) && $icon_position === 'left') { ?><?php echo get_icon($icon); ?><?php } ?>
            <?php echo $label; ?>
            <?php if (!empty($icon) && $icon_position === 'right') { ?><?php echo get_icon($icon); ?><?php } ?>
            <?php if ($loader) { ?>
                <span class="c-button__loader">
                    <?php get_partial('components/spinner', array(
                        'style' => $loader_style,
                        'small' => true,
                    )); ?>
                </span>
            <?php } ?>
        </button>
    <?php } ?>
    <?php if ($type === 'span') { ?>
        <span class="c-button c-button--<?php echo $style; ?> <?php echo $bordered ? 'c-button--bordered' : ''; ?> <?php echo $transparent ? 'c-button--transparent' : ''; ?> <?php echo $small ? 'c-button--small' : ''; ?> <?php echo $full_width ? 'c-button--full' : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? $typography : 'u-b0 u-fw-500'; ?>" <?php echo !empty($id) ? 'id="' . esc_attr($id) . '"' : ''; ?> <?php echo !empty($data_attribute) ? $data_attribute : ''; ?>>
            <?php if (!empty($icon) && $icon_position === 'left') { ?><?php echo get_icon($icon); ?><?php } ?>
            <?php echo $label; ?>
            <?php if (!empty($icon) && $icon_position === 'right') { ?><?php echo get_icon($icon); ?><?php } ?>
        </span>
    <?php } ?>
    <!--end BUTTON-->
<?php } ?>
