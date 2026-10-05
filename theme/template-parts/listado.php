<?php
/**
 * Story listing: sections, tags, search results and paged views.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="listado">
	<div class="contenedor">
		<header class="listado-cabeza">
			<h1><?php echo esc_html( an_titulo_listado() ); ?></h1>
			<?php if ( ( is_category() || is_tag() ) && term_description() ) : ?>
				<p><?php echo esc_html( wp_strip_all_tags( term_description() ) ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="grilla grilla-ancha">
				<?php
				while ( have_posts() ) {
					the_post();
					an_tarjeta( get_the_ID(), 'tarjeta', 'h2' );
				}
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => 'Anteriores',
					'next_text'          => 'Siguientes',
					'screen_reader_text' => 'Más páginas',
				)
			);
			?>
		<?php else : ?>
			<p class="sin-resultados">
				<?php echo is_search() ? 'No encontramos notas con esas palabras. Probá con otras.' : 'Todavía no hay notas en esta sección.'; ?>
			</p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>
