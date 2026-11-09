=== Blank Theme ===
Contributors: yourname
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A minimal classic WordPress starter theme.

== Description ==

Blank Theme gives you the smallest useful set of template files, a clean
functions.php, and nothing else. Rename it and start building.

== Getting started ==

1. Rename the folder (e.g. my-theme).
2. Find and replace, case-sensitive, across every file:
   - 'blank-theme'   -> 'my-theme'     (text domain, handles)
   - blank_theme_    -> my_theme_      (function prefix)
   - BLANK_THEME_    -> MY_THEME_      (constants)
   - Blank_Theme     -> My_Theme       (@package)
   - Blank Theme     -> My Theme       (Theme Name in style.css)
3. Add a 1200x900 screenshot.png to the theme root.
4. Zip the folder or copy it to wp-content/themes/, then activate it under
   Appearance -> Themes.

== File structure ==

style.css                 Theme header (required) + core WP classes
functions.php             Theme supports, menus, widgets, enqueues
index.php                 Fallback template (required)
header.php / footer.php   Site header and footer
single.php                Single post
page.php                  Static page
archive.php               Category, tag, author, date archives
search.php                Search results
404.php                   Not found
sidebar.php               Widget area
comments.php              Comment list + form
searchform.php            Search form markup
template-parts/           Reusable content partials
inc/template-tags.php     Small output helpers
inc/customizer.php        Customizer settings
assets/css/main.css       Main styles
assets/js/main.js         Mobile menu toggle
languages/                Translation files (.pot/.po/.mo)

== Changelog ==

= 1.0.0 =
* Initial release.
