<?php
/**
 * Helper functions for Woocommerce Order On Processing Notifications
 *
 * @package miniorange-order-notifications-woocommerce
 */

namespace WCSMSOTP\Notifications\WcSMSNotification\Helper\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnMessages;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnUtility;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\SMSNotification;

/**
 * This class is used to handle all the settings and function related
 * to the WooCommerce Order Processing SMS Notification. It initializes the
 * notification related settings and implements the functionality for
 * sending the SMS to the user.
 */
if ( ! class_exists( 'WooCommerceOrderProcessingNotification' ) ) {
	/**
	 * WooCommerceOrderProcessingNotification class
	 */
	class WooCommerceOrderProcessingNotification extends SMSNotification {

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
			$this->title             = 'Processing Order';
			$this->page              = 'wc_order_processing_notif';
			$this->is_enabled        = false;
			$this->tool_tip_header   = 'ORDER_PROCESSING_NOTIF_HEADER';
			$this->tool_tip_body     = 'ORDER_PROCESSING_NOTIF_BODY';
			$this->recipient         = 'customer';
			$this->sms_body          = MoWcAddOnMessages::showMessage( MoWcAddOnMessages::PROCESSING_ORDER_SMS );
			$this->default_sms_body  = MoWcAddOnMessages::showMessage( MoWcAddOnMessages::PROCESSING_ORDER_SMS );
			$this->premium_tags      = '{payment-method},{total-Amount},{transaction-ID},{order-key},{billing-firstName},{billing-phone},{billing-email},{billing-address},{billing-city},{billing-state},{billing-postcode},{billing-country},{shipping-firstName},{shipping-phone},{shipping-address},{shipping-city},{shipping-state},{shipping-postcode},{shipping-country}';
			$this->available_tags    = '{site-name},{order-number},{username},{order-date}';
			$this->page_header       = mowc_( 'ORDER PROCESSING NOTIFICATION SETTINGS' );
			$this->page_description  = mowc_( 'SMS notifications sent to the users when order status changes to Processing.' );
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
			$order_details = $args['orderDetails'];
			if ( MoUtility::is_blank( $order_details ) ) {
				return;
			}
			$this->set_notif_in_session( $this->page );
			$userdetails  = get_userdata( $order_details->get_customer_id() );
			$site_name    = get_bloginfo();
			$username     = MoUtility::is_blank( $userdetails ) ? '' : $userdetails->user_login;
			$phone_number = MoWcAddOnUtility::get_customer_number_from_order( $order_details );
			$date_created = $order_details->get_date_created()->date_i18n();
			$order_no     = $order_details->get_order_number();

			$replaced_string = array(
				'site-name'    => $site_name,
				'username'     => $username,
				'order-date'   => $date_created,
				'order-number' => $order_no,
			);
			$replaced_string = apply_filters( 'mo_wc_customer_order_processing_notif_string_replace', $replaced_string );
			$sms_body        = MoUtility::replace_string( $replaced_string, $this->sms_body );

			if ( MoUtility::is_blank( $phone_number ) ) {
				return;
			}
			MoUtility::send_phone_notif( $phone_number, $sms_body );
		}
	}

}
