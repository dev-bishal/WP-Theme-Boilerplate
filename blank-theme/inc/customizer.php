<?php
/**
 * Customizer settings (Appearance → Customize).
 *
 * @package Blank_Theme
 */

/**
 * Add a sample "Footer text" setting. Use it in a template with:
 * echo esc_html( get_theme_mod( 'blank_theme_footer_text', '' ) );
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function blank_theme_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'blank_theme_options',
		array(
			'title'    => esc_html__( 'Theme Options', 'blank-theme' ),
			'priority' => 130,
		)
	);

	$wp_customize->add_setting(
		'blank_theme_footer_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'blank_theme_footer_text',
		array(
			'label'   => esc_html__( 'Footer text', 'blank-theme' ),
			'section' => 'blank_theme_options',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'blank_theme_customize_register' );
