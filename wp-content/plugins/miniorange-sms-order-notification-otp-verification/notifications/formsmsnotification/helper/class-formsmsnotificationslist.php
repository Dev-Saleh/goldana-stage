<?php
/**
 * Ultimate Member Notifications List
 *
 * @package miniorange-otp-verification/Notifications/umsmsnotification/helper
 */

namespace WCSMSOTP\Notifications\FormSMSNotification\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Notifications\FormSMSNotification\Helper\Notifications\CF7FormSubmitionNotification;
use WCSMSOTP\Notifications\FormSMSNotification\Helper\Notifications\WPFormsSubmissionNotification;
use WCSMSOTP\Notifications\FormSMSNotification\Helper\Notifications\NinjaFormSubmissionNotification;
use WCSMSOTP\Traits\Instance;

/**
 * This class is used to list down all the Ultimate Member  Notifications and initialize
 * each of the Notification classes so that it's accessible plugin wide. This
 * class is basically used to handle all the specific Ultimate Member  Notification classes.
 */
if ( ! class_exists( 'FormSMSNotificationsList' ) ) {
	/**
	 * FormSMSNotificationsList class
	 */
	class FormSMSNotificationsList {

		/**
		 * New customer notification class
		 *
		 * @var CF7FormSubmitionNotification
		 */
		public $mo_contact_form7_notif;

		/**
		 * New User Admin Notification
		 *
		 * @var WPFormsSubmissionNotification
		 */
		public $mo_wpform_notif;

		/**
		 * New customer notification class
		 *
		 * @var NinjaFormSubmissionNotification
		 */
		public $mo_ninja_form_notif;

		use Instance;

		/**
		 * Initializes values
		 */
		protected function __construct() {

			$this->mo_contact_form7_notif = CF7FormSubmitionNotification::getInstance();
			$this->mo_wpform_notif        = WPFormsSubmissionNotification::getInstance();
			$this->mo_ninja_form_notif    = NinjaFormSubmissionNotification::getInstance();
		}


		/**
		 * Getter function of the $mo_contact_form7_notif. Returns the instance
		 * of the CF7FormSubmitionNotification class.
		 */
		public function get_mo_contact_form7_notif() {
			return $this->mo_contact_form7_notif;
		}

		/**
		 * Getter function of the $mo_wpform_notif. Returns the instance
		 * of the WPFormsSubmissionNotification class.
		 */
		public function get_mo_wpform_notif() {
			return $this->mo_wpform_notif;
		}

		/**
		 * Getter function of the $mo_contact_form7_notif. Returns the instance
		 * of the NinjaFormSubmissionNotification class.
		 */
		public function get_mo_ninja_form_notif() {
			return $this->mo_ninja_form_notif;
		}
	}
}
