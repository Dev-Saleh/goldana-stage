<?php
/**
 * Controller for settings
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\Tabs;
use WCSMSOTP\Helper\MoFormList;

$page_list = add_query_arg( 'post_type', 'page', admin_url() . 'edit.php' );
$plan_type = MoUtility::micv() ? 'wp_otp_verification_upgrade_plan' : 'wp_otp_verification_basic_plan';

$form_handler_list = MoFormList::instance();


$nonce    = $admin_handler->get_nonce_value();
$moaction = add_query_arg(
	array(
		'page' => $tab_details->tab_details[ Tabs::FORMS ]->menu_slug,
		'form' => 'configured_forms#configured_forms',
	)
);

$forms_list_page = add_query_arg(
	'page',
	$tab_details->tab_details[ Tabs::FORMS ]->menu_slug . '#form_search',
	remove_query_arg( array( 'form' ) )
);

$form_name             = isset( $_GET['form'] ) ? sanitize_text_field( wp_unslash( $_GET['form'] ) ) : false;  // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for checking the Form name, doesn't require nonce verification.
$show_configured_forms = 'configured_forms' === $form_name;

$support = MoConstants::FEEDBACK_EMAIL;

require MOV_WC_DIR . 'views/settings.php';
