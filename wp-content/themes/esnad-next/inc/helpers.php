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
			$items .= '<li><img src="' . esc_url( $base . $logo ) . '" alt="" width="156" height="156" loading="lazy" decoding="async"></li>';
		}
		// The list is printed twice so the loop is seamless; the copy is hidden from assistive tech.
		$out .= '<div class="marquee__row' . ( $r ? ' is-reverse' : '' ) . '"><ul class="marquee__track">' . $items . '</ul><ul class="marquee__track" aria-hidden="true">' . $items . '</ul></div>';
	}
	return $out . '</div>';
}

/**
 * Clients as orbits: four concentric rings turn slowly around the company's emblem; each logo stays upright,
 * the rings pause on hover and the logo under the pointer grows. On phones a swipeable three-row strip replaces it.
 */
function esnad_clients_orbit(): string {
	$logos = esnad_json( 'clients.json' );
	if ( ! $logos ) {
		return '';
	}
	$base   = content_url( 'uploads/' );
	$total  = count( $logos );
	$plan   = array(
		array( 'r' => 18.0, 'n' => 9, 't' => 150, 'dir' => 'normal' ),
		array( 'r' => 28.2, 'n' => 14, 't' => 210, 'dir' => 'reverse' ),
		array( 'r' => 37.6, 'n' => 18, 't' => 270, 'dir' => 'normal' ),
		array( 'r' => 46.4, 'n' => 21, 't' => 330, 'dir' => 'reverse' ),
	);
	$item   = 7.0; // logo diameter as a share of the whole orbit
	$i      = 0;
	$rings  = '';
	foreach ( $plan as $k => $ring ) {
		$n = ( count( $plan ) - 1 === $k ) ? max( 0, $total - $i ) : min( $ring['n'], $total - $i );
		if ( $n <= 0 ) {
			break;
		}
		$size  = round( $item / ( 2 * $ring['r'] ) * 100, 3 );
		$items = '';
		for ( $j = 0; $j < $n; $j++ ) {
			$a      = 2 * M_PI * $j / $n - M_PI / 2 + $k * 0.35;
			$left   = round( 50 + 50 * cos( $a ), 3 );
			$top    = round( 50 + 50 * sin( $a ), 3 );
			$items .= '<li style="left:' . $left . '%;top:' . $top . '%;width:' . $size . '%"><span><img src="' . esc_url( $base . $logos[ $i ] ) . '" alt="" width="120" height="120" loading="lazy" decoding="async"></span></li>';
			++$i;
		}
		$rings .= '<ol class="ring" style="left:' . round( 50 - $ring['r'], 3 ) . '%;top:' . round( 50 - $ring['r'], 3 ) . '%;width:' . round( 2 * $ring['r'], 3 ) . '%;--t:' . (int) $ring['t'] . 's;--dir:' . $ring['dir'] . ';--idir:' . ( 'normal' === $ring['dir'] ? 'reverse' : 'normal' ) . '">' . $items . '</ol>';
	}
	$strip = '';
	foreach ( $logos as $logo ) {
		$strip .= '<li><img src="' . esc_url( $base . $logo ) . '" alt="" width="96" height="96" loading="lazy" decoding="async"></li>';
	}
	$label = esc_attr( esnad_t( 'شعارات عملائنا', 'Our clients\' logos' ) );
	return '<div class="orbit reveal" role="group" aria-label="' . $label . '"><div class="orbit__core">' . esnad_emblem( 'emblem-ghost.svg', 'orbit__emblem' ) . '<strong>' . esc_html( esnad_t( 'عملاؤنا', 'Our clients' ) ) . '</strong></div>' . $rings . '</div><ul class="orbit-swipe" aria-label="' . $label . '">' . $strip . '</ul>';
}

/**
 * Clients as an octagon mosaic (the logo's own shape). Large tiles, all visible; on a pointer device the
 * tiles near the cursor come into colour and grow like a lens (progressive enhancement in main.js).
 * Phones get the swipe strip. Tiles that have neighbours below and beside carry a diamond where four meet.
 */
function esnad_clients_mosaic(): string {
	$logos = esnad_json( 'clients.json' );
	if ( ! $logos ) {
		return '';
	}
	$base  = content_url( 'uploads/' );
	$total = count( $logos );
	$items = '';
	foreach ( $logos as $i => $logo ) {
		$cls = '';
		foreach ( array( 8, 6 ) as $cols ) {
			if ( ( $i + 1 ) % $cols && $i + $cols + 1 < $total ) {
				$cls .= ' d' . $cols;
			}
		}
		$items .= '<li class="t' . $cls . '" style="--i:' . (int) $i . '"><span class="o"><span class="n"><img src="' . esc_url( $base . $logo ) . '" alt="" width="160" height="160" loading="lazy" decoding="async"></span></span></li>';
	}
	$strip = '';
	foreach ( $logos as $logo ) {
		$strip .= '<li><img src="' . esc_url( $base . $logo ) . '" alt="" width="96" height="96" loading="lazy" decoding="async"></li>';
	}
	$label = esc_attr( esnad_t( 'شعارات عملائنا', 'Our clients\' logos' ) );
	return '<ul class="mosaic reveal" data-mosaic aria-label="' . $label . '">' . $items . '</ul><ul class="orbit-swipe" aria-label="' . $label . '">' . $strip . '</ul>';
}

/** Inline emblem (the abstract mark built from the logo's four pieces); unique mask id per use. */
function esnad_emblem( string $file = 'emblem-ghost.svg', string $class = '' ): string {
	static $n = 0;
	$path = get_template_directory() . '/assets/img/' . $file;
	if ( ! file_exists( $path ) ) {
		return '';
	}
	$svg = (string) file_get_contents( $path );
	++$n;
	$svg = str_replace( array( 'id="h"', 'url(#h)', ' role="img"' ), array( 'id="h' . $n . '"', 'url(#h' . $n . ')', ' aria-hidden="true" focusable="false"' ), $svg );
	return '<span class="emblem ' . esc_attr( $class ) . '" aria-hidden="true">' . $svg . '</span>';
}

/** Section title with one gold keyword: the text is escaped, then the keyword is wrapped in <em>. */
function esnad_gold( string $text, string $word ): string {
	$html = esc_html( $text );
	$kw   = esc_html( $word );
	return '' !== $kw && false !== strpos( $html, $kw ) ? preg_replace( '/' . preg_quote( $kw, '/' ) . '/u', '<em>' . $kw . '</em>', $html, 1 ) : $html;
}

/** Every client logo in a plain, static grid (no motion): used inside page content. */
function esnad_clients_grid(): string {
	$logos = esnad_json( 'clients.json' );
	if ( ! $logos ) {
		return '';
	}
	$base  = content_url( 'uploads/' );
	$items = '';
	foreach ( $logos as $logo ) {
		$items .= '<li><img src="' . esc_url( $base . $logo ) . '" alt="" width="140" height="140" loading="lazy" decoding="async"></li>';
	}
	return '<ul class="client-grid" aria-label="' . esc_attr( esnad_t( 'شعارات عملائنا', 'Our clients\' logos' ) ) . '">' . $items . '</ul>';
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
			// Original illustration per service (theme asset); the old uploaded photo is only a fallback.
			'img'   => file_exists( get_template_directory() . '/assets/img/services/' . $key . '.svg' )
				? get_template_directory_uri() . '/assets/img/services/' . $key . '.svg?v=' . filemtime( get_template_directory() . '/assets/img/services/' . $key . '.svg' )
				: content_url( 'uploads/' . $row['img'] ),
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
		$services_children[] = array( 'label' => $svc['title'], 'url' => $svc['url'], 'text' => wp_trim_words( (string) $svc['text'], 11, '…' ) );
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
		array( 'label' => esnad_t( 'الإفصاح والمعلومات التنظيمية', 'Regulatory Information' ), 'url' => esnad_url( 'disclosure' ), 'text' => esnad_t( 'المعلومات التنظيمية والإفصاحات والتقارير الخاصة بالشركة.', 'Regulatory information, disclosures and reports of the company.' ) ),
		array( 'label' => esnad_t( 'المقالات', 'Insights' ), 'url' => esnad_url( 'blog' ), 'text' => esnad_t( 'مقالات حول الحوكمة والمخاطر والالتزام والخدمات المالية.', 'Articles on governance, risk, compliance and financial services.' ) ),
		array( 'label' => esnad_t( 'اتصل بنا', 'Contact' ), 'url' => esnad_url( 'contact' ), 'text' => esnad_t( 'تواصل مع فريق الشركة أو اطلب استشارة.', 'Get in touch with our team or request a consultation.' ) ),
	);
}

/** URL of the current page in the other language. */
function esnad_switch_url(): string {
	$other = esnad_is_rtl() ? 'en' : 'ar';
	if ( has_filter( 'wpml_object_id' ) && is_singular() ) {
		$id = apply_filters( 'wpml_object_id', get_the_ID(), get_post_type(), false, $other );
		if ( $id ) {
			return (string) apply_filters( 'wpml_permalink', get_permalink( $id ), $other ); // WPML rewrites get_permalink() to the current language; name the target explicitly
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
		'Esnad Advisory Company is licensed by the Capital Market Authority to provide advisory services in securities business and arranging. It is not licensed to trade in shares or forex. Please be aware that some individuals impersonate Esnad Advisory Company; beware of them.',
		'Esnad Advisory Company does not collect funds through social media. Subscriptions to the investment opportunities offered are made only through the Esnad Advisory website.',
	);
}

/** Inline SVG icon from the theme sprite. */
function esnad_icon( string $name, int $size = 24 ): string {
	return sprintf( '<svg class="i" width="%1$d" height="%1$d" aria-hidden="true" focusable="false"><use href="%2$s#%3$s"/></svg>', $size, esc_url( get_template_directory_uri() . '/assets/img/sprite.svg?v=' . filemtime( get_template_directory() . '/assets/img/sprite.svg' ) ), esc_attr( $name ) );
}
