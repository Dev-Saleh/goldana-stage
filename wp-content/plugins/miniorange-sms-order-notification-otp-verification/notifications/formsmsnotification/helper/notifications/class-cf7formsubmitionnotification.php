<?php
/**
 * Contact Form-7 Submission Notifications helper
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
 * to the UltimateMember New Customer SMS Notification. It initializes the
 * notification related settings and implements the functionality for
 * sending the SMS to the user.
 *
 * @param mixed $instance.
 */
if ( ! class_exists( 'CF7FormSubmitionNotification' ) ) {
	/**
	 * CF7FormSubmitionNotification class
	 */
	class CF7FormSubmitionNotification extends SMSNotification {

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
			$this->title             = 'Contact Form- 7';
			$this->page              = 'mo_contact_form7_notif';
			$this->is_enabled        = false;
			$this->tool_tip_body     = 'NEW_FORMS_NOTIF_BODY';
			$this->admin_recipient   = get_fmsn_option( 'moform_notif_admin_recipient' );
			$this->sms_body          = FormSMSNotificationMessages::showMessage(
				FormSMSNotificationMessages::NEW_CONTACT_FORM_SMS
			);
			$this->default_sms_body  = FormSMSNotificationMessages::showMessage(
				FormSMSNotificationMessages::NEW_CONTACT_FORM_SMS
			);
			$this->available_tags    = '{site-name},{username},{accountpage-url},{login-url},{email},{fullname}';
			$this->page_description  = mowc_( 'SMS notifications template send on submission of Contact form-7' );
			$this->phone_input       = 'Name of Phone field';
			$this->notification_type = mowc_( 'Admin and Customer' );
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
		public function send_sms( $args ) {
			if ( ! $this->is_enabled ) {
				return;
			}
			$this->set_notif_in_session( $this->page );
			$data = MoUtility::mowc_sanitize_array( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need for nonce verification as the function is called on third party plugin hook.

			$phone_number        = $data[ $this->recipient ];
			$admin_phone_numbers = maybe_unserialize( $this->admin_recipient );
			$phone_numbers       = explode( ';', $admin_phone_numbers );
			array_push( $phone_numbers, $phone_number );

			$username = $data['your-name'];
			$email    = $data['your-email'];

			$replaced_string = array(
				'site-name' => get_bloginfo(),
				'username'  => $username,
				'email'     => $email,
			);
			$replaced_string = apply_filters( 'mo_contact_form7_notif_string_replace', $replaced_string );
			$sms_body        = MoUtility::replace_string( $replaced_string, $this->sms_body );

			if ( MoUtility::is_blank( $phone_numbers ) ) {
				return;
			}
			foreach ( $phone_numbers as $phone_number ) {
				MoUtility::send_phone_notif( $phone_number, $sms_body );
			}
		}
	}
}
