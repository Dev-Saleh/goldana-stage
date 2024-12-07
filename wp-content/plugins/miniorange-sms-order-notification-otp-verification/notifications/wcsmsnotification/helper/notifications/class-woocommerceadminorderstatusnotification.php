<?php
/**
 * Helper functions for Woocommerce Admin Order Notifications
 *
 * @package miniorange-order-notifications-woocommerce
 */

namespace WCSMSOTP\Notifications\WcSMSNotification\Helper\Notifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnMessages;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnUtility;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\WcOrderStatus;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\SMSNotification;

/**
 * This class is used to handle all the settings and function related
 * to the WooCommerce Admin Order Status SMS Notification. It initializes the
 * notification related settings and implements the functionality for
 * sending the SMS to the user.
 */
if ( ! class_exists( 'WooCommerceAdminOrderstatusNotification' ) ) {
	/**
	 * WooCommerceAdminOrderstatusNotification class
	 */
	class WooCommerceAdminOrderstatusNotification extends SMSNotification {

		/** Global Variable
		 *
		 * @var instance - initiates the instance of the file.
		 */
		public static $instance;
		/** Global Variable
		 *
		 * @var array statuses - defines the order status.
		 */
		public static $statuses;

		/** Declare Default variables */
		protected function __construct() {
			parent::__construct();
			$this->title             = 'Order Status';
			$this->page              = 'wc_admin_order_status_notif';
			$this->is_enabled        = false;
			$this->tool_tip_header   = 'NEW_ORDER_NOTIF_HEADER';
			$this->tool_tip_body     = 'NEW_ORDER_NOTIF_BODY';
			$this->recipient         = MoWcAddOnUtility::get_admin_phone_number();
			$this->sms_body          = MoWcAddOnMessages::showMessage( MoWcAddOnMessages::ADMIN_STATUS_SMS );
			$this->default_sms_body  = MoWcAddOnMessages::showMessage( MoWcAddOnMessages::ADMIN_STATUS_SMS );
			$this->available_tags    = '{site-name},{order-number},{username},{order-date},{payment-method},{total-Amount},{transaction-ID},{order-key},{billing-firstName},{billing-phone},{billing-email},{billing-address},{billing-city},
            {billing-state},{billing-postcode},{billing-country},{shipping-firstName},{shipping-phone},{shipping-address},{shipping-city},{shipping-state},{shipping-postcode},{shipping-country}';
			$this->page_description  = mowc_( 'Order Status updates SMS notifications sent to the admins' );
			$this->notification_type = mowc_( 'Administrator' );
			self::$instance          = $this;
			self::$statuses          = WcOrderStatus::get_all_status();
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
			$new_status    = $args['new_status'];
			if ( MoUtility::is_blank( $order_details ) ) {
				return;
			}
			if ( ! in_array( $new_status, self::$statuses, true ) ) {
				return;
			}
			$this->set_notif_in_session( $this->page );
			$userdetails   = get_userdata( $order_details->get_customer_id() );
			$site_name     = get_bloginfo();
			$username      = MoUtility::is_blank( $userdetails ) ? '' : $userdetails->user_login;
			$phone_numbers = maybe_unserialize( $this->recipient );
			$date_created  = $order_details->get_date_created()->date_i18n();
			$order_no      = $order_details->get_order_number();

			/* Adding extra tags */
			$shipping_total = $order_details->get_shipping_total();
			$total_amt      = $order_details->get_total();
			$order_key      = $order_details->get_order_key();

			/* billing details (_b) */
			$first_name_b       = $order_details->get_billing_first_name();
			$last_name_b        = $order_details->get_billing_last_name();
			$phone_customer_b   = $order_details->get_billing_phone();
			$user_email_b       = $order_details->get_billing_email();
			$billing_address_b  = $order_details->get_billing_address_1();
			$billing_city_b     = $order_details->get_billing_city();
			$billing_state_b    = $order_details->get_billing_state();
			$billing_postcode_b = $order_details->get_billing_postcode();
			$billing_country_b  = $order_details->get_billing_country();

			$payment_method = $order_details->get_payment_method_title();
			$transaction_id = $order_details->get_transaction_id();

			/* shipping details (_s) */
			$first_name_s        = $order_details->get_shipping_first_name();
			$last_name_s         = $order_details->get_shipping_last_name();
			$phone_customer_s    = $order_details->get_shipping_phone();
			$shipping_address_s  = $order_details->get_shipping_address_1();
			$shipping_city_s     = $order_details->get_shipping_city();
			$shipping_state_s    = $order_details->get_shipping_state();
			$shipping_postcode_s = $order_details->get_shipping_postcode();
			$shipping_country_s  = $order_details->get_shipping_country();

			$replaced_string = array(
				'site-name'          => $site_name,
				'username'           => $username,
				'order-date'         => $date_created,
				'order-number'       => $order_no,
				'order-status'       => $new_status,
				'shipping-total'     => $shipping_total,
				'total-amount'       => $total_amt,
				'order-key'          => $order_key,

				'billing-firstName'  => $first_name_b,
				'billing-phone'      => $phone_customer_b,
				'billing-email'      => $user_email_b,
				'billing-address'    => $billing_address_b,
				'billing-city'       => $billing_city_b,
				'billing-state'      => $billing_state_b,
				'billing-postcode'   => $billing_postcode_b,
				'billing-country'    => $billing_country_b,

				'payment-mode'       => $payment_method,
				'transaction-ID'     => $transaction_id,

				'shipping-firstName' => $first_name_s,
				'shipping-phone'     => $phone_customer_s,
				'shipping-address'   => $shipping_address_s,
				'shipping-city'      => $shipping_city_s,
				'shipping-state'     => $shipping_state_s,
				'shipping-postcode'  => $shipping_postcode_s,
				'shipping-country'   => $shipping_country_s,
			);
			$replaced_string = apply_filters( 'mo_wc_admin_order_notif_string_replace', $replaced_string );
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

