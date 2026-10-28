<?php
/**
 * ESNAD NEXT theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

define( 'ESNAD_VER', '1.0.0' );

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/seo.php';

add_action( 'after_setup_theme', static function (): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_image_size( 'esnad-card', 720, 480, true );
	register_nav_menus( array( 'primary' => 'Primary', 'footer' => 'Footer' ) );
} );

/** Assets: one stylesheet, one deferred script, fonts preloaded from the theme. */
add_action( 'wp_enqueue_scripts', static function (): void {
	$uri = get_template_directory_uri();
	$dir = get_template_directory();
	// Version by file time so every change busts browser and CDN caches.
	wp_enqueue_style( 'esnad', $uri . '/assets/css/main.css', array(), (string) filemtime( $dir . '/assets/css/main.css' ) );
	wp_enqueue_script( 'esnad', $uri . '/assets/js/main.js', array(), (string) filemtime( $dir . '/assets/js/main.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	// Drop block/global CSS the theme does not need on the front end.
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wp-block-library-theme' );
}, 20 );

add_action( 'wp_head', static function (): void {
	$uri = get_template_directory_uri() . '/assets/fonts/';
	$sub = esnad_is_rtl() ? 'arabic' : 'latin';
	printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $uri . "readex-pro-$sub.woff2" ) );
}, 1 );

/** Official logo mark as favicon when no Site Icon is set in the Customizer. */
add_action( 'wp_head', static function (): void {
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" type="image/png" href="%s">' . "\n", esc_url( get_template_directory_uri() . '/assets/img/favicon.png' ) );
	}
}, 2 );

/** Remove default WordPress head clutter and emoji scripts. */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
add_filter( 'the_generator', '__return_empty_string' );

add_filter( 'language_attributes', static function ( string $out ): string {
	return 'lang="' . ( esnad_is_rtl() ? 'ar' : 'en' ) . '" dir="' . ( esnad_is_rtl() ? 'rtl' : 'ltr' ) . '"';
} );

add_filter( 'body_class', static function ( array $classes ): array {
	$classes[] = esnad_is_rtl() ? 'is-rtl' : 'is-ltr';
	return $classes;
} );

add_filter( 'excerpt_length', static fn(): int => 28 );
add_filter( 'excerpt_more', static fn(): string => '…' );

/** The page hero owns the single <h1>; any <h1> inside migrated content becomes an <h2>. */
add_filter( 'the_content', static function ( string $html ): string {
	return is_singular() ? (string) preg_replace( '/<(\/?)h1\b/i', '<$1h2', $html ) : $html;
}, 20 );

/** [esnad_clients]: the approved client logo wall (used by the migrated About page). */
add_shortcode( 'esnad_clients', static function (): string {
	$logos = esnad_json( 'clients.json' );
	if ( ! $logos ) {
		return '';
	}
	$out = '<ul class="clients__grid">';
	foreach ( $logos as $logo ) {
		$out .= '<li><img src="' . esc_url( content_url( 'uploads/' . $logo ) ) . '" alt="" width="160" height="90" loading="lazy" decoding="async"></li>';
	}
	return $out . '</ul>';
} );

/** Is the current page the articles page (either language)? */
function esnad_is_blog_page(): bool {
	return is_page() && in_array( get_the_ID(), array( 6346, 29470 ), true );
}

/** Is the current page one of the eight service pages (either language)? */
function esnad_is_service_page(): bool {
	if ( ! is_page() ) {
		return false;
	}
	foreach ( esnad_services() as $svc ) {
		if ( untrailingslashit( $svc['url'] ) === untrailingslashit( get_permalink() ) ) {
			return true;
		}
	}
	return false;
}
