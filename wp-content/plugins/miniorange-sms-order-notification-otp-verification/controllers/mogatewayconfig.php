<?php
/**
 * Loads admin view for Gateway Settings functionality.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$request_uri = remove_query_arg( array( 'addon', 'form', 'subpage' ), isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' );
$license_url = add_query_arg( array( 'page' => 'mowcpricing' ), $request_uri );
$pricing_url = MOV_WC_PORTAL . '/initializepayment?requestOrigin=wp_otp_verification_basic_plan';

require_once MOV_WC_DIR . 'views/mogatewayconfig.php';
