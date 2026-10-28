<?php
/** Site header: logo, navigation (with services mega-menu), language switch, consultation CTA. */
defined( 'ABSPATH' ) || exit;
$uri   = get_template_directory_uri() . '/assets/img/';
$logo  = esnad_is_rtl() ? $uri . 'logo-ar.png' : $uri . 'logo-en.png'; // official full-colour logos, as on the current site
$hero  = is_front_page();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#222826">
<?php wp_head(); ?>
</head>
<body <?php body_class( $hero ? 'has-hero' : '' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php echo esc_html( esnad_t( 'انتقل إلى المحتوى', 'Skip to content' ) ); ?></a>

<header class="site-header" id="site-header" data-over-hero="<?php echo $hero ? '1' : '0'; ?>">
	<div class="topbar">
		<div class="container topbar__in">
			<span class="topbar__loc"><?php echo esnad_icon( 'star', 14 ); // phpcs:ignore ?> <?php echo esc_html( esnad_t( 'الرياض، المملكة العربية السعودية', 'Riyadh, Kingdom of Saudi Arabia' ) ); ?></span>
			<span class="topbar__date"><time data-hijri></time><i aria-hidden="true">·</i><time data-greg></time></span>
			<span class="topbar__tools">
				<?php echo esnad_social_links( 'social social--top' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<form class="topsearch" role="search" method="get" action="<?php echo esc_url( home_url( esnad_is_rtl() ? '/' : '/en/' ) ); ?>">
					<label class="screen-reader-text" for="topsearch-q"><?php echo esc_html( esnad_t( 'بحث', 'Search' ) ); ?></label>
					<input id="topsearch-q" type="search" name="s" placeholder="<?php echo esc_attr( esnad_t( 'بحث...', 'Search...' ) ); ?>">
					<button type="submit" aria-label="<?php echo esc_attr( esnad_t( 'بحث', 'Search' ) ); ?>"><?php echo esnad_icon( 'search', 16 ); // phpcs:ignore ?></button>
				</form>
			</span>
		</div>
	</div>
	<div class="container site-header__bar">
		<a class="brand" href="<?php echo esc_url( home_url( esnad_is_rtl() ? '/' : '/en/' ) ); ?>" aria-label="<?php echo esc_attr( esnad_t( 'شركة إسناد المالية - الرئيسية', 'Esnad Advisory Company - Home' ) ); ?>">
			<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( esnad_t( 'شركة إسناد المالية', 'Esnad Advisory Company' ) ); ?>" width="220" height="56" decoding="async">
		</a>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">
			<span class="screen-reader-text"><?php echo esc_html( esnad_t( 'القائمة', 'Menu' ) ); ?></span>
			<span class="nav-toggle__bars" aria-hidden="true"></span>
		</button>

		<nav class="primary-nav" id="primary-nav" aria-label="<?php echo esc_attr( esnad_t( 'القائمة الرئيسية', 'Primary' ) ); ?>">
			<ul class="menu">
				<?php foreach ( esnad_nav() as $i => $item ) :
					$has = ! empty( $item['children'] );
					$here = untrailingslashit( (string) get_permalink() );
					$cur  = untrailingslashit( $item['url'] ) === $here;
					$in   = $cur;
					foreach ( (array) ( $item['children'] ?? array() ) as $ch ) {
						$in = $in || untrailingslashit( $ch['url'] ) === $here;
					}
					// Section-aware: articles belong to "Insights"; service pages to "Services".
					$in = $in || ( is_singular( 'post' ) && untrailingslashit( $item['url'] ) === untrailingslashit( esnad_url( 'blog' ) ) ) || ( ! empty( $item['mega'] ) && esnad_is_service_page() );
					?>
					<li class="menu__item<?php echo $has ? ' has-sub' : ''; ?><?php echo ! empty( $item['mega'] ) ? ' is-mega' : ''; ?><?php echo $in ? ' is-current' : ''; ?>">
						<a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $cur ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a>
						<?php if ( $has ) : ?>
							<button class="sub-toggle" type="button" aria-expanded="false" aria-controls="sub-<?php echo (int) $i; ?>">
								<span class="screen-reader-text"><?php echo esc_html( $item['label'] ); ?></span>
								<?php echo esnad_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</button>
							<ul class="sub" id="sub-<?php echo (int) $i; ?>">
								<?php foreach ( $item['children'] as $child ) : ?>
									<li><a href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="primary-nav__tools">
				<a class="lang-switch" href="<?php echo esc_url( esnad_switch_url() ); ?>" hreflang="<?php echo esnad_is_rtl() ? 'en' : 'ar'; ?>" lang="<?php echo esnad_is_rtl() ? 'en' : 'ar'; ?>"><?php echo esnad_is_rtl() ? 'English' : 'العربية'; ?></a>
				<a class="btn btn--gold btn--sm" href="<?php echo esc_url( esnad_consult_url() ); ?>"><?php echo esc_html( esnad_t( 'اطلب استشارة', 'Request a consultation' ) ); ?></a>
			</div>
		</nav>
	</div>
</header>

<main id="main" tabindex="-1">
