/**
 * f(x) ReCaptcha
 *
 * @since 1.0.0
 */
var fxReCaptcha = function() {
	jQuery( '.fx-recaptcha' ).each( function(i) {
		grecaptcha.render( jQuery( this )[0], {
			sitekey: fxReCaptchaData.sitekey,
		} );
	} );
}
