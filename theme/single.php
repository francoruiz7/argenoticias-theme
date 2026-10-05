<?php
/**
 * Single article.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$an_id  = get_the_ID();
	$an_cat = an_seccion( $an_id );
	?>
	<div class="nota">
		<div class="contenedor">
			<article class="nota-columna">
				<header class="nota-cabeza">
					<?php an_etiqueta( $an_id ); ?>
					<h1><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="bajada"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<?php an_fecha( $an_id ); ?>
				</header>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="nota-foto">
						<?php the_post_thumbnail( 'large' ); ?>
						<?php if ( get_the_post_thumbnail_caption() ) : ?>
							<figcaption><?php echo esc_html( get_the_post_thumbnail_caption() ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endif; ?>

				<div class="nota-texto">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>

				<?php if ( ! function_exists( 'sharing_display' ) ) : ?>
					<?php
					$an_url    = rawurlencode( get_permalink() );
					$an_titulo = rawurlencode( get_the_title() );
					?>
					<div class="compartir">
						<span>Compartir</span>
						<a href="https://wa.me/?text=<?php echo esc_attr( $an_titulo . '%20' . $an_url ); ?>" target="_blank" rel="noopener">WhatsApp</a>
						<a href="https://twitter.com/intent/tweet?text=<?php echo esc_attr( $an_titulo ); ?>&amp;url=<?php echo esc_attr( $an_url ); ?>" target="_blank" rel="noopener">X</a>
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $an_url ); ?>" target="_blank" rel="noopener">Facebook</a>
					</div>
				<?php endif; ?>
			</article>

			<?php
			$an_relacionadas = get_posts(
				array(
					'posts_per_page'      => 3,
					'post__not_in'        => array( $an_id ),
					'category__in'        => $an_cat ? array( $an_cat->term_id ) : array(),
					'ignore_sticky_posts' => true,
					'fields'              => 'ids',
				)
			);
			if ( $an_relacionadas ) :
				?>
				<section class="relacionadas">
					<h2 class="titulo-seccion"><?php echo $an_cat ? esc_html( 'Más de ' . $an_cat->name ) : 'Más noticias'; ?></h2>
					<div class="grilla">
						<?php
						foreach ( $an_relacionadas as $an_rel ) {
							an_tarjeta( $an_rel );
						}
						?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
