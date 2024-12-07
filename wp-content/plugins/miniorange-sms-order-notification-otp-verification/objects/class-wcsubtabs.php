<?php
/**Load Abstract Class SubTabs
 *
 * @package miniorange-order-notifications-woocommerce/objects
 */

namespace WCSMSOTP\Objects;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WcSubTabs class.
 */
if ( ! class_exists( 'WcSubTabs' ) ) {
	/**
	 * WcSubTabs class
	 */
	final class WcSubTabs {
		const MOWC_WC_NOTIF    = 'wc_notification';
		const MOWC_CUSTOM_MSG  = 'custom_message';
		const MOWC_DOKAN_NOTIF = 'dokan_vendor_notifications';
		const MOWC_WCFM_NOTIF  = 'wcfm_vendor_notifications';
		const MOWC_FORM_NOTIF  = 'form_sms_notification';
	}
}
