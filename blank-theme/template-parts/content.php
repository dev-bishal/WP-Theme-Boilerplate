<?php
/**
 * Post summary used in loops (blog index, archives).
 *
 * @package Blank_Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php blank_theme_posted_on(); ?>
			</div>
		<?php endif; ?>
	</header>

	<?php blank_theme_post_thumbnail(); ?>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
</article>
