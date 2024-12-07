<?php
/**
 * Loads admin view for OTP Settings functionality.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$show_dropdown_on_form = get_mo_wc_option( 'show_dropdown_on_form' ) ? 'checked' : '';

require_once MOV_WC_DIR . 'views/mowcotpsettings.php';
