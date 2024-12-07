<?php
/**Load Abstract Class SMSNotification
 *
 * @package miniorange-order-notifications-woocommerce/objects
 */

namespace WCSMSOTP\Objects;

use WCSMSOTP\Helper\MoPHPSessions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class is used to define the base class of the SMS Notifications.
 * Each of the SMS Notification class needs to extend this class. They
 * will have to implement the send_sms function.
 */
if ( ! class_exists( 'SMSNotification' ) ) {
	/**
	 * SMSNotification class
	 */
	abstract class SMSNotification {


		/**
		 * The settings page associated with the class
		 *
		 * @var string
		 */
		public $page;

		/**
		 * If the notification is enabled or not
		 *
		 * @var bool
		 */
		public $is_enabled;


		/**
		 * The header of the tooltip in the table
		 *
		 * @var string
		 */
		public $tool_tip_header;

		/**
		 * The body of the tooltip in the table
		 *
		 * @var string
		 */
		public $tool_tip_body;

		/**
		 * The recipient of the SMS Notification
		 *
		 * @var string
		 */
		public $recipient;

		/**
		 * The admin recipient of the SMS Notification
		 *
		 * @var string
		 */
		public $admin_recipient;

		/**
		 * The text body of the SMS
		 *
		 * @var string
		 */
		public $sms_body;

		/**
		 * The default SMS Body set if no sms
		 * text is explicitly specified
		 *
		 * @var string
		 */
		public $default_sms_body;

		/**
		 * The Title of the page
		 *
		 * @var string
		 */
		public $title;

		/**
		 * The available Tags that can be placed
		 * in the SMS body to dynamically add
		 * values to the messages being sent
		 *
		 * @var string
		 */
		public $available_tags;

		/**
		 * The Header of the Page
		 *
		 * @var string
		 */
		public $page_header;

		/**
		 * The description of the page
		 *
		 * @var string
		 */
		public $page_description;

		/**
		 * The description of the page
		 *
		 * @var string
		 */
		public $phone_input;

		/**
		 * This determines who the notification is for: customer / admin / vendor etc.
		 *
		 * @var string
		 */
		public $notification_type;


		/**
		 * ----------------------------------------------------------------
		 * constructor
		 * ----------------------------------------------------------------
		 */
		public function __construct() {}

		/**
		 * ----------------------------------------------------------------
		 * methods
		 * ---------------------------------------------------------------
		 *
		 * @param array $args sms object.
		 */
		abstract public function send_sms( array $args );


		/**
		 * ----------------------------------------------------------------
		 * setters
		 * ----------------------------------------------------------------
		 *
		 * @param object $notification_type notification object.
		 */
		public function set_notif_in_session( $notification_type ) {
			MoPHPSessions::add_session_var( 'mo_addon_notif_type', $this->page );
		}

		/**
		 * Setter function for is_enabled variable
		 *
		 * @param mixed $is_enabled check if enabled.
		 * @return SMSNotification
		 */
		public function setis_enabled( $is_enabled ) {
			$this->is_enabled = $is_enabled;
			return $this;
		}


		/**
		 * Setter function for recipient variable
		 *
		 * @param mixed $recipient sms reciepient.
		 *
		 * @return SMSNotification
		 */
		public function set_recipient( $recipient ) {
			$this->recipient = $recipient;
			return $this;
		}

		/**
		 * Setter function for recipient variable
		 *
		 * @param mixed $admin_recipient sms reciepient.
		 *
		 * @return SMSNotification
		 */
		public function set_admin_recipient( $admin_recipient ) {
			$this->admin_recipient = $admin_recipient;
			return $this;
		}

		/**
		 * Setter function for recipient variable
		 *
		 * @param mixed $sms_body sms body.
		 *
		 * @return SMSNotification
		 */
		public function set_sms_body( $sms_body ) {
			$this->sms_body = $sms_body;
			return $this;
		}
	}
}
