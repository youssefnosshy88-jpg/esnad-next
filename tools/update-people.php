<?php
global $wpdb;

function esnad_person( $letter, $name, $role, array $paras, $mail = '' ) {
	$bio = '';
	foreach ( $paras as $p ) {
		$bio .= '<p>' . $p . '</p>';
	}
	$m = $mail ? '<a class="person__mail" href="mailto:' . $mail . '">' . $mail . '</a>' : '';
	return '<article class="person"><div class="person__avatar" aria-hidden="true">' . $letter . '</div><div class="person__body"><h3>' . $name . '</h3><p class="person__role">' . $role . '</p><div class="person__bio">' . $bio . '</div>' . $m . '</div></article>';
}
function esnad_people( array $items ) {
	return "<!-- wp:html -->\n<div class=\"people\">" . implode( '', $items ) . "</div>\n<!-- /wp:html -->";
}

$ar = array(
	'latifa' => array( 'ل', 'لطيفة الهديب', 'رئيسة مجلس الإدارة والعضو المنتدب', array(
		'تتمتع السيدة لطيفة الهديب بخبرة تزيد على 15 عامًا في الخدمات المصرفية والاستثمارية والاستراتيجية، شغلت خلالها مناصب عدة في البنك السعودي الفرنسي والعربي للاستثمار وشركة مطارات الرياض وجهات أخرى.',
		'أسّست وشاركت في تأسيس إدارات التميز المؤسسي والرقابة الداخلية والحوكمة والمخاطر، إضافة إلى عدد من صناديق رأس المال الجريء المرخّصة من هيئة السوق المالية.',
		'حاصلة على بكالوريوس العلوم من جامعة الملك سعود بالرياض، وماجستير إدارة الأعمال (مالية) من Dublin City University، وماجستير في الإدارة الرياضية والقانون الرياضي من ISD.',
	) ),
	'marwa' => array( 'م', 'مروة مراد', 'عضو مجلس إدارة مستقل', array(
		'تخرجت السيدة مروة مراد في جامعة القاهرة بدرجة البكالوريوس في الهندسة المعمارية مع مرتبة الشرف عام 2003، وحصلت على الماجستير في الهندسة المعمارية من الجامعة نفسها عام 2008. وهي مهندسة محترفة معتمدة من الهيئة السعودية للمهندسين منذ عام 2018، وحاصلة على شهادة محترف إدارة المشاريع (PMP) منذ عام 2009.',
		'لديها أكثر من 18 عامًا من الخبرة العملية في قيادة فرق التصميم والإشراف على المشاريع ومتابعتها، بما يشمل الجدولة والتخطيط ومراقبة التكاليف، وفي تحليل وتصميم المباني السكنية والتجارية والإدارية ومباني الضيافة. واكتسبت خبرة في تنسيق ومراجعة الرسومات التنفيذية المعمارية ضمن مشاريع دولية بالتعاون مع شركات متعددة الجنسيات.',
		'كما تمتلك خبرة في الإشراف على التنفيذ والتنسيق مع الإدارات الأخرى والتعامل مع البلديات والجهات المحلية، وتشغل حاليًا منصب الرئيسة التنفيذية لمكتب MDMS.',
	) ),
	'rana' => array( 'ر', 'رنا العمير', 'عضو مجلس إدارة غير مستقل', array(
		'تتمتع السيدة رنا العمير بخبرة تزيد على 12 عامًا في قطاع الخدمات المالية بالمملكة العربية السعودية، وهي خبيرة في مبادرات الامتثال التنظيمي المصرفي وتقييم مخاطر المنشآت والتحقيق في حالات الاحتيال. اجتازت اختبار الاعتماد في مكافحة غسل الأموال (CAMS)، وحصلت على الشهادات التنظيمية اللازمة للامتثال من هيئة السوق المالية، وتشغل حاليًا منصب رئيسة قسم الالتزام في مجموعة النفيعي للاستثمار.',
		'إلى جانب أدوارها المهنية، هي عضو في لجان المراجعة في عدد من الشركات منها مجموعة لومي سيرا وتنمية كابيتال. حاصلة على ماجستير إدارة الأعمال من جامعة الفيصل وبكالوريوس اللغة الإنجليزية من جامعة الملك سعود، وأتمّت عددًا من الدورات والتأهيلات منها CME 1 و2.',
	) ),
	'ahmed' => array( 'أ', 'أحمد فوزي', 'نائب الرئيس التنفيذي ومدير إدارة المشورة', array(
		'يتمتع السيد أحمد فوزي بخبرة تزيد على 12 عامًا في عدد من شركات القطاع المالي، ساعد خلالها شركات عدة في الحصول على ترخيص هيئة السوق المالية، وقدّم استشارات مالية في دراسات الجدوى الاقتصادية وتقييم الشركات في قطاعات مختلفة.',
		'أتمّ عددًا من الدورات في المجال المالي، وهو حاصل على بكالوريوس التجارة وإدارة الأعمال من مصر.',
	), 'advisoryteam@esnadadvisory.sa' ),
	'haitham' => array( 'ه', 'هيثم سلامة', 'الرئيس التنفيذي ومدير المصرفية الاستثمارية', array(
		'مدير ذو خبرة متميزة في الخدمات المالية وسجل حافل في هذا المجال. متخصص في التخطيط الاستراتيجي للأعمال ودراسات الجدوى وتمويل الشركات والتقييم والتحليل المالي.',
		'حاصل على ماجستير إدارة الأعمال من جامعة ريادة الأعمال والأعمال (EBU).',
	), 'arr@esnadadvisory.sa' ),
	'bahaa' => array( 'ب', 'بهاء الدين أحمد', 'المدير المالي', array(
		'يتمتع الأستاذ بهاء الدين أحمد بخبرة في الشركات المالية في مجالات الإدارة المالية وإعداد التقارير المالية والأعمال الإدارية.',
		'حاصل على شهادة محاسب مالي محترف وشهادة المحاسبة الشاملة، وبكالوريوس التجارة تخصص محاسبة من مصر.',
	), 'cfo@esnadadvisory.sa' ),
);
$en = array(
	'latifa' => array( 'L', 'Latifa Al-Hudaib', 'Chair of the Board and Managing Director', array(
		'Ms. Latifa Al-Hudaib has more than 15 years of experience in banking, investment and strategic services. She has held several positions at Banque Saudi Fransi, Arab Investment Company, Riyadh Airports Company and others.',
		'She established, and contributed to establishing, the institutional excellence, internal control, governance and risk functions, as well as several venture capital funds licensed by the Capital Market Authority.',
		'She holds a Bachelor of Science from King Saud University in Riyadh, a Master of Business Administration (Finance) from Dublin City University, and a Master\'s in Sports Management and Sports Law from ISD.',
	) ),
	'marwa' => array( 'M', 'Marwa Murad', 'Independent Board Member', array(
		'Ms. Marwa Murad graduated from Cairo University with honors in architectural engineering in 2003 and holds a master\'s degree in architecture from the same university (2008). She is a professional engineer accredited by the Saudi Council of Engineers since 2018 and a Project Management Professional (PMP) since 2009.',
		'She has more than 18 years of practical experience leading design teams and supervising and controlling projects, including scheduling, planning and cost control, and in analyzing and designing residential, commercial, administrative and hospitality buildings. She has coordinated and reviewed architectural shop drawings on international projects with multinational companies.',
		'She also has experience supervising implementation, coordinating with other departments and dealing with municipalities and local authorities, and is currently the CEO of MDMS Office.',
	) ),
	'rana' => array( 'R', 'Rana Al-Omair', 'Non-independent Board Member', array(
		'Ms. Rana Al-Omair has more than 12 years of experience in the financial services sector in the Kingdom of Saudi Arabia. She is an expert in banking regulatory compliance initiatives, enterprise risk assessment and fraud investigations. She passed the Certified Anti-Money Laundering Specialist (CAMS) exam, holds the regulatory compliance certifications required by the Capital Market Authority, and is currently Head of Compliance at Al-Nafie Investment Group.',
		'She also sits on the audit committees of several companies, including Lumi Sera Group and Tanmia Capital. She holds a master\'s degree in business administration from Alfaisal University and a bachelor\'s degree in English from King Saud University, and has completed several courses and qualifications, including CME 1 & 2.',
	) ),
	'ahmed' => array( 'A', 'Ahmed Fawzy', 'Deputy Chief Executive Officer and Head of Advisory', array(
		'Mr. Ahmed Fawzy has more than 12 years of experience in a number of financial sector companies. He has helped several companies obtain a Capital Market Authority license and has provided financial advice on economic feasibility studies and company valuation across different sectors.',
		'He has completed a number of courses in finance and holds a bachelor\'s degree in commerce and business administration from Egypt.',
	), 'advisoryteam@esnadadvisory.sa' ),
	'haitham' => array( 'H', 'Haitham Salamah', 'Chief Executive Officer and Head of Investment Banking', array(
		'Haitham is an experienced manager with a strong record in financial services, skilled in business planning, feasibility studies, corporate finance, valuation and financial analysis.',
		'He holds a Master of Business Administration (MBA) from Entrepreneurship and Business University (EBU).',
	), 'arr@esnadadvisory.sa' ),
	'bahaa' => array( 'B', 'Bahaa El Din Ahmed', 'Chief Financial Officer', array(
		'Mr. Bahaa El Din Ahmed has experience in financial companies in financial management, financial reporting and administrative work.',
		'He holds a professional financial accountant certificate and a comprehensive accounting certificate, and a bachelor\'s degree in commerce specializing in accounting from Egypt.',
	), 'cfo@esnadadvisory.sa' ),
);

function esnad_mk( array $p, $mail = null ) {
	return esnad_person( $p[0], $p[1], $p[2], $p[3], $mail ?? ( $p[4] ?? '' ) );
}
$pages = array(
	'board-ar'                 => array( $ar, array( 'latifa', 'marwa', 'rana' ), array() ),
	'management-ar'            => array( $ar, array( 'latifa', 'ahmed', 'haitham', 'bahaa' ), array( 'latifa' => 'ceo@esnadadvisory.sa' ) ),
	'board-en'                 => array( $en, array( 'latifa', 'marwa', 'rana' ), array() ),
	'executive-management-en'  => array( $en, array( 'latifa', 'ahmed', 'haitham', 'bahaa' ), array( 'latifa' => 'ceo@esnadadvisory.sa' ) ),
);
foreach ( $pages as $slug => $def ) {
	$id = (int) $wpdb->get_var( $wpdb->prepare( "select ID from {$wpdb->posts} where post_name=%s and post_type='page' and post_status='publish' limit 1", $slug ) );
	if ( ! $id ) { echo "MISSING $slug\n"; continue; }
	$items = array();
	foreach ( $def[1] as $k ) {
		$p      = $def[0][ $k ];
		$mail   = $def[2][ $k ] ?? ( $p[4] ?? '' );
		$items[] = esnad_person( $p[0], $p[1], $p[2], $p[3], $mail );
	}
	$wpdb->update( $wpdb->posts, array( 'post_content' => esnad_people( $items ) ), array( 'ID' => $id ) );
	clean_post_cache( $id );
	echo "updated $slug ($id)\n";
}

// Remove the leaked chat transcript line and any other AI residue from page bodies.
foreach ( $wpdb->get_results( "select ID, post_content from {$wpdb->posts} where post_type in ('page','post') and post_content like '%ChatGPT said%'" ) as $r ) {
	$c = preg_replace( '#(<[^>]+>\s*)*ChatGPT said:\s*(</[^>]+>)?#iu', '', $r->post_content );
	$c = preg_replace( '#ChatGPT said:\s*#iu', '', $c );
	$wpdb->update( $wpdb->posts, array( 'post_content' => $c ), array( 'ID' => $r->ID ) );
	clean_post_cache( $r->ID );
	echo "cleaned ChatGPT residue in {$r->ID}\n";
}
