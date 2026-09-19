<?php
/**
 * @var int $current_page
 * @var int $max_pages
 * @var string $base_url
 * @var string $page_slug
 * 
 * Get current page example:
 * $current_page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
 * 
 */
use App\helpers\PaginationHelper;

if( ! isset( $base_url )  ) {
    $base_url = home_url( $_SERVER['REQUEST_URI'] );
}

if( ! isset( $page_slug )  ) {
    $page_slug = 'page';
}

if ( ! empty( $max_pages ) && ! empty( $current_page ) && $max_pages > 1 ) {
    $pagination = new PaginationHelper( $base_url, $page_slug );
    $prev_page  = $pagination->get_pagination_start_page( $current_page );
    $next_page  = $pagination->get_pagination_end_page( $current_page, $max_pages );
    ?>
    <div class="o-pagination">
        <ul class="c-pagination">
            <li class="c-pagination__item">
                <a href="<?php echo $pagination->get_page_url( $current_page > 1 ? $current_page - 1 : '' ); ?>" data-val="<?php echo $current_page > 1 ? $current_page - 1 : ''; ?>"
                   class="c-pagination-arrow c-pagination-arrow--previous js-pagination-item <?php echo $current_page > $prev_page ? '' : 'is-disabled'; ?>">
					<?php echo get_icon( 'chevron-left' ); ?>
                </a>
            </li>
			<?php
            if ( $prev_page > 1 ) { ?>
                <li class="c-pagination__item">
                    <a href="<?php echo $pagination->get_page_url(1); ?>" data-val="1" id="1"
                       class="c-pagination-number <?php echo $prev_page === $current_page ? 'is-active' : ''; ?> js-pagination-item">
                        <span>1</span>
                    </a>
                </li>
                <li class="c-pagination__item">
                    <span class="c-pagination-separator">
                        ...
                    </span>
                </li>
				<?php
            }

            for ( $i = $prev_page; $i < $next_page; $i ++ ) {
                ?>
                <li class="c-pagination__item">
                    <a href="<?php echo $pagination->get_page_url( $i ); ?>" data-val="<?php echo $i; ?>" id="<?php echo $i; ?>"
                       class="c-pagination-number <?php echo $i === $current_page ? 'is-active' : ''; ?> js-pagination-item">
						<?php echo $i; ?>
                    </a>
                </li>
				<?php
            }
            if ( $next_page < $max_pages ) { ?>
                <li class="c-pagination__item">
                    <span class="c-pagination-separator">
                        ...
                    </span>
                </li>
                <li class="c-pagination__item">
                    <a href="<?php echo $pagination->get_page_url( $max_pages ); ?>" data-val="<?php echo $max_pages; ?>" id="<?php echo $max_pages; ?>"
                       class="c-pagination-number <?php echo $max_pages === $current_page ? 'is-active' : ''; ?>js-pagination-item">
						<?php echo $max_pages; ?>
                    </a>
                </li>
				<?php
            }
            ?>
            <li class="c-pagination__item">
                <a href="<?php echo $pagination->get_page_url( $current_page < $max_pages ? $current_page + 1 : '' ); ?>" data-val="<?php echo $current_page < $max_pages ? $current_page + 1 : ''; ?>"
                   class="c-pagination-arrow c-pagination-arrow--next <?php echo $current_page < $max_pages ? '' : 'is-disabled'; ?> js-pagination-item">
					<?php echo get_icon( 'chevron-right' ); ?>
                </a>
            </li>
        </ul>
    </div>
	<?php
}
