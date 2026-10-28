<?php
/**
 * Language, company facts, services and navigation.
 * Every fact below was taken from the existing website; do not edit regulatory wording without compliance review.
 */

defined( 'ABSPATH' ) || exit;

/** Current language: 'ar' (default) or 'en'. Uses WPML when active, otherwise the /en/ URL prefix. */
function esnad_lang(): string {
	static $lang = null;
	if ( null !== $lang ) {
		return $lang;
	}
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		$lang = 'en' === ICL_LANGUAGE_CODE ? 'en' : 'ar';
	} else {
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
		$lang = ( is_string( $path ) && 0 === strpos( $path, '/en/' ) || '/en' === $path ) ? 'en' : 'ar';
	}
	return $lang;
}

function esnad_is_rtl(): bool {
	return 'ar' === esnad_lang();
}

/** Pick the string for the current language. */
function esnad_t( string $ar, string $en ): string {
	return esnad_is_rtl() ? $ar : $en;
}

/** Read a JSON data file shipped with the theme (tolerates a UTF-8 BOM). */
function esnad_json( string $name ): array {
	$file = get_template_directory() . '/inc/' . $name;
	if ( ! is_readable( $file ) ) {
		return array();
	}
	$raw  = (string) file_get_contents( $file );
	$data = json_decode( ltrim( $raw, "\xEF\xBB\xBF" ), true );
	return is_array( $data ) ? $data : array();
}

/** Official social channels, exactly as published on the current site (sticky social bar, header and contact page). */
function esnad_social(): array {
	return array(
		array( 'facebook', 'Facebook', 'https://www.facebook.com/EsnadAdvisory/' ),
		array( 'x', 'X', 'https://x.com/esnadadvisorysa' ),
		array( 'instagram', 'Instagram', 'https://www.instagram.com/esnad.advisory/' ),
		array( 'linkedin', 'LinkedIn', 'https://www.linkedin.com/company/esnad-advisory-company/' ),
		array( 'tiktok', 'TikTok', 'https://www.tiktok.com/@esnadadvisory' ),
		array( 'whatsapp', 'WhatsApp', 'https://wa.me/966920014522' ),
		array( 'pin', esnad_t( 'خرائط جوجل', 'Google Maps' ), 'https://goo.gl/maps/XwcpBDG8MbYUGenr7' ),
	);
}

function esnad_social_links( string $class = 'social' ): string {
	$out = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( esnad_social() as $s ) {
		$out .= '<li><a href="' . esc_url( $s[2] ) . '" target="_blank" rel="noopener noreferrer me" aria-label="' . esc_attr( $s[1] ) . '">' . esnad_icon( $s[0], 18 ) . '</a></li>';
	}
	return $out . '</ul>';
}

/** FATCA file shown at the bottom of the old homepage; one bilingual document per language version. */
function esnad_fatca(): array {
	return esnad_is_rtl()
		? array( 'قانون الامتثال الضريبي للحسابات الأجنبية إنجليزي – عربي', content_url( 'uploads/2024/06/قانون-الامتثال-الضريبي-للحسابات-الأجنبية.pdf' ) )
		: array( 'FOREIGN ACCOUNT TAX COMPLIANCE ACT: ENGLISH – ARABIC', content_url( 'uploads/2024/06/Foreign-Account-Tax-Compliance-Act.pdf' ) );
}

/**
 * Client logo wall: two opposing marquee rows (CSS only). Falls back to a static wrapped grid when the user
 * prefers reduced motion. Logos are the approved client logos already published on the current site.
 */
function esnad_clients_marquee(): string {
	$logos = esnad_json( 'clients.json' );
	if ( ! $logos ) {
		return '';
	}
	$base = content_url( 'uploads/' );
	$rows = array_chunk( $logos, (int) ceil( count( $logos ) / 2 ) );
	$out  = '<div class="marquee" aria-label="' . esc_attr( esnad_t( 'شعارات عملائنا', 'Our clients\' logos' ) ) . '">';
	foreach ( $rows as $r => $row ) {
		$items = '';
		foreach ( $row as $logo ) {
			$items .= '<li><img src="' . esc_url( $base . $logo ) . '" alt="" width="160" height="90" loading="lazy" decoding="async"></li>';
		}
		// The list is printed twice so the loop is seamless; the copy is hidden from assistive tech.
		$out .= '<div class="marquee__row' . ( $r ? ' is-reverse' : '' ) . '"><ul class="marquee__track">' . $items . '</ul><ul class="marquee__track" aria-hidden="true">' . $items . '</ul></div>';
	}
	return $out . '</div>';
}

/** Corporate facts (source: existing homepage, footer and contact page). */
function esnad_company(): array {
	return array(
		'name_ar'    => 'شركة إسناد المالية',
		'name_en'    => 'Esnad Advisory Company',
		'license'    => '20-19206',
		'cr'         => '1010575421',
		'unified'    => '7008921160',
		'vat'        => '310358527000003',
		'phone'      => '00966920014522',
		'phone_tel'  => '+966920014522',
		'email'      => 'info@esnadadvisory.sa',
		'street_ar'  => 'شارع الملك فيصل - حي المربع',
		'street_en'  => 'King Faisal Street, Al Murabba District',
		'city_ar'    => 'الرياض',
		'city_en'    => 'Riyadh',
		'lat'        => 24.6469,
		'lng'        => 46.7106,
	);
}

/** Western digits for the license number in both languages (the old site mixed Arabic-Indic digits). */
function esnad_license_label(): string {
	return esnad_t( 'ترخيص هيئة السوق المالية رقم ', 'CMA License No. ' ) . esnad_company()['license'];
}

/** The eight approved service pages, with the original Arabic/English wording. */
function esnad_services(): array {
	static $services = null;
	if ( null !== $services ) {
		return $services;
	}
	$data = esnad_json( 'services.json' );
	// Page IDs per language (WPML translation groups share the slug pattern).
	$ids = array(
		'governance'  => array( 'ar' => 32016, 'en' => 33791 ),
		'risk'        => array( 'ar' => 33605, 'en' => 34391 ),
		'funds'       => array( 'ar' => 33634, 'en' => 34752 ),
		'compliance'  => array( 'ar' => 33665, 'en' => 34012 ),
		'licensing'   => array( 'ar' => 33654, 'en' => 34559 ),
		'audit'       => array( 'ar' => 33616, 'en' => 33871 ),
		'arrangement' => array( 'ar' => 35021, 'en' => 35156 ),
		'feasibility' => array( 'ar' => 36159, 'en' => 36205 ),
	);
	$lang     = esnad_lang();
	$services = array();
	foreach ( (array) $data as $row ) {
		$key  = $row['key'];
		$page = $ids[ $key ][ $lang ] ?? 0;
		$url  = $page ? get_permalink( $page ) : '';
		$services[ $key ] = array(
			'key'   => $key,
			'title' => $row[ $lang ]['title'],
			'text'  => $row[ $lang ]['text'],
			'url'   => $url ? $url : home_url( $row[ $lang ]['url'] ),
			'img'   => content_url( 'uploads/' . $row['img'] ),
		);
	}
	return $services;
}

/** Page IDs for the main navigation, per language. */
function esnad_page_ids(): array {
	return esnad_is_rtl()
		? array( 'home' => 979, 'about' => 40905, 'board' => 17102, 'exec' => 16843, 'org' => 16852, 'disclosure' => 16854, 'careers' => 6180, 'blog' => 6346, 'contact' => 1483, 'services' => 36118, 'sanad' => 41494 )
		: array( 'home' => 29472, 'about' => 43104, 'board' => 29458, 'exec' => 29480, 'org' => 29468, 'disclosure' => 29478, 'careers' => 29476, 'blog' => 29470, 'contact' => 29484, 'services' => 36126, 'sanad' => 44835 );
}

function esnad_url( string $key ): string {
	$id  = esnad_page_ids()[ $key ] ?? 0;
	$url = $id ? get_permalink( $id ) : '';
	return $url ? $url : home_url( '/' );
}

/** Consultation page: a dedicated page created by esnad-core; falls back to the contact page. */
function esnad_consult_url(): string {
	$page = get_page_by_path( esnad_is_rtl() ? 'request-consultation' : 'request-consultation-en' );
	return $page ? get_permalink( $page ) : esnad_url( 'contact' );
}

function esnad_complaints_url(): string {
	$page = get_page_by_path( esnad_is_rtl() ? 'complaints' : 'complaints-en' );
	return $page ? get_permalink( $page ) : esnad_url( 'contact' );
}

function esnad_nav(): array {
	$s = esnad_services();
	$services_children = array();
	foreach ( $s as $svc ) {
		$services_children[] = array( 'label' => $svc['title'], 'url' => $svc['url'] );
	}
	return array(
		array( 'label' => esnad_t( 'عن الشركة', 'About' ), 'url' => esnad_url( 'about' ), 'children' => array(
			array( 'label' => esnad_t( 'الرؤية والرسالة والقيم', 'Vision, Mission & Values' ), 'url' => esnad_url( 'about' ) ),
			array( 'label' => esnad_t( 'مجلس الإدارة', 'Board of Directors' ), 'url' => esnad_url( 'board' ) ),
			array( 'label' => esnad_t( 'الإدارة التنفيذية', 'Executive Management' ), 'url' => esnad_url( 'exec' ) ),
			array( 'label' => esnad_t( 'الهيكل التنظيمي', 'Organization Chart' ), 'url' => esnad_url( 'org' ) ),
			array( 'label' => esnad_t( 'التوظيف', 'Careers' ), 'url' => esnad_url( 'careers' ) ),
		) ),
		array( 'label' => esnad_t( 'خدماتنا', 'Services' ), 'url' => esnad_url( 'services' ), 'children' => $services_children, 'mega' => true ),
		array( 'label' => esnad_t( 'الإفصاح والمعلومات التنظيمية', 'Regulatory Information' ), 'url' => esnad_url( 'disclosure' ) ),
		array( 'label' => esnad_t( 'المقالات', 'Insights' ), 'url' => esnad_url( 'blog' ) ),
		array( 'label' => esnad_t( 'اتصل بنا', 'Contact' ), 'url' => esnad_url( 'contact' ) ),
	);
}

/** URL of the current page in the other language. */
function esnad_switch_url(): string {
	$other = esnad_is_rtl() ? 'en' : 'ar';
	if ( has_filter( 'wpml_object_id' ) && is_singular() ) {
		$id = apply_filters( 'wpml_object_id', get_the_ID(), get_post_type(), false, $other );
		if ( $id ) {
			return (string) get_permalink( $id );
		}
	}
	if ( function_exists( 'icl_get_languages' ) ) {
		$langs = icl_get_languages( 'skip_missing=0' );
		if ( isset( $langs[ $other ]['url'] ) ) {
			return $langs[ $other ]['url'];
		}
	}
	return esnad_is_rtl() ? home_url( '/en/' ) : home_url( '/' );
}

/** Approved regulatory notice: Arabic wording is copied verbatim from the existing site. */
function esnad_notice(): array {
	if ( esnad_is_rtl() ) {
		return array(
			'شركة إسناد المالية شركة مرخصة من هيئة السوق المالية لتقديم أعمال المشورة في الأوراق المالية والترتيب وغير مرخصة بأعمال التداول في الأسهم أو أسواق الفوركس، ونود التنويه عن قيام بعض الأشخاص بإنتحال الصفة الإعتبارية لشركة إسناد المالية، ويرجى الحذر منهم.',
			'لاتقوم اسناد المالية بجمع الاموال عن طريق مواقع التواصل الاجتماعي ويتم الاكتتاب في الفرص الاستثمارية المطروحة من خلال موقع اسناد المالية فقط.',
		);
	}
	// English rendering of the same notice: pending compliance review.
	return array(
		'Esnad Financial Company is licensed by the Capital Market Authority to provide advisory services in securities business and arranging, and is not licensed to engage in share trading or forex markets. Please note that some persons impersonate the corporate identity of Esnad Financial Company; please beware of them.',
		'Esnad Financial does not collect funds through social media. Subscription to the investment opportunities offered is made through the Esnad Financial website only.',
	);
}

/** Inline SVG icon from the theme sprite. */
function esnad_icon( string $name, int $size = 24 ): string {
	return sprintf( '<svg class="i" width="%1$d" height="%1$d" aria-hidden="true" focusable="false"><use href="%2$s#%3$s"/></svg>', $size, esc_url( get_template_directory_uri() . '/assets/img/sprite.svg?v=' . filemtime( get_template_directory() . '/assets/img/sprite.svg' ) ), esc_attr( $name ) );
}
