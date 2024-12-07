<?php
/**
 * Loads the tav view and initializes other controllers.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Handler\MoActionHandlerHandler;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\TabDetails;
use WCSMSOTP\Objects\SubTabDetails;

$registered               = MoUtility::micr();
$activated                = MoUtility::mclv();
$gatewayconfigured        = MoUtility::is_gateway_config();
$plan                     = MoUtility::micv();
$disabled                 = ( ( $registered && $activated ) || ( strcmp( MOV_WC_TYPE, 'MiniOrangeGateway' ) === 0 ) ) ? '' : 'disabled';
$mo_current_user          = wp_get_current_user();
$email                    = get_mo_wc_option( 'admin_email' );
$phone                    = get_mo_wc_option( 'admin_phone' );
$controller               = MOV_WC_DIR . 'controllers/';
$notifications_controller = MOV_WC_DIR . 'notifications/wcsmsnotification/controllers/';
$admin_handler            = MoActionHandlerHandler::instance();
$req_url                  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
$addon                    = add_query_arg( array( 'page' => 'addon' ), remove_query_arg( 'addon', $req_url ) );



$tab_details     = TabDetails::instance();
$sub_tab_details = SubTabDetails::instance();

echo "<div id='main-outer-div' class='w-full flex flex-col border bg-white'>";

require $controller . 'titlebar.php';
require $controller . 'errormessage.php';

echo '<div id="nav-and-view" class="flex">';

		require $controller . 'navbar.php';

echo "  <div class='flex-1'>
            <div id='moblock' class='mo_customer_validation-modal-backdrop dashboard'>" .
				"<img src='" . esc_url( MOV_WC_LOADER_URL ) . "'>" .
		'</div>';
		require $controller . 'subtabs.php';
if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- Reading GET parameter from the URL for checking the tab name, doesn't require nonce verification.

	foreach ( $tab_details->tab_details as $mo_tabs ) {
		if ( sanitize_text_field( wp_unslash( $_GET['page'] ) ) === $mo_tabs->menu_slug ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- Reading GET parameter from the URL for checking the tab name, doesn't require nonce verification.
			include $controller . $mo_tabs->view;
		}
	}

	do_action( 'mo_otp_verification_add_on_controller' );
	include $controller . 'support.php';
}

echo '  </div>';
echo '</div>';

require MOV_WC_DIR . 'views/contactus.php';
