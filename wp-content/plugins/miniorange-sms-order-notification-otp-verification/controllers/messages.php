<?php
/**
 * Loads admin view for common message tab.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoUtility;

$nonce                = $admin_handler->get_nonce_value();
$otp_success_email    = get_mo_wc_option( 'success_email_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'success_email_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::OTP_SENT_EMAIL );
$otp_success_phone    = get_mo_wc_option( 'success_phone_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'success_phone_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::OTP_SENT_PHONE );
$otp_error_phone      = get_mo_wc_option( 'error_phone_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'error_phone_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::ERROR_OTP_PHONE );
$otp_error_email      = get_mo_wc_option( 'error_email_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'error_email_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::ERROR_OTP_EMAIL );
$phone_invalid_format = get_mo_wc_option( 'invalid_phone_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'invalid_phone_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::ERROR_PHONE_FORMAT );
$email_invalid_format = get_mo_wc_option( 'invalid_email_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'invalid_email_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::ERROR_EMAIL_FORMAT );
$invalid_otp          = MoUtility::get_invalid_otp_method();
$otp_blocked_email    = get_mo_wc_option( 'blocked_email_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'blocked_email_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::ERROR_EMAIL_BLOCKED );
$otp_blocked_phone    = get_mo_wc_option( 'blocked_phone_message', 'mo_wc_otp_' ) ? get_mo_wc_option( 'blocked_phone_message', 'mo_wc_otp_' ) : MoMessages::showMessage( MoMessages::ERROR_PHONE_BLOCKED );

require MOV_WC_DIR . 'views/messages.php';
