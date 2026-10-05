<?php
/**
 * Theme functions.
 *
 * @package argenoticias
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Contact address shown in the footer. */
define( 'AN_CORREO', 'prensa@argenoticias.com.ar' );

/** Stories on the front page: lead + secondary, and grid batch size. */
define( 'AN_PORTADA', 5 );
define( 'AN_TANDA', 9 );

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
		register_nav_menus( array( 'secciones' => 'Secciones (barra celeste)' ) );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$version = wp_get_theme()->get( 'Version' );

		// Version is null so WordPress does not alter the Google Fonts URL.
		wp_enqueue_style(
			'an-fuentes',
			'https://fonts.googleapis.com/css2?family=Chivo:wght@400;500;700;900&family=Newsreader:opsz,wght@6..72,500&family=Faustina:ital,wght@0,400;0,500;1,400&display=swap',
			array(),
			null
		);
		wp_enqueue_style( 'an-estilos', get_stylesheet_uri(), array( 'an-fuentes' ), $version );
		wp_enqueue_script( 'an-main', get_template_directory_uri() . '/assets/js/main.js', array(), $version, true );
		wp_localize_script( 'an-main', 'AN', array( 'rest' => esc_url_raw( rest_url( 'wp/v2/posts' ) ) ) );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
);

/**
 * Site sections in editorial order. Empty ones and the default category are not shown.
 */
function an_secciones() {
	$orden   = array( 'politica', 'sociedad', 'policiales', 'deportes', 'internacional', 'espectaculos', 'efemerides', 'bizarras' );
	$ocultas = array( 'sin-categoria', 'uncategorized' );
	$resto   = array();

	foreach ( get_categories( array( 'parent' => 0, 'hide_empty' => true ) ) as $cat ) {
		if ( ! in_array( $cat->slug, $ocultas, true ) ) {
			$resto[ $cat->slug ] = $cat;
		}
	}

	$lista = array();
	foreach ( $orden as $slug ) {
		if ( isset( $resto[ $slug ] ) ) {
			$lista[] = $resto[ $slug ];
			unset( $resto[ $slug ] );
		}
	}

	// Any other category with posts comes after, largest first.
	usort(
		$resto,
		function ( $a, $b ) {
			return $b->count - $a->count;
		}
	);

	return array_slice( array_merge( $lista, $resto ), 0, 10 );
}

/**
 * Sections menu fallback when none is assigned under Appearance > Menus.
 */
function an_menu_secciones() {
	$actual = is_category() ? (int) get_queried_object_id() : 0;
	echo '<ul>';
	foreach ( an_secciones() as $cat ) {
		printf(
			'<li%s><a href="%s">%s</a></li>',
			$actual === (int) $cat->term_id ? ' class="current-cat"' : '',
			esc_url( get_category_link( $cat ) ),
			esc_html( $cat->name )
		);
	}
	echo '</ul>';
}

function an_menu() {
	wp_nav_menu(
		array(
			'theme_location' => 'secciones',
			'container'      => false,
			'items_wrap'     => '<ul>%3$s</ul>',
			'depth'          => 1,
			'fallback_cb'    => 'an_menu_secciones',
		)
	);
}

/**
 * First "real" category of a post (skips the default category).
 */
function an_seccion( $post_id ) {
	$cats = get_the_category( $post_id );
	if ( empty( $cats ) ) {
		return null;
	}
	foreach ( $cats as $cat ) {
		if ( ! in_array( $cat->slug, array( 'uncategorized', 'sin-categoria' ), true ) ) {
			return $cat;
		}
	}
	return null;
}

function an_etiqueta( $post_id ) {
	$cat = an_seccion( $post_id );
	if ( $cat ) {
		printf( '<a class="etiqueta" href="%s">%s</a>', esc_url( get_category_link( $cat ) ), esc_html( $cat->name ) );
	}
}

function an_fecha( $post_id ) {
	printf(
		'<time class="fecha" datetime="%s">%s</time>',
		esc_attr( get_the_date( 'c', $post_id ) ),
		esc_html( get_the_date( 'j \d\e F \d\e Y', $post_id ) )
	);
}

/**
 * Story image: the featured image or, failing that, the first image in the content.
 */
function an_foto_url( $post_id, $size ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$url = get_the_post_thumbnail_url( $post_id, $size );
		if ( $url ) {
			return $url;
		}
	}
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $m ) ) {
		return $m[1];
	}
	return '';
}

function an_foto( $post_id, $size = 'medium_large', $lazy = true ) {
	$url = an_foto_url( $post_id, $size );
	printf( '<a class="foto" href="%s" tabindex="-1" aria-hidden="true">', esc_url( get_permalink( $post_id ) ) );
	if ( $url ) {
		printf( '<img src="%s" alt=""%s>', esc_url( $url ), $lazy ? ' loading="lazy"' : '' );
	}
	echo '</a>';
}

/**
 * Story card. $clase: "tarjeta" (grid) or "secundaria" (front page). $nivel: h2 or h3.
 */
function an_tarjeta( $post_id, $clase = 'tarjeta', $nivel = 'h3' ) {
	$nivel = in_array( $nivel, array( 'h2', 'h3' ), true ) ? $nivel : 'h3';
	echo '<article class="' . esc_attr( $clase ) . '">';
	an_foto( $post_id, 'medium_large' );
	echo '<div>';
	an_etiqueta( $post_id );
	printf( '<%1$s><a href="%2$s">%3$s</a></%1$s>', $nivel, esc_url( get_permalink( $post_id ) ), esc_html( get_the_title( $post_id ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	an_fecha( $post_id );
	echo '</div></article>';
}

/**
 * Trending stories: most viewed in the last week according to Jetpack Stats.
 * If stats are unavailable, falls back to the most commented of the last month, then the most recent.
 */
function an_tendencias( $cantidad = 5, $excluir = array() ) {
	$ids = get_transient( 'an_tendencias' );

	if ( ! is_array( $ids ) ) {
		$ids = array();

		if ( class_exists( '\Automattic\Jetpack\Stats\WPCOM_Stats' ) ) {
			try {
				$stats = new \Automattic\Jetpack\Stats\WPCOM_Stats();
				$top   = $stats->get_top_posts(
					array(
						'max'       => 20,
						'summarize' => 1,
						'num'       => 7,
					)
				);
				if ( is_array( $top ) && ! empty( $top['summary']['postviews'] ) && is_array( $top['summary']['postviews'] ) ) {
					foreach ( $top['summary']['postviews'] as $fila ) {
						$id = isset( $fila['id'] ) ? (int) $fila['id'] : 0;
						if ( $id && 'post' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
							$ids[] = $id;
						}
					}
				}
			} catch ( \Throwable $e ) {
				$ids = array();
			}
		}

		if ( count( $ids ) < $cantidad ) {
			$comentadas = get_posts(
				array(
					'posts_per_page'      => 10,
					'orderby'             => 'comment_count',
					'order'               => 'DESC',
					'date_query'          => array( array( 'after' => '30 days ago' ) ),
					'ignore_sticky_posts' => true,
					'fields'              => 'ids',
				)
			);
			$recientes  = get_posts(
				array(
					'posts_per_page'      => 20,
					'ignore_sticky_posts' => true,
					'fields'              => 'ids',
				)
			);
			$ids        = array_values( array_unique( array_merge( $ids, $comentadas, $recientes ) ) );
		}

		set_transient( 'an_tendencias', $ids, HOUR_IN_SECONDS );
	}

	$ids = array_values( array_diff( array_map( 'intval', $ids ), array_map( 'intval', $excluir ) ) );
	return array_slice( $ids, 0, $cantidad );
}

/**
 * Listing title (section, tag, search, date).
 */
function an_titulo_listado() {
	if ( is_search() ) {
		return 'Resultados para «' . get_search_query() . '»';
	}
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_author() ) {
		return get_the_author();
	}
	if ( is_day() ) {
		return get_the_date( 'j \d\e F \d\e Y' );
	}
	if ( is_month() ) {
		return ucfirst( get_the_date( 'F \d\e Y' ) );
	}
	if ( is_year() ) {
		return get_the_date( 'Y' );
	}
	return 'Todas las noticias';
}

/**
 * Short excerpt for the lead story.
 */
add_filter(
	'excerpt_length',
	function () {
		return 32;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '…';
	}
);

/**
 * Admin notice on the dashboard and the posts list when published posts have no section.
 */
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			return;
		}
		$pantalla = get_current_screen();
		if ( ! $pantalla || ! in_array( $pantalla->id, array( 'dashboard', 'edit-post' ), true ) ) {
			return;
		}

		$sin = null;
		foreach ( array( 'sin-categoria', 'uncategorized' ) as $slug ) {
			$term = get_term_by( 'slug', $slug, 'category' );
			if ( $term ) {
				$sin = $term;
				break;
			}
		}
		if ( ! $sin || $sin->count < 1 ) {
			return;
		}

		$texto = 1 === (int) $sin->count
			? 'Hay 1 nota publicada sin sección.'
			: sprintf( 'Hay %d notas publicadas sin sección.', (int) $sin->count );

		printf(
			'<div class="notice notice-warning"><p><strong>Argenoticias:</strong> %s No aparecen en ninguna sección de la web. <a href="%s">Asignarles una sección</a></p></div>',
			esc_html( $texto ),
			esc_url( admin_url( 'edit.php?category_name=' . $sin->slug ) )
		);
	}
);
