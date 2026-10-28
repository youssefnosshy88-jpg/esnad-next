<?php
/**
 * Plugin Name: ESNAD Staging Guard
 * Description: On the Hostinger preview domain only: password gate, noindex, and no outbound email.
 */

defined( 'ABSPATH' ) || exit;

// Never active on the real domain.
$esnad_host = wp_parse_url( home_url(), PHP_URL_HOST );
if ( ! is_string( $esnad_host ) || false === strpos( $esnad_host, 'hostingersite.com' ) ) {
	return;
}

// Password gate (bcrypt hash kept outside the web root).
if ( 'cli' !== PHP_SAPI ) {
	// Defines ESNAD_STAGING_USER and ESNAD_STAGING_HASH (bcrypt). Not versioned; created on the server only.
	$esnad_file = WP_CONTENT_DIR . '/esnad-staging-auth.php';
	if ( is_readable( $esnad_file ) ) {
		require_once $esnad_file;
	}
	$esnad_ok   = false;
	$esnad_hdr  = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
	$esnad_user = $_SERVER['PHP_AUTH_USER'] ?? '';
	$esnad_pass = $_SERVER['PHP_AUTH_PW'] ?? '';
	if ( '' === $esnad_user && 0 === stripos( $esnad_hdr, 'basic ' ) ) {
		$parts = explode( ':', (string) base64_decode( substr( $esnad_hdr, 6 ) ), 2 );
		if ( 2 === count( $parts ) ) {
			list( $esnad_user, $esnad_pass ) = $parts;
		}
	}
	if ( '' !== $esnad_user && defined( 'ESNAD_STAGING_HASH' ) ) {
		$esnad_ok = hash_equals( ESNAD_STAGING_USER, $esnad_user ) && password_verify( $esnad_pass, ESNAD_STAGING_HASH );
	}
	if ( ! $esnad_ok ) {
		header( 'WWW-Authenticate: Basic realm="ESNAD staging"' );
		header( 'X-LiteSpeed-Cache-Control: no-cache' );
		header( 'Cache-Control: no-store' );
		status_header( 401 );
		exit( 'Authentication required.' );
	}
	// Keep gated pages out of any shared cache.
	header( 'X-LiteSpeed-Cache-Control: no-cache' );
	header( 'Cache-Control: private, no-store' );
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
