<?php
/**
 * Comments.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<section class="comentarios" id="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="titulo-seccion">Comentarios</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 0,
				)
			);
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'        => 'Dejá tu comentario',
			'label_submit'       => 'Publicar comentario',
			'title_reply_before' => '<h2 class="titulo-seccion" id="reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);
	?>
</section>
