<?php
$p = get_post( 29474 );
$c = $p->post_content;
foreach ( array( 'differentiator', 'quality of services', 'Quality' ) as $needle ) {
	$pos = stripos( $c, $needle );
	echo $needle, ' => ', var_export( $pos, true ), ' ', false === $pos ? '' : json_encode( substr( $c, max( 0, $pos - 120 ), 300 ), JSON_UNESCAPED_UNICODE ), "\n";
}
