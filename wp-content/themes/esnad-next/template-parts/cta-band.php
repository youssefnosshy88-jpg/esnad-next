<?php
/** Closing call-to-action band shown at the end of every inner page. */
defined( 'ABSPATH' ) || exit;
?>
<section class="cta-band" aria-labelledby="cta-band-title">
	<div class="container cta-band__in">
		<div>
			<p class="eyebrow"><?php echo esc_html( esnad_t( 'ابدأ بحوار واضح', 'Start with a clear conversation' ) ); ?></p>
			<h2 id="cta-band-title"><?php echo esc_html( esnad_t( 'القرار المدروس يبدأ بحوار واضح.', 'A considered decision starts with a clear conversation.' ) ); ?></h2>
		</div>
		<div class="cta-band__act">
			<a class="btn btn--gold" href="<?php echo esc_url( esnad_consult_url() ); ?>"><?php echo esc_html( esnad_t( 'اطلب استشارة', 'Request a consultation' ) ); ?></a>
			<a class="btn btn--outline" href="tel:<?php echo esc_attr( esnad_company()['phone_tel'] ); ?>" dir="ltr"><?php echo esc_html( esnad_company()['phone'] ); ?></a>
		</div>
	</div>
</section>
