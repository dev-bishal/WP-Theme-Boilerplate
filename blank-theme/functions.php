<?php
/**
 * Blank Theme functions and definitions.
 *
 * @package Blank_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Block direct access.
}

define( 'BLANK_THEME_VERSION', '1.0.0' );
define( 'BLANK_THEME_DIR', get_template_directory() );
define( 'BLANK_THEME_URI', get_template_directory_uri() );

/**
 * Register theme features.
 */
function blank_theme_setup() {
	load_theme_textdomain( 'blank-theme', BLANK_THEME_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'blank-theme' ),
			'footer'  => esc_html__( 'Footer Menu', 'blank-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'blank_theme_setup' );

/**
 * Default content width for embeds and images.
 */
function blank_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'blank_theme_content_width', 800 );
}
add_action( 'after_setup_theme', 'blank_theme_content_width', 0 );

/**
 * Register widget areas.
 */
function blank_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'blank-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Widgets shown beside posts and pages.', 'blank-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'blank_theme_widgets_init' );

/**
 * Enqueue front-end styles and scripts.
 */
function blank_theme_scripts() {
	wp_enqueue_style( 'blank-theme-style', get_stylesheet_uri(), array(), BLANK_THEME_VERSION );
	wp_enqueue_style( 'blank-theme-main', BLANK_THEME_URI . '/assets/css/main.css', array( 'blank-theme-style' ), BLANK_THEME_VERSION );

	wp_enqueue_script( 'blank-theme-main', BLANK_THEME_URI . '/assets/js/main.js', array(), BLANK_THEME_VERSION, array( 'strategy' => 'defer', 'in_footer' => true ) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'blank_theme_scripts' );

// Template helpers and Customizer settings.
require BLANK_THEME_DIR . '/inc/template-tags.php';
require BLANK_THEME_DIR . '/inc/customizer.php';
