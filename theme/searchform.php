<?php
/**
 * Search form.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="form-buscar" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Buscar una noticia" aria-label="Buscar una noticia" required>
	<button type="submit" class="boton boton-celeste">Buscar</button>
</form>
