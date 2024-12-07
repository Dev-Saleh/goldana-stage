<?php
/**
 * Notification Name: Form Submission SMS Notification
 * Plugin URI: http://miniorange.com
 * Description: Send out SMS notifications to admins and users after form submission.
 * Version: 1.0.0
 * Author: miniOrange
 * Author URI: http://miniorange.com
 * Text Domain: miniorange-otp-verification
 * License: GPL2
 *
 * @package miniorange-otp-verification/notifications/formsmsnotification
 */

namespace WCSMSOTP\Notifications\FormSMSNotification;

use WCSMSOTP\Notifications\FormSMSNotification\Handler\FormSMSNotificationsHandler;
use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationsList;
use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationMessages;
use WCSMSOTP\Helper\AddOnList;
use WCSMSOTP\Objects\AddOnInterface;
use WCSMSOTP\Objects\BaseAddOn;
use WCSMSOTP\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require 'formnotifautoload.php';

/**
 * This is the constant class which consists of the necessary function used in the addon.
 */
if ( ! class_exists( 'FormSubmissionSMSNotification' ) ) {
	/**
	 * FormSubmissionSMSNotification class
	 */
	final class FormSubmissionSMSNotification extends BaseAddon implements AddOnInterface {

		use Instance;

		/**
		 * Initializes values
		 */
		public function __construct() {
			parent::__construct();
			add_action( 'mo_otp_verification_delete_addon_options', array( $this, 'form_sms_notif_delete_options' ) );
		}

		/**
		 * Initialize all handlers associated with the addon
		 */
		public function initialize_handlers() {
			$list = AddOnList::instance();

			$handler = FormSMSNotificationsHandler::instance();
		}

		/**
		 * Initialize all helper associated with the addon
		 */
		public function initialize_helpers() {
			FormSMSNotificationMessages::instance();
			FormSMSNotificationsList::instance();
		}


		/**
		 * This function hooks into the s
		 * hook to show ultimate notification settings page and forms for
		 * validation.
		 *
		 * @todo change the addon framework to notifications framework
		 */
		public function show_addon_settings_page() {
		}

		/**
		 * Function is called during deletion of the plugin to delete any options
		 * related to the add-on. This function hooks into the 'mo_otp_verification_delete_addon_options'
		 * hook of the OTP verification plugin.
		 */
		public function form_sms_notif_delete_options() {
			delete_site_option( 'mo_form_sms_notification_settings' );
		}
	}
}
