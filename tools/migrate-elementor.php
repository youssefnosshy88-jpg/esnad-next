<?php
/**
 * Converts Elementor page data into plain block markup so pages render without Elementor or its add-ons.
 *
 * Run with WP-CLI on the target site:
 *   wp eval-file tools/migrate-elementor.php                     # dry run, prints a report
 *   ESNAD_WRITE=1 wp eval-file tools/migrate-elementor.php       # writes post_content
 *
 * Safety: the original post_content is saved once in post meta `_esnad_pre_migration_content`, and
 * `_elementor_data` is never touched, so reverting is: re-activate Elementor + the old theme.
 * Only the words in the Elementor widgets are carried over; layout, animation and add-on widgets are dropped.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

$write = '1' === getenv( 'ESNAD_WRITE' );


/** Marker that precedes each emitted unit: kind + short hash of its content. */
function esnad_m_tag( string $kind, string $key ): string {
	return "\x01" . $kind . ':' . substr( md5( $key ), 0, 12 ) . ':' . ( mb_strlen( $key ) >= 12 || 'I' === $kind || 'B' === $kind || 'H' === $kind ? '1' : '0' ) . "\x02";
}

/**
 * Elementor pages usually ship a desktop and a mobile copy of each section. Drop repeated paragraphs, images and
 * buttons, and drop a heading when everything under it was a repeat. Markers are stripped from the result.
 */
function esnad_m_dedupe( string $raw ): string {
	if ( ! preg_match_all( '/\x01(\w):([0-9a-f]+):([01])\x02(.*?)(?=\x01|\z)/s', $raw, $m, PREG_SET_ORDER ) ) {
		return $raw;
	}
	$seen  = array();
	$units = array();
	foreach ( $m as $u ) {
		$kind = $u[1];
		$dup  = false;
		if ( 'H' !== $kind && '1' === $u[3] ) {
			$k   = $kind . $u[2];
			$dup = isset( $seen[ $k ] );
			$seen[ $k ] = true;
		}
		$units[] = array( 'kind' => $kind, 'dup' => $dup, 'html' => $u[4], 'key' => $u[2] );
	}
	// Headings: keep only if at least one unit below them (until the next heading) survives, or nothing follows.
	$n = count( $units );
	for ( $i = 0; $i < $n; $i++ ) {
		if ( 'H' !== $units[ $i ]['kind'] ) {
			continue;
		}
		$alive = 0;
		$total = 0;
		for ( $j = $i + 1; $j < $n && 'H' !== $units[ $j ]['kind']; $j++ ) {
			$total++;
			if ( ! $units[ $j ]['dup'] ) {
				$alive++;
			}
		}
		if ( $total > 0 && 0 === $alive ) {
			$units[ $i ]['dup'] = true;
		} elseif ( 0 === $total ) {
			// A heading with no body (e.g. a promo-box title) repeated for the mobile copy.
			$k = 'E' . $units[ $i ]['key'];
			if ( isset( $seen[ $k ] ) ) {
				$units[ $i ]['dup'] = true;
			}
			$seen[ $k ] = true;
		}
	}
	$out = '';
	foreach ( $units as $u ) {
		if ( ! $u['dup'] ) {
			$out .= $u['html'];
		}
	}
	return $out;
}
function esnad_m_text( $html ): string {
	return trim( (string) $html );
}

function esnad_m_plain( $html ): string {
	return trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( html_entity_decode( (string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ) );
}

function esnad_m_heading( string $text, int $level = 2 ): string {
	$GLOBALS['esnad_m_last_heading'] = $text;
	$level = max( 2, min( 4, $level ) );
	return esnad_m_tag( 'H', $text ) . "<!-- wp:heading {\"level\":$level} -->\n<h$level class=\"wp-block-heading\">" . esc_html( $text ) . "</h$level>\n<!-- /wp:heading -->\n\n";
}

function esnad_m_para_html( string $html ): string {
	$html = esnad_m_text( $html );
	if ( '' === esnad_m_plain( $html ) && false === strpos( $html, '<img' ) ) {
		return '';
	}
	// Classic block keeps arbitrary editor HTML (lists, links, tables) intact and editable.
	return esnad_m_tag( 'P', esnad_m_plain( $html ) ) . "<!-- wp:freeform -->\n" . $html . "\n<!-- /wp:freeform -->\n\n";
}

function esnad_m_button( string $label, string $url ): string {
	if ( '' === $label ) {
		return '';
	}
	$key = $label . "|" . $url . "|" . ( $GLOBALS['esnad_m_last_heading'] ?? '' ); // context keeps legit same-URL buttons under different headings
	$url = esc_url( $url );
	return esnad_m_tag( 'B', $key ) . "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\"" . ( $url ? " href=\"$url\"" : '' ) . '>' . esc_html( $label ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n";
}

function esnad_m_image( array $img ): string {
	$url = $img['url'] ?? '';
	if ( '' === $url ) {
		return '';
	}
	$id  = (int) ( $img['id'] ?? 0 );
	$alt = $id ? (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) : '';
	return esnad_m_tag( 'I', $url ) . "<!-- wp:image" . ( $id ? " {\"id\":$id,\"sizeSlug\":\"full\"}" : '' ) . " -->\n<figure class=\"wp-block-image size-full\"><img src=\"" . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . "\" loading=\"lazy\"/></figure>\n<!-- /wp:image -->\n\n";
}

function esnad_m_walk( array $nodes, string $title, array &$stats ): string {
	$out = '';
	foreach ( $nodes as $el ) {
		if ( ( $el['elType'] ?? '' ) !== 'widget' ) {
			if ( ! empty( $el['elements'] ) ) {
				$out .= esnad_m_walk( $el['elements'], $title, $stats );
			}
			continue;
		}
		$s = $el['settings'] ?? array();
		switch ( $el['widgetType'] ?? '' ) {
			case 'heading':
				$t = esnad_m_plain( $s['title'] ?? '' );
				if ( '' === $t || $t === $title ) {
					break;
				}
				$lvl = isset( $s['header_size'] ) ? (int) substr( (string) $s['header_size'], 1 ) : 2;
				$out .= esnad_m_heading( $t, $lvl <= 2 ? 2 : $lvl );
				break;
			case 'elementskit-heading':
				$t = esnad_m_plain( $s['ekit_heading_title'] ?? '' );
				if ( '' !== $t && $t !== $title ) {
					$out .= esnad_m_heading( $t, 2 );
				}
				$sub = esnad_m_plain( $s['ekit_heading_sub_title'] ?? '' );
				if ( '' !== $sub ) {
					$out .= esnad_m_para_html( '<p>' . esc_html( $sub ) . '</p>' );
				}
				break;
			case 'text-editor':
				$out .= esnad_m_para_html( $s['editor'] ?? '' );
				break;
			case 'icon-box':
				$t = esnad_m_plain( $s['title_text'] ?? '' );
				$d = esnad_m_plain( $s['description_text'] ?? '' );
				$u = $s['link']['url'] ?? '';
				if ( '' !== $t ) {
					$out .= esnad_m_heading( $t, 3 );
				}
				if ( '' !== $d ) {
					$out .= esnad_m_para_html( '<p>' . esc_html( $d ) . '</p>' );
				}
				if ( $u ) {
					$out .= esnad_m_button( esnad_t_label( 'download', $title ), $u );
				}
				break;
			case 'elementskit-image-box':
				$t = esnad_m_plain( $s['ekit_image_box_title_text'] ?? '' );
				$d = esnad_m_plain( $s['ekit_image_box_description_text'] ?? '' );
				if ( ! empty( $s['ekit_image_box_image']['url'] ) ) {
					$out .= esnad_m_image( $s['ekit_image_box_image'] );
				}
				if ( '' !== $t ) {
					$out .= esnad_m_heading( $t, 3 );
				}
				if ( '' !== $d ) {
					$out .= esnad_m_para_html( '<p>' . esc_html( $d ) . '</p>' );
				}
				$btn_url = $s['ekit_image_box_btn_url']['url'] ?? '';
				if ( $btn_url ) {
					$out .= esnad_m_button( esnad_m_plain( $s['ekit_image_box_btn_text'] ?? '' ) ?: esnad_t_label( 'download', $title ), $btn_url );
				}
				break;
			case 'wpr-promo-box':
				$t = esnad_m_plain( $s['promo_box_title'] ?? '' );
				$d = esnad_m_plain( $s['promo_box_description'] ?? '' );
				if ( '' !== $t ) {
					$out .= esnad_m_heading( $t, 3 );
				}
				if ( '' !== $d ) {
					$out .= esnad_m_para_html( '<p>' . esc_html( $d ) . '</p>' );
				}
				break;
			case 'wpr-posts-timeline':
				foreach ( (array) ( $s['timeline_repeater_list'] ?? array() ) as $row ) {
					$t = esnad_m_plain( $row['repeater_story_title'] ?? '' );
					if ( '' !== $t ) {
						$out .= esnad_m_heading( rtrim( $t, ' :' ), 3 );
					}
					$out .= esnad_m_para_html( $row['repeater_description'] ?? '' );
				}
				break;
			case 'social-icons':
				// Personal/department mailto links (executive page) are content; brand channels live in the theme header/footer.
				foreach ( (array) ( $s['social_icon_list'] ?? array() ) as $it ) {
					$u = (string) ( $it['link']['url'] ?? '' );
					if ( 0 === strpos( $u, 'mailto:' ) ) {
						$mail = substr( $u, 7 );
						$out .= esnad_m_para_html( '<p><a href="' . esc_attr( $u ) . '">' . esc_html( $mail ) . '</a></p>' );
					}
				}
				break;
			case 'image':				$out .= esnad_m_image( $s['image'] ?? array() );
				break;
			case 'elementskit-button':
			case 'button':
				$label = esnad_m_plain( $s['ekit_btn_text'] ?? ( $s['text'] ?? '' ) );
				$url   = $s['ekit_btn_url']['url'] ?? ( $s['link']['url'] ?? '' );
				$out  .= esnad_m_button( $label, $url );
				break;
			case 'elementskit-dual-button':
				$out .= esnad_m_button( esnad_m_plain( $s['ekit_btn_text_one'] ?? '' ), $s['ekit_btn_url_one']['url'] ?? '' );
				break;
			case 'icon-list':
				$items = '';
				foreach ( (array) ( $s['icon_list'] ?? array() ) as $it ) {
					$txt = esnad_m_plain( $it['text'] ?? '' );
					if ( '' !== $txt ) {
						$items .= "<!-- wp:list-item -->\n<li>" . esc_html( $txt ) . "</li>\n<!-- /wp:list-item -->\n";
					}
				}
				if ( $items ) {
					$out .= "<!-- wp:list -->\n<ul class=\"wp-block-list\">\n$items</ul>\n<!-- /wp:list -->\n\n";
				}
				break;
			case 'eael-adv-accordion':
				foreach ( (array) ( $s['eael_adv_accordion_tab'] ?? array() ) as $tab ) {
					$q = esnad_m_plain( $tab['eael_adv_accordion_tab_title'] ?? '' );
					$a = esnad_m_text( $tab['eael_adv_accordion_tab_content'] ?? '' );
					if ( '' !== $q ) {
						$out .= "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . esc_html( $q ) . "</summary>\n<!-- wp:freeform -->\n$a\n<!-- /wp:freeform --></details>\n<!-- /wp:details -->\n\n";
					}
				}
				break;
			case 'eael-adv-tabs':
				foreach ( (array) ( $s['eael_adv_tabs_tab'] ?? array() ) as $tab ) {
					$q = esnad_m_plain( $tab['eael_adv_tabs_tab_title'] ?? '' );
					if ( '' !== $q ) {
						$out .= esnad_m_heading( $q, 3 );
					}
					$out .= esnad_m_para_html( $tab['eael_adv_tabs_text_content'] ?? '' );
				}
				break;
			case 'shortcode':
				$sc = trim( (string) ( $s['shortcode'] ?? '' ) );
				if ( '' !== $sc && false === strpos( $sc, 'rev_slider' ) ) {
					$out .= "<!-- wp:shortcode -->\n$sc\n<!-- /wp:shortcode -->\n\n";
				}
				break;
			case 'html':
				$h = esnad_m_text( $s['html'] ?? '' );
				if ( '' !== esnad_m_plain( $h ) ) {
					$out .= "<!-- wp:html -->\n$h\n<!-- /wp:html -->\n\n";
				}
				break;
			case 'image-carousel':
				// Client logos: rendered by the theme shortcode, deduplicated and lazy-loaded.
				if ( empty( $stats['clients'] ) ) {
					$out .= "<!-- wp:shortcode -->\n[esnad_clients]\n<!-- /wp:shortcode -->\n\n";
					$stats['clients'] = true;
				}
				break;
			case 'video':
				$u = $s['youtube_url'] ?? ( $s['vimeo_url'] ?? '' );
				if ( $u ) {
					$out .= "<!-- wp:embed {\"url\":\"" . esc_url( $u ) . "\"} -->\n<figure class=\"wp-block-embed\"><div class=\"wp-block-embed__wrapper\">\n" . esc_url( $u ) . "\n</div></figure>\n<!-- /wp:embed -->\n\n";
				}
				break;
			default:
				$stats['dropped'][ $el['widgetType'] ?? '?' ] = 1 + ( $stats['dropped'][ $el['widgetType'] ?? '?' ] ?? 0 );
		}
	}
	return $out;
}


/** Flatten an Elementor tree into an ordered list of widgets. */
function esnad_m_flat( array $nodes ): array {
	$out = array();
	foreach ( $nodes as $el ) {
		if ( ( $el['elType'] ?? '' ) === 'widget' ) {
			$out[] = $el;
		} elseif ( ! empty( $el['elements'] ) ) {
			$out = array_merge( $out, esnad_m_flat( $el['elements'] ) );
		}
	}
	return $out;
}

/**
 * Designed layouts for pages whose content is a repeated pattern (people, yearly disclosures, vision/mission/values).
 * Only the original words are used; nothing is added except the section label "Values" derived from the page title.
 * Returns null when the page is not one of the special pages.
 */
function esnad_m_special( int $id, array $data, string $title ): ?string {
	$people_pages = array( 17102, 29458, 16843, 29480 );
	$disc_pages   = array( 16854, 29478 );
	$about_pages  = array( 40905, 43104 );
	if ( ! in_array( $id, array_merge( $people_pages, $disc_pages, $about_pages ), true ) ) {
		return null;
	}
	$w  = esnad_m_flat( $data );
	$ar = (bool) preg_match( '/\p{Arabic}/u', $title );

	if ( in_array( $id, $people_pages, true ) ) {
		$people = array();
		$cur    = null;
		foreach ( $w as $e ) {
			$s = $e['settings'] ?? array();
			if ( 'heading' === $e['widgetType'] ) {
				$t = esnad_m_plain( $s['title'] ?? '' );
				if ( '' === $t || $t === $title ) {
					continue;
				}
				if ( $cur && '' === $cur['role'] && '' === $cur['bio'] ) {
					$cur['role'] = $t;
				} else {
					if ( $cur ) {
						$people[ $cur['name'] ] = $people[ $cur['name'] ] ?? $cur;
					}
					$cur = array( 'name' => $t, 'role' => '', 'bio' => '', 'mail' => '' );
				}
			} elseif ( 'text-editor' === $e['widgetType'] && $cur && '' === $cur['bio'] ) {
				$cur['bio'] = esnad_m_text( $s['editor'] ?? '' );
			} elseif ( 'social-icons' === $e['widgetType'] && $cur ) {
				foreach ( (array) ( $s['social_icon_list'] ?? array() ) as $it ) {
					$u = (string) ( $it['link']['url'] ?? '' );
					if ( 0 === strpos( $u, 'mailto:' ) ) {
						$cur['mail'] = substr( $u, 7 );
					}
				}
			}
		}
		if ( $cur ) {
			$people[ $cur['name'] ] = $people[ $cur['name'] ] ?? $cur;
		}
		$html = '<div class="people">';
		foreach ( $people as $p ) {
			$initial = mb_substr( preg_replace( '/^(Mr\.|Ms\.|Mrs\.|السيد|السيدة)\s*/u', '', $p['name'] ), 0, 1 );
			$html   .= '<article class="person"><div class="person__avatar" aria-hidden="true">' . esc_html( $initial ) . '</div><div class="person__body"><h3>' . esc_html( $p['name'] ) . '</h3>';
			$html   .= '' !== $p['role'] ? '<p class="person__role">' . esc_html( $p['role'] ) . '</p>' : '';
			$html   .= '<div class="person__bio">' . $p['bio'] . '</div>';
			$html   .= '' !== $p['mail'] ? '<a class="person__mail" href="mailto:' . esc_attr( $p['mail'] ) . '">' . esc_html( $p['mail'] ) . '</a>' : '';
			$html   .= '</div></article>';
		}
		return "<!-- wp:html -->\n" . $html . "</div>\n<!-- /wp:html -->";
	}

	if ( in_array( $id, $disc_pages, true ) ) {
		$html = '<div class="year-grid">';
		foreach ( $w as $e ) {
			if ( 'elementskit-image-box' !== $e['widgetType'] ) {
				continue;
			}
			$s   = $e['settings'];
			$t   = esnad_m_plain( $s['ekit_image_box_title_text'] ?? '' );
			$u   = $s['ekit_image_box_btn_url']['url'] ?? '';
			$yr  = preg_match( '/(20\d{2})/', $t, $m ) ? $m[1] : '';
			$lbl = esnad_m_plain( $s['ekit_image_box_btn_text'] ?? '' ) ?: ( $ar ? 'تحميل' : 'Download' );
			if ( '' === $t ) {
				continue;
			}
			$html .= '<a class="year" href="' . esc_url( $u ) . '" target="_blank" rel="noopener"><b aria-hidden="true">' . esc_html( $yr ) . '</b><span>' . esc_html( $t ) . '</span><i>' . esc_html( $lbl ) . '</i></a>';
		}
		return "<!-- wp:html -->\n" . $html . "</div>\n<!-- /wp:html -->";
	}

	// About: vision and mission cards, values grid, clients.
	$cards = array();
	$cur   = '';
	$vals  = array();
	$tail  = '';
	foreach ( $w as $e ) {
		$s = $e['settings'] ?? array();
		if ( 'heading' === $e['widgetType'] ) {
			$t = esnad_m_plain( $s['title'] ?? '' );
			if ( '' === $t || $t === $title ) {
				continue;
			}
			if ( preg_match( '/[:：]\s*$/u', $t ) ) {
				$cur = rtrim( $t, ' :：' );
			} elseif ( preg_match( '/^(عملائنا|Our Clients)$/u', $t ) ) {
				$tail = $t;
			}
		} elseif ( 'text-editor' === $e['widgetType'] && '' !== $cur ) {
			$cards[ $cur ] = $cards[ $cur ] ?? esnad_m_text( $s['editor'] ?? '' );
			$cur           = '';
		} elseif ( 'wpr-posts-timeline' === $e['widgetType'] ) {
			foreach ( (array) ( $s['timeline_repeater_list'] ?? array() ) as $row ) {
				$k = rtrim( esnad_m_plain( $row['repeater_story_title'] ?? '' ), ' :' );
				if ( '' !== $k && ! isset( $vals[ $k ] ) ) {
					$vals[ $k ] = esnad_m_text( $row['repeater_description'] ?? '' );
				}
			}
		}
	}
	$html = '<div class="vm">';
	$n    = 0;
	foreach ( $cards as $k => $body ) {
		$n++;
		$html .= '<article class="vm__card"><span class="vm__no" aria-hidden="true">0' . $n . '</span><h3>' . esc_html( $k ) . '</h3>' . $body . '</article>';
	}
	$html .= '</div>';
	if ( $vals ) {
		$html .= '<h2>' . ( $ar ? 'القيم' : 'Values' ) . '</h2><div class="values">';
		$n     = 0;
		foreach ( $vals as $k => $body ) {
			$n++;
			$html .= '<article class="value"><span class="value__no" aria-hidden="true">0' . $n . '</span><h3>' . esc_html( $k ) . '</h3>' . $body . '</article>';
		}
		$html .= '</div>';
	}
	$out = "<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->";
	if ( '' !== $tail ) {
		$out .= "\n\n" . esnad_m_heading( $tail, 2 ) . "<!-- wp:shortcode -->\n[esnad_clients]\n<!-- /wp:shortcode -->";
	}
	return $out;
}
function esnad_t_label( string $k, string $title ): string {
	return 'download' === $k ? ( preg_match( '/\p{Arabic}/u', $title ) ? 'تحميل' : 'Download' ) : $k;
}

$only = getenv( 'ESNAD_IDS' ); // optional comma-separated IDs
$ids  = get_posts( array(
	'post_type'      => array( 'page', 'post' ),
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
	'suppress_filters' => true,
	'post__in'       => $only ? array_map( 'intval', explode( ',', $only ) ) : array(),
) );

$report = array( 'converted' => 0, 'skipped_no_data' => 0, 'empty_result' => array() );
$dropped_all = array();
foreach ( $ids as $id ) {
	$raw = get_post_meta( $id, '_elementor_data', true );
	if ( ! $raw ) {
		$report['skipped_no_data']++;
		continue;
	}
	$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
	if ( ! is_array( $data ) ) {
		$report['skipped_no_data']++;
		continue;
	}
	$stats  = array( 'dropped' => array() );
	$title  = esnad_m_plain( get_the_title( $id ) );
	$special = esnad_m_special( (int) $id, $data, $title );
	$blocks  = null !== $special ? $special : trim( str_replace( array( "\x01", "\x02" ), '', preg_match( '/\x01/', $walked = esnad_m_walk( $data, $title, $stats ) ) ? esnad_m_dedupe( $walked ) : $walked ) );
	foreach ( $stats['dropped'] as $w => $n ) {
		$dropped_all[ $w ] = ( $dropped_all[ $w ] ?? 0 ) + $n;
	}
	if ( '' === $blocks ) {
		$report['empty_result'][] = $id . ' ' . $title;
		if ( $write ) {
			if ( ! metadata_exists( 'post', $id, '_esnad_pre_migration_content' ) ) {
				update_post_meta( $id, '_esnad_pre_migration_content', get_post_field( 'post_content', $id ) );
			}
			$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, array( 'post_content' => '' ), array( 'ID' => $id ) );
			clean_post_cache( $id );
		}
		continue;
	}
	$report['converted']++;
	if ( $write ) {
		if ( ! metadata_exists( 'post', $id, '_esnad_pre_migration_content' ) ) {
			update_post_meta( $id, '_esnad_pre_migration_content', get_post_field( 'post_content', $id ) );
		}
		$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, array( 'post_content' => $blocks ), array( 'ID' => $id ) ); clean_post_cache( $id ); // direct write: avoids WPML save hooks
	}
}

WP_CLI::log( ( $write ? 'WROTE' : 'DRY RUN' ) . ': converted ' . $report['converted'] . ', no elementor data ' . $report['skipped_no_data'] . ', empty result ' . count( $report['empty_result'] ) );
foreach ( $report['empty_result'] as $line ) {
	WP_CLI::log( '  empty: ' . $line );
}
arsort( $dropped_all );
WP_CLI::log( 'Dropped (decorative/add-on) widgets: ' . wp_json_encode( $dropped_all ) );
