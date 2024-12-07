<?php
/**
 * Load admin view for WooCommerceSocialLoginForm.
 *
 * @package miniorange-order-notifications-woocommerce/controller/forms/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Handler\Forms\WooCommerceSocialLoginForm;

$handler         = WooCommerceSocialLoginForm::instance();
$wc_social_login = (bool) $handler->is_form_enabled() ? 'checked' : '';
$form_name       = $handler->get_form_name();

require MOV_WC_DIR . 'views/forms/woocommercesocialloginform.php';
