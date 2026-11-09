Developer Guide

# How to Create a Custom WordPress Theme from Scratch

A step-by-step walkthrough of building a classic PHP theme: files, the template hierarchy, the Loop, functions.php, security, translation, testing and shipping.
```
Companion to the blank-theme boilerplate in this folder

Targets WordPress 6.0+ · PHP 7.4+ · October 2026
```

## Contents

1.  What a theme is
2.  Set up a local environment
3.  The two required files
4.  The template hierarchy
5.  Header and footer
6.  The Loop
7.  functions.php
8.  Loading CSS and JavaScript
9.  Navigation menus
10. Template parts
11. Single, page, archive, search, 404
12. Sidebars and widgets
13. Comments
14. Customizer settings
15. Custom page templates
16. Security: escape, sanitize, verify
17. Translation (i18n)
18. Child themes
19. Testing and debugging
20. Packaging and distribution
21. Using the blank-theme boilerplate
22. Launch checklist and resources

## 1. What a theme is

A WordPress theme is a folder inside `wp-content/themes/` that controls how a site looks. WordPress decides *what* content to show (a post, a category, search results); the theme decides *how* to show it.

There are two kinds of theme today:

| Classic theme | Block theme |
|---------------|-------------|
|Templates are PHP files (single.php, header.php). Layout is edited in code. Menus, widgets and the Customizer work as before. | Templates are HTML files of blocks (templates/single.html). Layout is edited in the Site Editor. Configured mostly through theme.json.|
Best for learning how WordPress works, for agency builds with fixed designs, and for full control in PHP. | Best when clients should edit headers, footers and templates visually.|

This guide builds a **classic theme**. Every concept here (the template hierarchy, hooks, enqueueing, escaping) still applies to block themes, which can also contain PHP.

### How a request becomes a page

1. A visitor opens a URL, e.g. `/2026/10/hello-world/`.
2. WordPress parses the URL and runs a database query for the matching content.
3. It walks the **template hierarchy** to choose a file from the active theme (here, `single.php`).
4. That template runs **the Loop** to print each post in the query.
5. Along the way, **hooks** (`wp_head`, `wp_footer`) let WordPress and plugins add their own output.

## 2. Set up a local environment

Never build a theme on a live site. Run WordPress on your own machine with one of these:

|Tool|Notes|
|----|-----|
|LocalWP (localwp.com)|One-click sites with PHP, MySQL and SSL. Easiest choice on Windows and macOS.|
|wp-env (@wordpress/env)|Docker-based, run with npx wp-env start from the theme folder. Good for teams.|
|XAMPP / Laragon / MAMP|Classic Apache + MySQL + PHP stacks; install WordPress by hand.|
|WordPress PlaygroundRuns | WordPress in the browser. Great for quick experiments, not for long work.|

### Turn on debugging

In `wp-config.php`, above the line *"That's all, stop editing!"*:

```
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );      // writes to wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false ); // keep errors out of the page
define( 'SCRIPT_DEBUG', true );      // load unminified core JS/CSS
```

### Useful tools

- A code editor (VS Code with the *PHP Intelephense* and *WordPress Snippets* extensions).
- **Query Monitor** plugin: shows which template file was used, queries, hooks and PHP errors.
- **Theme Check** plugin: runs the same checks as the WordPress.org theme review.
- **WP-CLI**: command line for WordPress (`wp theme activate`, `wp i18n make-pot`).
- **Theme Unit Test data**: an XML import with every edge case (long titles, nested comments, galleries).

## 3. The two required files

A theme needs only two files to appear under **Appearance → Themes**: `style.css` and `index.php`.

### Create the folder

Create `wp-content/themes/my-theme/`. The folder name is the theme's *slug*: lowercase, hyphens, no spaces.

### style.css: the theme header

WordPress reads the comment at the top of `style.css` to learn the theme's name and details. Only `Theme Name` is required.

my-theme/style.css

```
/*
Theme Name:        My Theme
Theme URI:         https://example.com/my-theme
Author:            Your Name
Author URI:        https://example.com
Description:       A custom theme built from scratch.
Version:           1.0.0
Requires at least: 6.0
Requires PHP:      7.4
License:           GNU General Public License v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:       my-theme
*/
```

### index.php: the fallback template

my-theme/index.php

```
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<h1><?php bloginfo( 'name' ); ?></h1>

	<?php while ( have_posts() ) : the_post(); ?>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php the_content(); ?>
	<?php endwhile; ?>

	<?php wp_footer(); ?>
</body>
</html>
```

Activate the theme. You now have a working (if plain) site. Everything that follows is about splitting this file up and adding features.

### Add a screenshot

Add `screenshot.png` (1200×900 px) to the theme root. It's the thumbnail shown in the Themes screen.

## 4. The template hierarchy

For every request WordPress looks for the most specific template file, falling back step by step until it reaches `index.php`. You only create the files you need.

RequestFiles tried, most specific firstFront pagefront-page.php → home.php (if showing posts) or page templates → index.phpBlog posts indexhome.php → index.phpSingle postsingle-{post-type}-{slug}.php → single-{post-type}.php → single.php → singular.php → index.phpStatic pageCustom template → page-{slug}.php → page-{id}.php → page.php → singular.php → index.phpCategorycategory-{slug}.php → category-{id}.php → category.php → archive.php → index.phpTagtag-{slug}.php → tag.php → archive.php → index.phpCustom taxonomytaxonomy-{tax}-{term}.php → taxonomy-{tax}.php → taxonomy.php → archive.phpAuthorauthor-{nicename}.php → author.php → archive.phpDatedate.php → archive.php → index.phpCustom post type archivearchive-{post-type}.php → archive.phpAttachment{mime-type}.php (e.g. image.php) → attachment.php → single.phpSearch resultssearch.php → index.phpNot found404.php → index.phpTipInstall Query Monitor and look at the "Template" panel on any page. It shows exactly which file WordPress picked and which ones it tried first.A sensible starting set, which the boilerplate ships with: `index.php`, `single.php`, `page.php`, `archive.php`, `search.php` and `404.php`.

## 5. Header and footer

Every template repeats the same opening and closing HTML. Move it into `header.php` and `footer.php`, then load them with `get_header()` and `get_footer()`.

my-theme/header.php

```
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
</header>
```

my-theme/footer.php

```
<footer class="site-footer">
	&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
```

### The three hooks you must never remove

FunctionWhereWhywp_head()Just before `</head>` Prints enqueued CSS, the `<title>`, SEO meta, and plugin output.wp_body_open()Right after `<body>`Used by analytics and tag-manager plugins.wp_footer()Just before `</body>`Prints footer scripts and the admin bar. Without it many plugins break.You can have several headers: `get_header( 'shop' )` loads `header-shop.php`. The same works for `get_footer()` and `get_sidebar()`.

Don't hard-code a `<title>` tag. Add `add_theme_support( 'title-tag' )` in functions.php and WordPress (or your SEO plugin) prints it.

## 6. The Loop

The Loop is how a template prints the posts WordPress queried. It's the same in every template; only the markup inside changes.

```
<?php if ( have_posts() ) : ?>

	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php the_excerpt(); ?>
		</article>
	<?php endwhile; ?>

	<?php the_posts_pagination(); ?>

<?php else : ?>
	<p><?php esc_html_e( 'Nothing found.', 'my-theme' ); ?></p>
<?php endif; ?>
```

`the_post()` moves to the next post and sets up the global `$post`, so template tags like `the_title()` know which post to print.

### Common template tags

PrintsReturns (for use in PHP)Outputthe_title()get_the_title()Post titlethe_content()get_the_content()Full content, with blocks and shortcodes renderedthe_excerpt()get_the_excerpt()Summarythe_permalink()get_permalink()Post URLthe_post_thumbnail()get_the_post_thumbnail()Featured imagethe_date() / the_time()get_the_date()Publish datethe_author()get_the_author()Author namethe_category() / the_tags()get_the_category_list()TermsThe rule of thumb: `the_*` functions echo, `get_the_*` functions return a value.

### Custom queries

To show a second list of posts (e.g. "Latest from News") use `WP_Query`, then reset afterwards so the main Loop isn't affected:

```
<?php
$news = new WP_Query( array( 'category_name' => 'news', 'posts_per_page' => 3 ) );
while ( $news->have_posts() ) : $news->the_post();
	the_title( '<h3>', '</h3>' );
endwhile;
wp_reset_postdata();
?>
```

Avoid query_posts()It replaces the main query and breaks pagination. To change the main query (e.g. 12 posts per page on archives), use the pre_get_posts action in functions.php instead.

## 7. functions.php

`functions.php` runs on every page load, before any template. It's where you switch on theme features and attach code to hooks. It works like a plugin that's only active with your theme.

### Hooks in one paragraph

WordPress fires **actions** at set moments (`after_setup_theme`, `wp_enqueue_scripts`, `widgets_init`) and runs your function when they fire. **Filters** pass a value through your function so you can change it and return it.

```
add_action( 'hook_name', 'my_theme_function' );        // do something
add_filter( 'excerpt_length', fn() => 30 );            // change a value
```

### Theme setup

my-theme/functions.php

```
<?php
function my_theme_setup() {
	load_theme_textdomain( 'my-theme', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );          // WordPress prints <title>
	add_theme_support( 'post-thumbnails' );    // featured images
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );         // wide/full block alignments
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
	) );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'my-theme' ),
	) );
}
add_action( 'after_setup_theme', 'my_theme_setup' );
```

### Prefix everything

All your functions, constants, handles and options share one global namespace with WordPress core and every plugin. Prefix them with your theme slug (`my_theme_`) so they never collide. Wrapping helpers in `if ( ! function_exists( ... ) )` lets a child theme override them.

### Keep it organised

Once functions.php grows, split it into files under `inc/` and load them:

```
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/customizer.php';
```

### Useful path functions

FunctionReturnsget_template_directory()Server path of the parent themeget_template_directory_uri()URL of the parent themeget_stylesheet_directory()Path of the active (child) themeget_stylesheet_uri()URL of the active theme's style.css

## 8. Loading CSS and JavaScript

Never paste `<link>` or `<script>` tags into header.php. **Enqueue** them so WordPress can manage dependencies, avoid duplicates and let plugins optimise them.

```
function my_theme_scripts() {
	$ver = wp_get_theme()->get( 'Version' );

	// The theme's style.css
	wp_enqueue_style( 'my-theme-style', get_stylesheet_uri(), array(), $ver );

	// An extra stylesheet
	wp_enqueue_style( 'my-theme-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'my-theme-style' ), $ver );

	// JavaScript, deferred and loaded in the footer
	wp_enqueue_script( 'my-theme-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(), $ver,
		array( 'strategy' => 'defer', 'in_footer' => true ) );

	// Threaded comment replies
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'my_theme_scripts' );
```

### Arguments explained

- **Handle**: a unique name, prefixed. Used for dependencies and for dequeuing.
- **Source**: the file URL.
- **Dependencies**: handles that must load first, e.g. `array( 'jquery' )`.
- **Version**: added as `?ver=` so browsers fetch a fresh copy when you release.
- **Args / media**: for scripts, `defer`/`async` and footer placement; for styles, the media query.

### Passing PHP data to JavaScript

```
wp_add_inline_script( 'my-theme-main',
	'const MyTheme = ' . wp_json_encode( array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'my_theme_ajax' ),
	) ) . ';',
	'before' );
```

### Editor styles

To make the block editor look like the front end, add `add_theme_support( 'editor-styles' )` and `add_editor_style( 'assets/css/editor.css' )` in your setup function.

Build tools are optionalPlain CSS and JS work fine. If you want Sass, modern JS bundling or Tailwind, run a build step (Vite, webpack or @wordpress/scripts) that outputs into assets/ and enqueue the built files.

## 9. Navigation menus

Register menu *locations* in your setup function (see section 7). Site owners then build menus under **Appearance → Menus** and assign them to a location.

Print a location in a template:

```
<nav class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'my-theme' ); ?>">
	<?php
	wp_nav_menu( array(
		'theme_location' => 'primary',
		'menu_id'        => 'primary-menu',
		'container'      => false,
		'depth'          => 2,
		'fallback_cb'    => false,  // print nothing if no menu is assigned
	) );
	?>
</nav>
```

Check whether a location has a menu with `has_nav_menu( 'footer' )` before printing its wrapper markup.

### Styling

WordPress adds helpful classes to menu items: `.menu-item`, `.current-menu-item`, `.current-menu-ancestor`, `.menu-item-has-children` and `.sub-menu` for nested lists.

### A mobile toggle

Add a button with `aria-controls` and `aria-expanded`, and toggle a class with a few lines of JavaScript. The boilerplate's `assets/js/main.js` does exactly this:

```
const nav = document.getElementById( 'site-navigation' );
const button = nav.querySelector( '.menu-toggle' );
button.addEventListener( 'click', () => {
	const open = nav.classList.toggle( 'toggled' );
	button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
} );
```

## 10. Template parts

The markup for "one post in a list" appears in index.php, archive.php and search.php. Put it in one file and include it with `get_template_part()`.

```
// In index.php, inside the Loop:
get_template_part( 'template-parts/content', get_post_type() );
```

For a post this looks for `template-parts/content-post.php`, then falls back to `template-parts/content.php`. A custom post type `book` would use `content-book.php` if it exists.

my-theme/template-parts/content.php

```
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
	</header>
	<?php the_post_thumbnail( 'medium_large' ); ?>
	<div class="entry-summary"><?php the_excerpt(); ?></div>
</article>
```

### Passing data to a part

Since WordPress 5.5 you can pass an array as the third argument; it's available as `$args` inside the part:

```
get_template_part( 'template-parts/card', null, array( 'show_date' => false ) );

// template-parts/card.php
if ( $args['show_date'] ?? true ) { the_date(); }
```

### Suggested parts

- `content.php`: summary in lists
- `content-single.php`: full single post
- `content-page.php`: page body
- `content-search.php`: search result
- `content-none.php`: "nothing found" message

## 11. Single, page, archive, search, 404

With header, footer and parts in place, each template becomes short.

single.php

```
<?php get_header(); ?>
<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) : the_post();
		get_template_part( 'template-parts/content', 'single' );
		the_post_navigation();
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</main>
<?php get_sidebar(); get_footer(); ?>
```

archive.php

```
<?php get_header(); ?>
<main id="primary" class="site-main">
	<?php if ( have_posts() ) : ?>
		<header class="page-header">
			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>
		<?php while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		endwhile;
		the_posts_pagination(); ?>
	<?php else :
		get_template_part( 'template-parts/content', 'none' );
	endif; ?>
</main>
<?php get_sidebar(); get_footer(); ?>
```

### The others

- **page.php**: same as single.php but uses `content-page` and usually no post navigation.
- **search.php**: prints `get_search_query()` in the heading (escaped), then the Loop with `content-search`.
- **404.php**: a friendly message and `get_search_form()`.
- **front-page.php**: optional; a custom home page layout used whatever the Reading settings say.

### Conditional tags

Inside any template you can branch on the type of request: `is_front_page()`, `is_home()`, `is_single()`, `is_page( 'about' )`, `is_category()`, `is_search()`, `is_404()`, `is_singular( 'book' )`.

## 12. Sidebars and widgets

A "sidebar" is any widget area: a column, a footer row, a banner slot. Register it on `widgets_init`:

```
function my_theme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'my-theme' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Shown beside posts and pages.', 'my-theme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'my_theme_widgets_init' );
```

sidebar.php

```
<?php if ( ! is_active_sidebar( 'sidebar-1' ) ) { return; } ?>
<aside id="secondary" class="widget-area">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
```

Load it from a template with `get_sidebar()`. For more areas, register them with new IDs (`footer-1`, `footer-2`) and print them with `dynamic_sidebar()` wherever they belong.

Block widgetsSince WordPress 5.8 the widgets screen uses blocks. Your registered areas work the same way; site owners just add blocks instead of classic widgets.

## 13. Comments

`comments_template()` loads `comments.php` from your theme. A minimal version:

```
<?php if ( post_password_required() ) { return; } ?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php printf(
				esc_html( _n( '%s comment', '%s comments', get_comments_number(), 'my-theme' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			); ?>
		</h2>

		<ol class="comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'avatar_size' => 48, 'short_ping' => true ) ); ?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php comment_form(); ?>
</div>
```

- `wp_list_comments()` handles threading, avatars and reply links. Pass a `callback` for fully custom markup.
- `comment_form()` prints the form with nonces and spam protection hooks. Change its labels through its arguments.
- Remember to enqueue `comment-reply` (section 8) so "Reply" moves the form under the comment.

## 14. Customizer settings

The Customizer (**Appearance → Customize**) lets site owners change theme options with a live preview. Each option needs a *section*, a *setting* (the stored value) and a *control* (the input).

```
function my_theme_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'my_theme_options', array(
		'title' => __( 'Theme Options', 'my-theme' ),
	) );

	$wp_customize->add_setting( 'my_theme_footer_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',   // always sanitize
	) );

	$wp_customize->add_control( 'my_theme_footer_text', array(
		'label'   => __( 'Footer text', 'my-theme' ),
		'section' => 'my_theme_options',
		'type'    => 'text',
	) );

	// A colour picker
	$wp_customize->add_setting( 'my_theme_accent', array(
		'default'           => '#2563eb',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control(
		$wp_customize, 'my_theme_accent',
		array( 'label' => __( 'Accent colour', 'my-theme' ), 'section' => 'my_theme_options' )
	) );
}
add_action( 'customize_register', 'my_theme_customize_register' );
```

Read a value in a template with `get_theme_mod()`, and escape it on output:

```
echo esc_html( get_theme_mod( 'my_theme_footer_text', '' ) );
```

Output a colour as a CSS variable so your stylesheet can use it:

```
wp_add_inline_style( 'my-theme-main',
	':root{--color-accent:' . esc_attr( get_theme_mod( 'my_theme_accent', '#2563eb' ) ) . ';}' );
```

## 15. Custom page templates

A page template is a PHP file the editor can pick from the **Template** panel when editing a page. Add a `Template Name` comment at the top:

my-theme/templates/full-width.php

```
<?php
/**
 * Template Name: Full Width
 * Template Post Type: page, post
 */
get_header();
?>
<main id="primary" class="site-main site-main--wide">
	<?php while ( have_posts() ) : the_post();
		get_template_part( 'template-parts/content', 'page' );
	endwhile; ?>
</main>
<?php get_footer(); ?>
```

Templates can live in the theme root or one level deep in a subfolder like `templates/`.

### Template file or page template?

- `page-about.php` applies automatically to the page with slug `about`. If the slug changes, it stops working.
- A page template applies to whichever page an editor chooses. More flexible for clients.

### Where custom post types belong

Content in plugins, presentation in themesRegister custom post types, taxonomies and shortcodes in a plugin (or a must-use plugin), not in the theme. If they lived in the theme, switching themes would make that content disappear from the admin. The theme should only provide templates for them, like single-book.php and archive-book.php.

## 16. Security: escape, sanitize, verify

Three habits prevent almost every theme vulnerability.

### 1. Escape on output, as late as possible

Any value that isn't a hard-coded string should pass through an escaping function right where it's printed.

FunctionUse foresc_html()Text inside HTML elementsesc_attr()Values inside HTML attributesesc_url()URLs in href and srcesc_js() / wp_json_encode()Values placed in inline JavaScriptwp_kses_post()HTML that may contain safe tags (like post content)esc_html__(), esc_html_e(), esc_attr__()Translate and escape in one step

```
<a href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_attr( $label ); ?>">
	<?php echo esc_html( $label ); ?>
</a>
```

### 2. Sanitize on input

Clean any data before storing it: `sanitize_text_field()`, `sanitize_email()`, `absint()`, `sanitize_hex_color()`, `esc_url_raw()`. Every Customizer setting needs a `sanitize_callback`.

### 3. Verify intent and permission

If your theme handles a form or an AJAX request, check a nonce and the user's capability:

```
if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'my_theme_action' ) ) {
	wp_die( esc_html__( 'Security check failed.', 'my-theme' ) );
}
if ( ! current_user_can( 'edit_theme_options' ) ) {
	wp_die( esc_html__( 'Not allowed.', 'my-theme' ) );
}
```

### Other rules

- Start PHP files that shouldn't be loaded directly with `if ( ! defined( 'ABSPATH' ) ) exit;`
- Use `$wpdb->prepare()` for any raw SQL (though a theme rarely needs raw SQL).
- Never `eval()`, never include files based on user input.

## 17. Translation (i18n)

Wrap every user-facing string in a translation function with your theme's **text domain** (it must match the folder slug and the `Text Domain` header).

FunctionDoes__( 'Text', 'my-theme' )Returns the translated string_e( 'Text', 'my-theme' )Echoes itesc_html__() / esc_html_e()Translate + escape for HTMLesc_attr__() / esc_attr_e()Translate + escape for attributes_n( 'One', 'Many', $count, 'my-theme' )Singular/plural_x( 'Post', 'verb', 'my-theme' )Adds context for translatorsUse placeholders rather than joining strings, and leave a comment for translators:

```
/* translators: %s: search query. */
printf( esc_html__( 'Search results for: %s', 'my-theme' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
```

Load the text domain in your setup function (section 7), then generate the template file with WP-CLI:

```
wp i18n make-pot . languages/my-theme.pot
```

Translators use the `.pot` file with a tool like Poedit to produce `.po`/`.mo` files (e.g. `languages/fr_FR.mo`).

## 18. Child themes

A child theme changes another theme without editing it, so updates to the parent don't wipe out your changes. If you're customising someone else's theme, always use a child theme.

my-theme-child/style.css

```
/*
Theme Name: My Theme Child
Template:   my-theme
Version:    1.0.0
*/
```

`Template` must be the parent's folder name.

my-theme-child/functions.php

```
<?php
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'my-theme-child', get_stylesheet_uri(), array( 'my-theme-style' ),
		wp_get_theme()->get( 'Version' ) );
} );
```

### How overriding works

- **Templates**: copy any template (`single.php`, `template-parts/content.php`) into the child at the same path. The child's copy wins.
- **functions.php**: both run. The child's loads first, which is why parent helpers wrapped in `function_exists()` can be replaced.
- **Hooks**: use `remove_action()` / `add_action()` in the child to change parent behaviour.

Make your own theme child-friendlyLoad files with get_template_part() (it checks the child first), use get_template_directory_uri() for parent assets, and wrap template helpers in function_exists().

## 19. Testing and debugging

### Test with realistic content

1. Download the Theme Unit Test XML (search "WordPress theme unit test data").
2. Import it via **Tools → Import → WordPress**.
3. Click through every post: long titles, no titles, sticky posts, nested comments, image alignments, galleries, password-protected posts, many tags.

### Run the checkers

- **Theme Check** plugin: required files, deprecated functions, missing escaping, licence issues.
- **PHP_CodeSniffer** with *WordPress Coding Standards*: `composer require --dev wp-coding-standards/wpcs` then `vendor/bin/phpcs --standard=WordPress .`
- **PHP lint**: `php -l functions.php` catches syntax errors before WordPress does.

### Check the front end

- Every screen width from 320 px up; no horizontal scroll.
- Keyboard only: Tab through the page, the skip link appears, focus is visible, the menu opens.
- A screen reader or the browser's accessibility panel: landmarks (`header`, `nav`, `main`, `footer`) and labels are present.
- Lighthouse or PageSpeed Insights for performance and accessibility scores.
- Logged in (admin bar visible) and logged out.

### When something breaks

- White screen: read `wp-content/debug.log`.
- Wrong template: check Query Monitor's Template panel.
- Styles not loading: view source, find your handle, check the URL and version.
- Changes not showing: clear caching plugins and the browser cache, or bump the version.

## 20. Packaging and distribution

### Before you zip

- Bump `Version` in `style.css` and add a changelog entry in `readme.txt`.
- Remove development files: `node_modules/`, `.git/`, source maps you don't need, `.DS_Store`.
- Include built assets, `screenshot.png`, `readme.txt` and the licence.

### Create the zip

The zip must contain the theme folder itself, not loose files:

```
my-theme.zip
└── my-theme/
    ├── style.css
    ├── index.php
    └── ...
```

Install it under **Appearance → Themes → Add New → Upload Theme**, or copy the folder to `wp-content/themes/` over SFTP.

### Publishing on WordPress.org

- Must be 100% GPL-compatible, including fonts, images and libraries.
- Must pass Theme Check and the manual review (escaping, prefixing, no plugin-territory features, no upsell nags).
- Must have a valid `readme.txt` with `Requires at least`, `Tested up to` and `Requires PHP`.
- Upload at wordpress.org/themes/upload once you have a WordPress.org account.

### Deploying to a client site

Keep the theme in Git. Deploy with your host's Git integration, a CI pipeline (GitHub Actions + rsync/SFTP), or by uploading the zip. Never edit theme files on the live server.

## 21. Using the blank-theme boilerplate

The `blank-theme/` folder next to this PDF contains everything in this guide, ready to rename and build on.

```
blank-theme/
├── style.css               Theme header + core WP classes
├── functions.php           Setup, menus, widgets, enqueues
├── index.php               Fallback template
├── header.php / footer.php Site header and footer
├── single.php              Single post
├── page.php                Static page
├── archive.php             Archives
├── search.php              Search results
├── 404.php                 Not found
├── sidebar.php             Widget area
├── comments.php            Comment list + form
├── searchform.php          Search form
├── readme.txt              Theme readme
├── template-parts/
│   ├── content.php         Post summary
│   ├── content-single.php  Full post
│   ├── content-page.php    Page body
│   ├── content-search.php  Search result
│   └── content-none.php    Nothing found
├── inc/
│   ├── template-tags.php   Date/author, footer meta, thumbnails
│   └── customizer.php      Sample "Footer text" setting
├── assets/
│   ├── css/main.css        Base styles
│   └── js/main.js          Mobile menu toggle
└── languages/              Translation files
```

### Make it yours in five steps

1. Rename the folder, e.g. `blank-theme` → `my-theme`.
2. Find and replace across all files (case-sensitive, in this order): FindReplace withWhat it is `'blank-theme'``'my-theme'`Text domain and handles `blank_theme_``my_theme_`Function prefix `BLANK_THEME_``MY_THEME_`Constants `Blank_Theme``My_Theme``@package` tag `Blank Theme``My Theme`Theme name
3. Fill in the `style.css` header: author, URIs, description.
4. Add a 1200×900 `screenshot.png`.
5. Copy the folder into `wp-content/themes/` and activate it.

Then start designing: edit `assets/css/main.css`, add the templates your design needs, and grow `inc/` as features arrive.

## 22. Launch checklist and resources

### Checklist

- `style.css` header complete; version bumped
- `wp_head()`, `wp_body_open()`, `wp_footer()`, `body_class()`, `post_class()` all present
- All CSS/JS enqueued, none hard-coded
- Every function, handle and option prefixed
- Every dynamic output escaped; every saved value sanitized
- Every string translatable with the correct text domain
- No custom post types, shortcodes or analytics in the theme
- Theme Unit Test data looks right; no PHP notices in `debug.log`
- Theme Check passes
- Works from 320 px wide; keyboard-navigable; skip link works
- `screenshot.png` and `readme.txt` included
- Zip contains the theme folder, without dev files

### Official resources

- Theme Handbook: developer.wordpress.org/themes/
- Template Hierarchy: developer.wordpress.org/themes/basics/template-hierarchy/
- Code Reference (every function and hook): developer.wordpress.org/reference/
- Coding Standards: developer.wordpress.org/coding-standards/
- Theme Review guidelines: make.wordpress.org/themes/handbook/review/
- Block themes and theme.json: developer.wordpress.org/themes/global-settings-and-styles/

### Where to go next

- Add a `theme.json` to control block editor colours, fonts and spacing, even in a classic theme.
- Register block patterns in a `patterns/` folder so editors can drop in ready-made sections.
- Try converting the theme into a block theme with `templates/` and `parts/` HTML files.