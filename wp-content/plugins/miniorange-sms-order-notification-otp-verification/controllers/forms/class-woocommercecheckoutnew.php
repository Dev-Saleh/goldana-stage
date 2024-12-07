<?php
/**
 * Load admin view for WooCommerce Checkout New Form.
 *
 * @package miniorange-order-notifications-woocommerce/controller/forms/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Handler\Forms\WooCommerceCheckoutNew;

$handler                     = WooCommerceCheckoutNew::instance();
$wc_new_checkout             = $handler->is_form_enabled() ? 'checked' : '';
$wc_new_checkout_hidden      = 'checked' === $wc_new_checkout ? '' : 'style=display:none';
$wc_new_checkout_enable_type = $handler->get_otp_type_enabled();
$wc_new_type_phone           = $handler->get_phone_html_tag();
$wc_new_type_email           = $handler->get_email_html_tag();
$form_name                   = $handler->get_form_name();

require_once MOV_WC_DIR . 'views/forms/woocommercecheckoutnew.php';
