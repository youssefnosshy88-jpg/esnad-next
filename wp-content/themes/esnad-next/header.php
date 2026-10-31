<?php
/** Site header: logo, two quick links, language and consultation, and the full-screen index menu. */
defined( 'ABSPATH' ) || exit;
$uri   = get_template_directory_uri() . '/assets/img/';
$logo  = esnad_is_rtl() ? $uri . 'logo-ar.png' : $uri . 'logo-en.png'; // official full-colour logos, as on the current site
$hero  = is_front_page();
$here  = untrailingslashit( (string) get_permalink() );
$home  = esc_url( home_url( esnad_is_rtl() ? '/' : '/en/' ) );
$on_services = esnad_is_service_page() || untrailingslashit( esnad_url( 'services' ) ) === $here;
$on_about    = untrailingslashit( esnad_url( 'about' ) ) === $here;
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
<div class="read-bar" aria-hidden="true"><span></span></div>
<a class="skip-link" href="#main"><?php echo esc_html( esnad_t( 'انتقل إلى المحتوى', 'Skip to content' ) ); ?></a>

<header class="site-header" id="site-header" data-over-hero="<?php echo $hero ? '1' : '0'; ?>">
	<div class="topbar">
		<div class="container topbar__in">
			<span class="topbar__loc"><?php echo esnad_icon( 'pin', 14 ); // phpcs:ignore ?> <?php echo esc_html( esnad_t( 'الرياض، المملكة العربية السعودية', 'Riyadh, Kingdom of Saudi Arabia' ) ); ?></span>
			<span class="topbar__date" aria-label="<?php echo esc_attr( esnad_t( 'التاريخ والوقت بتوقيت الرياض', 'Date and time in Riyadh' ) ); ?>"><time data-hijri></time><i aria-hidden="true">·</i><time data-greg></time><i aria-hidden="true">·</i><time class="topbar__clock" data-clock></time></span>
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
		<a class="brand" href="<?php echo $home; // phpcs:ignore WordPress.Security.EscapeOutput ?>" aria-label="<?php echo esc_attr( esnad_t( 'شركة إسناد المالية - الرئيسية', 'Esnad Advisory Company - Home' ) ); ?>">
			<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( esnad_t( 'شركة إسناد المالية', 'Esnad Advisory Company' ) ); ?>" width="220" height="56" decoding="async">
		</a>

		<nav class="quick" aria-label="<?php echo esc_attr( esnad_t( 'روابط سريعة', 'Quick links' ) ); ?>">
			<a href="<?php echo esc_url( esnad_url( 'services' ) ); ?>"<?php echo $on_services ? ' class="is-current"' : ''; ?>><?php echo esc_html( esnad_t( 'خدماتنا', 'Services' ) ); ?></a>
			<a href="<?php echo esc_url( esnad_url( 'about' ) ); ?>"<?php echo $on_about ? ' class="is-current"' : ''; ?>><?php echo esc_html( esnad_t( 'عن الشركة', 'About' ) ); ?></a>
		</nav>

		<div class="hbar__tools">
			<a class="lang-switch" href="<?php echo esc_url( esnad_switch_url() ); ?>" hreflang="<?php echo esnad_is_rtl() ? 'en' : 'ar'; ?>" lang="<?php echo esnad_is_rtl() ? 'en' : 'ar'; ?>"><?php echo esnad_is_rtl() ? 'English' : 'العربية'; ?></a>
			<a class="btn btn--gold btn--sm hbar__cta" href="<?php echo esc_url( esnad_consult_url() ); ?>"><?php echo esc_html( esnad_t( 'اطلب استشارة', 'Request a consultation' ) ); ?></a>
			<button class="menu-btn" type="button" aria-expanded="false" aria-controls="menu-panel">
				<span class="menu-btn__label"><?php echo esc_html( esnad_t( 'القائمة', 'Menu' ) ); ?></span>
				<span class="menu-btn__bars" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</header>

<div class="mpanel" id="menu-panel" role="region" aria-label="<?php echo esc_attr( esnad_t( 'القائمة الرئيسية', 'Main menu' ) ); ?>">
	<?php echo esnad_emblem( 'emblem-ghost.svg', 'mpanel__emblem' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<div class="container mpanel__in">
		<ul class="mp">
			<?php foreach ( esnad_nav() as $i => $item ) :
				$kids = (array) ( $item['children'] ?? array() );
				$cur  = untrailingslashit( $item['url'] ) === $here;
				foreach ( $kids as $ch ) {
					$cur = $cur || untrailingslashit( $ch['url'] ) === $here;
				}
				$cur = $cur || ( is_singular( 'post' ) && untrailingslashit( $item['url'] ) === untrailingslashit( esnad_url( 'blog' ) ) ) || ( ! empty( $item['mega'] ) && esnad_is_service_page() );
				?>
				<li class="mp__item<?php echo 0 === $i ? ' is-active' : ''; ?><?php echo $cur ? ' is-current' : ''; ?>">
					<span class="mp__row">
						<a class="mp__link" href="<?php echo esc_url( $item['url'] ); ?>">
							<span class="mp__no" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="mp__t"><?php echo esc_html( $item['label'] ); ?></span>
						</a>
						<?php if ( $kids ) : ?>
							<button class="mp__more" type="button" aria-expanded="false" aria-controls="mp-sub-<?php echo (int) $i; ?>">
								<span class="screen-reader-text"><?php echo esc_html( $item['label'] ); ?></span>
								<?php echo esnad_icon( 'chevron', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</button>
						<?php endif; ?>
					</span>
					<div class="mp__sub" id="mp-sub-<?php echo (int) $i; ?>">
						<a class="mp__all" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?> <?php echo esnad_icon( 'arrow', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<?php if ( ! $kids && ! empty( $item['text'] ) ) : ?><p class="mp__lead"><?php echo esc_html( $item['text'] ); ?></p><?php endif; ?>
						<?php if ( $kids ) : ?>
							<ul class="mp__kids<?php echo ! empty( $item['mega'] ) ? ' mp__kids--num' : ''; ?>">
								<?php foreach ( $kids as $n => $child ) : ?>
									<li>
										<a href="<?php echo esc_url( $child['url'] ); ?>">
											<?php if ( ! empty( $item['mega'] ) ) : ?><span class="mp__n" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $n + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><?php endif; ?>
											<span class="mp__b"><span class="mp__bt"><?php echo esc_html( $child['label'] ); ?></span><?php if ( ! empty( $child['text'] ) ) : ?><small><?php echo esc_html( $child['text'] ); ?></small><?php endif; ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="mpanel__foot">
			<span class="mpanel__lic"><?php echo esc_html( esnad_t( 'مرخصة من هيئة السوق المالية · ترخيص رقم 20-19206', 'Licensed by the Capital Market Authority · Licence No. 20-19206' ) ); ?></span>
			<span class="mpanel__acts">
				<?php echo esnad_social_links( 'social social--top' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<a class="lang-switch" href="<?php echo esc_url( esnad_switch_url() ); ?>" hreflang="<?php echo esnad_is_rtl() ? 'en' : 'ar'; ?>" lang="<?php echo esnad_is_rtl() ? 'en' : 'ar'; ?>"><?php echo esnad_is_rtl() ? 'English' : 'العربية'; ?></a>
				<a class="btn btn--gold btn--sm" href="<?php echo esc_url( esnad_consult_url() ); ?>"><?php echo esc_html( esnad_t( 'اطلب استشارة', 'Request a consultation' ) ); ?></a>
			</span>
		</div>
	</div>
</div>

<main id="main" tabindex="-1">
