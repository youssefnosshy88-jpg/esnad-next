<?php
/**
 * Polishes the English wording of migrated pages (grammar, naming, leftovers) without touching Arabic.
 * Matches whole paragraphs/headings by their normalised text, so entity forms and curly quotes do not matter.
 * Run on staging: ESNAD_WRITE=1 wp eval-file tools/polish-english.php   (without ESNAD_WRITE it only reports)
 * Idempotent: once a paragraph has the new text it no longer matches the old one.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

$write = (bool) getenv( 'ESNAD_WRITE' );

function esnad_pe_norm( string $s ): string {
	$s = html_entity_decode( wp_strip_all_tags( $s ), ENT_QUOTES, 'UTF-8' );
	$s = str_replace( array( "\u{2019}", "\u{2018}", "\u{201C}", "\u{201D}", "\u{00A0}" ), array( "'", "'", '"', '"', ' ' ), $s );
	return trim( (string) preg_replace( '/\s+/u', ' ', $s ) );
}

/** Replace the inner HTML of paragraphs/headings whose normalised text equals a key. $map: old text => new text ('' removes). */
function esnad_pe_apply( string $html, array $map, int &$n, array &$hit = array() ): string {
	$norm = array();
	foreach ( $map as $old => $new ) {
		$norm[ esnad_pe_norm( $old ) ] = $new;
	}
	return (string) preg_replace_callback(
		'#<(p|h2|h3|h4)(\b[^>]*)>(.*?)</\1>#s',
		static function ( array $m ) use ( $norm, &$n, &$hit ): string {
			$key = esnad_pe_norm( $m[3] );
			if ( ! array_key_exists( $key, $norm ) ) {
				// Long paragraphs: fall back to the first 70 characters (punctuation variants).
				$found = null;
				foreach ( array_keys( $norm ) as $k ) {
					if ( mb_strlen( $k ) > 100 && 0 === strpos( $key, mb_substr( $k, 0, 70 ) ) ) {
						$found = $k;
					}
				}
				if ( null === $found ) {
					return $m[0];
				}
				$key = $found;
			}
			$n++;
			return '' === $norm[ $key ] ? '' : '<' . $m[1] . $m[2] . '>' . esc_html( $norm[ $key ] ) . '</' . $m[1] . '>';
		},
		$html
	);
}

$pages = array(
	// Vision, mission and values (English)
	29474 => array(
		'Vision & Mission & Values' => 'Vision, Mission & Values',
		'Providing exceptional independent consulting services to clients in our field of specialization with the transparency.' => 'Providing exceptional, independent consulting services to clients in our field of specialization, with transparency.',
		"We do believe that providing both a professional image and quality service attract and retain our clients. We at Esnad Advisory Company are uniquely qualified team who behave professionally and solve your problems in a professional and courteous manner. We do what we say we’re going to do and deliver it on time within the deadlines we’ve promised, we don’t lie, even it means losing a sale or looking bad in front of a partner." => "We believe that a professional image and quality service attract and retain clients. At Esnad Advisory Company we are a uniquely qualified team that behaves professionally and solves your problems courteously. We do what we say we will do and deliver on time, within the deadlines we have promised. We never lie, even if it means losing a sale or looking bad in front of a partner.",
		"The quality of services is the key differentiator between good, bad and indifferent companies. We at Esnad believe that good quality services keep our clients coming back. We aim to deliver excellence, nothing less will do. We are continually scrutinizing and enhancing our service capability in order to deliver and exceed our customers’ expectations. Quality comes before anything else within our business… Our client references say it better than we can. Just ask us and we will send you a long list." => "Quality of service is what separates good companies from poor and indifferent ones. At Esnad we believe good service keeps clients coming back. We aim for excellence and nothing less, continually reviewing and enhancing our service capability so that we meet and exceed our clients' expectations. Quality comes before everything else in our business. Our client references say it better than we can: just ask, and we will send you a long list.",
		"Before we start any business relationship with you, we analyze, what drives & value for your business relative to competitors. Implementation of just a few of our approaches to your business can more than pay for itself in terms of reduced time and expense long after our work is complete — day after day, year after year. We ensure to create a win-win price of our services to build sustainable business relationship." => "Before we start any business relationship, we analyze what drives value for your business relative to your competitors. Implementing just a few of our approaches can more than pay for itself through reduced time and expense long after our work is complete, day after day and year after year. We price our services fairly, so that both sides win and the relationship lasts.",
		"This is the cornerstone of everything we do. We care about your business, your people, your specific needs, and results that you can use and measure immediately. We are enthusiastic, innovative, and dedicated in helping you reach your full potential. We love to share what we know and to create value together with our clients, partners, and associates." => "This is the cornerstone of everything we do. We care about your business, your people, your specific needs, and results you can use and measure immediately. We are enthusiastic, innovative and dedicated to helping you reach your full potential, and we love to share what we know and create value together with our clients, partners and associates.",
		"Trust is the basic rule of any business transaction, especially if it is financial. Without it, the client will not risk his money with an anonymous consulting firm and will not want to share his data with him, so trust is always the primary currency between us and our clients and transparency is vital to building this trust. And because this is our philosophy with our customers, we earn this trust with merit." => "Trust is the basic rule of any business transaction, especially a financial one. Without it, a client will not risk their money with an unknown consulting firm or share their data with it. Trust is therefore the primary currency between us and our clients, and transparency is vital to building it. Because this is our philosophy with clients, we earn that trust on merit.",
		"We are always available to serve you and answer your inquiries. You will always find us available at any time whenever you need us at the company’s headquarters or over the phone. We carry out many of our services after official working hours, so we will not affect your daily work schedule. Just ask us and we will be ready and available. Contact us by e-mail or phone, register your request on the customer service page, or schedule an appointment to meet with us through the appointment booking page. Our staff is always ready and waiting to serve you." => "We are always available to serve you and answer your inquiries. You will find us whenever you need us, at the company's headquarters or by phone. We carry out many of our services after official working hours so that your daily schedule is not affected. Contact us by email or phone, submit your request through the consultation page, or arrange a meeting with us. Our team is always ready to serve you.",
	),
	// About (English)
	43104 => array(
		'Analyze what can add value to your business compared to your competitors. By implementing a few of our services for your business, you can achieve much greater value than expected in terms of time and cost reduction, even long after our work and services are completed.' => 'We analyze what can add value to your business compared with your competitors. Implementing a few of our services can deliver far more value than expected, in time and cost savings, long after our work is complete.',
		'The fundamental currency between us and our clients is trust, and transparency is vital in building this trust. Because this is our philosophy with our clients, we earn this trust deservedly and rightfully.' => 'Trust is the fundamental currency between us and our clients, and transparency is vital to building it. Because this is our philosophy, we earn that trust on merit.',
		'We care about your business, aspirations, and needs, and the results can be seen and measured immediately. We are enthusiastic, creative, and dedicated to helping you achieve your full objectives.' => 'We care about your business, aspirations and needs, with results you can see and measure immediately. We are enthusiastic, creative and dedicated to helping you achieve your full objectives.',
		"Collaboration and coordination between different departments and divisions within the company aim to provide integration and achieve outstanding results. Additionally, establishing effective and supportive partnerships to achieve the full efficiency of your business and meet all your aspirations." => "We foster collaboration and coordination between the company's departments to deliver integrated, outstanding results, and we build effective, supportive partnerships so that your business operates at full efficiency and meets all your aspirations.",
		'Ease of Access to Us' => 'Accessibility',
		'Complete Commitment' => 'Total Commitment',
	),
	// Careers (English)
	29476 => array(
		'Thank you for your interest in working for Esnad Financial Company. We at Esnad always strive to employ distinguished cadres who have a high level of professional ambition and give them competitive salaries and distinctive annual allowances and bonuses. We are also committed to training and developing our employees through advanced programs to increase their knowledge, skills and productivity to build a promising future for themselves and the company.' => 'Thank you for your interest in working with Esnad Advisory Company. At Esnad we strive to hire distinguished professionals with high ambition and to offer them competitive salaries and annual allowances and bonuses. We are also committed to training and developing our employees through advanced programs that increase their knowledge, skills and productivity and build a promising future for them and for the company.',
		'Please upload your updated CV here for future vacancies.' => 'Send us your updated CV for future vacancies.',
		'Upload CV' => 'Send your CV',
		'Search jobs' => '',
		'Career' => '', // the page title already shows it
		'Careers' => '',
	),
);

global $wpdb;
$report = array();
foreach ( $pages as $id => $map ) {
	$post = get_post( $id );
	if ( ! $post ) {
		$report[] = "$id missing";
		continue;
	}
	$n   = 0;
	$hit = array();
	$new = esnad_pe_apply( $post->post_content, $map, $n, $hit );
	foreach ( array_keys( $map ) as $k ) {
		if ( ! isset( $hit[ esnad_pe_norm( $k ) ] ) && false === strpos( $post->post_content, "\u{0}" ) && ! str_contains( esnad_pe_norm( $post->post_content ), esnad_pe_norm( $map[ $k ] ) ) ) {
			WP_CLI::log( "  not matched ($id): " . mb_substr( esnad_pe_norm( $k ), 0, 60 ) );
		}
	}
	if ( $write && $new !== $post->post_content ) {
		$wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $id ) );
		clean_post_cache( $id );
	}
	$report[] = "$id: $n of " . count( $map ) . ' replaced';
}

// Brand name: the English company name is "Esnad Advisory Company" everywhere (financial statements keep their own title).
$ids = $wpdb->get_col( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE language_code='en' AND element_type IN ('post_page','post_post')" );
$brand = 0;
foreach ( $ids as $eid ) {
	$post = get_post( (int) $eid );
	if ( ! $post || false === strpos( $post->post_content, 'Esnad Financial Company' ) ) {
		continue;
	}
	$new = str_replace( 'Esnad Financial Company', 'Esnad Advisory Company', $post->post_content );
	$brand++;
	if ( $write ) {
		$wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $post->ID ) );
		clean_post_cache( $post->ID );
	}
}
$report[] = "brand name fixed in $brand page(s)";
WP_CLI::log( ( $write ? 'WROTE ' : 'DRY RUN ' ) . implode( ' | ', $report ) );
