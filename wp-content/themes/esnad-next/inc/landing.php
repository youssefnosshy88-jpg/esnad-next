<?php
/**
 * Content restructuring for pages migrated from the old builder (landing, investment, article-style pages).
 * Loaded through a guarded include in functions.php, so a failure here can never break the site.
 *
 * The wording is never changed; only wrappers, lists, ordering and groups of facts.
 *  - landing mode: pages that contain the client logo grid and the Esnad form.
 *  - article mode: other long pages (three or more H2) that are not handled by a dedicated template.
 */

defined( 'ABSPATH' ) || exit;

/** Keep the form markup out of the DOM round trip. */
function esnad_lp_extract_form( string $html, ?string &$form ): string {
	$form = null;
	if ( preg_match( '#<form\b[^>]*class="[^"]*esnad-form[^"]*".*?</form>#s', $html, $m ) ) {
		$form = $m[0];
		return str_replace( $form, '<div data-lp-form="1"></div>', $html );
	}
	return $html;
}

function esnad_lp_text( DOMNode $n ): string {
	return trim( (string) preg_replace( '/\s+/u', ' ', $n->textContent ) );
}

/** Children of the root without whitespace-only text nodes. */
function esnad_lp_kids( DOMNode $root ): array {
	$out = array();
	foreach ( iterator_to_array( $root->childNodes ) as $n ) {
		if ( $n instanceof DOMText && '' === trim( $n->textContent ) ) {
			continue;
		}
		$out[] = $n;
	}
	return $out;
}

function esnad_lp_is( DOMNode $n, string $tag ): bool {
	return $n instanceof DOMElement && $tag === $n->tagName;
}

/** Replace a run of nodes with one new node. */
function esnad_lp_replace_run( array $run, DOMNode $with ): void {
	$run[0]->parentNode->insertBefore( $with, $run[0] );
	foreach ( $run as $n ) {
		$n->parentNode->removeChild( $n );
	}
}

/** "label + short value" pairs become fact tiles; "heading + sentence" runs become cards. */
function esnad_lp_pairs( DOMDocument $dom, DOMElement $root ): void {
	$kids = esnad_lp_kids( $root );
	$run  = array(); // list of [h2, value]
	$flush = static function () use ( &$run, $dom ): void {
		if ( count( $run ) >= 2 ) {
			$maxv = 0;
			foreach ( $run as $pair ) {
				$maxv = max( $maxv, mb_strlen( esnad_lp_text( $pair[1] ) ) );
			}
			$box = $dom->createElement( 'div' );
			$facts = $maxv <= 46;
			$box->setAttribute( 'class', $facts ? 'lp-facts' : 'lp-cards' );
			foreach ( $run as $pair ) {
				$item = $dom->createElement( 'div' );
				$item->setAttribute( 'class', $facts ? 'lp-fact' : 'lp-card' );
				$label = (string) preg_replace( '/[\s:\x{FF1A}]+$/u', '', esnad_lp_text( $pair[0] ) );
				if ( $facts ) {
					$k = $dom->createElement( 'span' );
					$k->setAttribute( 'class', 'lp-fact__k' );
					$k->appendChild( $dom->createTextNode( $label ) );
					$v = $dom->createElement( 'strong' );
					$v->setAttribute( 'class', 'lp-fact__v' );
					$v->appendChild( $dom->createTextNode( esnad_lp_text( $pair[1] ) ) );
					$item->appendChild( $k );
					$item->appendChild( $v );
				} else {
					$h = $dom->createElement( 'h3' );
					$h->appendChild( $dom->createTextNode( $label ) );
					$item->appendChild( $h );
					$p = $dom->createElement( 'p' );
					while ( $pair[1]->firstChild ) {
						$p->appendChild( $pair[1]->firstChild );
					}
					$item->appendChild( $p );
				}
				$box->appendChild( $item );
			}
			$nodes = array();
			foreach ( $run as $pair ) {
				$nodes[] = $pair[0];
				$nodes[] = $pair[1];
			}
			esnad_lp_replace_run( $nodes, $box );
		}
		$run = array();
	};
	$count = count( $kids );
	for ( $i = 0; $i < $count; $i++ ) {
		$n = $kids[ $i ];
		$v = $kids[ $i + 1 ] ?? null;
		$ok = esnad_lp_is( $n, 'h2' ) && $v instanceof DOMElement && in_array( $v->tagName, array( 'p', 'div' ), true )
			&& mb_strlen( esnad_lp_text( $n ) ) <= 48
			&& '' !== esnad_lp_text( $v )
			&& mb_strlen( esnad_lp_text( $v ) ) <= 220
			&& 0 === $v->getElementsByTagName( 'img' )->length
			&& 0 === $v->getElementsByTagName( 'a' )->length
			&& ! preg_match( '/^[\x{2013}\x{2014}\-\x{2022}]/u', esnad_lp_text( $v ) );
		if ( $ok ) {
			// A tile run and a card run are different things: start a new group when the kind changes.
			$short = mb_strlen( esnad_lp_text( $v ) ) <= 46;
			if ( $run && ( mb_strlen( esnad_lp_text( $run[0][1] ) ) <= 46 ) !== $short ) {
				$flush();
			}
			$run[] = array( $n, $v );
			$i++;
		} else {
			$flush();
		}
	}
	$flush();
}

/** Paragraphs that start with a dash or bullet become a list (marker removed). */
function esnad_lp_dash_lists( DOMDocument $dom, DOMElement $root ): void {
	$run = array();
	$flush = static function () use ( &$run, $dom ): void {
		if ( count( $run ) >= 2 ) {
			$ul = $dom->createElement( 'ul' );
			$ul->setAttribute( 'class', 'lp-list' );
			foreach ( $run as $p ) {
				$li = $dom->createElement( 'li' );
				while ( $p->firstChild ) {
					$li->appendChild( $p->firstChild );
				}
				if ( $li->firstChild instanceof DOMText ) {
					$li->firstChild->nodeValue = preg_replace( '/^[\s\x{2013}\x{2014}\-\x{2022}]+/u', '', (string) $li->firstChild->nodeValue );
				}
				$ul->appendChild( $li );
			}
			esnad_lp_replace_run( $run, $ul );
		}
		$run = array();
	};
	foreach ( esnad_lp_kids( $root ) as $n ) {
		if ( esnad_lp_is( $n, 'p' ) && preg_match( '/^[\x{2013}\x{2014}\-\x{2022}]\s*\S/u', esnad_lp_text( $n ) ) ) {
			$run[] = $n;
		} else {
			$flush();
		}
	}
	$flush();
}

/** Runs of three or more short plain paragraphs become a list. */
function esnad_lp_short_lists( DOMDocument $dom, DOMElement $root ): void {
	$run = array();
	$flush = static function () use ( &$run, $dom ): void {
		if ( count( $run ) >= 3 ) {
			$ul = $dom->createElement( 'ul' );
			$ul->setAttribute( 'class', 'lp-list' );
			foreach ( $run as $p ) {
				$li = $dom->createElement( 'li' );
				while ( $p->firstChild ) {
					$li->appendChild( $p->firstChild );
				}
				$ul->appendChild( $li );
			}
			esnad_lp_replace_run( $run, $ul );
		}
		$run = array();
	};
	foreach ( esnad_lp_kids( $root ) as $n ) {
		$short = esnad_lp_is( $n, 'p' ) && '' !== esnad_lp_text( $n ) && mb_strlen( esnad_lp_text( $n ) ) <= 125 && 0 === $n->getElementsByTagName( 'img' )->length;
		if ( $short ) {
			$run[] = $n;
		} else {
			$flush();
		}
	}
	$flush();
}

function esnad_restructure( string $html, string $mode ): string {
	$form = null;
	$work = esnad_lp_extract_form( $html, $form );

	$dom = new DOMDocument();
	libxml_use_internal_errors( true );
	$dom->loadHTML( '<?xml encoding="utf-8" ?><div id="lp-root">' . $work . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$xp   = new DOMXPath( $dom );
	$list = $xp->query( '//div[@id="lp-root"]' );
	if ( ! $list || 0 === $list->length ) {
		return $html;
	}
	$root = $list->item( 0 );

	// 1. Any figure before the first heading is the old logo; the picture after the intro is kept.
	foreach ( esnad_lp_kids( $root ) as $n ) {
		if ( esnad_lp_is( $n, 'h2' ) ) {
			break;
		}
		if ( esnad_lp_is( $n, 'figure' ) ) {
			$root->removeChild( $n );
		}
	}
	foreach ( iterator_to_array( $xp->query( '//figure[.//img[contains(@src,"avicon")]]' ) ) as $fig ) {
		$fig->parentNode->removeChild( $fig );
	}

	if ( 'thanks' === $mode ) {
		foreach ( iterator_to_array( $xp->query( '//figure' ) ) as $fig ) {
			$fig->parentNode->removeChild( $fig );
		}
	}

	// A list made only of in-page anchors (the old section menu) points at sections that no longer exist.
	foreach ( iterator_to_array( $xp->query( '//ul[.//a]' ) ) as $ul ) {
		$all_anchor = true;
		foreach ( $xp->query( './/a', $ul ) as $a ) {
			$h = trim( (string) $a->getAttribute( 'href' ) );
			if ( '' !== $h && '#' !== $h[0] ) {
				$all_anchor = false;
			}
		}
		if ( $all_anchor && $ul->parentNode ) {
			$ul->parentNode->removeChild( $ul );
		}
	}

	// 2. Scattered buttons become one action row.
	$actions = array();
	foreach ( iterator_to_array( $xp->query( '//*[contains(@class,"wp-block-buttons")]' ) ) as $wrap ) {
		foreach ( $xp->query( './/a', $wrap ) as $a ) {
			$href = trim( (string) $a->getAttribute( 'href' ) );
			if ( '' !== $href && '#' !== $href[0] && ! isset( $actions[ $href ] ) ) {
				$actions[ $href ] = esnad_lp_text( $a );
			}
		}
		$wrap->parentNode->removeChild( $wrap );
	}

	// 3. Structure inside the flow. A heading directly followed by another heading is a small label (eyebrow).
	if ( 'article' === $mode ) {
		$kk = esnad_lp_kids( $root );
		foreach ( $kk as $i => $n ) {
			if ( esnad_lp_is( $n, 'h2' ) && isset( $kk[ $i + 1 ] ) && esnad_lp_is( $kk[ $i + 1 ], 'h2' ) && mb_strlen( esnad_lp_text( $n ) ) <= 40 ) {
				$e = $dom->createElement( 'p' );
				$e->setAttribute( 'class', 'lp-eyebrow' );
				$e->appendChild( $dom->createTextNode( rtrim( esnad_lp_text( $n ), ':' ) ) );
				$root->replaceChild( $e, $n );
			}
		}
		esnad_lp_pairs( $dom, $root );
	}
	esnad_lp_dash_lists( $dom, $root );
	esnad_lp_short_lists( $dom, $root );

	// 4. Sections: one per H2.
	$sections = array();
	$cur      = null;
	foreach ( esnad_lp_kids( $root ) as $n ) {
		$is_eyebrow = esnad_lp_is( $n, 'p' ) && 'lp-eyebrow' === $n->getAttribute( 'class' );
		$after_eyebrow = $cur && $cur->lastChild instanceof DOMElement && 'lp-eyebrow' === $cur->lastChild->getAttribute( 'class' );
		if ( $is_eyebrow || ( esnad_lp_is( $n, 'h2' ) && ! $after_eyebrow ) || null === $cur ) {
			$cur = $dom->createElement( 'section' );
			$cur->setAttribute( 'class', 'lp-sec' );
			$sections[] = $cur;
		}
		$cur->appendChild( $n );
	}
	if ( ! $sections ) {
		return $html;
	}

	$lead = null;
	$wall = array();
	$cta  = null;
	$rest = array();
	$tail = array();
	foreach ( $sections as $s ) {
		$inner    = $dom->saveHTML( $s );
		$has_form = false !== strpos( $inner, 'data-lp-form' );
		$has_grid = false !== strpos( $inner, 'client-grid' );
		if ( $has_form ) {
			$s->setAttribute( 'class', 'lp-sec lp-cta' );
			$cta = $s;
		} elseif ( $has_grid ) {
			$s->setAttribute( 'class', 'lp-sec lp-partners' );
			$seen = false;
			foreach ( iterator_to_array( $s->childNodes ) as $c ) {
				if ( $seen ) {
					$tail[] = $c;
				} elseif ( $c instanceof DOMElement && 'ul' === $c->tagName && false !== strpos( (string) $c->getAttribute( 'class' ), 'client-grid' ) ) {
					$seen = true;
				}
			}
			$wall[] = $s;
		} else {
			$rest[] = $s;
		}
	}
	if ( $rest ) {
		$rest[0]->setAttribute( 'class', 'landing' === $mode ? 'lp-sec lp-hero' : 'lp-sec lp-lead' );
		$lead = $rest[0];
	}
	$target = $lead ?? ( $wall[0] ?? null );
	foreach ( $tail as $c ) {
		if ( $target ) {
			$target->appendChild( $c );
		}
	}

	// 5. The action row goes after the first paragraph of the lead section.
	if ( $actions && $target ) {
		$row = $dom->createElement( 'div' );
		$row->setAttribute( 'class', 'lp-actions' );
		$k = 0;
		foreach ( $actions as $href => $label ) {
			$a = $dom->createElement( 'a' );
			$a->setAttribute( 'href', $href );
			$a->setAttribute( 'class', 0 === $k ? 'btn btn--gold btn--sm' : 'btn btn--line btn--sm' );
			$a->appendChild( $dom->createTextNode( $label ) );
			$row->appendChild( $a );
			$k++;
		}
		$firstp = null;
		foreach ( $target->childNodes as $c ) {
			if ( $c instanceof DOMElement && 'p' === $c->tagName ) {
				$firstp = $c;
				break;
			}
		}
		if ( 'thanks' === $mode ) {
			// The buttons close the message instead of splitting it.
			$last_h2 = null;
			foreach ( $rest as $s ) {
				foreach ( $s->getElementsByTagName( 'h2' ) as $h ) {
					$last_h2 = $h;
				}
			}
			if ( $last_h2 && $last_h2->parentNode ) {
				$last_h2->parentNode->insertBefore( $row, $last_h2->nextSibling );
			} else {
				$target->appendChild( $row );
			}
		} elseif ( $firstp && $firstp->nextSibling ) {
			$target->insertBefore( $row, $firstp->nextSibling );
		} else {
			$target->appendChild( $row );
		}
	}

	while ( $root->firstChild ) {
		$root->removeChild( $root->firstChild );
	}
	foreach ( array_merge( $rest, $wall ) as $s ) {
		$root->appendChild( $s );
	}
	if ( $cta ) {
		$root->appendChild( $cta );
	}

	$out = '';
	foreach ( $root->childNodes as $n ) {
		$out .= $dom->saveHTML( $n );
	}
	if ( null !== $form ) {
		$out = str_replace( '<div data-lp-form="1"></div>', $form, $out );
	}
	return '<div class="lp lp--' . $mode . '">' . $out . '</div>';
}

/** Which restructuring (if any) applies to this page content. */
function esnad_lp_mode( string $html ): string {
	if ( preg_match( '/شكر|thank/iu', (string) get_the_title() ) ) {
		return 'thanks';
	}
	if ( false !== strpos( $html, 'client-grid' ) && false !== strpos( $html, 'esnad-form' ) ) {
		return 'landing';
	}
	if ( function_exists( 'esnad_is_service_page' ) && esnad_is_service_page() ) {
		return '';
	}
	foreach ( array( 'class="vm', 'class="people', 'year-grid', 'svlist' ) as $skip ) {
		if ( false !== strpos( $html, $skip ) ) {
			return '';
		}
	}
	return substr_count( $html, '<h2' ) >= 3 ? 'article' : '';
}

add_filter( 'the_content', static function ( string $html ): string {
	if ( ! is_singular( 'page' ) || ! in_the_loop() || ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}
	$mode = esnad_lp_mode( $html );
	if ( '' === $mode ) {
		return $html;
	}
	try {
		return esnad_restructure( $html, $mode );
	} catch ( Throwable $e ) {
		return $html;
	}
}, 27 );
