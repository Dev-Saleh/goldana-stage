<?php
/**
 * Autoload file for some common functions used all over the addon
 *
 * @package miniorange-order-notifications-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_MSN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WC_MSN_URL', plugin_dir_url( __FILE__ ) );
define( 'WC_MSN_VERSION', '1.0.0' );
define( 'WC_MSN_CSS_URL', WC_MSN_URL . 'includes/css/settings.min.css?version=' . WC_MSN_VERSION );
define( 'MOWC_MSN_JS_URL', WC_MSN_URL . 'includes/js/mo_wc_settings.min.js?version=' . WC_MSN_VERSION );

/*
|------------------------------------------------------------------------------------------------------
| SOME COMMON FUNCTIONS USED ALL OVER THE ADD-ON
|------------------------------------------------------------------------------------------------------
 */


/**
 * This function is used to handle the add-ons get option call. A separate function has been created so that
 * we can manage getting of database values all from one place. Any changes need to be made can be made here
 * rather than having to make changes in all of the add-on files.
 *
 * Calls the mains plugins get_mo_option function.
 *
 * @param string      $string - option name.
 * @param bool|string $prefix - prefix for database keys.
 * @return String
 */
function get_mowc_option( $string, $prefix = null ) {
	$string = ( null === $prefix ? 'mo_wc_sms_customer_' : $prefix ) . $string;
	return get_mo_wc_option( $string, '' );
}

/**
 * This function is used to handle the add-ons update option call. A separate function has been created so that
 * we can manage getting of database values all from one place. Any changes need to be made can be made here
 * rather than having to make changes in all of the add-on files.
 *
 * Calls the mains plugins get_mo_option function.
 *
 * @param string      $option_name - key of the option name.
 * @param string      $value - value of the option.
 * @param null|string $prefix - prefix of the key for database entry.
 */
function update_mowc_option( $option_name, $value, $prefix = null ) {
	$option_name = ( null === $prefix ? 'mo_wc_sms_customer_' : $prefix ) . $option_name;
	update_mo_wc_option( $option_name, $value, '' );
}
