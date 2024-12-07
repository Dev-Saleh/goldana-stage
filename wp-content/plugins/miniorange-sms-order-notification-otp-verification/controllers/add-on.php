<?php
/**
 * Loads list of addons.
 *
 * @package miniorange-otp-verification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( isset( $_GET['mowcaddon'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- Reading GET parameter from the addon for checking the addon name, doesn't require nonce verification.
	return;
}

require_once MOV_WC_DIR . 'views/add-on.php';
