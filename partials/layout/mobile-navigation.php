<?php
/**
 * MOBILE NAVIGATION
 *
 * Native <dialog>, opened by the header hamburger via Modal.js
 * (data-modal-open="mobile-navigation" -> #mobile-navigation, see
 * partials/layout/navigation.php). Subitems expand/collapse with the
 * existing AccordionAnimation.js component (.js-accordion-*).
 *
 * @var array $items | hierarchical menu items from App\helpers\MenuHelper::create_menu_hierarchy_with_current()
 */

if ( ! isset( $items ) ) {
	$items = array();
}
?>
<!-- MOBILE NAVIGATION -->
<dialog id="mobile-navigation" class="c-mobile-navigation js-modal">
	<div class="o-container">
		<!--header-->
		<div class="c-mobile-navigation__header">
			<button type="button" class="c-mobile-navigation__close js-modal-close" aria-label="<?php esc_attr_e( 'Close menu', 'altrd8-wp-starter' ); ?>">
				<span></span>
				<span></span>
			</button>
		</div>
		<!--end header-->
		<?php if ( ! empty( $items ) ) { ?>
			<!--menu-->
			<ul class="c-mobile-navigation__list js-accordion" data-mono>
				<?php foreach ( $items as $item ) { ?>
					<?php
					$has_children = ! empty( $item['sub'] );
					// A menu item with no real URL (e.g. a "#" placeholder used only to
					// group subitems) renders as a <span>, not a clickable <a>.
					$is_link = ! empty( $item['url'] ) && $item['url'] !== '#';
					?>
					<li class="c-mobile-navigation__item <?php echo $has_children ? 'js-accordion-single' : ''; ?> <?php echo ! empty( $item['current'] ) ? 'is-active' : ''; ?>">
						<div class="c-mobile-navigation__row">
							<?php if ( $is_link ) { ?>
								<a href="<?php echo esc_url( $item['url'] ); ?>" class="c-mobile-navigation__link u-a5">
									<?php echo esc_html( $item['title'] ); ?>
								</a>
							<?php } else { ?>
								<span class="c-mobile-navigation__link u-a5">
									<?php echo esc_html( $item['title'] ); ?>
								</span>
							<?php } ?>
							<?php if ( $has_children ) { ?>
								<button type="button" class="c-mobile-navigation__toggle js-accordion-header" aria-label="<?php esc_attr_e( 'Toggle submenu', 'altrd8-wp-starter' ); ?>">
									<span></span>
								</button>
							<?php } ?>
						</div>
						<?php if ( $has_children ) { ?>
							<ul class="c-mobile-navigation__sublist js-accordion-panel">
								<?php foreach ( $item['sub'] as $sub_item ) { ?>
									<?php $sub_is_link = ! empty( $sub_item['url'] ) && $sub_item['url'] !== '#'; ?>
									<li class="c-mobile-navigation__subitem <?php echo ! empty( $sub_item['current'] ) ? 'is-active' : ''; ?>">
										<?php if ( $sub_is_link ) { ?>
											<a href="<?php echo esc_url( $sub_item['url'] ); ?>" class="c-mobile-navigation__sublink u-b0">
												<?php echo esc_html( $sub_item['title'] ); ?>
											</a>
										<?php } else { ?>
											<span class="c-mobile-navigation__sublink u-b0">
												<?php echo esc_html( $sub_item['title'] ); ?>
											</span>
										<?php } ?>
									</li>
								<?php } ?>
							</ul>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
			<!--end menu-->
		<?php } ?>
	</div>
</dialog>
<!-- //MOBILE NAVIGATION -->
