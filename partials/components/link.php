<?php
/**
 * @var string $label
 * @var string $url
 * @var string $type 'link' | 'button' | 'span'
 * @var boolean $download
 * @var boolean $new_tab
 * @var boolean $submit
 * @var boolean $disabled
 * @var boolean $hover_inverted
 * @var string $icon
 * @var string $icon_position 'left' | 'right'
 * @var string $modal_id
 * @var string $typography
 * @var string $data_attribute
 * @var string $modifier_class
 */

if (empty($type)) {
    $type = 'link';
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

if (!isset($hover_inverted)) {
    $hover_inverted = false;
}

if (empty($icon_position)) {
    $icon_position = 'right';
}

$modal = !empty($modal_id) ? sprintf('data-modal-open="%s"', esc_attr($modal_id)) : '';
?>

<?php if (!empty($label)) { ?>
    <!--LINK-->
    <?php if ($type === 'link') { ?>
        <a href="<?php echo !empty($url) ? $url : '#'; ?>"
           class="c-link <?php echo $hover_inverted ? 'c-link--hover-inverted' : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? $typography : 'u-b0 u-fw-500'; ?>" <?php echo $modal; ?> <?php echo $download ? 'download' : ''; ?> <?php echo $new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> <?php echo !empty($data_attribute) ? $data_attribute : ''; ?>>
            <?php if (!empty($icon) && $icon_position === 'left') { ?><?php echo get_icon($icon); ?><?php } ?>
            <span><span><?php echo $label; ?></span></span>
            <?php if (!empty($icon) && $icon_position === 'right') { ?><?php echo get_icon($icon); ?><?php } ?>
        </a>
    <?php } ?>
    <?php if ($type === 'button') { ?>
        <button class="c-link <?php echo $hover_inverted ? 'c-link--hover-inverted' : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? $typography : 'u-b0 u-fw-500'; ?>" <?php echo $submit ? 'type="submit"' : ''; ?> <?php echo $disabled ? 'disabled' : ''; ?> <?php echo $modal; ?> <?php echo !empty($data_attribute) ? $data_attribute : ''; ?>>
            <?php if (!empty($icon) && $icon_position === 'left') { ?><?php echo get_icon($icon); ?><?php } ?>
            <span><span><?php echo $label; ?></span></span>
            <?php if (!empty($icon) && $icon_position === 'right') { ?><?php echo get_icon($icon); ?><?php } ?>
        </button>
    <?php } ?>
    <?php if ($type === 'span') { ?>
        <span class="c-link <?php echo $hover_inverted ? 'c-link--hover-inverted' : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? $typography : 'u-b0 u-fw-500'; ?>" <?php echo !empty($data_attribute) ? $data_attribute : ''; ?>>
            <?php if (!empty($icon) && $icon_position === 'left') { ?><?php echo get_icon($icon); ?><?php } ?>
            <span><span><?php echo $label; ?></span></span>
            <?php if (!empty($icon) && $icon_position === 'right') { ?><?php echo get_icon($icon); ?><?php } ?>
        </span>
    <?php } ?>
    <!--end LINK-->
<?php } ?>
