<?php
/**
 * Replaces the legacy Forminator shortcodes in migrated pages (run AFTER migrate-elementor.php).
 *   - service / landing / contact-style forms  -> [esnad_contact_form]
 *   - duplicate "mobile" variants               -> removed
 *   - careers CV-upload forms                   -> e-mail instruction (no public file upload)
 *   - investor registration forms (ID numbers)  -> "pending regulatory review" notice (no onboarding without approved spec)
 * Dry run by default; ESNAD_WRITE=1 writes.
 */
if ( ! defined( 'WP_CLI' ) ) {
	exit;
}
$write = '1' === getenv( 'ESNAD_WRITE' );

$contact_forms = array( 54941, 54972, 53639, 55104, 52792, 31764, 40496, 44930 );
$remove_forms  = array( 44350, 44932 );
$careers_forms = array( 8006 => 'ar', 30198 => 'en' );
$invest_forms  = array( 48735, 56235 );

$block = static fn( string $inner ): string => "<!-- wp:shortcode -->\n$inner\n<!-- /wp:shortcode -->";
$para  = static fn( string $html ): string => "<!-- wp:paragraph -->\n<p>$html</p>\n<!-- /wp:paragraph -->";

global $wpdb;
$posts = $wpdb->get_results( "SELECT ID, post_content FROM {$wpdb->posts} WHERE post_type IN ('page','post') AND post_status='publish' AND post_content LIKE '%forminator_form%'" );
$stats = array( 'contact' => 0, 'removed' => 0, 'careers' => 0, 'invest' => 0, 'unknown' => 0 );
foreach ( $posts as $p ) {
	$new = preg_replace_callback(
		'/<!-- wp:shortcode -->\s*\[forminator_form[^\]]*?id="?(\d+)"?[^\]]*\]\s*<!-- \/wp:shortcode -->/',
		static function ( array $m ) use ( $contact_forms, $remove_forms, $careers_forms, $invest_forms, $block, $para, &$stats ): string {
			$id = (int) $m[1];
			if ( in_array( $id, $contact_forms, true ) ) {
				$stats['contact']++;
				return $block( '[esnad_contact_form]' );
			}
			if ( in_array( $id, $remove_forms, true ) ) {
				$stats['removed']++;
				return '';
			}
			if ( isset( $careers_forms[ $id ] ) ) {
				$stats['careers']++;
				return 'ar' === $careers_forms[ $id ]
					? $para( 'لإرسال سيرتك الذاتية يرجى التواصل عبر البريد الإلكتروني <a href="mailto:info@esnadadvisory.sa">info@esnadadvisory.sa</a>.' )
					: $para( 'To send your CV, please contact us at <a href="mailto:info@esnadadvisory.sa">info@esnadadvisory.sa</a>.' );
			}
			if ( in_array( $id, $invest_forms, true ) ) {
				$stats['invest']++;
				return $para( 'نموذج التسجيل في هذه الفرصة قيد المراجعة التنظيمية ولم يُفعَّل بعد على الموقع الجديد.' );
			}
			$stats['unknown']++;
			return $m[0];
		},
		$p->post_content
	);
	if ( $write && $new !== $p->post_content ) {
		$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, array( 'post_content' => $new ), array( 'ID' => $p->ID ) ); clean_post_cache( $p->ID ); // direct write: avoids WPML save hooks
	}
}
WP_CLI::log( ( $write ? 'WROTE ' : 'DRY RUN ' ) . wp_json_encode( $stats ) );
