<?php
/**
 * @var string $style 'primary' | 'secondary'
 * @var boolean $small
 * @var string $modifier_class
 */

if (empty($style)) {
    $style = 'primary';
}

if (!isset($small)) {
    $small = false;
}
?>

<!--SPINNER-->
<span class="c-spinner <?php echo $small ? 'c-spinner--small' : ''; ?> <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> js-spinner">
    <!--inner-->
    <span class="c-spinner__inner c-spinner__inner--<?php echo $style; ?>"></span>
    <!--end inner-->
</span>
<!--end SPINNER-->
