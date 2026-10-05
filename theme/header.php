<?php
/**
 * Header: utility bar, brand, sections nav and latest news ticker.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="saltar" href="#contenido">Ir al contenido</a>

<div class="servicios sobre-negro">
	<div class="contenedor">
		<time id="reloj"></time>
		<ul aria-label="Lo que necesitás saber">
			<li><a href="https://weather.com/es-AR/tiempo/hoy/l/ARBA0009:1:AR" target="_blank" rel="noopener">Clima</a></li>
			<li><a href="https://dolarhoy.com/" target="_blank" rel="noopener">Dólar</a></li>
			<li><a href="https://www.enelsubte.com/estado/" target="_blank" rel="noopener">Subte</a></li>
			<li><a href="https://coinmarketcap.com/es/" target="_blank" rel="noopener">Cripto</a></li>
			<li><a href="https://www.boletinoficial.gob.ar/" target="_blank" rel="noopener">Boletín Oficial</a></li>
			<li><a href="https://moovitapp.com/" target="_blank" rel="noopener">Cómo llegar</a></li>
			<li><a href="https://www.horamundial.com/" target="_blank" rel="noopener">Hora mundial</a></li>
		</ul>
	</div>
</div>

<header class="cabecera sobre-negro">
	<div class="contenedor">
		<a class="identidad" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.png' ); ?>" alt="" width="240" height="245">
			<span class="marca"><?php bloginfo( 'name' ); ?></span>
		</a>
		<div class="cabecera-acciones">
			<button type="button" class="boton boton-contorno" id="abrir-buscador" aria-expanded="false" aria-controls="buscador"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><line x1="15.6" y1="15.6" x2="21" y2="21"/></svg>Buscar</button>
		</div>
	</div>
</header>

<div class="buscador sobre-negro" id="buscador" hidden>
	<div class="contenedor">
		<?php get_search_form(); ?>
	</div>
</div>

<nav class="secciones" aria-label="Secciones">
	<div class="contenedor">
		<?php an_menu(); ?>
	</div>
</nav>

<?php
$an_cinta = get_posts(
	array(
		'posts_per_page'      => 6,
		'ignore_sticky_posts' => true,
		'fields'              => 'ids',
	)
);
if ( $an_cinta ) :
	?>
<div class="cinta sobre-negro" aria-label="Últimas noticias">
	<div class="cinta-rotulo">Últimas noticias</div>
	<div class="cinta-pista">
		<div class="cinta-lista">
			<?php
			// Headlines are repeated once so the loop has no visible seam.
			foreach ( array( false, true ) as $an_repetida ) {
				foreach ( $an_cinta as $an_id ) {
					printf(
						'<a href="%s"%s>%s</a>',
						esc_url( get_permalink( $an_id ) ),
						$an_repetida ? ' aria-hidden="true" tabindex="-1"' : '',
						esc_html( get_the_title( $an_id ) )
					);
				}
			}
			?>
		</div>
	</div>
</div>
<?php endif; ?>

<main id="contenido">
