<?php
/**
 * Plugin Name: ESNAD Staging Guard
 * Description: Keeps the staging site out of search engines and stops it from sending any email.
 */

defined( 'ABSPATH' ) || exit;

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
