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

function esnad_m_text( $html ): string {
	return trim( (string) $html );
}

function esnad_m_plain( $html ): string {
	return trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( html_entity_decode( (string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) ) );
}

function esnad_m_heading( string $text, int $level = 2 ): string {
	$level = max( 2, min( 4, $level ) );
	return "<!-- wp:heading {\"level\":$level} -->\n<h$level class=\"wp-block-heading\">" . esc_html( $text ) . "</h$level>\n<!-- /wp:heading -->\n\n";
}

function esnad_m_para_html( string $html ): string {
	$html = esnad_m_text( $html );
	if ( '' === esnad_m_plain( $html ) && false === strpos( $html, '<img' ) ) {
		return '';
	}
	// Classic block keeps arbitrary editor HTML (lists, links, tables) intact and editable.
	return "<!-- wp:freeform -->\n" . $html . "\n<!-- /wp:freeform -->\n\n";
}

function esnad_m_button( string $label, string $url ): string {
	if ( '' === $label ) {
		return '';
	}
	$url = esc_url( $url );
	return "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\"" . ( $url ? " href=\"$url\"" : '' ) . '>' . esc_html( $label ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->\n\n";
}

function esnad_m_image( array $img ): string {
	$url = $img['url'] ?? '';
	if ( '' === $url ) {
		return '';
	}
	$id  = (int) ( $img['id'] ?? 0 );
	$alt = $id ? (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) : '';
	return "<!-- wp:image" . ( $id ? " {\"id\":$id,\"sizeSlug\":\"full\"}" : '' ) . " -->\n<figure class=\"wp-block-image size-full\"><img src=\"" . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . "\" loading=\"lazy\"/></figure>\n<!-- /wp:image -->\n\n";
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
			case 'image':
				$out .= esnad_m_image( $s['image'] ?? array() );
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

function esnad_t_label( string $k, string $title ): string {
	return 'download' === $k ? ( preg_match( '/\p{Arabic}/u', $title ) ? 'تحميل' : 'Download' ) : $k;
}

$ids = get_posts( array(
	'post_type'      => array( 'page', 'post' ),
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
	'suppress_filters' => true,
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
	$blocks = trim( esnad_m_walk( $data, $title, $stats ) );
	foreach ( $stats['dropped'] as $w => $n ) {
		$dropped_all[ $w ] = ( $dropped_all[ $w ] ?? 0 ) + $n;
	}
	if ( '' === $blocks ) {
		$report['empty_result'][] = $id . ' ' . $title;
		continue;
	}
	$report['converted']++;
	if ( $write ) {
		if ( ! metadata_exists( 'post', $id, '_esnad_pre_migration_content' ) ) {
			update_post_meta( $id, '_esnad_pre_migration_content', get_post_field( 'post_content', $id ) );
		}
		wp_update_post( wp_slash( array( 'ID' => $id, 'post_content' => $blocks ) ) );
	}
}

WP_CLI::log( ( $write ? 'WROTE' : 'DRY RUN' ) . ': converted ' . $report['converted'] . ', no elementor data ' . $report['skipped_no_data'] . ', empty result ' . count( $report['empty_result'] ) );
foreach ( $report['empty_result'] as $line ) {
	WP_CLI::log( '  empty: ' . $line );
}
arsort( $dropped_all );
WP_CLI::log( 'Dropped (decorative/add-on) widgets: ' . wp_json_encode( $dropped_all ) );
