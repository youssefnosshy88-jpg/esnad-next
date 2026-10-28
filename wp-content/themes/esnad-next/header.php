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
					$cur = untrailingslashit( $item['url'] ) === untrailingslashit( (string) get_permalink() );
					?>
					<li class="menu__item<?php echo $has ? ' has-sub' : ''; ?><?php echo ! empty( $item['mega'] ) ? ' is-mega' : ''; ?>">
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
