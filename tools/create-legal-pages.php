<?php
/**
 * Creates the privacy policy pages (Arabic + English, linked as WPML translations).
 * The text is a DRAFT for the company and its legal adviser to approve before production.
 * Run on staging: wp eval-file tools/create-legal-pages.php
 * Idempotent: an existing page (by slug) is updated, not duplicated.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

function esnad_lg_page( string $slug, string $title, string $content, string $lang, ?int $trid_from = null ): int {
	global $wpdb;
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing ) {
		$wpdb->update( $wpdb->posts, array( 'post_content' => $content, 'post_title' => $title ), array( 'ID' => $existing->ID ) );
		clean_post_cache( $existing->ID );
		return (int) $existing->ID;
	}
	$id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content,
	) );
	if ( $trid_from ) {
		$trid = apply_filters( 'wpml_element_trid', null, $trid_from, 'post_page' );
		do_action( 'wpml_set_element_language_details', array(
			'element_id'           => $id,
			'element_type'         => 'post_page',
			'trid'                 => $trid,
			'language_code'        => $lang,
			'source_language_code' => 'ar',
		) );
	}
	return (int) $id;
}

$h  = static fn( string $t ): string => "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">$t</h2>\n<!-- /wp:heading -->\n\n";
$p  = static fn( string $t ): string => "<!-- wp:paragraph -->\n<p>$t</p>\n<!-- /wp:paragraph -->\n\n";
$ul = static function ( array $items ): string {
	$li = '';
	foreach ( $items as $i ) {
		$li .= "<!-- wp:list-item -->\n<li>$i</li>\n<!-- /wp:list-item -->\n";
	}
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">\n$li</ul>\n<!-- /wp:list -->\n\n";
};

$ar = '<!-- wp:html -->' . "\n" . '<div class="draft-note"><strong>مسودة بانتظار الاعتماد.</strong> هذا النص أُعدّ من واقع ما يجمعه الموقع فعليًا، ويحتاج اعتماد الشركة والمستشار القانوني قبل النشر على الموقع الأساسي.</div>' . "\n<!-- /wp:html -->\n\n"
	. $p( 'تلتزم شركة إسناد المالية (المرخصة من هيئة السوق المالية برقم 20-19206) بحماية بياناتك الشخصية وفق نظام حماية البيانات الشخصية في المملكة العربية السعودية. توضح هذه السياسة ما نجمعه من بيانات عبر هذا الموقع، ولماذا، وحقوقك بشأنها.' )
	. $h( 'البيانات التي نجمعها' )
	. $p( 'نجمع البيانات التي تدخلها بنفسك في النماذج فقط:' )
	. $ul( array(
		'نموذج طلب الاستشارة: الاسم، وسيلة التواصل (البريد الإلكتروني والجوال)، الجهة، الخدمة المطلوبة، ووصف مختصر للاحتياج.',
		'نموذج التواصل: الاسم، البريد الإلكتروني، الجوال (اختياري)، ونص الرسالة.',
		'نموذج الشكاوى: الاسم، وسيلة التواصل، وتفاصيل الشكوى.',
		'بيانات تقنية محدودة لحماية النماذج من الإساءة، مثل عنوان الشبكة وعدد المحاولات خلال الساعة.',
	) )
	. $p( 'لا نطلب عبر الموقع أي بيانات مصرفية أو أرقام بطاقات أو كلمات مرور، ولا نجمع أموالًا عبر الموقع أو عبر مواقع التواصل الاجتماعي.' )
	. $h( 'لماذا نستخدم بياناتك' )
	. $ul( array(
		'الرد على طلبك أو استفسارك أو شكواك والتواصل معك بشأنه.',
		'تقييم احتياجك وتحديد الخدمة الاستشارية المناسبة.',
		'الالتزام بالأنظمة واللوائح التي تخضع لها الشركة، بما فيها متطلبات هيئة السوق المالية.',
		'حماية الموقع من الرسائل المزعجة وإساءة الاستخدام.',
	) )
	. $h( 'مشاركة البيانات' )
	. $p( 'لا نبيع بياناتك ولا نؤجرها. قد نشاركها مع مزوّدي الخدمات التقنية الذين يستضيفون الموقع أو يرسلون البريد نيابةً عنا، ومع الجهات الرسمية عند وجود التزام نظامي.' )
	. $h( 'مدة الاحتفاظ والحماية' )
	. $p( 'نحتفظ ببياناتك للمدة اللازمة لتحقيق الغرض الذي جُمعت من أجله أو للمدة التي تفرضها الأنظمة. [تحدد الشركة مدة الاحتفاظ المعتمدة]. نطبق إجراءات تقنية وتنظيمية لحماية البيانات، ويقتصر الاطلاع على الطلبات على الموظفين المختصين.' )
	. $h( 'ملفات الارتباط (الكوكيز)' )
	. $ul( array(
		'ملفات ضرورية: تحفظ تفضيل اللغة وتشغّل الموقع، ولا تحتاج موافقة.',
		'ملفات التحليلات: لا تُفعَّل إلا بعد موافقتك عبر شريط الموافقة، وتُستخدم لفهم استخدام الموقع بشكل مجمّع. يمكنك رفضها دون أن يتأثر استخدامك للموقع.',
	) )
	. $h( 'حقوقك' )
	. $p( 'يحق لك الاطلاع على بياناتك وطلب تصحيحها أو إتلافها وسحب موافقتك متى شئت، وفق ما يتيحه النظام. لممارسة أي من هذه الحقوق راسلنا على info@esnadadvisory.sa.' )
	. $h( 'التواصل' )
	. $p( 'شركة إسناد المالية - الرياض، طريق الملك فيصل - حي المربع. الهاتف: 00966920014522. البريد الإلكتروني: info@esnadadvisory.sa.' )
	. $p( 'قد نحدّث هذه السياسة، وسيظهر تاريخ آخر تحديث أعلى هذه الصفحة.' );

$en = '<!-- wp:html -->' . "\n" . '<div class="draft-note"><strong>Draft pending approval.</strong> This text reflects what the website actually collects. It needs approval from the company and its legal adviser before the production release.</div>' . "\n<!-- /wp:html -->\n\n"
	. $p( 'Esnad Advisory Company (licensed by the Capital Market Authority, No. 20-19206) protects your personal data in line with the Personal Data Protection Law of the Kingdom of Saudi Arabia. This policy explains what this website collects, why, and your rights.' )
	. $h( 'Data we collect' )
	. $p( 'We only collect what you type into our forms:' )
	. $ul( array(
		'Consultation request: name, contact details (email and mobile), organisation, requested service and a short description of your need.',
		'Contact form: name, email, mobile (optional) and your message.',
		'Complaint form: name, contact details and the details of the complaint.',
		'Limited technical data used to protect the forms from abuse, such as the network address and the number of attempts per hour.',
	) )
	. $p( 'The website never asks for bank details, card numbers or passwords, and the company does not collect funds through the website or social media.' )
	. $h( 'Why we use your data' )
	. $ul( array(
		'To answer your request, question or complaint and to contact you about it.',
		'To understand your need and suggest the right advisory service.',
		'To comply with the laws and regulations that apply to the company, including Capital Market Authority requirements.',
		'To protect the website from spam and misuse.',
	) )
	. $h( 'Sharing' )
	. $p( 'We do not sell or rent your data. We may share it with technical providers who host the website or send email on our behalf, and with public authorities when the law requires it.' )
	. $h( 'Retention and security' )
	. $p( 'We keep your data for as long as needed for the purpose it was collected or as the law requires. [The company will state the approved retention period]. We apply technical and organisational measures, and only the relevant staff can view requests.' )
	. $h( 'Cookies' )
	. $ul( array(
		'Essential cookies: remember your language and run the site; no consent needed.',
		'Analytics cookies: switched on only after you accept in the consent bar, used to understand site usage in aggregate. You can decline without affecting how you use the site.',
	) )
	. $h( 'Your rights' )
	. $p( 'You may access your data, ask for correction or deletion, and withdraw consent at any time, as the law allows. To exercise a right, write to info@esnadadvisory.sa.' )
	. $h( 'Contact' )
	. $p( 'Esnad Advisory Company - Riyadh, King Faisal Road, Al Murabba District. Phone: 00966920014522. Email: info@esnadadvisory.sa.' )
	. $p( 'We may update this policy; the date of the last update will appear on this page.' );

$ar_id = esnad_lg_page( 'privacy-policy', 'سياسة الخصوصية وملفات الارتباط', $ar, 'ar' );
$en_id = esnad_lg_page( 'privacy-policy-en', 'Privacy policy and cookies', $en, 'en', $ar_id );
WP_CLI::log( "privacy: ar=$ar_id en=$en_id" );
