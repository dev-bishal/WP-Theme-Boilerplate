<?php
/**
 * Page content for page.php.
 *
 * @package Blank_Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header>

	<?php blank_theme_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'blank-theme' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>
</article>
