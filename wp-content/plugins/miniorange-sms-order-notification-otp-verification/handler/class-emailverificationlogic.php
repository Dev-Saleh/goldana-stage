<?php
/**
 * Comman handler to handle the email logic during phone verification.
 *
 * @package miniorange-order-notifications-woocommerce/handler.
 */

namespace WCSMSOTP\Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\FormSessionVars;
use WCSMSOTP\Helper\GatewayFunctions;
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Helper\SessionUtils;
use WCSMSOTP\Objects\VerificationLogic;
use WCSMSOTP\Traits\Instance;


if ( ! class_exists( 'EmailVerificationLogic' ) ) {
	/**
 * This class handles all the email related logic for OTP Verification
 * Process the email address and starts the Email verification process.
 */
	final class EmailVerificationLogic extends VerificationLogic {

		use Instance;

		/**
		 * This function is called to handle Email Verification request. Processes
		 * the request and starts the OTP Verification process.
		 *
		 * @param string $user_login    username of the user.
		 * @param string $user_email    email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type      email or sms verification.
		 * @param string $from_both     has user enabled from both.
		 */
		public function mowc_handle_logic( $user_login, $user_email, $phone_number, $otp_type, $from_both ) {
			$this->checkIfUserRegistered( $otp_type, $from_both );
			if ( is_email( $user_email ) ) {
				$this->mowc_handle_matched( $user_login, $user_email, $phone_number, $otp_type, $from_both );
			} else {
				$this->mowc_handle_not_matched( $user_email, $otp_type, $from_both );
			}
		}


		/**
		 * Funtion checks if the user is registered with miniorange and show the error message if not registered.
		 *
		 * @param array $otp_type email or sms verification.
		 * @param array $from_both has user enabled from both.
		 * @return void
		 */
		private function checkIfUserRegistered( $otp_type, $from_both ) {
			if ( ! MoUtility::micr() ) {
				$message = MoMessages::showMessage( MoMessages::NEED_TO_REGISTER );
				if ( $this->is_ajax_form() ) {

					wp_send_json( MoUtility::create_json( $message, MoConstants::ERROR_JSON_TYPE ) );
				} else {
					miniorange_wc_site_otp_validation_form( null, null, null, $message, $otp_type, $from_both );
				}
			}
		}


		/**
		 * This function starts the OTP Verification process if email address matches the
		 * correct format and is not blocked by the admin.
		 *
		 * @param string $user_login    username of the user.
		 * @param string $user_email    email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type      email or sms verification.
		 * @param string $from_both     has user enabled from both option.
		 */
		public function mowc_handle_matched( $user_login, $user_email, $phone_number, $otp_type, $from_both ) {
			$message = str_replace( '##email##', $user_email, $this->mowc_get_is_blocked_message() );
			if ( $this->mowc_is_blocked( $user_email, $phone_number ) ) {
				if ( $this->is_ajax_form() ) {
					wp_send_json( MoUtility::create_json( $message, MoConstants::ERROR_JSON_TYPE ) );
				} else {
					miniorange_wc_site_otp_validation_form( null, null, null, $message, $otp_type, $from_both );
				}
			} else {
				$this->mowc_start_otp_verification( $user_login, $user_email, $phone_number, $otp_type, $from_both );
			}
		}


		/**
		 * This function handles what message needs to be shown to the user if email
		 * doesn't match the correct format. Check if admin has set any message, and
		 * check if the form is an ajax form to show the message in the correct format.
		 *
		 * @param string $user_email    the phone number being processed.
		 * @param string $otp_type      email or sms verification.
		 * @param string $from_both     has user enabled from both.
		 */
		public function mowc_handle_not_matched( $user_email, $otp_type, $from_both ) {
			$message = str_replace( '##email##', $user_email, $this->mowc_get_otp_invalid_format_message() );
			if ( $this->is_ajax_form() ) {
				wp_send_json( MoUtility::create_json( $message, MoConstants::ERROR_JSON_TYPE ) );
			} else {
				miniorange_wc_site_otp_validation_form( null, null, null, $message, $otp_type, $from_both );
			}
		}


		/**
		 * This function is called to handle Email Verification request. Processes
		 * the request and starts the OTP Verification process to send OTP to user's
		 * email address.
		 *
		 * @param string $user_login    username of the user.
		 * @param string $user_email    email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type      email or sms verification.
		 * @param string $from_both     string has user enabled from both.
		 */
		public function mowc_start_otp_verification( $user_login, $user_email, $phone_number, $otp_type, $from_both ) {
			$gateway = GatewayFunctions::instance();
			$content = $gateway->mo_send_otp_token( 'EMAIL', $user_email, '' );
			switch ( $content['status'] ) {
				case 'SUCCESS':
					$this->mowc_handle_otp_sent( $user_login, $user_email, $phone_number, $otp_type, $from_both, $content );
					break;
				default:
					$this->mowc_handle_otp_sent_failed( $user_login, $user_email, $phone_number, $otp_type, $from_both, $content );
					break;
			}
		}


		/**
		 * This function is called to handle what needs to be done when OTP sending is successful.
		 * Checks if the current form is an AJAX form and decides what message has to be
		 * shown to the user.
		 *
		 * @param string $user_login    username of the user.
		 * @param string $user_email    email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type      email or sms verification.
		 * @param string $from_both     has user enabled from both.
		 * @param array  $content        string the json decoded response from server.
		 */
		public function mowc_handle_otp_sent( $user_login, $user_email, $phone_number, $otp_type, $from_both, $content ) {
			SessionUtils::set_email_transaction_id( $content['txId'] );

			if ( MoUtility::micr() && MoUtility::is_mg() ) {
				$avail_email = get_mo_wc_option( 'email_transactions_remaining' );
				if ( ( $avail_email > 0 ) && ( MO_WC_TEST_MODE === false ) ) {
					update_mo_wc_option( 'email_transactions_remaining', $avail_email - 1 );
				}
			}

			$message = str_replace( '##email##', $user_email, $this->mowc_get_otp_sent_message() );

			if ( $this->is_ajax_form() ) {
				wp_send_json( MoUtility::create_json( $message, MoConstants::SUCCESS_JSON_TYPE ) );
			} else {
				miniorange_wc_site_otp_validation_form( $user_login, $user_email, $phone_number, $message, $otp_type, $from_both );
			}
		}


		/**
		 * This function is called to handle what needs to be done when OTP sending fails.
		 * Checks if the current form is an AJAX form and decides what message has to be
		 * shown to the user.
		 *
		 * @param string $user_login    username of the user.
		 * @param string $user_email    email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type      email or sms verification.
		 * @param string $from_both     has user enabled from both.
		 * @param array  $content       the json decoded response from server.
		 */
		public function mowc_handle_otp_sent_failed( $user_login, $user_email, $phone_number, $otp_type, $from_both, $content ) {
			$message = str_replace( '##email##', $user_email, $this->mowc_get_otp_sent_failed_message() );

			if ( $this->is_ajax_form() ) {
				wp_send_json( MoUtility::create_json( $message, MoConstants::ERROR_JSON_TYPE ) );
			} else {
				miniorange_wc_site_otp_validation_form( null, null, null, $message, $otp_type, $from_both );
			}
		}


		/**
		 * Get the success message to be shown to the user when OTP was sent
		 * successfully. If admin has set his own unique message then
		 * show that to the user instead of the default one.
		 */
		public function mowc_get_otp_sent_message() {
			$sent_msg = get_mo_wc_option( 'success_email_message', 'mo_wc_otp_' );
			return $sent_msg ? mowc_( $sent_msg ) : MoMessages::showMessage( MoMessages::OTP_SENT_EMAIL );
		}


		/**
		 * Get the error message to be shown to the user when there was an
		 * error sending OTP. If admin has set his own unique message then
		 * show that to the user instead of the default one.
		 */
		public function mowc_get_otp_sent_failed_message() {
			$failed_msg = get_mo_wc_option( 'error_email_message', 'mo_wc_otp_' );
			return $failed_msg ? mowc_( $failed_msg ) : MoMessages::showMessage( MoMessages::ERROR_OTP_EMAIL );
		}


		/**
		 * This function checks if the email domain has been blocked by the admin
		 *
		 * @param string $user_email    user email.
		 * @param string $phone_number  phone number.
		 * @return bool
		 */
		public function mowc_is_blocked( $user_email, $phone_number ) {
			$blocked_email_domains = explode( ';', get_mo_wc_option( 'blocked_domains' ) );
			$blocked_email_domains = apply_filters( 'mo_blocked_email_domains', $blocked_email_domains );
			return in_array( MoUtility::get_domain( $user_email ), $blocked_email_domains, true );
		}


		/**
		 * Function decides what message needs to be shown to the user when he enters a
		 * blocked email domain. It checks if the admin has set any message in the
		 * plugin settings and returns that instead of the default one.
		 */
		public function mowc_get_is_blocked_message() {
			$blocked_emails = get_mo_wc_option( 'blocked_email_message', 'mo_wc_otp_' );
			return $blocked_emails ? mowc_( $blocked_emails ) : MoMessages::showMessage( MoMessages::ERROR_EMAIL_BLOCKED );
		}


		/**
		 * Function decides what message needs to be sent to the user when the
		 * email does not match the required format. It checks if the admin
		 * has set any message in the plugin settings and returns that instead
		 * of the string default one.
		 */
		public function mowc_get_otp_invalid_format_message() {
			$message = get_mo_wc_option( 'invalid_email_message', 'mo_wc_otp_' );
			return $message ? mowc_( $message ) : MoMessages::showMessage( MoMessages::ERROR_EMAIL_FORMAT );
		}
	}
}
