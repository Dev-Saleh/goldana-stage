<?php
/**
 * Helper functions for Woocommerce Messages
 *
 * @package miniorange-order-notifications-woocommerce
 */

namespace WCSMSOTP\Notifications\WcSMSNotification\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\BaseMessages;
use WCSMSOTP\Traits\Instance;

if ( ! class_exists( 'MoWcAddOnMessages' ) ) {
	/**
	 * MoWcAddOnMessages class
	 */
	final class MoWcAddOnMessages extends BaseMessages {

		/** Global Variable
			 *
			 * @var instance - initiates the instance of the file.
			 */
		use Instance;

		/** Declare Default variables */
		private function __construct() {
			define(
				'MOWC_ADDON_MESSAGES',
				maybe_unserialize(
					array(
						self::NEW_CUSTOMER_NOTIF_HEADER    => mowc_( 'NEW ACCOUNT NOTIFICATION' ),
						self::NEW_CUSTOMER_NOTIF_BODY      => mowc_(
							'Customers are sent a new account SMS notification when ' .
																'they sign up via checkout or account page.'
						),
						self::NEW_CUSTOMER_SMS_WITH_PASS   => mowc_(
							'Thanks for creating an account on {site-name}. Your username is {username} -miniorange'
						),
						self::NEW_CUSTOMER_SMS             => mowc_(
							'Thanks for creating an account on {site-name}. Your username is {username} -miniorange'
						),
						self::CUSTOMER_NOTE_NOTIF_HEADER   => mowc_( 'CUSTOMER NOTE NOTIFICATION' ),
						self::CUSTOMER_NOTE_NOTIF_BODY     => mowc_(
							'Customers are sent a new note SMS notification when ' .
																				'the admin adds a customer note to one of their orders.'
						),
						self::CUSTOMER_NOTE_SMS            => mowc_( 'Hi {username}, A note has been added to your order id {order-number} with {site-name} ordered on {order-date} -miniorange' ),

						self::NEW_ORDER_NOTIF_HEADER       => mowc_( 'ORDER STATUS NOTIFICATION' ),
						self::NEW_ORDER_NOTIF_BODY         => mowc_(
							'Recipients will be sent a new sms notification ' .
																				'notifying that the status of a order has changed ' .
																				'and they need to process it.'
						),
						self::ADMIN_STATUS_SMS             => mowc_( '{username} placed an order with ID {order-number} on {order-date}. Status changed to {order-status}. Store:{site-name} -miniorange' ),

						self::ORDER_ON_HOLD_NOTIF_HEADER   => mowc_( 'ORDER ON HOLD NOTIFICATION' ),
						self::ORDER_ON_HOLD_NOTIF_BODY     => mowc_(
							'Customer will be sent a new sms notification notifying' .
																				' that the status of the order has changed to ON-HOLD.'
						),
						self::ORDER_ON_HOLD_SMS            => mowc_( 'Hello {username}, Your order id {order-number} with {site-name} ordered on {order-date} has been put on hold. -miniorange' ),

						self::ORDER_PROCESSING_NOTIF_HEADER => mowc_( 'PROCESSING ORDER NOTIFICATION' ),
						self::ORDER_PROCESSING_NOTIF_BODY  => mowc_(
							'Customer will be sent a new sms notification notifying ' .
																				'that the order is currently under processing.'
						),
						self::PROCESSING_ORDER_SMS         => mowc_( 'Hello {username}, Your order id {order-number} with {site-name} ordered on {order-date} is processing. -miniorange' ),

						self::ORDER_COMPLETED_NOTIF_HEADER => mowc_( 'ORDER COMPLETED NOTIFICATION' ),
						self::ORDER_COMPLETED_NOTIF_BODY   => mowc_(
							'Customer will be sent a new sms notification ' .
																				'notifying that the order processing has been completed.'
						),
						self::ORDER_COMPLETED_SMS          => mowc_( 'Hello {username}, Your order id {order-number} with {site-name} ordered on {order-date} has been processed. It will be delivered shortly. -miniorange' ),

						self::ORDER_REFUNDED_NOTIF_HEADER  => mowc_( 'ORDER REFUNDED NOTIFICATION' ),
						self::ORDER_REUNDED_NOTIF_BODY     => mowc_(
							'Customer will be sent a new sms notification notifying ' .
																				'that the ordered has been refunded.'
						),
						self::ORDER_REFUNDED_SMS           => mowc_( 'Hello {username}, Your order id {order-number} with {site-name} ordered on {order-date} has been refunded. -miniorange' ),

						self::ORDER_CANCELLED_NOTIF_HEADER => mowc_( 'ORDER CANCELLED NOTIFICATION' ),
						self::ORDER_CANCELLED_NOTIF_BODY   => mowc_(
							'Customer will be sent a new sms notification notifying ".
			                                                "that the order has been cancelled.'
						),
						self::ORDER_CANCELLED_SMS          => mowc_( 'Hello {username}, Your order id {order-number} with {site-name} ordered on {order-date} has been cancelled. -miniorange' ),

						self::ORDER_FAILED_NOTIF_HEADER    => mowc_( 'ORDER FAILED NOTIFICATION' ),
						self::ORDER_FAILED_NOTIF_BODY      => mowc_(
							'Customer will be sent a new sms notification notifying ' .
																				'that the order processing has failed.'
						),
						self::ORDER_FAILED_SMS             => mowc_( 'Hello {username}, Your order id {order-number} with {site-name} ordered on {order-date} has failed. We will contact you shortly. -miniorange' ),

						self::ORDER_PENDING_NOTIF_HEADER   => mowc_( 'ORDER PENDING PAYMENT NOTIFICATION' ),
						self::ORDER_PENDING_NOTIF_BODY     => mowc_(
							'Customer will be sent a new sms notification notifying' .
																				' that the order is pending payment.'
						),
						self::ORDER_PENDING_SMS            => mowc_( 'Hello {username}, Your order id {order-number} with {site-name} ordered on {order-date} is pending payment. -miniorange' ),
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
			$messages        = maybe_unserialize( MOWC_ADDON_MESSAGES );
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
