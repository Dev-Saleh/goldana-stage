<?php
/**
 * Helper functions for Woocommerce Customer Note Notification
 *
 * @package miniorange-order-notifications-woocommerce
 */

namespace WCSMSOTP\Notifications\WcSMSNotification\Helper\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnMessages;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\SMSNotification;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\WooCommerceNotificationsList;

/**
 * This class is used to handle all the settings and function related
 * to the WooCommerce New Customer SMS Notification. It initializes the
 * notification related settings and implements the functionality for
 * sending the SMS to the user.
 */
if ( ! class_exists( 'WooCommerceCutomerNoteNotification' ) ) {
	/**
	 * WooCommerceCutomerNoteNotification class
	 */
	class WooCommerceNewCustomerNotification extends SMSNotification {

		/** Global Variable
		 *
		 * @var instance - initiates the instance of the file.
		 */
		public static $instance;

		/**
		 * Woocommerce premium tags.
		 *
		 * @var array
		 */
		public $premium_tags;

		/** Declare Default variables */
		protected function __construct() {
			parent::__construct();
			$this->title             = 'New Account';
			$this->page              = 'wc_new_customer_notif';
			$this->is_enabled        = false;
			$this->tool_tip_header   = 'NEW_CUSTOMER_NOTIF_HEADER';
			$this->tool_tip_body     = 'NEW_CUSTOMER_NOTIF_BODY';
			$this->recipient         = 'customer';
			$this->sms_body          = get_mowc_option( 'woocommerce_registration_generate_password', '' ) === 'yes'
									? MoWcAddOnMessages::showMessage( MoWcAddOnMessages::NEW_CUSTOMER_SMS_WITH_PASS )
									: MoWcAddOnMessages::showMessage( MoWcAddOnMessages::NEW_CUSTOMER_SMS );
			$this->default_sms_body  = get_mowc_option( 'woocommerce_registration_generate_password', '' ) === 'yes'
									? MoWcAddOnMessages::showMessage( MoWcAddOnMessages::NEW_CUSTOMER_SMS_WITH_PASS )
									: MoWcAddOnMessages::showMessage( MoWcAddOnMessages::NEW_CUSTOMER_SMS );
			$this->premium_tags      = '{user-email},{registration-date},{accountpage-url}';
			$this->available_tags    = '{site-name},{username}';
			$this->page_header       = mowc_( 'NEW ACCOUNT NOTIFICATION SETTINGS' );
			$this->page_description  = mowc_( 'SMS notifications sent to the user after New Account creation.' );
			$this->notification_type = mowc_( 'Customer' );
			self::$instance          = $this;
		}

		/**
		 * Checks if there exists an existing instance of the class.
		 * If not then creates an instance and returns it.
		 */
		public static function getInstance() {
			return null === self::$instance ? new self() : self::$instance;
		}

		/**
		 * Initialize all the variables required to modify the sms template
		 * and send the SMS to the user. Checks if the SMS notification
		 * has been enabled and send SMS to the user. Do not send SMS
		 * if phone number of the customer doesn't exist.
		 *
		 * @param  array $args all the arguments required to send SMS.
		 */
		public function send_sms( array $args ) {
			if ( ! $this->is_enabled ) {
				return;
			}
			$this->set_notif_in_session( $this->page );
			$customer_id     = $args['customer_id'];
			$customer_data   = $args['new_customer_data'];
			$site_name       = get_bloginfo();
			$username        = get_userdata( $customer_id )->user_login;
			$phone_number    = get_user_meta( $customer_id, 'billing_phone', true );
			$posted_phone_no = MoUtility::sanitize_check( 'billing_phone', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need for nonce verification as the function is called on third party plugin hook.
			$phone_number    = MoUtility::is_blank( $phone_number ) && $posted_phone_no ? $posted_phone_no : $phone_number;
			$accountpage     = wc_get_page_permalink( 'myaccount' ); // phpcs:ignore -- Default function of Woocommerce.

			/* Adding extra tags */
			$user_email        = get_userdata( $customer_id )->user_email;
			$registration_date = get_userdata( $customer_id )->user_registered;
			$reg_date          = substr( $registration_date, 0, 10 );

			$replaced_string = array(
				'site-name'         => get_bloginfo(),
				'username'          => $username,
				'accountpage-url'   => $accountpage,
				'user-email'        => $user_email,
				'registration-date' => $reg_date,
			);
			$replaced_string = apply_filters( 'mo_wc_new_customer_notif_string_replace', $replaced_string );
			$sms_body        = MoUtility::replace_string( $replaced_string, $this->sms_body );

			if ( MoUtility::is_blank( $phone_number ) ) {
				return;
			}
			MoUtility::send_phone_notif( $phone_number, $sms_body );
		}
	}

}
