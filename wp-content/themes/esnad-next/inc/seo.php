<?php
/**
 * Structured data and machine-readable summaries (search engines and AI crawlers).
 * Rank Math keeps ownership of titles, descriptions, canonical, hreflang and the XML sitemap;
 * this file only adds the Organization/FinancialService graph, FAQ data and /llms.txt.
 */

defined( 'ABSPATH' ) || exit;

function esnad_org_schema(): array {
	$c = esnad_company();
	return array(
		'@type'          => array( 'Organization', 'FinancialService' ),
		'@id'            => home_url( '/#organization' ),
		'name'           => $c['name_ar'],
		'alternateName'  => $c['name_en'],
		'url'            => home_url( '/' ),
		'telephone'      => $c['phone_tel'],
		'email'          => $c['email'],
		'address'        => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $c['street_en'],
			'addressLocality' => $c['city_en'],
			'addressCountry'  => 'SA',
		),
		'areaServed'     => 'SA',
		'identifier'     => array(
			array( '@type' => 'PropertyValue', 'name' => 'Capital Market Authority license', 'value' => $c['license'] ),
			array( '@type' => 'PropertyValue', 'name' => 'Commercial registration', 'value' => $c['cr'] ),
			array( '@type' => 'PropertyValue', 'name' => 'Unified number', 'value' => $c['unified'] ),
			array( '@type' => 'PropertyValue', 'name' => 'VAT number', 'value' => $c['vat'] ),
		),
		'logo'           => get_template_directory_uri() . '/assets/img/logo-ar.png',
	);
}

/** FAQ entries: every answer restates facts already published on the site. */
function esnad_faq(): array {
	$c   = esnad_company();
	$n   = esnad_notice();
	$svc = implode( esnad_t( '، ', ', ' ), wp_list_pluck( esnad_services(), 'title' ) );
	return array(
		array(
			esnad_t( 'هل شركة إسناد المالية مرخصة؟', 'Is Esnad Financial licensed?' ),
			esnad_t(
				'نعم. حصلت الشركة على ترخيص هيئة السوق المالية رقم (' . $c['license'] . ') بتاريخ 1441/1/11هـ الموافق 2019/9/1م لتقديم خدمات الترتيب والمشورة في أعمال الأوراق المالية.',
				'Yes. The company obtained Capital Market Authority License No. ' . $c['license'] . ' on 11/01/1441 AH (01/09/2019 AD) to provide arranging and advisory services in securities business.'
			),
		),
		array(
			esnad_t( 'هل تقدم إسناد خدمات التداول في الأسهم أو الفوركس؟', 'Does Esnad offer share or forex trading?' ),
			$n[0],
		),
		array(
			esnad_t( 'هل تجمع إسناد الأموال عبر مواقع التواصل الاجتماعي؟', 'Does Esnad collect funds through social media?' ),
			$n[1],
		),
		array(
			esnad_t( 'ما الخدمات التي تقدمها الشركة؟', 'Which services does the company provide?' ),
			$svc . '.',
		),
		array(
			esnad_t( 'أين يقع مقر الشركة وكيف أتواصل معها؟', 'Where is the company based and how can I contact it?' ),
			esnad_t(
				'المقر في الرياض - ' . $c['street_ar'] . '. الهاتف: ' . $c['phone'] . '. البريد الإلكتروني: ' . $c['email'] . '.',
				'Headquarters: ' . $c['city_en'] . ', ' . $c['street_en'] . '. Phone: ' . $c['phone'] . '. Email: ' . $c['email'] . '.'
			),
		),
	);
}

add_action( 'wp_head', static function (): void {
	$graph = array( esnad_org_schema() );
	if ( is_front_page() ) {
		$graph[] = array(
			'@type'      => 'WebSite',
			'@id'        => home_url( '/#website' ),
			'url'        => home_url( '/' ),
			'name'       => esnad_t( 'شركة إسناد المالية', 'Esnad Advisory Company' ),
			'inLanguage' => esnad_is_rtl() ? 'ar' : 'en',
			'publisher'  => array( '@id' => home_url( '/#organization' ) ),
		);
		$items = array();
		foreach ( esnad_faq() as $qa ) {
			$items[] = array( '@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $qa[1] ) );
		}
		$graph[] = array( '@type' => 'FAQPage', 'mainEntity' => $items );
	}
	if ( is_singular( 'post' ) ) {
		$graph[] = array(
			'@type'         => 'Article',
			'headline'      => get_the_title(),
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'inLanguage'    => esnad_is_rtl() ? 'ar' : 'en',
			'mainEntityOfPage' => get_permalink(),
			'publisher'     => array( '@id' => home_url( '/#organization' ) ),
			'author'        => array( '@id' => home_url( '/#organization' ) ),
		);
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
}, 5 );

/** Serve /llms.txt: a plain-text map of the site for AI crawlers (replaces the host's placeholder file). */
add_action( 'init', static function (): void {
	if ( isset( $_SERVER['REQUEST_URI'] ) && '/llms.txt' === wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) ) {
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'X-Robots-Tag: noindex' );
		$c = esnad_company();
		echo "# {$c['name_en']} (شركة إسناد المالية)\n\n";
		echo "> Saudi financial advisory firm based in Riyadh, licensed by the Capital Market Authority (License No. {$c['license']}) to provide arranging and advisory services in securities business. Not licensed for share trading or forex.\n\n";
		echo "## Facts\n- Commercial registration: {$c['cr']}\n- Unified number: {$c['unified']}\n- VAT number: {$c['vat']}\n- Address: {$c['street_en']}, {$c['city_en']}\n- Phone: {$c['phone']}\n- Email: {$c['email']}\n\n## Services\n";
		foreach ( esnad_services() as $svc ) {
			echo '- [' . $svc['title'] . '](' . $svc['url'] . ")\n";
		}
		echo "\n## Key pages\n- [" . 'About' . '](' . esnad_url( 'about' ) . ")\n- [Regulatory disclosures](" . esnad_url( 'disclosure' ) . ")\n- [Contact](" . esnad_url( 'contact' ) . ")\n";
		exit;
	}
} );
