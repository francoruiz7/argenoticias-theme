<?php
/**
 * Not found.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="listado">
	<div class="contenedor">
		<header class="listado-cabeza">
			<h1>Esta página no existe</h1>
			<p>Puede que la dirección esté mal escrita o que la nota se haya movido. Buscala por título o volvé a la portada.</p>
		</header>
		<?php get_search_form(); ?>
		<p style="margin-top:1.5rem;"><a class="boton" href="<?php echo esc_url( home_url( '/' ) ); ?>">Ir a la portada</a></p>
	</div>
</section>
<?php
get_footer();
