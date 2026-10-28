<?php
/** Footer: regulatory notice band, four columns, legal line. */
defined( 'ABSPATH' ) || exit;
$c     = esnad_company();
$uri   = get_template_directory_uri() . '/assets/img/';
$notice = esnad_notice();
$nav   = esnad_nav();
$about = $nav[0]['children'];
?>
</main>

<section class="notice" aria-labelledby="notice-title">
	<div class="container notice__inner">
		<h2 id="notice-title" class="notice__title"><?php echo esc_html( esnad_t( 'تنويه تنظيمي', 'Regulatory notice' ) ); ?></h2>
		<div class="notice__text">
			<?php foreach ( $notice as $p ) : ?>
				<p><?php echo esc_html( $p ); ?></p>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-col footer-col--brand">
			<img src="<?php echo esc_url( esnad_is_rtl() ? $uri . 'logo-ar-white.png' : $uri . 'logo-en-white.png' ); ?>" alt="<?php echo esc_attr( esnad_t( 'شركة إسناد المالية', 'Esnad Advisory Company' ) ); ?>" width="220" height="56" loading="lazy" decoding="async">
			<p class="footer-about"><?php echo esc_html( esnad_t( 'شركة ذات مسؤولية محدودة مقرها الرياض، مرخصة من هيئة السوق المالية لتقديم خدمات الترتيب والمشورة في أعمال الأوراق المالية.', 'A limited liability company based in Riyadh, licensed by the Capital Market Authority to provide arranging and advisory services in securities business.' ) ); ?></p>
		</div>

		<nav class="footer-col" aria-labelledby="f-about">
			<h2 id="f-about"><?php echo esc_html( esnad_t( 'عن إسناد', 'About Esnad' ) ); ?></h2>
			<ul>
				<?php foreach ( $about as $item ) : ?>
					<li><a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a></li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( esnad_url( 'blog' ) ); ?>"><?php echo esc_html( esnad_t( 'المقالات', 'Insights' ) ); ?></a></li>
			</ul>
		</nav>

		<nav class="footer-col" aria-labelledby="f-services">
			<h2 id="f-services"><?php echo esc_html( esnad_t( 'الخدمات', 'Services' ) ); ?></h2>
			<ul>
				<?php foreach ( esnad_services() as $svc ) : ?>
					<li><a href="<?php echo esc_url( $svc['url'] ); ?>"><?php echo esc_html( $svc['title'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="footer-col" aria-labelledby="f-reg">
			<h2 id="f-reg"><?php echo esc_html( esnad_t( 'المعلومات التنظيمية', 'Regulatory information' ) ); ?></h2>
			<dl class="facts">
				<div><dt><?php echo esc_html( esnad_t( 'ترخيص هيئة السوق المالية', 'CMA license' ) ); ?></dt><dd dir="ltr"><?php echo esc_html( $c['license'] ); ?></dd></div>
				<div><dt><?php echo esc_html( esnad_t( 'السجل التجاري', 'Commercial registration' ) ); ?></dt><dd dir="ltr"><?php echo esc_html( $c['cr'] ); ?></dd></div>
				<div><dt><?php echo esc_html( esnad_t( 'الرقم الموحد', 'Unified number' ) ); ?></dt><dd dir="ltr"><?php echo esc_html( $c['unified'] ); ?></dd></div>
				<div><dt><?php echo esc_html( esnad_t( 'الرقم الضريبي', 'VAT number' ) ); ?></dt><dd dir="ltr"><?php echo esc_html( $c['vat'] ); ?></dd></div>
			</dl>
			<p><a class="text-link" href="<?php echo esc_url( esnad_url( 'disclosure' ) ); ?>"><?php echo esc_html( esnad_t( 'الإفصاح والقوائم المالية', 'Disclosure and financial statements' ) ); ?></a></p>
			<p><a class="text-link" href="<?php echo esc_url( esnad_complaints_url() ); ?>"><?php echo esc_html( esnad_t( 'الشكاوى', 'Complaints' ) ); ?></a></p>
		</div>

		<address class="footer-col">
			<h2><?php echo esc_html( esnad_t( 'تواصل معنا', 'Contact' ) ); ?></h2>
			<p><?php echo esc_html( esnad_t( $c['city_ar'] . ' - ' . $c['street_ar'], $c['city_en'] . ', ' . $c['street_en'] ) ); ?></p>
			<p><a href="tel:<?php echo esc_attr( $c['phone_tel'] ); ?>" dir="ltr"><?php echo esc_html( $c['phone'] ); ?></a></p>
			<p><a href="mailto:<?php echo esc_attr( $c['email'] ); ?>"><?php echo esc_html( $c['email'] ); ?></a></p>
			<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( esnad_consult_url() ); ?>"><?php echo esc_html( esnad_t( 'اطلب استشارة', 'Request a consultation' ) ); ?></a>
		</address>
	</div>

	<div class="container footer-legal">
		<p><?php echo esc_html( esnad_t( 'جميع الحقوق محفوظة ', 'All rights reserved ' ) . gmdate( 'Y' ) . ' ' . esnad_t( 'شركة إسناد المالية', 'Esnad Advisory Company' ) . ' - ' . esnad_license_label() ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
