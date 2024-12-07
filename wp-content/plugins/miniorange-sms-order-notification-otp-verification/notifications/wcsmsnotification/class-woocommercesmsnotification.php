<?php
/**
 * Initializer functions for addon files.
 *
 * @package miniorange-order-notifications-woocommerce
 */

namespace WCSMSOTP\Notifications\WcSMSNotification;

use WCSMSOTP\Notifications\WcSMSNotification\Handler\WooCommerceNotifications;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnMessages;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\WooCommerceNotificationsList;
use WCSMSOTP\Helper\AddOnList;
use WCSMSOTP\Objects\AddOnInterface;
use WCSMSOTP\Objects\BaseAddOn;
use WCSMSOTP\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require 'autoload.php';

/**
 * This class is used to initialize all the Handlers, Helpers, Controllers,
 * Styles and Scripts of the addon.
 */
if ( ! class_exists( 'WooCommerceSmsNotification' ) ) {
	/**
	 * WooCommerceSmsNotification class
	 */
	final class WooCommerceSmsNotification extends BaseAddon implements AddOnInterface {

		use Instance;

		/** Declare Default variables */
		public function __construct() {
			parent::__construct();

			add_action( 'mo_otp_verification_delete_addon_options', array( $this, 'mowc_sms_notif_delete_options' ) );
		}


		/**
		 * Initialize all handlers associated with the addon
		 */
		public function initialize_handlers() {
			/** Initialize instance for addon list handler
			 *
			 *  @var AddOnList $list
			 */
			$list = AddOnList::instance();
			/** Initialize instance for Woocommerce Notifications handler
			 *
			 *  @var WooCommerceNotifications $handler
			 */
			$handler = WooCommerceNotifications::instance();
			$list->add( $handler->getAddOnKey(), $handler );
		}

		/**
		 * Initialize all helper associated with the addon
		 */
		public function initialize_helpers() {
			MoWcAddOnMessages::instance();
			WooCommerceNotificationsList::instance();
		}


		/**
		 * This function hooks into the mo_otp_verification_add_on_controller
		 * hook to show woocommerce notification settings page and forms for
		 * validation.
		 */
		public function show_addon_settings_page() {
			include WC_MSN_DIR . '/controllers/main-controller.php';
		}


		/**
		 * Function is called during deletion of the plugin to delete any options
		 * related to the add-on. This function hooks into the 'mo_otp_verification_delete_addon_options'
		 * hook of the OTP verification plugin.
		 */
		public function mowc_sms_notif_delete_options() {
			delete_site_option( 'mowc_sms_notification_settings' );
		}
	}


}
