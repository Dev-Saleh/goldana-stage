<?php //phpcs:ignore -- legacy plugin
/**
 * Plugin Name: SMS Notifications / Order Notification WooCommerce / Login & Register with Phone
 * Plugin URI: http://miniorange.com/miniorange-woocommerce-otp-plugin
 * Description: WooCommerce SMS Notification. WCFM & Dokan Notifications. SMS & Email OTP Verification for all WooCommerce forms. PasswordLess Login. 24/7 support.
 * Version: 4.3.4
 * Author: miniOrange
 * Author URI: http://miniorange.com
 * Text Domain: miniorange-order-notifications-woocommerce
 * Domain Path: /lang
 * WC requires at least: 2.0.0
 * WC tested up to: 8.5.2
 * License: GPL2
 *
 * @package miniorange-order-notifications-woocommerce
 */

use WCSMSOTP\MoWcInit;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'MOVWC_PLUGIN_NAME', plugin_basename( __FILE__ ) );
$dir_name = substr( MOVWC_PLUGIN_NAME, 0, strpos( MOVWC_PLUGIN_NAME, '/' ) );
define( 'MOVWC_NAME', $dir_name );

/**
 * WooCommerce hook to show that the plugin is compatible with the HPOS functionality.
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

require 'autoload.php';
MoWcInit::instance(); // initialize the main class.
