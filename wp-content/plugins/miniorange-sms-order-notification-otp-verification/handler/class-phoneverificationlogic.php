<?php
/**
 * Comman handler to handle the phone logic during phone verification.
 *
 * @package miniorange-order-notifications-woocommerce/handler
 */

namespace WCSMSOTP\Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\GatewayFunctions;
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Helper\SessionUtils;
use WCSMSOTP\Objects\VerificationLogic;
use WCSMSOTP\Traits\Instance;

/**
 * This class handles all the phone related logic for OTP Verification
 * Process the phone number and starts the phone verification process.
 */
if ( ! class_exists( 'PhoneVerificationLogic' ) ) {
	/**
	 * PhoneVerificationLogic class
	 */
	final class PhoneVerificationLogic extends VerificationLogic {

		use Instance;

		/**
		 * This function is called to handle Email Verification request. Processes
		 * the request and starts the OTP Verification process. Starts of with
		 * checking if the phone number is in the requested format.
		 *
		 * @param string $user_login username of the user.
		 * @param string $user_email email of the user.
		 * @param string $phone_number phone number of the user.
		 * @param string $otp_type email or sms verification.
		 * @param string $from_both has user enabled from both.
		 */
		public function mowc_handle_logic( $user_login, $user_email, $phone_number, $otp_type, $from_both ) {
			$this->checkIfUserRegistered( $otp_type, $from_both );
			$match = MoUtility::validate_phone_number( $phone_number );
			switch ( $match ) {
				case 0:
					$this->mowc_handle_not_matched( $phone_number, $otp_type, $from_both );
					break;
				case 1:
					$this->mowc_handle_matched( $user_login, $user_email, $phone_number, $otp_type, $from_both );
					break;
			}
		}


		/**
		 * Checks if the user is registered with miniorange and show the error message if not registereed.
		 *
		 * @param array $otp_type .
		 * @param array $from_both .
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
		 * This function starts the OTP Verification process if phone number matches the
		 * correct format and is not blocked by the admin.
		 *
		 * @param string $user_login username of the user.
		 * @param string $user_email email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type email or sms verification.
		 * @param string $from_both string has user enabled from both.
		 */
		public function mowc_handle_matched( $user_login, $user_email, $phone_number, $otp_type, $from_both ) {
			$message = str_replace( '##phone##', $phone_number, $this->mowc_get_is_blocked_message() );
			if ( $this->mowc_is_blocked( $user_email, $phone_number ) ) {
				if ( $this->is_ajax_form() ) {
					wp_send_json( MoUtility::create_json( $message, MoConstants::ERROR_JSON_TYPE ) );
				} else {
					miniorange_wc_site_otp_validation_form( null, null, null, $message, $otp_type, $from_both );
				}
			} else {
				do_action( 'mowc_globally_banned_phone_check', $phone_number, $this->is_ajax_form() );
				$this->mowc_start_otp_verification( $user_login, $user_email, $phone_number, $otp_type, $from_both );
			}
		}


		/**
		 * This function starts the OTP Verification process and contacts server to send OTP to the
		 * user's phone number.
		 *
		 * @param string $user_login    username of the user.
		 * @param string $user_email email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type email or sms verification.
		 * @param string $from_both string has user enabled from both.
		 */
		public function mowc_start_otp_verification( $user_login, $user_email, $phone_number, $otp_type, $from_both ) {
			$gateway           = GatewayFunctions::instance();
			$verification_type = 'SMS';
			$verification_type = apply_filters( 'otp_over_call_activation', $verification_type );
			$content           = $gateway->mo_send_otp_token( $verification_type, '', $phone_number );
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
		 * This function handles what message needs to be shown to the user if phone number
		 * doesn't match the correct format. Check if admin has set any message, and check
		 * if the form is an ajax form to show the message in the correct format.
		 *
		 * @param string $phone_number  the phone number being processed.
		 * @param string $otp_type      email or sms verification.
		 * @param string $from_both has user enabled from both.
		 */
		public function mowc_handle_not_matched( $phone_number, $otp_type, $from_both ) {
			$message = str_replace( '##phone##', $phone_number, $this->mowc_get_otp_invalid_format_message() );
			if ( $this->is_ajax_form() ) {
				wp_send_json( MoUtility::create_json( $message, MoConstants::ERROR_JSON_TYPE ) );
			} else {
				miniorange_wc_site_otp_validation_form( null, null, null, $message, $otp_type, $from_both );
			}
		}


		/**
		 * This function is called to handle what needs to be done when OTP sending fails.
		 * Checks if the current form is an AJAX form and decides what message has to be
		 * shown to the user.
		 *
		 * @param string $user_login    username of the user.
		 * @param string $user_email email of the user.
		 * @param string $phone_number  phone number of the user.
		 * @param string $otp_type email or sms verification.
		 * @param string $from_both has user enabled from both.
		 * @param array  $content string the json decoded response from server.
		 */
		public function mowc_handle_otp_sent_failed( $user_login, $user_email, $phone_number, $otp_type, $from_both, $content ) {
			$message = str_replace( '##phone##', $phone_number, $this->mowc_get_otp_sent_failed_message() );

			if ( $this->is_ajax_form() ) {
				wp_send_json( MoUtility::create_json( $message, MoConstants::ERROR_JSON_TYPE ) );
			} else {
				miniorange_wc_site_otp_validation_form( null, null, null, $message, $otp_type, $from_both );
			}
		}


		/**
		 * This function is called to handle what needs to be done when OTP sending is successful.
		 * Checks if the current form is an AJAX form and decides what message has to be
		 * shown to the user.
		 *
		 * @param string $user_login username of the user.
		 * @param string $user_email email of the user.
		 * @param string $phone_number phone number of the user.
		 * @param string $otp_type email or sms verification.
		 * @param string $from_both has user enabled from both.
		 * @param array  $content string the json decoded response from server.
		 */
		public function mowc_handle_otp_sent( $user_login, $user_email, $phone_number, $otp_type, $from_both, $content ) {
			SessionUtils::set_phone_transaction_id( $content['txId'] );

			if ( MoUtility::micr() && MoUtility::is_mg() ) {
				$avail_sms = get_mo_wc_option( 'phone_transactions_remaining' );
				if ( ( $avail_sms > 0 ) && ( MO_WC_TEST_MODE === false ) ) {
					update_mo_wc_option( 'phone_transactions_remaining', $avail_sms - 1 );
				}
			}

			$message = str_replace( '##phone##', $phone_number, $this->mowc_get_otp_sent_message() );
			if ( $this->is_ajax_form() ) {
				wp_send_json( MoUtility::create_json( $message, MoConstants::SUCCESS_JSON_TYPE ) );
			} else {
				miniorange_wc_site_otp_validation_form( $user_login, $user_email, $phone_number, $message, $otp_type, $from_both );
			}
		}


		/**
		 * Get the success message to be shown to the user when OTP was sent
		 * successfully. If admin has set his own unique message then
		 * show that to the user instead of the default one.
		 */
		public function mowc_get_otp_sent_message() {
			$send_msg = get_mo_wc_option( 'success_phone_message', 'mo_wc_otp_' );
			return $send_msg ? mowc_( $send_msg ) : MoMessages::showMessage( MoMessages::OTP_SENT_PHONE );
		}


		/**
		 * Get the error message to be shown to the user when there was an
		 * error sending OTP. If admin has set his own unique message then
		 * show that to the user instead of the default one.
		 */
		public function mowc_get_otp_sent_failed_message() {
			$failed_msg = get_mo_wc_option( 'error_phone_message', 'mo_wc_otp_' );
			$failed_msg = $failed_msg ? mowc_( $failed_msg ) : MoMessages::showMessage( MoMessages::ERROR_OTP_PHONE );

			$failed_msg = apply_filters( 'mowc_get_otp_sent_failed_message', $failed_msg );

			return $failed_msg;
		}


		/**
		 * Function decides what message needs to be sent to the user when the
		 * phone number does not match the required format. It checks if the admin
		 * has set any message in the plugin settings and returns that instead of the
		 * default one.
		 */
		public function mowc_get_otp_invalid_format_message() {
			$invalid_msg = get_mo_wc_option( 'invalid_phone_message', 'mo_wc_otp_' );
			return $invalid_msg ? mowc_( $invalid_msg ) : MoMessages::showMessage( MoMessages::ERROR_PHONE_FORMAT );
		}


		/**
		 * This function checks if the phone number has been blocked by the admin
		 *
		 * @param string $user_email email of the user.
		 * @param string $phone_number phone number of the user.
		 * @return bool
		 */
		public function mowc_is_blocked( $user_email, $phone_number ) {
			$blocked_phone_numbers = explode( ';', get_mo_wc_option( 'blocked_phone_numbers' ) );
			$blocked_phone_numbers = apply_filters( 'mo_blocked_phones', $blocked_phone_numbers, $phone_number );
			return in_array( $phone_number, $blocked_phone_numbers, true );
		}


		/**
		 * Function decides what message needs to be shown to the user when he enters a
		 * blocked phone number. It checks if the admin has set any message in the
		 * plugin settings and returns that instead of the default one.
		 */
		public function mowc_get_is_blocked_message() {
			$blocked_msg = get_mo_wc_option( 'blocked_phone_message', 'mo_wc_otp_' );
			return $blocked_msg ? mowc_( $blocked_msg ) : MoMessages::showMessage( MoMessages::ERROR_PHONE_BLOCKED );
		}
	}
}
