<?php
/**
 * Static page.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="nota">
		<div class="contenedor">
			<article class="nota-columna">
				<header class="nota-cabeza">
					<h1><?php the_title(); ?></h1>
				</header>
				<div class="nota-texto">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
			</article>
		</div>
	</div>
	<?php
endwhile;

get_footer();
