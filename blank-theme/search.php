<?php
/**
 * Template for search results.
 *
 * @package Blank_Theme
 */

get_header();
?>

<main id="primary" class="site-main">
	<?php if ( have_posts() ) : ?>

		<header class="page-header">
			<h1 class="page-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search results for: %s', 'blank-theme' ), '<span>' . get_search_query() . '</span>' );
				?>
			</h1>
		</header>

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'search' );
		endwhile;

		the_posts_pagination();

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;
	?>
</main>

<?php
get_sidebar();
get_footer();
