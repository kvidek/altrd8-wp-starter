<?php
/**
 * NAVIGATION
 *
 * Desktop header nav — renders the "header-menu" location (App\config\MenuConfig)
 * via App\helpers\MenuHelper, with a hover/focus dropdown for items that have
 * subitems. The same hierarchy is passed through to the mobile nav below so
 * the menu is only fetched once.
 */

use App\config\MenuConfig;
use App\helpers\MenuHelper;

$menu_helper = new MenuHelper();
$menu_items  = $menu_helper->create_menu_hierarchy_with_current( MenuConfig::HEADER_MENU_LOCATION );
?>
<!-- NAVIGATION -->
<nav class="c-navigation-wrapper js-navigation-wrapper js-navigation">
	<div class="o-container o-container--full">
		<div class="c-navigation">
			<!--logo-->
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="c-navigation__logo u-b0 u-fw-700">
				<?php bloginfo( 'name' ); ?>
			</a>
			<!--end logo-->
			<?php if ( ! empty( $menu_items ) ) { ?>
				<!--menu-->
				<ul class="c-navigation__list">
					<?php foreach ( $menu_items as $item ) { ?>
						<?php
						$has_children = ! empty( $item['sub'] );
						// A menu item with no real URL (e.g. a "#" placeholder used only to
						// group subitems) renders as a <span>, not a clickable <a>.
						$is_link = ! empty( $item['url'] ) && $item['url'] !== '#';
						?>
						<li class="c-navigation__item <?php echo $has_children ? 'c-navigation__item--has-children' : ''; ?> <?php echo ! empty( $item['current'] ) ? 'is-active' : ''; ?>">
							<?php if ( $is_link ) { ?>
								<a href="<?php echo esc_url( $item['url'] ); ?>"
								   class="c-navigation__link u-b0"
									<?php echo $has_children ? 'aria-haspopup="true"' : ''; ?>
									<?php echo ! empty( $item['target'] ) ? 'target="' . esc_attr( $item['target'] ) . '"' : ''; ?>>
									<?php echo esc_html( $item['title'] ); ?>
								</a>
							<?php } else { ?>
								<span class="c-navigation__link u-b0" <?php echo $has_children ? 'aria-haspopup="true"' : ''; ?>>
									<?php echo esc_html( $item['title'] ); ?>
								</span>
							<?php } ?>
							<?php if ( $has_children ) { ?>
								<!--dropdown-->
								<ul class="c-navigation__dropdown">
									<?php foreach ( $item['sub'] as $sub_item ) { ?>
										<?php $sub_is_link = ! empty( $sub_item['url'] ) && $sub_item['url'] !== '#'; ?>
										<li class="c-navigation__dropdown-item <?php echo ! empty( $sub_item['current'] ) ? 'is-active' : ''; ?>">
											<?php if ( $sub_is_link ) { ?>
												<a href="<?php echo esc_url( $sub_item['url'] ); ?>" class="c-navigation__dropdown-link u-b1">
													<?php echo esc_html( $sub_item['title'] ); ?>
												</a>
											<?php } else { ?>
												<span class="c-navigation__dropdown-link u-b1">
													<?php echo esc_html( $sub_item['title'] ); ?>
												</span>
											<?php } ?>
										</li>
									<?php } ?>
								</ul>
								<!--end dropdown-->
							<?php } ?>
						</li>
					<?php } ?>
				</ul>
				<!--end menu-->
			<?php } ?>
			<!--hamburger-->
			<button type="button"
			        class="c-hamburger js-hamburger"
			        data-modal-open="mobile-navigation"
			        aria-haspopup="dialog"
			        aria-label="<?php esc_attr_e( 'Open menu', 'altrd8-wp-starter' ); ?>">
				<span></span>
				<span></span>
				<span></span>
			</button>
			<!--end hamburger-->
		</div>
	</div>
</nav>
<!-- //NAVIGATION -->

<?php
get_partial( 'layout/mobile-navigation', array( 'items' => $menu_items ) );
