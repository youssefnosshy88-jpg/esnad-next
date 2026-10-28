<?php
/**
 * Homepage. Sections follow the approved sequence; all copy is either the original site wording
 * (services, about, regulatory) or short navigational text.
 */
defined( 'ABSPATH' ) || exit;
get_header();

$c        = esnad_company();
$services = esnad_services();
$ksa      = require get_template_directory() . '/inc/ksa.php';
$uploads  = content_url( 'uploads/' );
$clients  = esnad_json( 'clients.json' );
$dir      = esnad_is_rtl() ? 'rtl' : 'ltr';
$arrow    = esnad_icon( 'arrow', 18 );

$about_text = esnad_t(
	'شركة ذات مسؤولية محدودة مقرها الرياض برأس مال قدره مليون ريال سعودي. حصلت علي ترخيص هيئة السوق المالية رقم (20-19206) بتاريخ 1441/1/11 الموافق 2019/9/1 م لتقديم خدمات الترتيب والمشورة في أعمال الأوراق المالية وقد بدأت ممارسة العمل بعد استيفاء جميع الشروط اللازمة لممارسة النشاط وذلك بتاريخ 1441/6/9 الموافق 2020/2/3 م ومسجلة بسجل تجاري رقم (1010575421) ورقم موحد (7008921160) وتتخصص الشركة في تقديم المشورة في أعمال الأوراق المالية. الشركة حاصلة على نشاط الترتيب بعد الحصول على موافقة الهيئة على تعديل قوائم أعمالها بتاريخ 2024/06/04 م.',
	'A limited liability company based in Riyadh with a capital of one million Saudi Riyals. It obtained a license from the Capital Market Authority (License No. 20-19206) on 11/01/1441 AH, corresponding to 01/09/2019 AD, to provide arranging and advisory services in securities activities. The company commenced operations after fulfilling all the necessary conditions to conduct its business on 09/06/1441 AH, corresponding to 03/02/2020 AD. It is registered under Commercial Registration No. 1010575421 and Unified Number 7008921160. The company specializes in providing advisory services in securities activities. It has obtained arranging activity approval after receiving the Authority\'s consent to amend its business lists on 04/06/2024 AD.'
);

/* Service explorer data: which services answer which need or challenge (navigation aid, not advice). */
$needs = array(
	array( esnad_t( 'الحصول على ترخيص أو تعديل نشاط', 'Obtain a license or amend an activity' ), array( 'licensing', 'compliance' ) ),
	array( esnad_t( 'تنظيم صفقة أو طرح', 'Organize a transaction or offering' ), array( 'arrangement', 'funds', 'feasibility' ) ),
	array( esnad_t( 'تقييم جدوى مشروع أو فرصة', 'Assess a project or opportunity' ), array( 'feasibility', 'arrangement' ) ),
	array( esnad_t( 'تعزيز الرقابة والحوكمة', 'Strengthen oversight and governance' ), array( 'governance', 'audit', 'risk' ) ),
	array( esnad_t( 'الالتزام بالمتطلبات التنظيمية', 'Meet regulatory requirements' ), array( 'compliance', 'licensing' ) ),
	array( esnad_t( 'هيكلة صندوق استثماري', 'Structure an investment fund' ), array( 'funds', 'licensing', 'compliance' ) ),
);
$challenges = array(
	array( esnad_t( 'غياب الدور الرقابي الفعّال', 'No effective oversight role' ), array( 'audit', 'governance' ) ),
	array( esnad_t( 'أدوار وصلاحيات غير محددة بوضوح', 'Unclear roles and authorities' ), array( 'governance' ) ),
	array( esnad_t( 'قياس المخاطر وإدارتها', 'Measuring and managing risk' ), array( 'risk', 'audit' ) ),
	array( esnad_t( 'متطلبات هيئة السوق المالية', 'Capital Market Authority requirements' ), array( 'licensing', 'compliance' ) ),
);
$modes = array(
	'type'      => esnad_t( 'نوع الخدمة', 'Type of service' ),
	'need'      => esnad_t( 'احتياج الأعمال', 'Business need' ),
	'challenge' => esnad_t( 'التحدي المؤسسي', 'Organizational challenge' ),
);

$journey = array(
	array( esnad_t( 'فهم الاحتياج', 'Understanding the need' ), esnad_t( 'نبدأ بالإنصات: نحدد هدف الجهة وسياق عملها والمتطلبات التنظيمية المرتبطة به.', 'We start by listening: the organization\'s goal, its context and the regulatory requirements that apply.' ) ),
	array( esnad_t( 'دراسة الوضع الحالي', 'Reviewing the current position' ), esnad_t( 'نراجع الوثائق والسياسات والإجراءات القائمة لنفهم نقطة البداية بدقة.', 'We review existing documents, policies and procedures to understand the starting point precisely.' ) ),
	array( esnad_t( 'التحليل والتقييم', 'Analysis and assessment' ), esnad_t( 'نحلل المعطيات ونقيّم الخيارات المتاحة وفق المعايير والضوابط ذات الصلة.', 'We analyse the information and assess the available options against the relevant standards and controls.' ) ),
	array( esnad_t( 'إعداد التوصيات', 'Preparing recommendations' ), esnad_t( 'نصوغ توصيات عملية وواضحة مبنية على نتائج التحليل.', 'We prepare clear, practical recommendations built on the results of the analysis.' ) ),
	array( esnad_t( 'تسليم المخرجات والمتابعة', 'Delivery and follow-up' ), esnad_t( 'نسلّم المخرجات ونواصل التواصل لدعم تطبيقها.', 'We deliver the outputs and stay in touch to support their implementation.' ) ),
);

$pillars = array(
	array( 'shield', esnad_t( 'الالتزام التنظيمي', 'Regulatory commitment' ), esnad_t( 'شركة مرخصة من هيئة السوق المالية برقم 20-19206.', 'Licensed by the Capital Market Authority, No. 20-19206.' ) ),
	array( 'pin', esnad_t( 'فهم بيئة الأعمال', 'Understanding the business environment' ), esnad_t( 'مقرنا في الرياض، ونعمل ضمن الإطار التنظيمي للمملكة العربية السعودية.', 'Headquartered in Riyadh, working within the regulatory framework of the Kingdom of Saudi Arabia.' ) ),
	array( 'check', esnad_t( 'منهجية واضحة', 'A clear method' ), esnad_t( 'مراحل عمل محددة من فهم الاحتياج حتى تسليم المخرجات والمتابعة.', 'Defined stages from understanding the need to delivery and follow-up.' ) ),
	array( 'doc', esnad_t( 'الاحترافية والخصوصية', 'Professionalism and privacy' ), esnad_t( 'لا نطلب بيانات مصرفية أو ملفات مالية حساسة عبر النماذج العامة في الموقع.', 'We never ask for bank details or sensitive financial files through the website\'s public forms.' ) ),
);

$posts = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'no_found_rows' => true, 'ignore_sticky_posts' => true ) );
?>

<!-- 01 HERO -->
<section class="hero" aria-labelledby="hero-title">
	<div class="hero__media" aria-hidden="true">
		<?php /* The same looping clip the current site uses; loaded after page load, skipped on slow/saver connections and reduced motion. */ ?>
		<video class="hero__video" muted loop playsinline preload="none" data-src-lg="<?php echo esc_url( $uploads . '2022/06/videohive_480p.mp4' ); ?>" data-src-sm="<?php echo esc_url( $uploads . '2022/06/videohive.mp4' ); ?>"></video>
	</div>
	<div class="hero__art" aria-hidden="true"></div>
	
	<div class="container hero__inner">
		<p class="eyebrow"><?php echo esc_html( esnad_t( 'شركة إسناد المالية', 'Esnad Advisory Company' ) ); ?></p>
		<h1 id="hero-title" class="hero__title">
			<?php if ( esnad_is_rtl() ) : ?>
				<span class="line">رؤية أوضح.</span> <span class="line line--gold">لقرارات مالية مدروسة.</span>
			<?php else : ?>
				<span class="line">A clearer view.</span> <span class="line line--gold">For considered financial decisions.</span>
			<?php endif; ?>
		</h1>
		<p class="hero__lead"><?php echo esc_html( esnad_t( 'خدمات استشارية مالية متخصصة تدعم الشركات في تقييم الخيارات وتعزيز الحوكمة وتنظيم خطوات النمو.', 'Specialised financial advisory services that help companies assess options, strengthen governance and organise their growth steps.' ) ); ?></p>
		<div class="hero__cta">
			<a class="btn btn--gold" href="#services"><?php echo esc_html( esnad_t( 'اكتشف خدماتنا', 'Discover our services' ) ); ?></a>
			<a class="btn btn--outline" href="<?php echo esc_url( esnad_url( 'contact' ) ); ?>"><?php echo esc_html( esnad_t( 'تواصل معنا', 'Contact us' ) ); ?></a>
		</div>
		<p class="hero__trust"><?php echo esnad_icon( 'shield', 20 ); // phpcs:ignore ?> <span><?php echo esc_html( esnad_t( 'مرخصة من هيئة السوق المالية', 'Licensed by the Capital Market Authority' ) ); ?> · <bdi dir="ltr"><?php echo esc_html( esnad_license_label() ); ?></bdi></span></p>
	</div>
</section>

<!-- 02 TRUST STRIP -->
<section class="trust" aria-label="<?php echo esc_attr( esnad_t( 'معلومات الشركة الموثقة', 'Verified company information' ) ); ?>">
	<div class="container trust__grid">
		<div class="trust__card"><span class="trust__ic"><?php echo esnad_icon( 'shield', 24 ); // phpcs:ignore ?></span><span class="trust__txt"><span class="trust__k"><?php echo esc_html( esnad_t( 'الجهة المرخِّصة', 'Licensing authority' ) ); ?></span><span class="trust__v"><?php echo esc_html( esnad_t( 'هيئة السوق المالية', 'Capital Market Authority' ) ); ?></span></span></div>
		<div class="trust__card"><span class="trust__ic"><?php echo esnad_icon( 'doc', 24 ); // phpcs:ignore ?></span><span class="trust__txt"><span class="trust__k"><?php echo esc_html( esnad_t( 'رقم الترخيص', 'License number' ) ); ?></span><span class="trust__v trust__v--num" dir="ltr"><?php echo esc_html( $c['license'] ); ?></span></span></div>
		<div class="trust__card"><span class="trust__ic"><?php echo esnad_icon( 'check', 24 ); // phpcs:ignore ?></span><span class="trust__txt"><span class="trust__k"><?php echo esc_html( esnad_t( 'الأنشطة المرخصة', 'Licensed activities' ) ); ?></span><span class="trust__v"><?php echo esc_html( esnad_t( 'الترتيب والمشورة في أعمال الأوراق المالية', 'Arranging and advising in securities business' ) ); ?></span></span></div>
		<div class="trust__card"><span class="trust__ic"><?php echo esnad_icon( 'pin', 24 ); // phpcs:ignore ?></span><span class="trust__txt"><span class="trust__k"><?php echo esc_html( esnad_t( 'المقر الرئيسي', 'Headquarters' ) ); ?></span><span class="trust__v"><?php echo esc_html( esnad_t( 'الرياض، المملكة العربية السعودية', 'Riyadh, Saudi Arabia' ) ); ?></span></span></div>
	</div>
</section>

<!-- TEXT BAND: the eight service names as an outlined ticker -->
<div class="ticker" aria-hidden="true">
	<div class="ticker__row">
		<?php for ( $k = 0; $k < 2; $k++ ) : ?>
			<ul class="ticker__track">
				<?php foreach ( $services as $svc ) : ?>
					<li><?php echo esc_html( $svc['title'] ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endfor; ?>
	</div>
</div>

<!-- 03 ABOUT -->
<section class="section about" id="about">
	<div class="container about__grid">
		<div class="about__media reveal">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/' . ( esnad_is_rtl() ? 'logo-ar.png' : 'logo-en.png' ) ); ?>" alt="<?php echo esc_attr( esnad_t( 'شعار شركة إسناد المالية', 'Esnad Advisory Company logo' ) ); ?>" width="420" height="108" loading="lazy" decoding="async">
			<ul class="about__ids" dir="ltr">
				<li><span><?php echo esc_html( esnad_t( 'CR', 'CR' ) ); ?></span><b><?php echo esc_html( $c['cr'] ); ?></b></li>
				<li><span>Unified</span><b><?php echo esc_html( $c['unified'] ); ?></b></li>
				<li><span>VAT</span><b><?php echo esc_html( $c['vat'] ); ?></b></li>
			</ul>
		</div>
		<div class="about__text reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'عن إسناد', 'About Esnad' ) ); ?></p>
			<h2 class="h2"><?php echo esc_html( esnad_t( 'لماذا إسناد المالية هي خيارك الإستراتيجي الأفضل لتحقيق أهدافك.', 'Why Esnad Advisory is your best strategic choice to achieve your goals.' ) ); ?></h2>
			<p><?php echo esc_html( $about_text ); ?></p>
			<a class="text-link" href="<?php echo esc_url( esnad_url( 'about' ) ); ?>"><?php echo esc_html( esnad_t( 'اقرأ المزيد عن الشركة', 'Read more about the company' ) ); ?> <?php echo $arrow; // phpcs:ignore ?></a>
		</div>
	</div>
</section>

<!-- 03b MILESTONES (verified dates from the company profile) -->
<section class="milestones" aria-labelledby="mile-title">
	<div class="container">
		<h2 class="screen-reader-text" id="mile-title"><?php echo esc_html( esnad_t( 'محطات الشركة', 'Company milestones' ) ); ?></h2>
		<ol class="mile">
			<li class="reveal"><b class="mile__y" aria-hidden="true">2019</b><span class="mile__t"><?php echo esc_html( esnad_t( 'الحصول على ترخيص هيئة السوق المالية رقم (20-19206)', 'CMA license No. 20-19206 obtained' ) ); ?></span><span class="mile__d"><?php echo esc_html( esnad_t( '1441/1/11 هـ الموافق 2019/9/1 م', '11/01/1441 AH · 01/09/2019 AD' ) ); ?></span></li>
			<li class="reveal"><b class="mile__y" aria-hidden="true">2020</b><span class="mile__t"><?php echo esc_html( esnad_t( 'بدء ممارسة النشاط', 'Commencement of operations' ) ); ?></span><span class="mile__d"><?php echo esc_html( esnad_t( '1441/6/9 هـ الموافق 2020/2/3 م', '09/06/1441 AH · 03/02/2020 AD' ) ); ?></span></li>
			<li class="reveal"><b class="mile__y" aria-hidden="true">2024</b><span class="mile__t"><?php echo esc_html( esnad_t( 'الموافقة على نشاط الترتيب بتعديل قوائم الأعمال', 'Arranging activity approved after amending the business lists' ) ); ?></span><span class="mile__d"><?php echo esc_html( esnad_t( '2024/06/04 م', '04/06/2024 AD' ) ); ?></span></li>
		</ol>
	</div>
</section>

<!-- 04 SERVICE EXPLORER -->
<section class="section section--ivory" id="services" aria-labelledby="services-title">
	<div class="container">
		<header class="section__head reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'خدماتنا', 'Our Services' ) ); ?></p>
			<h2 class="h2" id="services-title"><?php echo esc_html( esnad_t( 'استكشف الخدمات حسب ما يناسب احتياجك', 'Explore services by what you need' ) ); ?></h2>
			<p class="section__sub"><?php echo esc_html( esnad_t( 'أداة للتنقل بين خدماتنا فقط، وليست استشارة مالية أو استثمارية.', 'A navigation aid for our services only. It is not financial or investment advice.' ) ); ?></p>
		</header>

		<div class="explorer" data-explorer>
			<div class="explorer__tabs" role="tablist" aria-label="<?php echo esc_attr( esnad_t( 'طريقة التصفح', 'Browse by' ) ); ?>">
				<?php $first = true; foreach ( $modes as $key => $label ) : ?>
					<button type="button" role="tab" id="tab-<?php echo esc_attr( $key ); ?>" aria-controls="panel-<?php echo esc_attr( $key ); ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" tabindex="<?php echo $first ? '0' : '-1'; ?>"><?php echo esc_html( $label ); ?></button>
				<?php $first = false; endforeach; ?>
			</div>

			<div class="explorer__panel" role="tabpanel" id="panel-type" aria-labelledby="tab-type">
				<div class="cards">
					<?php $n = 0; foreach ( $services as $svc ) : $n++; ?>
						<a class="card" href="<?php echo esc_url( $svc['url'] ); ?>">
							<span class="card__img"><img src="<?php echo esc_url( $svc['img'] ); ?>" alt="" width="720" height="480" loading="lazy" decoding="async"><b class="card__no" aria-hidden="true"><?php echo esc_html( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ); ?></b><span class="card__go" aria-hidden="true"><?php echo esnad_icon( 'arrow-up-right', 22 ); // phpcs:ignore ?></span></span>
							<span class="card__body">
								<span class="card__title"><?php echo esc_html( $svc['title'] ); ?></span>
								<span class="card__text"><?php echo esc_html( $svc['text'] ); ?></span>
								<span class="card__more"><?php echo esc_html( esnad_t( 'اقرأ المزيد', 'Read more' ) ); ?> <?php echo $arrow; // phpcs:ignore ?></span>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>

			<?php foreach ( array( 'need' => $needs, 'challenge' => $challenges ) as $mode => $list ) : ?>
				<div class="explorer__panel" role="tabpanel" id="panel-<?php echo esc_attr( $mode ); ?>" aria-labelledby="tab-<?php echo esc_attr( $mode ); ?>" hidden>
					<div class="chooser">
						<ul class="chooser__list">
							<?php foreach ( $list as $i => $row ) : ?>
								<li><button type="button" data-targets="<?php echo esc_attr( implode( ',', $row[1] ) ); ?>" aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"><?php echo esc_html( $row[0] ); ?></button></li>
							<?php endforeach; ?>
						</ul>
						<div class="chooser__result" aria-live="polite">
							<?php foreach ( $services as $svc ) : ?>
								<a class="chip-card" data-service="<?php echo esc_attr( $svc['key'] ); ?>" href="<?php echo esc_url( $svc['url'] ); ?>">
									<span class="chip-card__title"><?php echo esc_html( $svc['title'] ); ?></span>
									<span class="chip-card__text"><?php echo esc_html( $svc['text'] ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- 05 ATLAS -->
<section class="atlas" id="atlas" aria-labelledby="atlas-title">
	<div class="container">
		<header class="section__head section__head--light reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'أطلس إسناد المالي', 'Esnad Financial Atlas' ) ); ?></p>
			<h2 class="h2" id="atlas-title"><?php echo esc_html( esnad_t( 'من الرياض… برؤية تفهم بيئة الأعمال السعودية', 'From Riyadh… with a view that understands the Saudi business environment' ) ); ?></h2>
		</header>

		<div class="atlas__grid" dir="ltr" data-atlas>
			<figure class="atlas__map">
				<?php
				/* Service constellation inside the outline. Positions are illustrative (not locations): every service radiates from the Riyadh headquarters. */
				$pos = array(
					'governance'  => array( 330, 215 ),
					'risk'        => array( 505, 185 ),
					'funds'       => array( 625, 270 ),
					'compliance'  => array( 215, 345 ),
					'licensing'   => array( 650, 405 ),
					'audit'       => array( 330, 480 ),
					'arrangement' => array( 500, 520 ),
					'feasibility' => array( 640, 540 ),
				);
				$rx = $ksa['riyadh'][0];
				$ry = $ksa['riyadh'][1];
				?>
				<div class="atlas__stage" style="aspect-ratio:<?php echo (int) $ksa['w']; ?>/<?php echo (int) $ksa['h']; ?>">
					<svg viewBox="0 0 <?php echo (int) $ksa['w']; ?> <?php echo (int) $ksa['h']; ?>" role="img" aria-labelledby="atlas-map-title atlas-map-desc">
						<title id="atlas-map-title"><?php echo esc_html( esnad_t( 'خريطة مبسطة للمملكة العربية السعودية', 'Simplified map of Saudi Arabia' ) ); ?></title>
						<desc id="atlas-map-desc"><?php echo esc_html( esnad_t( 'تُظهر المقر الرئيسي في الرياض وخدمات الشركة موزعة حوله.', 'Shows the Riyadh headquarters with the company\'s services arranged around it.' ) ); ?></desc>
						<defs>
							<radialGradient id="glow"><stop offset="0" stop-color="#C7901D" stop-opacity=".55"/><stop offset="1" stop-color="#C7901D" stop-opacity="0"/></radialGradient>
						</defs>
						<path class="atlas__land" d="<?php echo esc_attr( $ksa['path'] ); ?>"/>
						<g class="atlas__routes">
							<?php foreach ( $services as $svc ) :
								$p = $pos[ $svc['key'] ] ?? array( $rx, $ry );
								$d = sprintf( 'M%1$s,%2$s Q%3$s,%4$s %5$s,%6$s', $rx, $ry, ( $rx + $p[0] ) / 2, min( $ry, $p[1] ) - 40, $p[0], $p[1] );
								?>
								<path class="route route--base" d="<?php echo esc_attr( $d ); ?>"/>
								<path class="route" data-route="<?php echo esc_attr( $svc['key'] ); ?>" d="<?php echo esc_attr( $d ); ?>" pathLength="1"/>
							<?php endforeach; ?>
						</g>
						<circle class="hq-glow" cx="<?php echo esc_attr( $rx ); ?>" cy="<?php echo esc_attr( $ry ); ?>" r="46" fill="url(#glow)"/>
						<circle class="hq" cx="<?php echo esc_attr( $rx ); ?>" cy="<?php echo esc_attr( $ry ); ?>" r="8"/>
					</svg>
					<span class="hq-tag" style="left:<?php echo esc_attr( round( $rx / $ksa['w'] * 100, 2 ) ); ?>%;top:<?php echo esc_attr( round( $ry / $ksa['h'] * 100, 2 ) ); ?>%"><?php echo esc_html( esnad_t( 'الرياض', 'Riyadh' ) ); ?></span>
					<?php $n = 0; foreach ( $services as $svc ) : $n++; $p = $pos[ $svc['key'] ] ?? array( $rx, $ry ); ?>
						<button type="button" class="node" data-service="<?php echo esc_attr( $svc['key'] ); ?>" aria-pressed="<?php echo 1 === $n ? 'true' : 'false'; ?>" style="left:<?php echo esc_attr( round( $p[0] / $ksa['w'] * 100, 2 ) ); ?>%;top:<?php echo esc_attr( round( $p[1] / $ksa['h'] * 100, 2 ) ); ?>%">
							<b><?php echo esc_html( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ); ?></b>
							<span><?php echo esc_html( $svc['title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</figure>

			<div class="atlas__panel" dir="<?php echo esc_attr( $dir ); ?>">
				<div class="atlas__hq">
					<h3><?php echo esc_html( esnad_t( 'المقر الرئيسي - الرياض', 'Headquarters - Riyadh' ) ); ?></h3>
					<p><?php echo esc_html( esnad_t( $c['street_ar'], $c['street_en'] ) ); ?></p>
					<p dir="ltr"><a href="tel:<?php echo esc_attr( $c['phone_tel'] ); ?>"><?php echo esc_html( $c['phone'] ); ?></a></p>
				</div>
				<p class="atlas__hint"><?php echo esc_html( esnad_t( 'اضغط على أي خدمة داخل الخريطة لعرض تفاصيلها:', 'Select a service on the map to see its details:' ) ); ?></p>
				<ul class="atlas__list">
					<?php $first = true; foreach ( $services as $svc ) : ?>
						<li><button type="button" data-service="<?php echo esc_attr( $svc['key'] ); ?>" aria-pressed="<?php echo $first ? 'true' : 'false'; ?>"><?php echo esc_html( $svc['title'] ); ?></button></li>
					<?php $first = false; endforeach; ?>
				</ul>
				<div class="atlas__detail" aria-live="polite">
					<?php foreach ( $services as $svc ) : ?>
						<div class="atlas__item" data-service="<?php echo esc_attr( $svc['key'] ); ?>" hidden>
							<p><?php echo esc_html( $svc['text'] ); ?></p>
							<a class="text-link" href="<?php echo esc_url( $svc['url'] ); ?>"><?php echo esc_html( esnad_t( 'صفحة الخدمة', 'Service page' ) ); ?> <?php echo $arrow; // phpcs:ignore ?></a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 06 ADVISORY JOURNEY -->
<section class="section journey" id="how" aria-labelledby="journey-title" data-journey>
	<div class="container">
		<header class="section__head reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'كيف نعمل', 'How we work' ) ); ?></p>
			<h2 class="h2" id="journey-title"><?php echo esc_html( esnad_t( 'رحلة استشارية واضحة من الفكرة إلى التسليم', 'A clear advisory journey from first question to delivery' ) ); ?></h2>
		</header>
		<ol class="journey__steps">
			<?php foreach ( $journey as $i => $step ) : ?>
				<li class="step<?php echo 0 === $i ? ' is-active' : ''; ?>" data-step="<?php echo (int) $i; ?>">
					<span class="step__no" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3 class="step__title"><?php echo esc_html( $step[0] ); ?></h3>
					<p class="step__text"><?php echo esc_html( $step[1] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
		<div class="journey__line" aria-hidden="true"><span></span></div>
	</div>
</section>

<!-- 07 WHY ESNAD -->
<section class="section section--ivory" id="why" aria-labelledby="why-title">
	<div class="container">
		<header class="section__head reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'لماذا إسناد', 'Why Esnad' ) ); ?></p>
			<h2 class="h2" id="why-title"><?php echo esc_html( esnad_t( 'أربعة أسس تقوم عليها خدماتنا', 'Four foundations behind our work' ) ); ?></h2>
		</header>
		<div class="pillars">
			<?php foreach ( $pillars as $p ) : ?>
				<article class="pillar reveal">
					<span class="pillar__icon"><?php echo esnad_icon( $p[0], 28 ); // phpcs:ignore ?></span>
					<h3><?php echo esc_html( $p[1] ); ?></h3>
					<p><?php echo esc_html( $p[2] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- 08 BUSINESS NEEDS -->
<section class="section" id="needs" aria-labelledby="needs-title">
	<div class="container">
		<header class="section__head reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'احتياجات الأعمال', 'Business needs' ) ); ?></p>
			<h2 class="h2" id="needs-title"><?php echo esc_html( esnad_t( 'ما تحتاجه جهتك، وأين تجد الخدمة المناسبة', 'What your organization needs, and where to find the service' ) ); ?></h2>
		</header>
		<div class="needs">
			<?php foreach ( $needs as $row ) : ?>
				<div class="need reveal">
					<h3><?php echo esc_html( $row[0] ); ?></h3>
					<ul>
						<?php foreach ( $row[1] as $key ) : if ( empty( $services[ $key ] ) ) { continue; } ?>
							<li><a href="<?php echo esc_url( $services[ $key ]['url'] ); ?>"><?php echo esc_html( $services[ $key ]['title'] ); ?> <?php echo $arrow; // phpcs:ignore ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- 09 CLIENTS -->
<?php if ( $clients ) : ?>
<section class="section clients clients--dark" id="clients" aria-labelledby="clients-title">
	<div class="container">
		<header class="section__head section__head--light reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'شريكك في النجاح', 'Your Partner for Success' ) ); ?></p>
			<h2 class="h2" id="clients-title"><?php echo esc_html( esnad_t( 'عملائنا', 'Our Clients' ) ); ?></h2>
		</header>
	</div>
	<?php echo esnad_clients_marquee(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</section>
<?php endif; ?>

<!-- 10 COMPLIANCE -->
<section class="section" id="compliance" aria-labelledby="compliance-title">
	<div class="container">
		<header class="section__head reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'الإفصاح', 'Disclosure' ) ); ?></p>
			<h2 class="h2" id="compliance-title"><?php echo esc_html( esnad_t( 'المعلومات التنظيمية والإفصاحات الرسمية', 'Regulatory information and official disclosures' ) ); ?></h2>
		</header>
		<div class="docs">
			<a class="doc" href="<?php echo esc_url( esnad_url( 'disclosure' ) ); ?>">
				<?php echo esnad_icon( 'doc', 32 ); // phpcs:ignore ?>
				<span><b><?php echo esc_html( esnad_t( 'قوائم الشركة المالية المعتمدة', 'Approved financial statements' ) ); ?></b><small><?php echo esc_html( esnad_t( 'من 2020 إلى 2025', 'From 2020 to 2025' ) ); ?></small></span>
			</a>
			<a class="doc" href="<?php echo esc_url( esnad_url( 'org' ) ); ?>">
				<?php echo esnad_icon( 'doc', 32 ); // phpcs:ignore ?>
				<span><b><?php echo esc_html( esnad_t( 'الهيكل التنظيمي', 'Organization chart' ) ); ?></b></span>
			</a>
			<a class="doc" href="<?php echo esc_url( esnad_url( 'board' ) ); ?>">
				<?php echo esnad_icon( 'shield', 32 ); // phpcs:ignore ?>
				<span><b><?php echo esc_html( esnad_t( 'مجلس الإدارة', 'Board of Directors' ) ); ?></b></span>
			</a>
			<?php $fatca = esnad_fatca(); ?>
			<a class="doc" href="<?php echo esc_url( $fatca[1] ); ?>" target="_blank" rel="noopener">
				<?php echo esnad_icon( 'download', 32 ); // phpcs:ignore ?>
				<span><b><?php echo esc_html( $fatca[0] ); ?></b><small><?php echo esc_html( esnad_t( 'تحميل الملف', 'Download File' ) ); ?></small></span>
			</a>
		</div>
	</div>
</section>

<!-- 11 KNOWLEDGE -->

<?php if ( $posts->have_posts() ) : ?>
<section class="section section--ivory" id="insights" aria-labelledby="insights-title">
	<div class="container">
		<header class="section__head reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'مركز المعرفة', 'Knowledge center' ) ); ?></p>
			<h2 class="h2" id="insights-title"><?php echo esc_html( esnad_t( 'أحدث المقالات', 'Latest articles' ) ); ?></h2>
		</header>
		<div class="articles">
			<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
				<article class="article reveal">
					<a href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'esnad-card', array( 'loading' => 'lazy' ) ); else : ?>
							<span class="article__ph" aria-hidden="true"></span>
						<?php endif; ?>
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24, '…' ) ); ?></p>
					</a>
				</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<p class="section__more"><a class="text-link" href="<?php echo esc_url( esnad_url( 'blog' ) ); ?>"><?php echo esc_html( esnad_t( 'كل المقالات', 'All articles' ) ); ?> <?php echo $arrow; // phpcs:ignore ?></a></p>
	</div>
</section>
<?php endif; ?>

<!-- 12 FAQ -->
<section class="section" id="faq" aria-labelledby="faq-title">
	<div class="container container--narrow">
		<header class="section__head reveal">
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'الأسئلة الشائعة', 'FAQ' ) ); ?></p>
			<h2 class="h2" id="faq-title"><?php echo esc_html( esnad_t( 'إجابات مختصرة وموثقة', 'Short, verified answers' ) ); ?></h2>
		</header>
		<div class="faq">
			<?php foreach ( esnad_faq() as $qa ) : ?>
				<details class="faq__item">
					<summary><span><?php echo esc_html( $qa[0] ); ?></span><?php echo esnad_icon( 'plus', 20 ); // phpcs:ignore ?></summary>
					<p><?php echo esc_html( $qa[1] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- 13 CTA -->
<section class="cta" aria-labelledby="cta-title">
	<div class="cta__art" aria-hidden="true"></div>
	<div class="container cta__inner">
		<h2 class="h2" id="cta-title"><?php echo esc_html( esnad_t( 'القرار المدروس يبدأ بحوار واضح.', 'A considered decision starts with a clear conversation.' ) ); ?></h2>
		<div class="hero__cta">
			<a class="btn btn--gold" href="<?php echo esc_url( esnad_consult_url() ); ?>"><?php echo esc_html( esnad_t( 'اطلب استشارة', 'Request a consultation' ) ); ?></a>
			<a class="btn btn--outline" href="<?php echo esc_url( esnad_url( 'contact' ) ); ?>"><?php echo esc_html( esnad_t( 'تواصل معنا', 'Contact us' ) ); ?></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
