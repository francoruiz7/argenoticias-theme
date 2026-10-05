<?php
/**
 * Front page.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Page 2 onwards: regular listing.
if ( is_paged() ) {
	get_template_part( 'template-parts/listado' );
	get_footer();
	return;
}

$an_portada = get_posts(
	array(
		'posts_per_page'      => AN_PORTADA,
		'ignore_sticky_posts' => true,
		'fields'              => 'ids',
	)
);

if ( ! $an_portada ) :
	?>
	<section class="listado"><div class="contenedor"><p class="sin-resultados">Todavía no hay notas publicadas.</p></div></section>
	<?php
	get_footer();
	return;
endif;

$an_principal = array_shift( $an_portada );
$an_usadas    = array_merge( array( $an_principal ), $an_portada );

$an_grilla = get_posts(
	array(
		'posts_per_page'      => AN_TANDA,
		'post__not_in'        => $an_usadas,
		'ignore_sticky_posts' => true,
		'fields'              => 'ids',
	)
);
$an_total  = (int) wp_count_posts( 'post' )->publish;
$an_cats   = array_slice( an_secciones(), 0, 8 );
?>

<section class="portada">
	<div class="contenedor">
		<article class="principal">
			<?php an_foto( $an_principal, 'large', false ); ?>
			<?php an_etiqueta( $an_principal ); ?>
			<h1><a href="<?php echo esc_url( get_permalink( $an_principal ) ); ?>"><?php echo esc_html( get_the_title( $an_principal ) ); ?></a></h1>
			<p class="bajada"><?php echo esc_html( get_the_excerpt( $an_principal ) ); ?></p>
			<?php an_fecha( $an_principal ); ?>
		</article>
		<div class="secundarias">
			<?php
			foreach ( $an_portada as $an_id ) {
				an_tarjeta( $an_id, 'secundaria', 'h2' );
			}
			?>
		</div>
	</div>
</section>

<section class="cuerpo">
	<div class="contenedor">
		<div>
			<h2 class="titulo-seccion">Más noticias</h2>
			<div class="filtros" id="filtros" role="group" aria-label="Filtrar por sección">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-seccion="" aria-current="true">Todas</a>
				<?php foreach ( $an_cats as $an_cat ) : ?>
					<?php
					if ( in_array( $an_cat->slug, array( 'uncategorized', 'sin-categoria' ), true ) ) {
						continue;
					}
					?>
					<a href="<?php echo esc_url( get_category_link( $an_cat ) ); ?>" data-seccion="<?php echo esc_attr( $an_cat->term_id ); ?>" aria-current="false"><?php echo esc_html( $an_cat->name ); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="grilla" id="grilla" aria-live="polite" data-excluir="<?php echo esc_attr( implode( ',', $an_usadas ) ); ?>" data-tanda="<?php echo esc_attr( AN_TANDA ); ?>">
				<?php
				foreach ( $an_grilla as $an_id ) {
					an_tarjeta( $an_id );
				}
				?>
			</div>
			<?php if ( $an_total > count( $an_usadas ) + count( $an_grilla ) ) : ?>
				<div class="mas"><a class="boton" id="cargar-mas" href="<?php echo esc_url( get_pagenum_link( 2 ) ); ?>">Cargar más noticias</a></div>
			<?php endif; ?>
		</div>

		<aside class="lateral">
			<?php $an_tendencias = an_tendencias( 5, array( $an_principal ) ); ?>
			<?php if ( $an_tendencias ) : ?>
				<div class="tendencias sobre-negro">
					<h2>Tendencias</h2>
					<ol>
						<?php foreach ( $an_tendencias as $an_id ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $an_id ) ); ?>"><?php echo esc_html( get_the_title( $an_id ) ); ?></a></li>
						<?php endforeach; ?>
					</ol>
				</div>
			<?php endif; ?>

			<?php if ( shortcode_exists( 'jetpack_subscription_form' ) ) : ?>
				<div class="newsletter">
					<h2>Suscribite a nuestro newsletter</h2>
					<p>Las noticias del día en tu correo.</p>
					<?php echo do_shortcode( '[jetpack_subscription_form title="" subscribe_text="" subscribe_button="Suscribirme" show_subscribers_total="0"]' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			<?php endif; ?>
		</aside>
	</div>
</section>

<?php
get_footer();
