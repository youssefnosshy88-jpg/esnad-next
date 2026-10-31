<?php
// Drops CSS rules that only style the retired dropdown navigation. Usage: php prune.php in.css out.css
$css  = file_get_contents( $argv[1] );
$dead = '/^\s*(?:html(?:\[[^\]]*\])?\s+|\.is-ltr\s+|\.has-hero\s+|body:not\(\.has-hero\)\s+|\.site-header(?:\.is-solid)?\s+)?(?:\.primary-nav|\.menu(?![-\w])|\.menu__item|\.sub(?![-\w])|\.sub__|\.sub-toggle|\.nav-toggle|\.has-sub|\.is-mega|\.primary-nav__tools)/';

function esnad_prune( string $css, string $dead, int &$removed ): string {
	$out = '';
	$len = strlen( $css );
	$i   = 0;
	while ( $i < $len ) {
		// Copy leading whitespace/comments.
		if ( preg_match( '/\G(\s+|\/\*.*?\*\/)/s', $css, $m, 0, $i ) ) {
			$out .= $m[0];
			$i   += strlen( $m[0] );
			continue;
		}
		$brace = strpos( $css, '{', $i );
		$semi  = strpos( $css, ';', $i );
		if ( false === $brace ) {
			$out .= substr( $css, $i );
			break;
		}
		if ( false !== $semi && $semi < $brace ) { // statement like @import / @charset
			$out .= substr( $css, $i, $semi - $i + 1 );
			$i    = $semi + 1;
			continue;
		}
		$head = substr( $css, $i, $brace - $i );
		// Find the matching close brace.
		$depth = 1;
		$j     = $brace + 1;
		$inStr = '';
		while ( $j < $len && $depth > 0 ) {
			$ch = $css[ $j ];
			if ( '' !== $inStr ) {
				if ( '\\' === $ch ) { $j++; } elseif ( $ch === $inStr ) { $inStr = ''; }
			} elseif ( '"' === $ch || "'" === $ch ) {
				$inStr = $ch;
			} elseif ( '{' === $ch ) {
				$depth++;
			} elseif ( '}' === $ch ) {
				$depth--;
			}
			$j++;
		}
		$body = substr( $css, $brace + 1, $j - $brace - 2 );
		if ( preg_match( '/^\s*@(media|supports)/i', $head ) ) {
			$inner = esnad_prune( $body, $dead, $removed );
			if ( '' === trim( $inner ) ) {
				$removed++;
			} else {
				$out .= $head . '{' . $inner . '}';
			}
		} elseif ( preg_match( '/^\s*@/', $head ) ) {
			$out .= $head . '{' . $body . '}';
		} else {
			$sels = preg_split( '/,(?![^(]*\))/', $head );
			$keep = array_filter( $sels, static fn( $s ) => ! preg_match( $dead, $s ) );
			if ( ! $keep ) {
				$removed++;
			} elseif ( count( $keep ) !== count( $sels ) ) {
				$removed++;
				$out .= implode( ',', $keep ) . '{' . $body . '}';
			} else {
				$out .= $head . '{' . $body . '}';
			}
		}
		$i = $j;
	}
	return $out;
}
$removed = 0;
$new     = esnad_prune( $css, $dead, $removed );
file_put_contents( $argv[2], $new );
echo "removed rules/selectors: $removed; bytes " . strlen( $css ) . ' -> ' . strlen( $new ) . "\n";
