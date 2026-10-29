<?php
/**
 * Organisation chart: the original picture (885 x 291) redrawn as accessible HTML.
 * Structure and names follow the original chart; the Arabic wording of the boxes is pending the company's approval.
 */

defined( 'ABSPATH' ) || exit;

function esnad_orgchart_html( string $original_img ): string {
	$box = static function ( string $ar, string $en, string $cls = '' ): string {
		$main = esnad_is_rtl() ? $ar : $en;
		$sub  = esnad_is_rtl() ? '<small lang="en" dir="ltr">' . esc_html( $en ) . '</small>' : '';
		return '<div class="org__node ' . esc_attr( $cls ) . '"><span>' . esc_html( $main ) . '</span>' . $sub . '</div>';
	};
	$units = array(
		array( 'التسويق', 'Marketing' ),
		array( 'المصرفية الاستثمارية', 'Investment Banking' ),
		array( 'المشورة والأبحاث', 'Research and Advisory' ),
		array( 'إدارة المشاريع', 'PMO' ),
		array( 'الرقابة الداخلية والوظائف المساندة', 'Internal Control and Support Functions' ),
		array( 'الخدمات المساندة', 'Shared Service' ),
		array( 'المالية', 'Accounting and Finance' ),
	);
	$out  = '<figure class="org" aria-label="' . esc_attr( esnad_t( 'الهيكل التنظيمي لشركة إسناد المالية', 'Organisation chart of Esnad Advisory Company' ) ) . '">';
	$out .= '<div class="org__top">';
	$out .= $box( 'مسؤول الالتزام ومكافحة غسل الأموال وتمويل الإرهاب', 'Compliance and AML Officer', 'org__side org__side--a' );
	$out .= $box( 'مجلس الإدارة', 'Board of Directors', 'org__board' );
	$out .= $box( 'المراجعة الداخلية', 'Internal Audit', 'org__side org__side--b' );
	$out .= '</div>';
	$out .= '<div class="org__ceo">' . $box( 'الرئيس التنفيذي', 'CEO', 'org__chief' ) . '</div>';
	$out .= '<ul class="org__units">';
	foreach ( $units as $u ) {
		$out .= '<li>' . $box( $u[0], $u[1] ) . '</li>';
	}
	$out .= '</ul>';
	if ( '' !== $original_img ) {
		$out .= '<figcaption><a href="' . esc_url( $original_img ) . '">' . esc_html( esnad_t( 'عرض الصورة الأصلية للهيكل', 'View the original chart image' ) ) . '</a></figcaption>';
	}
	return $out . '</figure>';
}

add_filter( 'the_content', static function ( string $html ): string {
	if ( ! is_singular( 'page' ) || ! in_the_loop() || ! in_array( get_the_ID(), array( 16852, 29468 ), true ) ) {
		return $html;
	}
	$img = '';
	if ( preg_match( '#<img[^>]+src="([^"]+)"#', $html, $m ) ) {
		$img = $m[1];
	}
	try {
		return esnad_orgchart_html( $img );
	} catch ( Throwable $e ) {
		return $html;
	}
}, 24 );
