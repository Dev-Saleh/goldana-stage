<?php
/**Load administrator changes for Miniorange Gateway
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WCSMSOTP\Notifications\WcSMSNotification\WooCommerceSmsNotification;
use WCSMSOTP\Notifications\FormSMSNotification\FormSubmissionSMSNotification;
use WCSMSOTP\Objects\BaseAddOnHandler;
use WCSMSOTP\Objects\IGatewayFunctions;
use WCSMSOTP\Objects\NotificationSettings;
use WCSMSOTP\Traits\Instance;

/**
 * This class has MiniOrange Gateway Plan specific functions
 *
 * @todo - Segregate the functions
 */
if ( ! class_exists( 'MiniOrangeGateway' ) ) {
	/**
	 * MiniOrangeGateway class
	 */
	class MiniOrangeGateway implements IGatewayFunctions {

		use Instance;

		/**Global variable
		 *
		 * @var string application_name used in API calls */
		private $application_name = 'wp_otp_verification';


		/**
		 * ---------------------------------------------------------------------------------------
		 * FUNCTIONS RELATED TO ADDONS
		 * ---------------------------------------------------------------------------------------
		 **/
		public function register_addons() {
			WooCommerceSmsNotification::instance();
			FormSubmissionSMSNotification::instance();
		}
		/**
		 * ---------------------------------------------------------------------------------------
		 * FUNCTIONS RELATED TO LICENSING AND SYNC
		 * ---------------------------------------------------------------------------------------
		 */
		public function hourly_sync() {
			$customer_key = get_mo_wc_option( 'admin_customer_key' );
			$api_key      = get_mo_wc_option( 'admin_api_key' );
			if ( isset( $customer_key ) && isset( $api_key ) ) {
				MoUtility::handle_mo_check_ln( false, $customer_key, $api_key );
			}
		}
		/** Flushing Cache
		 */
		public function flush_cache() {

		}

		/** MoInternal Function
		 *
		 * @param object $post postarray.
		 */
		public function vlk( $post ) {

		}

		/** MoInternal Function
		 *
		 * @return bool
		 */
		public function mclv() {
			return true;
		}

		/** MoInternal Function
		 *
		 * @return bool
		 */
		public function is_gateway_config() {
			return true;
		}

		/** MoInternal Function
		 *
		 * @return bool
		 */
		public function is_mg() {
			return $this->mclv();
		}

		/**
		 * Returns the application Name for the gateway
		 *
		 * @return string
		 */
		public function get_application_name() {
			return $this->application_name;
		}

		/**
		 * ---------------------------------------------------------------------------------------
		 * FUNCTIONS RELATED TO CUSTOM SMS AND EMAIL TEMPLATES
		 * ---------------------------------------------------------------------------------------
		 *
		 * @param string $original_email_from email of user.
		 */
		public function custom_wp_mail_from_name( $original_email_from ) {
				return $original_email_from;
		}

		/**
		 * Returns the sms template
		 *
		 * @param object $posted post values.
		 */
		public function mo_configure_sms_template( $posted ) {
		}

		/**
		 * Returns the email template
		 *
		 * @param object $posted post values.
		 */
		public function mo_configure_email_template( $posted ) {
		}



		/**
		 * Calls the server to send OTP to the user's phone or email
		 *
		 * @param string $auth_type  OTP Type - EMAIL or SMS.
		 * @param string $email     Email Address of the user.
		 * @param string $phone     Phone Number of the user.
		 * @return array
		 */
		public function mo_send_otp_token( $auth_type, $email, $phone ) {
			if ( MO_WC_TEST_MODE ) {
				return array(
					'status' => 'SUCCESS',
					'txId'   => MoUtility::rand(),
				);
			} else {
				$content = MocURLCall::mo_send_otp_token( $auth_type, $email, $phone );
				return json_decode( $content, true );
			}
		}

		/**
		 * Calls server apis to send email or sms message to the user
		 *
		 * @param NotificationSettings $settings notification object.
		 * @return string
		 */
		public function mo_send_notif( NotificationSettings $settings ) {
			$url = '';
			if ( $settings->send_email ) {
				$url = MoConstants::HOSTNAME . '/moas/api/notify/send';
			} else {
				$url = MoConstants::HOSTNAME . '/moas/api/plugin/notify/send';
			}

			$customer_key = get_mo_wc_option( 'admin_customer_key' );
			$api_key      = get_mo_wc_option( 'admin_api_key' );

			$fields = array(
				'customerKey' => $customer_key,
				'sendEmail'   => $settings->send_email,
				'sendSMS'     => $settings->send_sms,
				'email'       => array(
					'customerKey' => $customer_key,
					'fromEmail'   => $settings->from_email,
					'bccEmail'    => $settings->bcc_email,
					'fromName'    => $settings->from_name,
					'toEmail'     => $settings->to_email,
					'toName'      => $settings->to_email,
					'subject'     => $settings->subject,
					'content'     => $settings->message,
				),
				'sms'         => array(
					'customerKey' => $customer_key,
					'phoneNumber' => $settings->phone_number,
					'message'     => $settings->message,
				),
			);

			$json        = wp_json_encode( $fields );
			$auth_header = MocURLCall::create_auth_header( $customer_key, $api_key );
			$response    = MocURLCall::call_api( $url, $json, $auth_header );
			return $response;
		}



		/**
		 * Calls the server to validate the OTP
		 *
		 * @param string $tx_id      Transaction ID from session.
		 * @param string $otp_token OTP Token to validate.
		 * @return array
		 */
		public function mowc_validate_otp_token( $tx_id, $otp_token ) {
			if ( MO_WC_TEST_MODE ) {
				return MO_WC_FAIL_MODE ? array( 'status' => '' ) : array( 'status' => 'SUCCESS' );
			} else {
				$content = MocURLCall::validate_otp_token( $tx_id, $otp_token );
				return json_decode( $content, true );
			}
		}


		/** FUNCTIONS RELATED TO VISUAL TOUR
		 *
		 * @return array
		 */
		public function get_config_page_pointers() {
			$visual_tour = MOVisualTour::instance();
			return array(
				$visual_tour->tour_template(
					'configuration_instructions',
					'right',
					'',
					'<br>Check the links here to see how to change email/sms template, custom gateway, senderID, etc.',
					'Next',
					'emailSms.svg',
					1
				),
			);
		}
	}
}
