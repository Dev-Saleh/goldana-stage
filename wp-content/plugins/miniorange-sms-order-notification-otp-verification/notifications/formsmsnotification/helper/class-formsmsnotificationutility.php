<?php
/**
 * Ultimate Member SMS Notification Utility
 *
 * @package miniorange-otp-verification/notifications/umsmsnotification/helper
 */

namespace WCSMSOTP\Notifications\FormSMSNotification\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\MoUtility;

/**
 * This class is used to define some plugin wide utility
 * functions. The functions described here are all static
 * in nature so that it's accessible without an instance.
 */
if ( ! class_exists( 'FormSMSNotificationUtility' ) ) {
	/**
	 * FormSMSNotificationUtility class
	 */
	class FormSMSNotificationUtility {

		/**
		 * Get the Phone of the first Admin user. This is used as
		 * the recipient of the admin SMS notifications if no
		 * phone number is saved for the notification.
		 *
		 * @return string
		 */
		public static function get_phone_number_key() {
			$notification_settings = get_fmsn_option( 'notification_settings_option' );
			if ( $notification_settings ) {
				$sms_settings    = $notification_settings->get_mo_contact_form7_notif(); // phpcs::ignore $notification_settings is an object.
				$recipient_value = maybe_unserialize( $sms_settings->recipient );
			}
			return ! empty( $recipient_value ) ? $recipient_value : '';
		}


		/**
		 * Checks if the customer is registered or not and shows a message on the page
		 * to the user so that they can register or login themselves to use the plugin.
		 */
		public static function is_addon_activated() {
			MoUtility::is_addon_activated();
		}
	}
}
