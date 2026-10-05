<?php
/**
 * Footer.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="pie sobre-negro">
	<div class="contenedor">
		<div>
			<a class="identidad" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.png' ); ?>" alt="" width="240" height="245" loading="lazy">
				<span class="marca"><?php bloginfo( 'name' ); ?></span>
			</a>
		</div>
		<div>
			<h2>Secciones</h2>
			<?php an_menu(); ?>
		</div>
		<div>
			<h2>Seguinos</h2>
			<ul>
				<li><a href="https://www.youtube.com/@ArgenoticiasCOM" target="_blank" rel="noopener">YouTube</a></li>
				<li><a href="https://www.instagram.com/argenoticiascom/" target="_blank" rel="noopener">Instagram</a></li>
				<li><a href="https://www.facebook.com/argenoticias/" target="_blank" rel="noopener">Facebook</a></li>
				<li><a href="mailto:<?php echo esc_attr( AN_CORREO ); ?>"><?php echo esc_html( AN_CORREO ); ?></a></li>
			</ul>
		</div>
	</div>
	<p class="legal">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Argenoticias</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
