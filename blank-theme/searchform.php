<?php
/**
 * Custom search form, used by get_search_form().
 *
 * @package Blank_Theme
 */

$blank_theme_search_id = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $blank_theme_search_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'blank-theme' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $blank_theme_search_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search&hellip;', 'blank-theme' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	<button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'blank-theme' ); ?></button>
</form>
