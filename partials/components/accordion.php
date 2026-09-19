<?php
/**
 * @var array $items
 * @var boolean $mono
 * @var array|int $initially_active | array of opened single accordions indexes, starting from 0, or single index
 * @var string $modifier_class
 */

if (!isset($mono)) {
    $mono = false;
}

// Convert scalar $initially_active to array for consistent handling
if (isset($initially_active) && !is_array($initially_active)) {
    $initially_active = [$initially_active];
}
?>

<?php if (!empty($items)) { ?>
    <!--ACCORDION-->
    <div class="c-accordion js-accordion <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?>"
         <?php if ($mono) { ?>data-mono<?php } ?>>
        <?php foreach ($items as $key => $item) { ?>
            <?php if (!empty($item['header']) && !empty($item['panel'])) { ?>
                <!--single-->
                <div class="c-accordion__single js-accordion-single <?php if (isset($initially_active) && is_array($initially_active) && in_array($key, $initially_active)) { ?>is-initially-active<?php } ?>">
                    <!--header-->
                    <button class="c-accordion__header js-accordion-header">
                        <!--label-->
                        <span class="c-accordion__label">
                            <?php echo $item['header']; ?>
                        </span>
                        <!--end label-->
                        <!--indicator-->
                        <span class="c-accordion__indicator">
                            <?php echo get_icon('chevron-down'); ?>
                        </span>
                        <!--end indicator-->
                    </button>
                    <!--end header-->
                    <!--panel-->
                    <div class="c-accordion__panel js-accordion-panel">
                        <!--content-->
                        <div class="c-accordion__content u-inline-richtext">
                            <?php echo $item['panel']; ?>
                        </div>
                        <!--end content-->
                    </div>
                    <!--end panel-->
                </div>
                <!--end single-->
            <?php } ?>
        <?php } ?>
    </div>
    <!--end ACCORDION-->
<?php } ?>
