<?php
/**
 * Plugin Name: ESNAD Staging Guard
 * Description: On the Hostinger preview domain only: noindex and no outbound email. (The password gate was removed at the owner's request.)
 */

defined( 'ABSPATH' ) || exit;

// Never active on the real domain.
$esnad_host = wp_parse_url( home_url(), PHP_URL_HOST );
if ( ! is_string( $esnad_host ) || false === strpos( $esnad_host, 'hostingersite.com' ) ) {
	return;
}

// Never index staging.
add_filter( 'wp_robots', static function ( array $robots ): array {
	$robots['noindex']  = true;
	$robots['nofollow'] = true;
	return $robots;
}, 999 );
add_action( 'send_headers', static function (): void {
	header( 'X-Robots-Tag: noindex, nofollow', true );
} );
add_filter( 'option_blog_public', '__return_zero' );

// No outbound email: forms, notifications and password resets are all swallowed.
add_filter( 'pre_wp_mail', '__return_false' );

// Visible reminder in the admin bar.
add_action( 'admin_bar_menu', static function ( WP_Admin_Bar $bar ): void {
	$bar->add_node( array(
		'id'    => 'esnad-staging',
		'title' => 'STAGING — no email, noindex',
	) );
}, 1 );
