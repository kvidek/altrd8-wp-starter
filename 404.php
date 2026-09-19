<?php
$template_name = 'error';
get_header();

get_partial( 'layout/loader' );
get_partial( 'layout/navigation' );

?>
	<!-- PAGE WRAPPER -->
	<div id="<?php echo esc_attr( $template_name ); ?>" class="o-page o-page--<?php echo esc_attr( $template_name ); ?>">
		<!-- PAGE CONTENT -->
		<div class="o-page__inner o-page__inner--<?php echo $template_name; ?>">
			<h1>404</h1>
			<p>Return back to the homepage</p>
			<a href="<?php echo get_home_url(); ?>" class="c-button">
				<span>
					Back
				</span>
			</a>
		</div>
		<!-- end PAGE CONTENT -->
		<?php
		get_partial( 'layout/footer' );
		?>
	</div>
	<!-- end PAGE WRAPPER -->
<?php
get_footer();
