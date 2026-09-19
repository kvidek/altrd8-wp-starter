<?php
/**
 * FOOTER
 *
 * Renders the "footer-menu" location (App\config\MenuConfig) as a
 * multi-column menu: each top-level item is a column. An item with no real
 * URL (blank or "#") renders as a non-clickable heading, its child items
 * (drag-indented under it in Appearance > Menus) become that column's links.
 * A top-level item with no children and a real URL renders as a single
 * standalone link column. Uses the same App\helpers\MenuHelper hierarchy as
 * the header nav — no separate admin config needed.
 *
 * Mobile: each column becomes a collapsible accordion item, reusing the same
 * AccordionAnimation.js already wired up for the mobile nav (.js-accordion-*).
 * Only the first column starts expanded on mobile; every column starts
 * expanded on desktop, where the toggle is CSS-hidden — see the viewport
 * check in static/js/index.js (must run before accordionAnimation.init(),
 * which only reads is-initially-active once). This keeps every desktop link
 * reachable/visible at all times regardless of which column "starts active"
 * on mobile — AccordionAnimation.js has no breakpoint awareness of its own,
 * so a column not marked active gets aria-hidden="true" on every viewport,
 * not just the one it's meant for.
 */

use App\config\MenuConfig;
use App\helpers\MenuHelper;

$menu_helper       = new MenuHelper();
$footer_menu_items = $menu_helper->create_menu_hierarchy_with_current( MenuConfig::FOOTER_MENU_LOCATION );
?>
<!-- FOOTER -->
<footer class="o-footer-wrapper js-footer">
	<div class="o-container">
		<div class="c-footer">
			<?php if ( ! empty( $footer_menu_items ) ) { ?>
				<!--columns-->
				<div class="c-footer__columns js-accordion">
					<?php foreach ( $footer_menu_items as $column ) { ?>
						<?php
						$has_links        = ! empty( $column['sub'] );
						$heading_is_link  = ! empty( $column['url'] ) && $column['url'] !== '#';
						?>
						<?php // is-initially-active is added by JS (index.js), viewport-aware — see the comment there for why. ?>
						<div class="c-footer__column <?php echo $has_links ? 'js-accordion-single' : ''; ?>">
							<div class="c-footer__column-row">
								<?php if ( $heading_is_link ) { ?>
									<a href="<?php echo esc_url( $column['url'] ); ?>"
									   class="c-footer__column-heading u-b2"
										<?php echo ! empty( $column['target'] ) ? 'target="' . esc_attr( $column['target'] ) . '"' : ''; ?>>
										<?php echo esc_html( $column['title'] ); ?>
									</a>
								<?php } else { ?>
									<span class="c-footer__column-heading u-b2">
										<?php echo esc_html( $column['title'] ); ?>
									</span>
								<?php } ?>
								<?php if ( $has_links ) { ?>
									<button type="button"
									        class="c-footer__column-toggle js-accordion-header"
									        aria-label="<?php echo esc_attr( sprintf( /* translators: %s: column heading, e.g. "Solutions" */ __( 'Toggle %s', 'altrd8-wp-starter' ), $column['title'] ) ); ?>">
										<span></span>
									</button>
								<?php } ?>
							</div>
							<?php if ( $has_links ) { ?>
								<ul class="c-footer__column-list js-accordion-panel">
									<?php foreach ( $column['sub'] as $link ) { ?>
										<?php
										// ACF "Spacer" field on the menu item (group_altrd8_wp_starter_menu_item_spacer)
										// — sub-items only, never checked for top-level columns/headings above.
										$is_spacer = ! empty( $link['ID'] ) && (bool) get_field( 'spacer', $link['ID'] );
										?>
										<?php if ( $is_spacer ) { ?>
											<li class="c-footer__column-item c-footer__column-item--spacer" aria-hidden="true">&nbsp;</li>
										<?php } else { ?>
											<?php $link_is_link = ! empty( $link['url'] ) && $link['url'] !== '#'; ?>
											<li class="c-footer__column-item <?php echo ! empty( $link['current'] ) ? 'is-active' : ''; ?>">
												<?php if ( $link_is_link ) { ?>
													<a href="<?php echo esc_url( $link['url'] ); ?>"
													   class="c-footer__column-link u-b1"
														<?php echo ! empty( $link['target'] ) ? 'target="' . esc_attr( $link['target'] ) . '"' : ''; ?>>
														<?php echo esc_html( $link['title'] ); ?>
													</a>
												<?php } else { ?>
													<span class="c-footer__column-link u-b1">
														<?php echo esc_html( $link['title'] ); ?>
													</span>
												<?php } ?>
											</li>
										<?php } ?>
									<?php } ?>
								</ul>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
				<!--end columns-->
			<?php } ?>
			<!--bottom bar-->
			<div class="c-footer__bottom">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="c-footer__logo u-b0 u-fw-700">
					<?php bloginfo( 'name' ); ?>
				</a>
				<p class="c-footer__copyright u-b2">
					<?php echo esc_html( get_bloginfo( 'name' ) ); ?>, Inc. &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> All rights reserved
				</p>
			</div>
			<!--end bottom bar-->
		</div>
	</div>
</footer>
<!-- //FOOTER -->
