<?php
/**Load Abstract Class SubTabDetails
 *
 * @package miniorange-order-notifications-woocommerce/objects
 */

namespace WCSMSOTP\Objects;

use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Subtab details class.
 */
if ( ! class_exists( 'SubTabDetails' ) ) {
	/**
	 * SubTabDetails class
	 */
	final class SubTabDetails {

		use Instance;

		/**
		 * Array of SubtabPageDetails Object detailing
		 * all the page menu options.
		 *
		 * @var array[SubtabPageDetails] $sub_tab_details
		 */
		public $sub_tab_details;

		/**
		 * Array of SubtabPageDetails Object detailing
		 * all the page menu options.
		 *
		 * @var array[SubtabPageDetails] $notification_sub_tab_details
		 */
		public $notification_sub_tab_details;

		/** Private constructor to avoid direct object creation */
		private function __construct() {

			$this->notification_sub_tab_details = array(
				WcSubTabs::MOWC_WC_NOTIF    => new SubtabPageDetails(
					'Notifications',
					mowc_( 'WooCommerce' ),
					mowc_( 'WooCommerce' ),
					'sms-notifications.php',
					'WcNotifSubTab',
					'background:#D8D8D8'
				),
				WcSubTabs::MOWC_FORM_NOTIF  => new SubtabPageDetails(
					'Form Notifications',
					mowc_( 'Form' ),
					mowc_( 'Forms Notifications' ),
					'sms-notifications.php',
					'formNotifSubTab',
					'background:#D8D8D8'
				),
				WcSubTabs::MOWC_DOKAN_NOTIF => new SubtabPageDetails(
					'Dokan Notifications',
					mowc_( 'Dokan' ),
					mowc_( 'Dokan (Vendor)' ),
					'sms-notifications.php',
					'dokanNotifSubTab',
					'background:#D8D8D8'
				),
				WcSubTabs::MOWC_WCFM_NOTIF  => new SubtabPageDetails(
					'WCFM Notifications',
					mowc_( 'WCFM' ),
					mowc_( 'WCFM (Vendor)' ),
					'sms-notifications.php',
					'wcfmNotifSubTab',
					'background:#D8D8D8'
				),
			);

			$this->sub_tab_details = array(
				'mowcnotifications' => $this->notification_sub_tab_details,
			);
		}
	}
}
