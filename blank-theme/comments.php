<?php
/**
 * Comments list and form.
 *
 * @package Blank_Theme
 */

// Don't show anything if the post is password protected and not yet unlocked.
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			printf(
				/* translators: 1: number of comments, 2: post title. */
				esc_html( _n( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'blank-theme' ) ),
				esc_html( number_format_i18n( get_comments_number() ) ),
				'<span>' . wp_kses_post( get_the_title() ) . '</span>'
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'blank-theme' ); ?></p>
			<?php
		endif;
	endif;

	comment_form();
	?>
</div>
