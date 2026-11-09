<?php
/**
 * The footer: closes #page and calls wp_footer().
 *
 * @package Blank_Theme
 */
?>
	<footer id="colophon" class="site-footer">
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer', 'blank-theme' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="site-info">
			&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			<?php
			$blank_theme_footer_text = get_theme_mod( 'blank_theme_footer_text', '' );
			if ( $blank_theme_footer_text ) :
				?>
				<span class="footer-text"><?php echo esc_html( $blank_theme_footer_text ); ?></span>
			<?php endif; ?>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
