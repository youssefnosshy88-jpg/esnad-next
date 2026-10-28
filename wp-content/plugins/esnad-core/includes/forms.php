<?php
/**
 * Forms: [esnad_consultation_form], [esnad_contact_form], [esnad_complaint_form].
 * Submissions are validated on the server, stored as private "esnad_lead" posts and e-mailed to the recipient.
 * The public forms never ask for bank credentials, card numbers, national IDs or file attachments.
 */

defined( 'ABSPATH' ) || exit;

/** Language of the current request (theme helper when available). */
function esnad_core_ar(): bool {
	return function_exists( 'esnad_is_rtl' ) ? esnad_is_rtl() : ( 0 !== strpos( determine_locale(), 'en' ) );
}

function esnad_core_t( string $ar, string $en ): string {
	return esnad_core_ar() ? $ar : $en;
}

/** Private storage so staff can review submissions in the admin; nothing is public. */
add_action( 'init', static function (): void {
	register_post_type( 'esnad_lead', array(
		'labels'          => array( 'name' => 'Form submissions', 'singular_name' => 'Submission' ),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-email-alt',
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

/** Field definitions per form. */
function esnad_core_services_options(): array {
	if ( function_exists( 'esnad_services' ) ) {
		return wp_list_pluck( esnad_services(), 'title' );
	}
	return array();
}

function esnad_core_render( string $type ): string {
	$ar     = esnad_core_ar();
	$status = isset( $_GET['esnad'] ) ? sanitize_key( wp_unslash( $_GET['esnad'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$uid    = 'esnad-' . $type;
	ob_start();

	if ( 'ok' === $status && isset( $_GET['f'] ) && sanitize_key( wp_unslash( $_GET['f'] ) ) === $type ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo '<div class="form-note form-note--ok" role="status"><strong>' . esc_html( esnad_core_t( 'تم استلام رسالتك.', 'Your message has been received.' ) ) . '</strong> ' . esc_html( esnad_core_t( 'سيتواصل معك فريقنا قريبًا.', 'Our team will get back to you shortly.' ) ) . '</div>';
		return (string) ob_get_clean();
	}
	if ( 'err' === $status ) {
		echo '<div class="form-note form-note--err" role="alert">' . esc_html( esnad_core_t( 'تعذر إرسال النموذج. يرجى مراجعة الحقول المطلوبة والمحاولة مرة أخرى.', 'The form could not be sent. Please check the required fields and try again.' ) ) . '</div>';
	}

	$steps = 'consultation' === $type;
	?>
	<form class="esnad-form<?php echo $steps ? ' is-steps' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate<?php echo $steps ? ' data-steps' : ''; ?>>
		<input type="hidden" name="action" value="esnad_submit">
		<input type="hidden" name="form_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="redirect" value="<?php echo esc_url( get_permalink() ?: home_url( '/' ) ); ?>">
		<input type="hidden" name="ts" value="<?php echo esc_attr( (string) time() ); ?>">
		<?php wp_nonce_field( 'esnad_submit_' . $type, '_esnad_nonce' ); ?>
		<div class="hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

		<?php if ( $steps ) : ?>
			<ol class="steps-bar" aria-hidden="true">
				<?php foreach ( array( esnad_core_t( 'الخدمة', 'Service' ), esnad_core_t( 'الجهة', 'Organization' ), esnad_core_t( 'الاحتياج', 'Need' ), esnad_core_t( 'بيانات التواصل', 'Contact' ), esnad_core_t( 'الإرسال', 'Submit' ) ) as $i => $s ) : ?>
					<li<?php echo 0 === $i ? ' class="is-on"' : ''; ?>><span><?php echo (int) ( $i + 1 ); ?></span><?php echo esc_html( $s ); ?></li>
				<?php endforeach; ?>
			</ol>

			<fieldset class="step" data-step="0">
				<legend><?php echo esc_html( esnad_core_t( 'الخدمة المطلوبة', 'Service of interest' ) ); ?></legend>
				<div class="choices">
					<?php foreach ( esnad_core_services_options() as $k => $label ) : ?>
						<label class="choice"><input type="radio" name="service" value="<?php echo esc_attr( $label ); ?>" required><span><?php echo esc_html( $label ); ?></span></label>
					<?php endforeach; ?>
					<label class="choice"><input type="radio" name="service" value="<?php echo esc_attr( esnad_core_t( 'غير متأكد', 'Not sure yet' ) ); ?>"><span><?php echo esc_html( esnad_core_t( 'غير متأكد بعد', 'Not sure yet' ) ); ?></span></label>
				</div>
			</fieldset>

			<fieldset class="step" data-step="1">
				<legend><?php echo esc_html( esnad_core_t( 'معلومات الجهة', 'Organization information' ) ); ?></legend>
				<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-org"><?php echo esc_html( esnad_core_t( 'اسم الجهة', 'Organization name' ) ); ?> *</label><input id="<?php echo esc_attr( $uid ); ?>-org" name="organization" type="text" maxlength="150" required></p>
				<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-sector"><?php echo esc_html( esnad_core_t( 'القطاع (اختياري)', 'Sector (optional)' ) ); ?></label><input id="<?php echo esc_attr( $uid ); ?>-sector" name="sector" type="text" maxlength="100"></p>
			</fieldset>

			<fieldset class="step" data-step="2">
				<legend><?php echo esc_html( esnad_core_t( 'الاحتياج بشكل عام', 'General business need' ) ); ?></legend>
				<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-need"><?php echo esc_html( esnad_core_t( 'صف احتياجك باختصار', 'Briefly describe your need' ) ); ?> *</label><textarea id="<?php echo esc_attr( $uid ); ?>-need" name="message" rows="5" maxlength="2000" required></textarea></p>
				<p class="form-hint"><?php echo esc_html( esnad_core_t( 'يرجى عدم كتابة بيانات مصرفية أو أرقام حسابات أو هويات في هذا النموذج.', 'Please do not include bank details, account numbers or ID numbers in this form.' ) ); ?></p>
			</fieldset>

			<fieldset class="step" data-step="3">
				<legend><?php echo esc_html( esnad_core_t( 'بيانات التواصل', 'Contact information' ) ); ?></legend>
				<?php esnad_core_contact_fields( $uid ); ?>
			</fieldset>

			<fieldset class="step" data-step="4">
				<legend><?php echo esc_html( esnad_core_t( 'مراجعة وإرسال', 'Review and submit' ) ); ?></legend>
				<?php esnad_core_privacy( $uid ); ?>
			</fieldset>

			<div class="form-nav">
				<button type="button" class="btn btn--ghost-dark" data-prev><?php echo esc_html( esnad_core_t( 'السابق', 'Back' ) ); ?></button>
				<button type="button" class="btn btn--gold" data-next><?php echo esc_html( esnad_core_t( 'التالي', 'Next' ) ); ?></button>
				<button type="submit" class="btn btn--gold" data-submit><?php echo esc_html( esnad_core_t( 'إرسال الطلب', 'Send request' ) ); ?></button>
			</div>

		<?php elseif ( 'complaint' === $type ) : ?>
			<?php esnad_core_contact_fields( $uid ); ?>
			<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-subject"><?php echo esc_html( esnad_core_t( 'موضوع الشكوى', 'Subject' ) ); ?> *</label><input id="<?php echo esc_attr( $uid ); ?>-subject" name="subject" type="text" maxlength="150" required></p>
			<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-msg"><?php echo esc_html( esnad_core_t( 'تفاصيل الشكوى', 'Details' ) ); ?> *</label><textarea id="<?php echo esc_attr( $uid ); ?>-msg" name="message" rows="6" maxlength="3000" required></textarea></p>
			<p class="form-hint"><?php echo esc_html( esnad_core_t( 'لا ترفق ملفات أو بيانات مصرفية في هذا النموذج. سنطلب أي مستندات لازمة عبر التواصل المباشر.', 'Do not attach files or bank details here. We will request any documents needed through direct contact.' ) ); ?></p>
			<?php esnad_core_privacy( $uid ); ?>
			<p><button type="submit" class="btn btn--gold"><?php echo esc_html( esnad_core_t( 'إرسال الشكوى', 'Submit complaint' ) ); ?></button></p>

		<?php else : ?>
			<?php esnad_core_contact_fields( $uid ); ?>
			<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-msg"><?php echo esc_html( esnad_core_t( 'رسالتك', 'Your message' ) ); ?> *</label><textarea id="<?php echo esc_attr( $uid ); ?>-msg" name="message" rows="5" maxlength="2000" required></textarea></p>
			<?php esnad_core_privacy( $uid ); ?>
			<p><button type="submit" class="btn btn--gold"><?php echo esc_html( esnad_core_t( 'إرسال', 'Send' ) ); ?></button></p>
		<?php endif; ?>
	</form>
	<?php
	return (string) ob_get_clean();
}

function esnad_core_contact_fields( string $uid ): void {
	?>
	<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-name"><?php echo esc_html( esnad_core_t( 'الاسم', 'Name' ) ); ?> *</label><input id="<?php echo esc_attr( $uid ); ?>-name" name="name" type="text" maxlength="100" autocomplete="name" required></p>
	<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-email"><?php echo esc_html( esnad_core_t( 'البريد الإلكتروني', 'Email' ) ); ?> *</label><input id="<?php echo esc_attr( $uid ); ?>-email" name="email" type="email" maxlength="120" autocomplete="email" required dir="ltr"></p>
	<p class="field"><label for="<?php echo esc_attr( $uid ); ?>-phone"><?php echo esc_html( esnad_core_t( 'رقم الجوال (اختياري)', 'Phone (optional)' ) ); ?></label><input id="<?php echo esc_attr( $uid ); ?>-phone" name="phone" type="tel" maxlength="25" autocomplete="tel" dir="ltr"></p>
	<?php
}

function esnad_core_privacy( string $uid ): void {
	?>
	<p class="field field--check"><label><input type="checkbox" name="privacy" value="1" required> <span><?php echo esc_html( esnad_core_t( 'أوافق على استخدام بياناتي للرد على طلبي فقط، وفق نظام حماية البيانات الشخصية.', 'I agree that my data is used only to respond to my request, in line with the Personal Data Protection Law.' ) ); ?> *</span></label></p>
	<?php
}

add_shortcode( 'esnad_consultation_form', static fn(): string => esnad_core_render( 'consultation' ) );
add_shortcode( 'esnad_contact_form', static fn(): string => esnad_core_render( 'contact' ) );
add_shortcode( 'esnad_complaint_form', static fn(): string => esnad_core_render( 'complaint' ) );

/** Handler. */
add_action( 'admin_post_nopriv_esnad_submit', 'esnad_core_handle' );
add_action( 'admin_post_esnad_submit', 'esnad_core_handle' );

function esnad_core_back( string $redirect, string $state, string $type ): void {
	$url = wp_validate_redirect( $redirect, home_url( '/' ) );
	wp_safe_redirect( add_query_arg( array( 'esnad' => $state, 'f' => $type ), $url ) . '#esnad-' . $type );
	exit;
}

function esnad_core_handle(): void {
	$type     = isset( $_POST['form_type'] ) ? sanitize_key( wp_unslash( $_POST['form_type'] ) ) : '';
	$redirect = isset( $_POST['redirect'] ) ? esc_url_raw( wp_unslash( $_POST['redirect'] ) ) : home_url( '/' );
	if ( ! in_array( $type, array( 'consultation', 'contact', 'complaint' ), true ) ) {
		wp_die( 'Invalid request', '', array( 'response' => 400 ) );
	}
	if ( ! isset( $_POST['_esnad_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_esnad_nonce'] ) ), 'esnad_submit_' . $type ) ) {
		esnad_core_back( $redirect, 'err', $type );
	}
	// Honeypot and minimum fill time (bots submit instantly).
	$age = time() - (int) ( $_POST['ts'] ?? 0 );
	if ( ! empty( $_POST['website'] ) || $age < 4 || $age > DAY_IN_SECONDS ) {
		esnad_core_back( $redirect, 'ok', $type ); // Pretend success; do not help bots.
	}
	// Rate limit: 5 submissions per hour per IP.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'esnad_rl_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) {
		esnad_core_back( $redirect, 'err', $type );
	}
	set_transient( $key, $n + 1, HOUR_IN_SECONDS );

	$f = array(
		'name'         => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
		'email'        => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
		'phone'        => sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ),
		'organization' => sanitize_text_field( wp_unslash( $_POST['organization'] ?? '' ) ),
		'sector'       => sanitize_text_field( wp_unslash( $_POST['sector'] ?? '' ) ),
		'service'      => sanitize_text_field( wp_unslash( $_POST['service'] ?? '' ) ),
		'subject'      => sanitize_text_field( wp_unslash( $_POST['subject'] ?? '' ) ),
		'message'      => sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) ),
	);
	$ok = '' !== $f['name'] && is_email( $f['email'] ) && '' !== $f['message'] && ! empty( $_POST['privacy'] );
	if ( 'consultation' === $type ) {
		$ok = $ok && '' !== $f['service'] && '' !== $f['organization'];
	}
	if ( 'complaint' === $type ) {
		$ok = $ok && '' !== $f['subject'];
	}
	// Refuse obvious sensitive data: long digit runs (cards, IBANs, IDs).
	if ( preg_match( '/\d{13,}|SA\d{2}\s?\d{4}/i', $f['message'] ) ) {
		$ok = false;
	}
	if ( ! $ok ) {
		esnad_core_back( $redirect, 'err', $type );
	}

	$labels = array( 'consultation' => 'Consultation request', 'contact' => 'Contact message', 'complaint' => 'Complaint' );
	$title  = $labels[ $type ] . ' - ' . $f['name'];
	$body   = '';
	foreach ( $f as $k => $v ) {
		if ( '' !== $v ) {
			$body .= ucfirst( $k ) . ': ' . $v . "\n";
		}
	}
	wp_insert_post( array( 'post_type' => 'esnad_lead', 'post_status' => 'private', 'post_title' => $title, 'post_content' => $body ) );

	$to = get_option( 'esnad_form_recipient' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	wp_mail( $to, '[Esnad website] ' . $title, $body, array( 'Reply-To: ' . $f['name'] . ' <' . $f['email'] . '>' ) );

	esnad_core_back( $redirect, 'ok', $type );
}

/** Styles and the step script (loaded only on pages that contain a form). */
add_action( 'wp_enqueue_scripts', static function (): void {
	global $post;
	if ( ! $post || ! preg_match( '/\[esnad_(consultation|contact|complaint)_form/', (string) $post->post_content ) ) {
		return;
	}
	wp_enqueue_style( 'esnad-forms', ESNAD_CORE_URL . 'assets/forms.css', array(), ESNAD_CORE_VER );
	wp_enqueue_script( 'esnad-forms', ESNAD_CORE_URL . 'assets/forms.js', array(), ESNAD_CORE_VER, array( 'strategy' => 'defer', 'in_footer' => true ) );
} );
