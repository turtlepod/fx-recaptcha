<?php
/**
 * Functions.
 *
 * @since 1.0.0
 */

/**
 * Get Option helper function
 * To get option easier when merging multiple option in single option name.
 *
 * @since 1.0.0
 *
 * @param string       $option      Option Key.
 * @param string|array $default     Default output.
 * @param string       $option_name Option name.
 */
function fx_recaptcha_get_option( $option, $default = '', $option_name = 'fx-recaptcha' ) {

	// Bail early if no option defined.
	if ( ! $option ) {
		return false;
	}

	// Get option from db.
	$get_option = get_option( $option_name );

	// Return false if invalid format (not array).
	if ( ! is_array( $get_option ) ) {
		return $default;
	}

	// Get data if set.
	if ( isset( $get_option[$option] ) ) {
		return $get_option[$option];
	} else {
		return $default;
	}
}

/**
 * Authenticate Captcha
 *
 * @since 1.0.0
 *
 * @return bool
 */
function fx_recaptcha_verify() {
	// Send data to google.
	$raw_response = wp_remote_post( esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify' ),
		array(
			'body' => array(
				'secret'   => fx_recaptcha_get_option( 'secret_key' ),
				'response' => $_POST['g-recaptcha-response'],
				'remoteip' => $_SERVER["REMOTE_ADDR"],
			),
		)
	);

	// Response error, fail.
	if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
		return false;
	}

	// Get data.
	$results = json_decode( trim( wp_remote_retrieve_body( $raw_response ) ), true );

	// If success, pass.
	return isset( $results['success'] ) && $results['success'] ? true : false;
}

/**
 * Register Script
 *
 * @since 1.0.0
 */
function fx_recaptcha_register_script() {

	// Google reCAPTCHA scripts
	wp_register_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js?onload=fxReCaptcha&render=explicit', array(), FX_RECAPTCHA_VERSION, false );

	// Loader callback.
	wp_register_script( 'fx-recaptcha', FX_RECAPTCHA_URI . 'assets/recaptcha.js', array( 'recaptcha', 'jquery' ), FX_RECAPTCHA_VERSION );
	wp_localize_script( 'fx-recaptcha', 'fxReCaptchaData', array(
		'sitekey' => esc_attr( fx_recaptcha_get_option( 'site_key' ) ),
	) );
}
add_action( 'wp_enqueue_scripts', 'fx_recaptcha_register_script', 1 );
add_action( 'admin_enqueue_scripts', 'fx_recaptcha_register_script', 1 );
add_action( 'login_enqueue_scripts', 'fx_recaptcha_register_script', 1 );

/**
 * Add Async and Defer to reCAPTCHA Script Tag.
 *
 * @since 1.0.0
 *
 * @param string $tag    Script tag.
 * @param string $handle Script handle.
 * @return string
 */
function fx_recaptcha_script_tag( $tag, $handle ) {
	if ( 'recaptcha' === $handle ) {
		$tag = str_replace( 'src=', 'async defer src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'fx_recaptcha_script_tag', 10, 2 );

/**
 * reCAPTCHA Field
 *
 * @since 1.0.0
 *
 * @param bool $reload True to add reload script.
 * @return string
 */
function fx_recaptcha_field( $reload = false ) {
	wp_enqueue_script( 'fx-recaptcha' );
	$field = '<div class="fx-recaptcha"></div>';
	if ( $reload ) {
		$field .= fx_recaptcha_reload_script();
	}
	return apply_filters( 'fx_recaptcha_field', $field, $reload );
}

/**
 * ReLoad. Utility script to reload reCAPTCHA script.
 * Useful if the form is loaded via AJAX.
 *
 * @since 1.0.0
 *
 * @return string
 */
function fx_recaptcha_reload_script() {
	wp_enqueue_script( 'fx-recaptcha' );
	ob_start();
	?>
	<script>
	if ( typeof jQuery !== "undefined" ) {
		jQuery( document ).ready( function($) {
			jQuery( '.fx-recaptcha' ).each( function(i) {
				if ( '' === $( this ).html() && typeof grecaptcha !== "undefined" ) {
					grecaptcha.render( jQuery( this )[0], {
						sitekey: '<?php echo esc_attr( fx_recaptcha_get_option( 'site_key' ) ); ?>',
					} );
				}
			} );
		} );
	}
	</script>
	<?php
	return apply_filters( 'fx_recaptcha_reload_script', ob_get_clean() );
}
