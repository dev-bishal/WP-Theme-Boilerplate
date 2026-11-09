<?php
/**
 * The sidebar widget area.
 *
 * @package Blank_Theme
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'blank-theme' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
