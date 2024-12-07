<?php
/**
 * Contains all the messages used in Ultimate Member SMS Notifications
 *
 * @package miniorange-otp-verification/Notifications/umsmsnotification/helper
 */

namespace WCSMSOTP\Notifications\FormSMSNotification\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\BaseMessages;
use WCSMSOTP\Traits\Instance;

/**
 * This is the constant class which lists all the messages
 * to be shown in the plugin.
 */
if ( ! class_exists( 'FormSMSNotificationMessages' ) ) {
	/**
	 * FormSMSNotificationMessages class
	 */
	final class FormSMSNotificationMessages extends BaseMessages {

		use Instance;

		/**
		 * Initializes values
		 */
		public function __construct() {
			/** Created an array instead of messages instead of constant variables for Translation reasons. */
			define(
				'MO_FORM_NOTIF_MESSAGES',
				maybe_serialize(
					array(
						self::NEW_FORMS_NOTIF_BODY     => mowc_(
							'Admins or Customers are sent a SMS notification when a user submit the form.'
						),
						self::NEW_CONTACT_FORM_SMS     => mowc_(
							'Hello {username}. Thanks for contacting us on {site-name}.We will get back to you shortly on {email}. -miniorange'
						),
						self::WPFORMS_CONTACT_FORM_SMS => mowc_(
							'Thanks for contacting us on {site-name}.We will get back to you shortly. -miniorange'
						),
					)
				)
			);
		}



		/**
		 * This function is used to fetch and process the Messages to
		 * be shown to the user. It was created to mostly show dynamic
		 * messages to the user.
		 *
		 * @param string $message_keys   message key or keys.
		 * @param array  $data           key value of the data to be replaced in the message.
		 * @return string
		 */
		public static function showMessage( $message_keys, $data = array() ) {
			$display_message = '';
			$message_keys    = explode( ' ', $message_keys );
			$messages        = maybe_unserialize( MO_FORM_NOTIF_MESSAGES );
			$common_messages = maybe_unserialize( MO_WC_MESSAGES );
			$messages        = array_merge( $messages, $common_messages );
			foreach ( $message_keys as $message_key ) {
				if ( MoUtility::is_blank( $message_key ) ) {
					return $display_message;
				}
				$format_message = $messages[ $message_key ];
				foreach ( $data as $key => $value ) {
					$format_message = str_replace( '{{' . $key . '}}', $value, $format_message );
				}
				$display_message .= $format_message;
			}
			return $display_message;
		}
	}
}
