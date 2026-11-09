<?php
/**
 * Shown when a query returns no posts.
 *
 * @package Blank_Theme
 */
?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing found', 'blank-theme' ); ?></h1>
	</header>

	<div class="page-content">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'No results matched your search. Try different keywords.', 'blank-theme' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( 'There is nothing here yet. Try a search?', 'blank-theme' ); ?></p>
		<?php endif; ?>

		<?php get_search_form(); ?>
	</div>
</section>
