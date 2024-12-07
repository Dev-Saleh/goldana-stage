<?php
/**
 * Loads admin view for WhatsApp functionality.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Objects\Tabs;

$license_url = add_query_arg( array( 'page' => $tab_details->tab_details[ Tabs::PRICING ]->menu_slug ), $request_uri );

require_once MOV_WC_DIR . 'views/mowhatsapp.php';
