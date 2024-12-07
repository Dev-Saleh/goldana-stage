<?php
/**
 * Load controller for SMS Notifications view
 *
 * @package miniorange-order-notifications-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Notifications\WcSMSNotification\Handler\WooCommerceNotifications;

$registerd           = WooCommerceNotifications::instance()->moAddOnV();
	$disabled        = ! $registerd ? 'disabled' : '';
	$mo_current_user = wp_get_current_user();
	$controller      = WC_MSN_DIR . 'controllers/';
	$url             = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$addon           = add_query_arg( array( 'page' => 'addon' ), remove_query_arg( 'addon', $url ) );
