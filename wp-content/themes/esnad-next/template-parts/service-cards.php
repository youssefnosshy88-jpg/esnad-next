<?php
/** Grid of the eight service cards (used on the services directory page). */
defined( 'ABSPATH' ) || exit;
?>
<div class="cards">
	<?php $n = 0; foreach ( esnad_services() as $svc ) : $n++; ?>
		<a class="card" href="<?php echo esc_url( $svc['url'] ); ?>">
			<span class="card__img"><img src="<?php echo esc_url( $svc['img'] ); ?>" alt="" width="720" height="480" loading="lazy" decoding="async"><b class="card__no" aria-hidden="true"><?php echo esc_html( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ); ?></b><span class="card__go" aria-hidden="true"><?php echo esnad_icon( 'arrow-up-right', 22 ); // phpcs:ignore ?></span></span>
			<span class="card__body">
				<span class="card__title"><?php echo esc_html( $svc['title'] ); ?></span>
				<span class="card__text"><?php echo esc_html( $svc['text'] ); ?></span>
				<span class="card__more"><?php echo esc_html( esnad_t( 'اقرأ المزيد', 'Read more' ) ); ?> <?php echo esnad_icon( 'arrow', 18 ); // phpcs:ignore ?></span>
			</span>
		</a>
	<?php endforeach; ?>
</div>
