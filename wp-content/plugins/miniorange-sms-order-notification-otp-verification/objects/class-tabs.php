<?php
/**Load Tabs
 *
 * @package miniorange-order-notifications-woocommerce/objects
 */

namespace WCSMSOTP\Objects;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Tabs' ) ) {
	/**
	 * This class is used to define the base url of tabs of plugin
	 */
	final class Tabs {

		const FORMS            = 'forms';
		const ACCOUNT          = 'account';
		const PRICING          = 'pricing';
		const WHATSAPP         = 'whatsapp';
		const ADD_ONS          = 'addons';
		const WOOCOMERCE_NOTIF = 'woocommerce_notifications';
		const GATEWAY_CONFIG   = 'gateway_config';
		const OTP_SETTINGS     = 'otp_settings';
	}
}
