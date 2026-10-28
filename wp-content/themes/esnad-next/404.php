<?php
defined( 'ABSPATH' ) || exit;
get_header();
?>
<header class="page-hero"><div class="container"><h1><?php echo esc_html( esnad_t( 'الصفحة غير موجودة', 'Page not found' ) ); ?></h1></div></header>
<div class="entry"><div class="container entry__layout entry__layout--single">
	<p><?php echo esc_html( esnad_t( 'ربما تغيّر رابط الصفحة. يمكنك العودة إلى الصفحة الرئيسية أو تصفح خدماتنا.', 'The page may have moved. Return to the homepage or browse our services.' ) ); ?></p>
	<p><a class="btn btn--gold" href="<?php echo esc_url( home_url( esnad_is_rtl() ? '/' : '/en/' ) ); ?>"><?php echo esc_html( esnad_t( 'الرئيسية', 'Home' ) ); ?></a></p>
</div></div>
<?php get_footer(); ?>
