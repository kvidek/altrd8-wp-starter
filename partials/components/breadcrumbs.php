<?php
/**
 * @var array $items
 * @var string $typography
 * @var string $modifier_class
 */
?>

<?php if (!empty($items)) { ?>
    <!--BREADCRUMBS-->
    <ul class="c-breadcrumbs <?php echo !empty($modifier_class) ? esc_attr($modifier_class) : ''; ?> <?php echo !empty($typography) ? esc_attr($typography) : 'u-b2 u-uppercase'; ?>"
        role="menu" aria-label="breadcrumbs">
        <?php foreach ($items as $item) { ?>
            <?php if (!empty($item['title'])) { ?>
                <?php if (!empty($item['url'])) { ?>
                    <li role="menuitem">
                        <?php get_partial('components/link', array(
                            'label' => $item['title'],
                            'url' => $item['url'],
                            'type' => 'link',
                            'hover_inverted' => true,
                            'icon' => 'chevron-right',
                            'icon_position' => 'right',
                            'typography' => !empty($typography) ? $typography : 'u-b2 u-uppercase',
                        )); ?>
                    </li>
                <?php } else { ?>
                    <li role="menuitem">
                        <span aria-current="page">
                            <?php echo $item['title']; ?>
                        </span>
                    </li>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </ul>
    <!--end BREADCRUMBS-->
<?php } ?>
