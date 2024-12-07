<?php
/**
 * Load the Form Notifications.
 *
 * @package miniorange-otp-verification/notifications/formsmsnotification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'FMSN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FMSN_URL', plugin_dir_url( __FILE__ ) );
define( 'FMSN_VERSION', '1.0.0' );

/*
|------------------------------------------------------------------------------------------------------
| SOME COMMON FUNCTIONS USED ALL OVER THE Notification
|------------------------------------------------------------------------------------------------------
*/


/**
 * This function is used to handle the notifications get option call. A separate function has been created so that
 * we can manage getting of database values all from one place. Any changes need to be made can be made here
 * rather than having to make changes in all of the add-on files.
 *
 * Calls the mains plugins get_mowc_option function.
 *
 * @param string $string - option name.
 * @param bool   $prefix - prefix of option name.
 * @return String
 */
function get_fmsn_option( $string, $prefix = null ) {
	$string = ( null === $prefix ? 'mo_form_sms_' : $prefix ) . $string;
	return get_mowc_option( $string, '' );
}

/**
 * This function is used to handle the notifications update option call. A separate function has been created so that
 * we can manage getting of database values all from one place. Any changes need to be made can be made here
 * rather than having to make changes in all of the add-on files.
 *
 * Calls the mains plugins get_mowc_option function.
 *
 * @param string $option_name - option name.
 * @param string $value - value of option name.
 * @param null   $prefix - prefix before option name.
 */
function update_fmsn_option( $option_name, $value, $prefix = null ) {
	$option_name = ( null === $prefix ? 'mo_form_sms_' : $prefix ) . $option_name;
	update_mowc_option( $option_name, $value, '' );
}
