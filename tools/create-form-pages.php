<?php
/**
 * Creates the consultation and complaints pages (Arabic + English, linked as WPML translations)
 * and swaps the old Forminator contact form for the Esnad contact form.
 * Run on staging: wp eval-file tools/create-form-pages.php
 * Idempotent: existing pages (by slug) are reused.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

function esnad_cp_page( string $slug, string $title, string $content, string $lang, ?int $trid_from = null ): int {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	$id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content,
	) );
	if ( $trid_from ) {
		$trid = apply_filters( 'wpml_element_trid', null, $trid_from, 'post_page' );
		do_action( 'wpml_set_element_language_details', array(
			'element_id'           => $id,
			'element_type'         => 'post_page',
			'trid'                 => $trid,
			'language_code'        => $lang,
			'source_language_code' => 'ar',
		) );
	}
	return (int) $id;
}

$sc = static fn( string $code ): string => "<!-- wp:shortcode -->\n[$code]\n<!-- /wp:shortcode -->";

$ar_c = esnad_cp_page( 'request-consultation', 'اطلب استشارة', "<!-- wp:paragraph -->\n<p>اختر الخدمة التي تهمك واترك بياناتك، وسيتواصل معك فريقنا. هذا النموذج لطلب الاستشارة فقط، ولا يُطلب فيه أي بيانات مصرفية.</p>\n<!-- /wp:paragraph -->\n\n" . $sc( 'esnad_consultation_form' ), 'ar' );
$en_c = esnad_cp_page( 'request-consultation-en', 'Request a consultation', "<!-- wp:paragraph -->\n<p>Choose the service you are interested in and leave your details; our team will contact you. This form is for consultation requests only and never asks for bank details.</p>\n<!-- /wp:paragraph -->\n\n" . $sc( 'esnad_consultation_form' ), 'en', $ar_c );
$ar_k = esnad_cp_page( 'complaints', 'الشكاوى', "<!-- wp:paragraph -->\n<p>يمكنك إرسال شكواك عبر النموذج التالي، أو التواصل معنا عبر البريد الإلكتروني info@esnadadvisory.sa.</p>\n<!-- /wp:paragraph -->\n\n" . $sc( 'esnad_complaint_form' ), 'ar' );
$en_k = esnad_cp_page( 'complaints-en', 'Complaints', "<!-- wp:paragraph -->\n<p>You can submit your complaint using the form below, or contact us at info@esnadadvisory.sa.</p>\n<!-- /wp:paragraph -->\n\n" . $sc( 'esnad_complaint_form' ), 'en', $ar_k );
WP_CLI::log( "pages: consult ar=$ar_c en=$en_c | complaints ar=$ar_k en=$en_k" );

// Contact pages: replace the Forminator form (id 7360) with the Esnad contact form.
foreach ( array( 1483, 29484 ) as $pid ) {
	$post = get_post( $pid );
	if ( ! $post ) {
		continue;
	}
	$new = preg_replace( '/\[forminator_form[^\]]*\]/', '[esnad_contact_form]', $post->post_content, -1, $n );
	if ( $n ) {
		$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, array( 'post_content' => $new ), array( 'ID' => $pid ) ); clean_post_cache( $pid ); // direct write: avoids WPML save hooks
		WP_CLI::log( "contact page $pid: replaced $n form(s)" );
	}
}
