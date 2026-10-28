<?php
/** Grid of the eight service cards (used on the services directory page). */
defined( 'ABSPATH' ) || exit;
?>
<div class="cards">
	<?php foreach ( esnad_services() as $svc ) : ?>
		<a class="card" href="<?php echo esc_url( $svc['url'] ); ?>">
			<span class="card__img"><img src="<?php echo esc_url( $svc['img'] ); ?>" alt="" width="720" height="480" loading="lazy" decoding="async"></span>
			<span class="card__body">
				<span class="card__title"><?php echo esc_html( $svc['title'] ); ?></span>
				<span class="card__text"><?php echo esc_html( $svc['text'] ); ?></span>
				<span class="card__more"><?php echo esc_html( esnad_t( 'اقرأ المزيد', 'Read more' ) ); ?> <?php echo esnad_icon( 'arrow', 18 ); // phpcs:ignore ?></span>
			</span>
		</a>
	<?php endforeach; ?>
</div>
