<?php
/**
 * Small template helpers used by the template parts.
 *
 * @package Blank_Theme
 */

if ( ! function_exists( 'blank_theme_posted_on' ) ) :
	/**
	 * Print the post date and author.
	 */
	function blank_theme_posted_on() {
		printf(
			'<span class="posted-on"><time class="entry-date" datetime="%1$s">%2$s</time></span> <span class="byline">%3$s <a href="%4$s">%5$s</a></span>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_html__( 'by', 'blank-theme' ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'blank_theme_entry_footer' ) ) :
	/**
	 * Print categories, tags and the edit link.
	 */
	function blank_theme_entry_footer() {
		if ( 'post' === get_post_type() ) {
			$categories = get_the_category_list( esc_html__( ', ', 'blank-theme' ) );
			if ( $categories ) {
				echo '<span class="cat-links">' . esc_html__( 'Posted in ', 'blank-theme' ) . $categories . '</span> '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			$tags = get_the_tag_list( '', esc_html__( ', ', 'blank-theme' ) );
			if ( $tags ) {
				echo '<span class="tags-links">' . esc_html__( 'Tagged ', 'blank-theme' ) . $tags . '</span> '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		edit_post_link( esc_html__( 'Edit', 'blank-theme' ), '<span class="edit-link">', '</span>' );
	}
endif;

if ( ! function_exists( 'blank_theme_post_thumbnail' ) ) :
	/**
	 * Print the featured image: linked on archives, plain on singular views.
	 */
	function blank_theme_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) {
			echo '<div class="post-thumbnail">';
			the_post_thumbnail();
			echo '</div>';
			return;
		}

		echo '<a class="post-thumbnail" href="' . esc_url( get_permalink() ) . '" aria-hidden="true" tabindex="-1">';
		the_post_thumbnail( 'medium_large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) );
		echo '</a>';
	}
endif;
