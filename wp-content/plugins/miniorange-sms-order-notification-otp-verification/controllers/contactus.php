<?php
/**
 * Loads contact us form.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$mo_current_user = wp_get_current_user();
$email           = get_mo_wc_option( 'admin_email' );
$phone           = get_mo_wc_option( 'admin_phone' );
$phone           = $phone ? $phone : '';


require MOV_WC_DIR . 'views/contactus.php';
