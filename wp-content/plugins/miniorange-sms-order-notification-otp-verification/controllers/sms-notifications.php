<?php
/**
 * Load view for SMS Notifications List
 *
 * @package miniorange-order-notifications-woocommerce/controllers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Objects\SubTabDetails;

$sub_tab_details = SubTabDetails::instance();

$subtab              = isset( $_GET['subpage'] ) ? sanitize_text_field( wp_unslash( $_GET['subpage'] ) ) : 'WcNotifSubTab'; //phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- Reading GET parameter from the URL for checking the form name, doesn't require nonce verification.
$wc_notif_controller = MOV_WC_DIR . 'notifications/wcsmsnotification/controllers/wc-sms-notification.php';

require $wc_notif_controller;
require $controller . 'wc-premium-notifications.php';
