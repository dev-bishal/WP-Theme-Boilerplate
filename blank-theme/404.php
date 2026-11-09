<?php
/**
 * Template for "page not found".
 *
 * @package Blank_Theme
 */

get_header();
?>

<main id="primary" class="site-main">
	<section class="error-404 not-found">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Page not found', 'blank-theme' ); ?></h1>
		</header>

		<div class="page-content">
			<p><?php esc_html_e( 'Nothing was found at this address. Try a search instead?', 'blank-theme' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	</section>
</main>

<?php
get_footer();
