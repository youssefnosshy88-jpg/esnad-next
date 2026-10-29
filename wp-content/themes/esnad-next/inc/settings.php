<?php
/**
 * Site settings the company can edit without code (Settings > General) and the consent bar.
 *  - esnad_form_recipient: where form submissions are e-mailed (defaults to the admin e-mail).
 *  - esnad_ga4_id: Google Analytics 4 measurement ID. Empty = no analytics and no consent bar.
 * Analytics load only after the visitor accepts; the choice is remembered in the browser.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_init', static function (): void {
	register_setting( 'general', 'esnad_form_recipient', array( 'type' => 'string', 'sanitize_callback' => 'sanitize_email' ) );
	register_setting( 'general', 'esnad_ga4_id', array( 'type' => 'string', 'sanitize_callback' => static fn( $v ) => preg_match( '/^G-[A-Z0-9]{6,14}$/', strtoupper( trim( (string) $v ) ) ) ? strtoupper( trim( (string) $v ) ) : '' ) );
	add_settings_section( 'esnad', 'إسناد - Esnad', static function (): void {
		echo '<p>' . esc_html__( 'Form recipient and analytics for the Esnad theme.', 'esnad-next' ) . '</p>';
	}, 'general' );
	add_settings_field( 'esnad_form_recipient', 'بريد استقبال النماذج (Form recipient e-mail)', static function (): void {
		printf( '<input type="email" class="regular-text" name="esnad_form_recipient" value="%s" placeholder="%s"><p class="description">%s</p>', esc_attr( (string) get_option( 'esnad_form_recipient', '' ) ), esc_attr( (string) get_option( 'admin_email' ) ), esc_html( 'Empty = the site administrator e-mail.' ) );
	}, 'general', 'esnad' );
	add_settings_field( 'esnad_ga4_id', 'معرّف Google Analytics 4 (G-XXXXXXXXXX)', static function (): void {
		printf( '<input type="text" class="regular-text" name="esnad_ga4_id" value="%s" placeholder="G-XXXXXXXXXX"><p class="description">%s</p>', esc_attr( (string) get_option( 'esnad_ga4_id', '' ) ), esc_html( 'Empty = no analytics and no consent bar. Analytics run only after the visitor accepts.' ) );
	}, 'general', 'esnad' );
} );

function esnad_consent_enabled(): bool {
	return '' !== (string) get_option( 'esnad_ga4_id', '' );
}

add_action( 'wp_footer', static function (): void {
	if ( ! esnad_consent_enabled() ) {
		return;
	}
	$priv = get_page_by_path( esnad_is_rtl() ? 'privacy-policy' : 'privacy-policy-en' );
	?>
	<div class="consent" data-consent data-ga="<?php echo esc_attr( (string) get_option( 'esnad_ga4_id' ) ); ?>" role="region" aria-label="<?php echo esc_attr( esnad_t( 'ملفات الارتباط', 'Cookies' ) ); ?>" hidden>
		<p><?php echo esc_html( esnad_t( 'نستخدم ملفات تحليلية لفهم استخدام الموقع، ولا نفعّلها إلا بموافقتك.', 'We use analytics cookies to understand site usage, and only switch them on with your consent.' ) ); ?>
			<?php if ( $priv ) : ?><a href="<?php echo esc_url( get_permalink( $priv ) ); ?>"><?php echo esc_html( esnad_t( 'سياسة الخصوصية', 'Privacy policy' ) ); ?></a><?php endif; ?></p>
		<div class="consent__actions">
			<button type="button" class="btn btn--gold btn--sm" data-consent-yes><?php echo esc_html( esnad_t( 'أوافق', 'Accept' ) ); ?></button>
			<button type="button" class="btn btn--line btn--sm" data-consent-no><?php echo esc_html( esnad_t( 'أرفض', 'Decline' ) ); ?></button>
		</div>
	</div>
	<?php
}, 30 );
