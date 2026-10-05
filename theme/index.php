<?php
/**
 * Generic template: sections, tags, search results and archives.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
get_template_part( 'template-parts/listado' );
get_footer();
