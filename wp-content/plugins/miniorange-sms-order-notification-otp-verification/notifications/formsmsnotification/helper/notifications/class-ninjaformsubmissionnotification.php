<?php
/**
 * Ninja Form Submission Notifications helper
 *
 * @package miniorange-otp-verification/addons/umsmsnotification/helper/notifications
 */

namespace WCSMSOTP\Notifications\FormSMSNotification\Helper\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationMessages;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\SMSNotification;
use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationUtility;

/**
 * This class is used to handle all the settings and function related
 * to the Gravity Form SMS Notification. It initializes the
 * notification related settings and implements the functionality for
 * sending the SMS to the user.
 *
 * @param mixed $instance.
 */
if ( ! class_exists( 'NinjaFormSubmissionNotification' ) ) {
	/**
	 * NinjaFormSubmissionNotification class
	 */
	class NinjaFormSubmissionNotification extends SMSNotification {

		/**
		 * Instance.
		 *
		 * @var mixed $insatance Instance.
		 */
		public static $instance;
		/**
		 * Initializes values
		 */
		protected function __construct() {
			parent::__construct();
			$this->title             = 'Ninja Contact Form';
			$this->page              = 'mo_ninja_form_notif';
			$this->is_enabled        = false;
			$this->admin_recipient   = get_fmsn_option( 'moform_notif_admin_recipient' );
			$this->sms_body          = FormSMSNotificationMessages::showMessage(
				FormSMSNotificationMessages::WPFORMS_CONTACT_FORM_SMS
			);
			$this->default_sms_body  = FormSMSNotificationMessages::showMessage(
				FormSMSNotificationMessages::WPFORMS_CONTACT_FORM_SMS
			);
			$this->available_tags    = '{site-name},{email}';
			$this->phone_input       = 'Phone field key';
			$this->page_description  = mowc_( 'SMS notifications template send on submission of Ninja Contact Form.' );
			$this->notification_type = mowc_( 'Admin & Customer' );
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
		 * This function is used to get the field id based on the field.
		 * label provided by the admin.
		 *
		 * @param array $data - the label of the field.
		 * @return null|string
		 */
		private function getFieldId( $data ) {
			global $wpdb;
			return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}nf3_fields where `key` = %s", array( $data ) ) );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, Direct database call without caching detected -- DB Direct Query is necessary here.
		}

		/**
		 * Initialize all the variables required to modify the sms template
		 * and send the SMS to the user. Checks if the SMS notification
		 * has been enabled and send SMS to the user. Do not send SMS
		 * if phone number of the customer doesn't exist.
		 *
		 * @param  array $args all the arguments required to send SMS.
		 */
		public function send_sms( $args ) {
			if ( ! $this->is_enabled ) {
				return;
			}
			$this->set_notif_in_session( $this->page );
			$data = MoUtility::mowc_sanitize_array( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need for nonce verification as the function is called on third party plugin hook.

			$phone_key           = $this->getFieldId( $this->recipient );
			$phone_number        = $args['fields'][ $phone_key ]['value'];
			$admin_phone_numbers = maybe_unserialize( $this->admin_recipient );
			$phone_numbers       = explode( ';', $admin_phone_numbers );
			array_push( $phone_numbers, $phone_number );

			// Will have to fetch the below data by processing the field key from the database:
			$username = $data['your-name'];
			$email    = $data['your-email'];

			$replaced_string = array(
				'site-name' => get_bloginfo(),
				'username'  => $username,
				'email'     => $email,
			);
			$replaced_string = apply_filters( 'mo_ninjaform_notif_string_replace', $replaced_string );
			$sms_body        = MoUtility::replace_string( $replaced_string, $this->sms_body );

			if ( MoUtility::is_blank( $phone_number ) ) {
				return;
			}
			MoUtility::send_phone_notif( $phone_number, $sms_body );
		}
	}
}
