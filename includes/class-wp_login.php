<?php
/**
 * WP Login.
 *
 * @since 1.0.0
 */

// Load Class.
fx_ReCAPTCHA_WP_Login::get_instance();

/**
 * WP Login
 *
 * @since 1.0.0
 */
class fx_ReCAPTCHA_WP_Login {

	/**
	 * Returns the instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public static function get_instance() {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new self;
		}
		return $instance;
	}

	/**
	 * Class Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add field in wp-login.php
		add_action( 'login_form', array( $this, 'wp_login_recaptcha_field' ) );

		// Add field in custom login form.
		add_filter( 'login_form_middle', array( $this, 'custom_login_recaptcha_field' ) );

		// Authenticate.
		add_filter( 'authenticate', array( $this, 'authenticate' ), 99, 3 );
	}

	/**
	 * reCAPTCHA Field
	 *
	 * @since 1.0.0
	 */
	public function wp_login_recaptcha_field() {
		?>
		<style>
			#login {
				width: 350px;
			}
			.fx-recaptcha {
				margin-bottom: 16px;
			}
		</style>
		<?php
		echo fx_recaptcha_field( true );
	}

	/**
	 * Custom Login reCAPTCHA Field.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function custom_login_recaptcha_field( $middle ) {
		return $middle . fx_recaptcha_field( true );
	}

	/**
	 * Login Auth. If fail, need to return WP_Error.
	 *
	 * @since 1.0.0
	 *
	 * @param null|WP_User|WP_Error $user     User.
	 * @param string                $username User name.
	 * @param string                $password User password.
	 */
	public function authenticate( $user, $username, $password ) {
		if ( isset( $_POST['g-recaptcha-response'] ) ) {
			if ( false === fx_recaptcha_verify() ) {
				return new WP_Error( 'denied', __( 'Captcha failed, are you robot? please try again.','fx-recaptcha' ) );
			}
		}
		return $user;
	}

}
