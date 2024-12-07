<?php
/**
 * Load admin view for WooCommerceCheckOutForm.
 *
 * @package miniorange-order-notifications-woocommerce/controller/forms/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Handler\Forms\WooCommerceCheckOutForm;

$handler                   = WooCommerceCheckOutForm::instance();
$wc_checkout               = $handler->is_form_enabled() ? 'checked' : '';
$wc_checkout_hidden        = 'checked' === $wc_checkout ? '' : 'hidden';
$wc_checkout_enable_type   = $handler->get_otp_type_enabled();
$guest_checkout            = $handler->isGuestCheckoutOnlyEnabled() ? 'checked' : '';
$checkout_button           = $handler->showButtonInstead() ? 'checked' : '';
$checkout_popup            = $handler->isPopUpEnabled() ? 'checked' : '';
$checkout_payment_plans    = $handler->getPaymentMethods();
$checkout_selection        = $handler->isSelectivePaymentEnabled() ? 'checked' : '';
$checkout_selection_hidden = 'checked' === $checkout_selection ? '' : 'hidden';
$wc_type_phone             = $handler->get_phone_html_tag();
$wc_type_email             = $handler->get_email_html_tag();
$button_text               = $handler->get_button_text();
$form_name                 = $handler->get_form_name();
$disable_autologin         = $handler->isAutoLoginDisabled() ? 'checked' : '';
$restrict_duplicates       = $handler->restrict_duplicates() ? 'checked' : '';

require MOV_WC_DIR . 'views/forms/woocommercecheckoutform.php';
