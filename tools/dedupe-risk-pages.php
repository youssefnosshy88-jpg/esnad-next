<?php
// One-off clean-up: the two risk-management pages repeat the same four steps three times (builder leftovers).
global $wpdb;
$ol = static function ( array $items ) {
	return "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">" . implode( '', array_map( static fn( $t ) => '<li>' . $t . '</li>', $items ) ) . "</ol>\n<!-- /wp:list -->";
};
$jobs = array(
	'risk-managment-services-2' => array(
		'#<!-- wp:freeform -->\s*<p>إكتشاف وتحديد المخاطر ومعرفة مسبباتها\.</p>.*?وضع الخطط والإستراتيجيات اللازمة والإستعداد لمواجهة تلك المخاطر\.</h3>\s*<!-- /wp:heading -->#us',
		array( 'اكتشاف وتحديد المخاطر ومعرفة مسبباتها.', 'تحليل المخاطر وتقييم أثرها على الممتلكات والأشخاص في حال حدوثها.', 'قياس المخاطر ومعرفة احتمالية تكرار كل خطر والخسائر المصاحبة له.', 'وضع الخطط والاستراتيجيات اللازمة والاستعداد لمواجهة تلك المخاطر.' ),
	),
	'risk-managment-services' => array(
		'#<!-- wp:heading \{"level":3\} -->\s*<h3 class="wp-block-heading">Identifying and understanding risks and their causes\.</h3>.*?<p>Developing the necessary plans and strategies and preparing to address those risks\.</p>\s*<!-- /wp:freeform -->#us',
		array( 'Identifying and understanding risks and their causes.', 'Analyzing risks and assessing their impact on assets and people if they occur.', 'Measuring risks and determining the likelihood of each risk recurring and the associated losses.', 'Developing the necessary plans and strategies and preparing to address those risks.' ),
	),
);
foreach ( $jobs as $slug => $job ) {
	$r = $wpdb->get_row( $wpdb->prepare( "select ID, post_content from {$wpdb->posts} where post_name=%s and post_type='page' and post_status='publish'", $slug ) );
	if ( ! $r ) { echo "MISSING $slug\n"; continue; }
	$n = preg_replace( $job[0], $ol( $job[1] ), $r->post_content, 1, $count );
	if ( $count ) {
		$wpdb->update( $wpdb->posts, array( 'post_content' => $n ), array( 'ID' => $r->ID ) );
		clean_post_cache( $r->ID );
	}
	echo "$slug replaced=$count\n";
}
