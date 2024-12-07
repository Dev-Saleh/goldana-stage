<?php
/**
 * Loads view for accounts tab.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Handler\MoRegistrationHandler;
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoUtility;

$url = MoConstants::HOSTNAME . '/moas/login?redirectUrl=' . MoConstants::HOSTNAME . '/moas/viewlicensekeys';

$handler = MoRegistrationHandler::instance();


if ( get_mo_wc_option( 'verify_customer' ) ) {
	$admin_email = get_mo_wc_option( 'admin_email' ) ? get_mo_wc_option( 'admin_email' ) : '';
	$nonce       = $handler->get_nonce_value();
	require_once MOV_WC_DIR . 'views/account/login.php';
} elseif ( ! MoUtility::micr() ) {
	$mo_current_user = wp_get_current_user();
	$admin_phone     = get_mo_wc_option( 'admin_phone' ) ? get_mo_wc_option( 'admin_phone' ) : '';
	$nonce           = $handler->get_nonce_value();
	delete_site_option( 'password_mismatch' );
	update_mo_wc_option( 'new_registration', 'true' );
	require_once MOV_WC_DIR . 'views/account/register.php';
} elseif ( MoUtility::micr() && ! MoUtility::mclv() ) {
	$nonce = $handler->get_nonce_value();
	require_once MOV_WC_DIR . 'views/account/verify-lk.php';
} else {
	$customer_id = get_mo_wc_option( 'admin_customer_key' );
	$api_key     = get_mo_wc_option( 'admin_api_key' );
	$token       = get_mo_wc_option( 'customer_token' );
	$vl          = MoUtility::mclv() && ! MoUtility::is_mg();
	$nonce       = $admin_handler->get_nonce_value();
	$regnonce    = $handler->get_nonce_value();
	require_once MOV_WC_DIR . 'views/account/profile.php';
}
