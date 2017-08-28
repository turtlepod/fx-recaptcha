<?php
/**
 * Settings.
 *
 * @since 1.0.0
 */

// Load Class.
fx_ReCAPTCHA_Settings::get_instance();

/**
 * Settings
 *
 * @since 1.0.0
 */
class fx_ReCAPTCHA_Settings {

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

		// Create settings page.
		add_action( 'admin_menu', function() {
			add_options_page(
				$page_title  = __( 'reCAPTCHA Settings', 'fx-recaptcha' ),
				$menu_title  = __( 'reCAPTCHA', 'fx-recaptcha' ),
				$capability  = 'manage_options',
				$menu_slug   = 'fx-recaptcha',
				$function    = function() {
				?>
				<div class="wrap">
					<h1><?php _e( 'reCAPTCHA Settings', 'fx-base' ); ?></h1>
					<form method="post" action="options.php">
						<?php do_settings_sections( 'fx-recaptcha' ); ?>
						<?php settings_fields( 'fx-recaptcha' ); ?>
						<?php submit_button(); ?>
					</form>
				</div><!-- wrap -->
				<?php
				}
			);
		} );

		// Register settings and fields.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register Settings
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		// Register Settings.
		register_setting(
			$option_group      = 'fx-recaptcha',
			$option_name       = 'fx-recaptcha',
			$sanitize_callback = function( $data ) {
				$data['site_key'] = esc_attr( $data['site_key'] );
				$data['secret_key'] = esc_attr( $data['secret_key'] );
				$data['locations'] = is_array( $data['locations'] ) ? $data['locations'] : array();
				return $data;
			}
		);

		// API Settings Section.
		add_settings_section(
			$section_id        = 'fx_recaptcha_api',
			$section_title     = '',
			$callback_function = function () {
				echo wpautop( sprintf( __( 'Get your reCAPTCHA api details at <a href="%s" target="_blank">Google reCAPTCHA Website</a>.', 'fx-recaptcha' ), 'https://www.google.com/recaptcha' ) );
			},
			$settings_slug     = 'fx-recaptcha'
		);

		// Site Key.
		add_settings_field(
			$field_id          = 'fx_recaptcha_api_site_key',
			$field_title       = __( 'Site Key', 'fx-recaptcha' ),
			$callback_function = function() {
				?>
				<p>
					<input class="regular-text" type="text" name="fx-recaptcha[site_key]" value="<?php echo sanitize_text_field( fx_recaptcha_get_option( 'site_key' ) ); ?>">
				</p>
				<?php
			},
			$settings_slug     = 'fx-recaptcha',
			$section_id        = 'fx_recaptcha_api'
		);

		// Secret Key.
		add_settings_field(
			$field_id          = 'fx_recaptcha_api_secret_key',
			$field_title       = __( 'Secret Key', 'fx-recaptcha' ),
			$callback_function = function() {
				?>
				<p>
					<input class="regular-text" type="text" name="fx-recaptcha[secret_key]" value="<?php echo sanitize_text_field( fx_recaptcha_get_option( 'secret_key' ) ); ?>">
				</p>
				<?php
			},
			$settings_slug     = 'fx-recaptcha',
			$section_id        = 'fx_recaptcha_api'
		);

		// Location Settings Section.
		add_settings_section(
			$section_id        = 'fx_recaptcha_location',
			$section_title     = '',
			$callback_function = '__return_false',
			$settings_slug     = 'fx-recaptcha'
		);

		// Enable in.
		add_settings_field(
			$field_id          = 'fx_recaptcha_locations',
			$field_title       = __( 'Enable in', 'fx-recaptcha' ),
			$callback_function = function() {
				$enabled = fx_recaptcha_get_option( 'locations' );
				$enabled = is_array( $enabled ) ? $enabled : array();
				$locations = apply_filters( 'fx_recaptcha_locations', array(
					'wp_login' => __( 'WP Login', 'fx-recaptcha' ),
				) );
				foreach ( $locations as $location => $label ) {
					?>
					<p>
						<label><input type="checkbox" name="fx-recaptcha[locations][]" value="<?php echo esc_attr( $location ); ?>" <?php echo in_array( $location, $enabled ) ? 'checked=checked' :  ''; ?>> <?php echo $label; ?></label>
					</p>
					<?php
				}
			},
			$settings_slug     = 'fx-recaptcha',
			$section_id        = 'fx_recaptcha_location'
		);
	}
}
