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
			esnad_t( 'هل شركة إسناد المالية مرخصة؟', 'Is Esnad Advisory licensed?' ),
			esnad_t(
				'نعم. حصلت الشركة على ترخيص هيئة السوق المالية رقم (' . $c['license'] . ') بتاريخ 1441/1/11هـ الموافق 2019/9/1م لتقديم خدمات الترتيب والمشورة في أعمال الأوراق المالية.',
				'Yes. The company obtained Capital Market Authority License No. ' . $c['license'] . ' on 11/01/1441 AH (1 September 2019) to provide arranging and advisory services in securities business.'
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

/**
 * Meta description, Open Graph and Twitter cards.
 * Rank Math remains the source of truth: its per-page title/description fields are used here,
 * and this block only prints when Rank Math has not already printed its own head tags.
 */
function esnad_seo_vars( string $text, ?WP_Post $post ): string {
	$sitename = get_bloginfo( 'name' );
	$map      = array(
		'%title%'    => $post ? get_the_title( $post ) : $sitename,
		'%sitename%' => $sitename,
		'%sitedesc%' => get_bloginfo( 'description' ),
		'%sep%'      => '–',
		'%page%'     => '',
		'%excerpt%'  => $post ? wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 30, '' ) : '',
	);
	return trim( (string) preg_replace( '/\s+/', ' ', strtr( $text, $map ) ) );
}

function esnad_seo_description( ?WP_Post $post ): string {
	if ( $post ) {
		$custom = (string) get_post_meta( $post->ID, 'rank_math_description', true );
		if ( '' !== $custom ) {
			return esnad_seo_vars( $custom, $post );
		}
		$ex = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_strip_all_tags( strip_shortcodes( (string) $post->post_content ) );
		$ex = trim( (string) preg_replace( '/\s+/u', ' ', $ex ) );
		if ( '' !== $ex ) {
			return wp_trim_words( $ex, 32, '…' );
		}
	}
	return get_bloginfo( 'description' );
}

add_filter( 'pre_get_document_title', static function ( string $title ): string {
	if ( has_action( 'rank_math/head' ) || ! is_singular() ) {
		return $title;
	}
	$post   = get_queried_object();
	$custom = $post instanceof WP_Post ? (string) get_post_meta( $post->ID, 'rank_math_title', true ) : '';
	return '' !== $custom ? esnad_seo_vars( $custom, $post ) : $title;
} );

add_action( 'wp_head', static function (): void {
	if ( has_action( 'rank_math/head' ) ) {
		return;
	}
	$post = is_singular() ? get_queried_object() : null;
	$post = $post instanceof WP_Post ? $post : null;
	$desc = esnad_seo_description( $post );
	$url  = $post ? get_permalink( $post ) : home_url( add_query_arg( array() ) );
	$img  = $post && has_post_thumbnail( $post ) ? (string) get_the_post_thumbnail_url( $post, 'large' ) : get_template_directory_uri() . '/assets/img/logo-' . ( esnad_is_rtl() ? 'ar' : 'en' ) . '.png';
	// Social previews cannot use SVG; the old portrait banners are not used either, so the company logo stands in.
	if ( preg_match( '#/(dma_\d\d|c[1-5]\.png)#i', $img ) ) {
		$img = get_template_directory_uri() . '/assets/img/logo-' . ( esnad_is_rtl() ? 'ar' : 'en' ) . '.png';
	}
	$ttl  = wp_get_document_title();
	$tags = array(
		array( 'name', 'description', $desc ),
		array( 'property', 'og:type', is_singular( 'post' ) ? 'article' : 'website' ),
		array( 'property', 'og:title', $ttl ),
		array( 'property', 'og:description', $desc ),
		array( 'property', 'og:url', $url ),
		array( 'property', 'og:site_name', get_bloginfo( 'name' ) ),
		array( 'property', 'og:locale', esnad_is_rtl() ? 'ar_SA' : 'en_US' ),
		array( 'property', 'og:image', $img ),
		array( 'name', 'twitter:card', 'summary_large_image' ),
		array( 'name', 'twitter:title', $ttl ),
		array( 'name', 'twitter:description', $desc ),
		array( 'name', 'twitter:image', $img ),
	);
	foreach ( $tags as $t ) {
		if ( '' !== $t[2] ) {
			printf( '<meta %s="%s" content="%s">' . "\n", esc_attr( $t[0] ), esc_attr( $t[1] ), esc_attr( $t[2] ) );
		}
	}
}, 4 );

/** Same rule when Rank Math prints the social image (production): old portrait banners fall back to the logo. */
foreach ( array( 'rank_math/opengraph/facebook/image', 'rank_math/opengraph/twitter/image' ) as $esnad_og_hook ) {
	add_filter( $esnad_og_hook, static function ( $image ) {
		if ( is_string( $image ) && preg_match( '#/(dma_\d\d|c[1-5]\.png)#i', $image ) ) {
			return get_template_directory_uri() . '/assets/img/logo-' . ( esnad_is_rtl() ? 'ar' : 'en' ) . '.png';
		}
		return $image;
	} );
}

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
