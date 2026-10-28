<?php
/** Numbered service directory: large titles on one side, a visual panel with summary and link on the other. */
defined( 'ABSPATH' ) || exit;
$services = array_values( esnad_services() );
?>
<div class="svlist" data-svlist>
	<div class="svlist__panel" aria-live="polite">
		<?php foreach ( $services as $i => $svc ) : ?>
			<article class="svlist__slide<?php echo 0 === $i ? ' is-active' : ''; ?>" data-slide="<?php echo (int) $i; ?>"<?php echo 0 === $i ? '' : ' aria-hidden="true"'; ?>>
				<img src="<?php echo esc_url( $svc['img'] ); ?>" alt="" width="720" height="480" loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>" decoding="async">
				<div class="svlist__caption">
					<span class="svlist__cap-no" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h2 class="svlist__cap-title"><?php echo esc_html( $svc['title'] ); ?></h2>
					<p><?php echo esc_html( wp_trim_words( (string) $svc['text'], 26, '…' ) ); ?></p>
					<a class="btn btn--gold btn--sm" href="<?php echo esc_url( $svc['url'] ); ?>"<?php echo 0 === $i ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( esnad_t( 'تفاصيل الخدمة', 'Service details' ) ); ?> <?php echo esnad_icon( 'arrow', 16 ); // phpcs:ignore ?></a>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<ol class="svlist__titles">
		<?php foreach ( $services as $i => $svc ) : ?>
			<li>
				<a class="svlist__item<?php echo 0 === $i ? ' is-active' : ''; ?>" href="<?php echo esc_url( $svc['url'] ); ?>" data-target="<?php echo (int) $i; ?>">
					<span class="svlist__no" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="svlist__name"><?php echo esc_html( $svc['title'] ); ?></span>
					<span class="svlist__go" aria-hidden="true"><?php echo esnad_icon( 'arrow-up-right', 20 ); // phpcs:ignore ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>
</div>
