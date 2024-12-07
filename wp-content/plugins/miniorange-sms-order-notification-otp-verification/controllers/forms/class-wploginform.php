<?php
/**
 * Load admin view for WPLoginForm.
 *
 * @package miniorange-order-notifications-woocommerce/controller/forms/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Handler\Forms\WPLoginForm;

$handler                = WPLoginForm::instance();
$wp_login_enabled       = (bool) $handler->is_form_enabled() ? 'checked' : '';
$wp_login_hidden        = 'checked' === $wp_login_enabled ? '' : 'hidden';
$wp_login_enabled_type  = (bool) $handler->savePhoneNumbers() ? 'checked' : '';
$wp_login_field_key     = $handler->get_phone_key_details();
$wp_login_admin         = (bool) $handler->byPassCheckForAdmins() ? 'checked' : '';
$wp_login_with_phone    = (bool) $handler->allowLoginThroughPhone() ? 'checked' : '';
$wp_handle_duplicates   = (bool) $handler->restrict_duplicates() ? 'checked' : '';
$wp_enabled_type        = $handler->get_otp_type_enabled();
$wp_phone_type          = $handler->get_phone_html_tag();
$wp_email_type          = $handler->get_email_html_tag();
$form_name              = $handler->get_form_name();
$skip_pass              = $handler->getSkipPasswordCheck() ? 'checked' : '';
$skip_pass_fallback_div = $handler->getSkipPasswordCheck() ? 'block' : 'hidden';
$skip_pass_fallback     = $handler->getSkipPasswordCheckFallback() ? 'checked' : '';
$user_field_text        = $handler->getUserLabel();
$otpd_enabled           = $handler->isDelayOtp() ? 'checked' : '';
$otpd_enabled_div       = $handler->isDelayOtp() ? 'block' : 'hidden';
$otpd_time_interval     = $handler->getDelayOtpInterval();

require MOV_WC_DIR . 'views/forms/wploginform.php';
