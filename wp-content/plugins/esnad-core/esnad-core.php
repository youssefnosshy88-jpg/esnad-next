<?php
/**
 * Plugin Name: ESNAD Core
 * Description: Consultation, contact and complaints forms for Esnad Financial (server-side validation, nonce, honeypot, rate limit, privacy acknowledgement).
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Text Domain: esnad-core
 */

defined( 'ABSPATH' ) || exit;

define( 'ESNAD_CORE_VER', '1.0.0' );
define( 'ESNAD_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ESNAD_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once ESNAD_CORE_DIR . 'includes/forms.php';
