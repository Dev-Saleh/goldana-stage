<?php
/**
 * Controller for pricing tab.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\Tabs;

$form_action                    = MoConstants::HOSTNAME . '/moas/login';
$redirect_url                   = MoConstants::HOSTNAME . '/moas/initializepayment';
$portal_host                    = MOV_WC_PORTAL . '/initializePayment';
$free_plan_name                 = 'FREE';
$gateway_plus_addon_name        = 'CUSTOM GATEWAY <br/>WITH ADDONS';
$twilio_gateway_plus_addon_name = 'TWILIO GATEWAY <br/>WITH ADDONS';
$enterprise_name                = 'ENTERPRISE PLAN';
$mo_gateway_plan_name           = 'MINIORANGE GATEWAY <br/>WITH ADDONS';
$free_plan_price                = 'Free';
$gateway_plus_addon             = '$29';
$twilio_gateway_plus_addon      = '$49';
$enterprise_addon               = '$99';
$mo_gateway_plan                = '$0';
$vl                             = MoUtility::mclv() && ! MoUtility::is_mg();
$nonce                          = $admin_handler->get_nonce_value();
$server_uri                     = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : null;
$form_settings                  = add_query_arg( array( 'page' => $tab_details->tab_details[ Tabs::FORMS ]->menu_slug ), $server_uri );

$free_plan_features = array(
	'WooCommerce Order Status SMS Notifications',
	'SMS Notifications to the Customers & Admins',
	'Replacable Order status Tags',
	'OTP Verification on WooCommerce Login, Registration Forms',
	'OTP Verification on WooCommerce Checkout Form',
	'Passwordless Login <br>(Login with Phone & OTP)',
);

$starter_plan_features = array(
	'WooCommerce Order Status SMS Notifications',
	'SMS Notifications to the Customers & Admins',
	'OTP Verification on WooCommerce Login, Registration Forms',
	'OTP Verification on WooCommerce Checkout Form',
	'Passwordless Login <br>(Login with Phone & OTP)',
);

$standard_plan_features = array(
	'<b>All features from Starter Plan</b>',
	'<span><a href="https://plugins.miniorange.com/supported-sms-email-gateways" target="_blank">Custom SMTP/HTTP SMS Gateway Supported</a></span>',
	'WooCommerce Password Reset Over OTP Verification',
	'Woocommerce Stock Notifications',
	'Twillio, MSG91 Gateway Supported',
);

$enterprise_plan_features = array(
	'<b>All features from Standard Plan</b>',
	'WooCommerce Premium Notifications tags supported',
	'OTP verification on WCFM & Checkout WC form',
	'<b style="color:#1261d8">OTP Spam Preventor </b> Addon Supported.',
	'Allow OTP for Selected Country Addon',
	'Extra premium forms support',
);

require MOV_WC_DIR . 'views/pricing.php';
