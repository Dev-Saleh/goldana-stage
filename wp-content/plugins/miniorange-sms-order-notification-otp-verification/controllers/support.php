<?php
/**
 * Loads support form view.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Helper\MoConstants;

$mo_current_user = wp_get_current_user();
$email           = get_mo_wc_option( 'admin_email' );
$phone           = get_mo_wc_option( 'admin_phone' );
$phone           = $phone ? $phone : '';
$support         = MoConstants::FEEDBACK_EMAIL;

require MOV_WC_DIR . 'views/support.php';
