<?php
/**
 * Single articles: table of contents, dash paragraphs as lists, reading time.
 * Loaded through a guarded include in functions.php; any failure falls back to the original content.
 */

defined( 'ABSPATH' ) || exit;

/** Estimated reading time in minutes (about 180 words per minute for Arabic and English). */
function esnad_reading_minutes( int $post_id ): int {
	$text  = wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', $post_id ) ) );
	$words = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
	return max( 1, (int) ceil( count( (array) $words ) / 180 ) );
}

function esnad_article_enhance( string $html ): string {
	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8" ?><div id="ar-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$xp   = new DOMXPath( $dom );
	$list = $xp->query( '//div[@id="ar-root"]' );
	if ( ! $list || 0 === $list->length ) {
		return $html;
	}
	$root = $list->item( 0 );

	// Paragraphs that start with a dash become a list (landing.php helper, loaded before this file).
	if ( function_exists( 'esnad_lp_dash_lists' ) ) {
		esnad_lp_dash_lists( $dom, $root );
	}

	// Widgets of the old theme left empty headings behind ("share", "related posts"): drop them when they carry no content.
	foreach ( iterator_to_array( $xp->query( '//div[@id="ar-root"]/h2' ) ) as $h ) {
		if ( ! preg_match( '/^\s*(شارك المقالة|منشورات ذات صلة|Share|Related posts?)/iu', (string) $h->textContent ) ) {
			continue;
		}
		$group = array( $h );
		$len   = 0;
		for ( $s = $h->nextSibling; $s && ! ( $s instanceof DOMElement && 'h2' === $s->tagName ); $s = $s->nextSibling ) {
			$group[] = $s;
			$len    += mb_strlen( trim( (string) $s->textContent ) );
		}
		if ( $len < 60 ) {
			foreach ( $group as $g ) {
				if ( $g->parentNode ) {
					$g->parentNode->removeChild( $g );
				}
			}
		}
	}

	// Table of contents from the H2 headings.
	$items = array();
	$n     = 0;
	foreach ( iterator_to_array( $xp->query( '//div[@id="ar-root"]/h2' ) ) as $h ) {
		$n++;
		$id = 'sec-' . $n;
		$h->setAttribute( 'id', $id );
		$items[] = array( $id, trim( (string) preg_replace( '/\s+/u', ' ', $h->textContent ) ) );
	}

	$out = '';
	foreach ( $root->childNodes as $c ) {
		$out .= $dom->saveHTML( $c );
	}
	if ( count( $items ) >= 3 && count( $items ) <= 18 ) {
		$toc = '<nav class="toc" aria-label="' . esc_attr( esnad_t( 'محتويات المقال', 'In this article' ) ) . '"><strong>' . esc_html( esnad_t( 'محتويات المقال', 'In this article' ) ) . '</strong><ol>';
		foreach ( $items as $it ) {
			$toc .= '<li><a href="#' . esc_attr( $it[0] ) . '">' . esc_html( $it[1] ) . '</a></li>';
		}
		$toc .= '</ol></nav>';
		$out  = $toc . $out;
	}
	return $out;
}

add_filter( 'the_content', static function ( string $html ): string {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}
	try {
		return esnad_article_enhance( $html );
	} catch ( Throwable $e ) {
		return $html;
	}
}, 28 );
