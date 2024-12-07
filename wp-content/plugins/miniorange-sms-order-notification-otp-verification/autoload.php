<?php
/**
 * Initializes plugin data.
 * Contains defination of common functions.
 *
 * @package miniorange-order-notifications-woocommerce
 */

use WCSMSOTP\Helper\MoFormList;
use WCSMSOTP\Helper\FormSessionData;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\FormHandler;
use WCSMSOTP\Objects\IFormHandler;
use WCSMSOTP\SplClassLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MOV_WC_DIR', plugin_dir_path( __FILE__ ) );
define( 'MOV_WC_URL', plugin_dir_url( __FILE__ ) );

$response     = wp_remote_retrieve_body( wp_remote_get( MOV_WC_URL . 'package.json', array( 'sslverify' => false ) ) );
$package_data = json_decode( $response );
if ( json_last_error() !== 0 ) {
	$package_data = json_decode( initialize_wc_package_json() );
}

define( 'MOV_WC_VERSION', $package_data->version );
define( 'MOV_WC_TYPE', $package_data->type );
define( 'MOV_WC_HOST', $package_data->hostname );
define( 'MOV_WC_PORTAL', $package_data->portal );
define( 'MOV_WC_DEFAULT_CUSTOMERKEY', $package_data->dcustomerkey );
define( 'MOV_WC_DEFAULT_APIKEY', $package_data->dapikey );
define( 'MOV_WC_SSL_VERIFY', $package_data->sslverify );
define( 'MOV_WC_CSS_URL', MOV_WC_URL . 'includes/css/mo_customer_validation_style.min.css?version=' . MOV_WC_VERSION );
define( 'MOV_WC_FORM_CSS', MOV_WC_URL . 'includes/css/mo_forms_css.min.css?version=' . MOV_WC_VERSION );
define( 'MO_WC_INTTELINPUT_CSS', MOV_WC_URL . 'includes/css/intlTelInput.min.css?version=' . MOV_WC_VERSION );
define( 'MOV_WC_JS_URL', MOV_WC_URL . 'includes/js/mowc_wc_settings.min.js?version=' . MOV_WC_VERSION );
define( 'WC_VALIDATION_JS_URL', MOV_WC_URL . 'includes/js/formValidation.min.js?version=' . MOV_WC_VERSION );
define( 'MO_WC_INTTELINPUT_JS', MOV_WC_URL . 'includes/js/intlTelInput.min.js?version=' . MOV_WC_VERSION );
define( 'MO_WC_DROPDOWN_JS', MOV_WC_URL . 'includes/js/dropdown.min.js?version=' . MOV_WC_VERSION );
define( 'MOV_WC_LOADER_URL', MOV_WC_URL . 'includes/images/loader.gif' );
define( 'MOV_WC_DONATE', MOV_WC_URL . 'includes/images/donate.png' );
define( 'MOV_WC_PAYPAL', MOV_WC_URL . 'includes/images/paypal.png' );
define( 'MOV_WC_FIREBASE', MOV_WC_URL . 'includes/images/firebase.png' );
define( 'MOV_WC_NETBANK', MOV_WC_URL . 'includes/images/netbanking.png' );
define( 'MOV_WC_CARD', MOV_WC_URL . 'includes/images/card.png' );
define( 'MOV_WC_LOGO_URL', MOV_WC_URL . 'includes/images/logo.png' );
define( 'MOV_WC_ICON', MOV_WC_URL . 'includes/images/miniorange_icon.png' );
define( 'MOV_WC_ICON_GIF', MOV_WC_URL . 'includes/images/mo_icon.gif' );
define( 'MO_WC_CUSTOM_FORM', MOV_WC_URL . 'includes/js/customForm.js?version=' . MOV_WC_VERSION );
define( 'MOV_WC_ADDON_DIR', MOV_WC_DIR . 'notifications/' );
define( 'MOV_WC_USE_POLYLANG', true );
define( 'MO_WC_TEST_MODE', $package_data->testmode );
define( 'MO_WC_FAIL_MODE', $package_data->failmode );
define( 'MOV_WC_SESSION_TYPE', $package_data->session );
define( 'MOV_WC_MAIL_LOGO', MOV_WC_URL . 'includes/images/mo_support_icon.png' );
define( 'MOV_WC_OFFERS_LOGO', MOV_WC_URL . 'includes/images/mo_sale_icon.png' );
define( 'MOV_WC_FEATURES_GRAPHIC', MOV_WC_URL . 'includes/images/mo_features_graphic.png' );
define( 'MOV_WC_TYPE_PLAN', $package_data->typeplan );
define( 'MOV_WC_LICENSE_NAME', $package_data->licensename );

define( 'MOV_WC_MAIN_CSS', MOV_WC_URL . 'includes/css/mow-main.min.css' );


require 'class-splclassloader.php';

$idp_class_loader = new SplClassLoader( 'WCSMSOTP', realpath( __DIR__ . DIRECTORY_SEPARATOR . '..' ) );
$idp_class_loader->register();
require_once 'views/common-elements.php';
initialize_wc_forms();

/**
 * Initializes hanlders of forms.
 */
function initialize_wc_forms() {
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( MOV_WC_DIR . 'handler/forms', RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ( $iterator as $it ) {
		$filename   = $it->getFilename();
		$filename   = str_replace( 'class-', '', $filename );
		$class_name = 'WCSMSOTP\\Handler\\Forms\\' . str_replace( '.php', '', $filename );

		$handler_list = MoFormList::instance();

		$form_handler = $class_name::instance();
		$handler_list->add( $form_handler->get_form_key(), $form_handler );
	}
}

/**
 * Returns admin post url.
 */
function admin_wc_post_url() {
	return admin_url( 'admin-post.php' ); }

/**
 * Returns wp ajax url.
 */
function wp_wc_ajax_url() {
	return admin_url( 'admin-ajax.php' ); }

/**
 * Used for transalating the string
 *
 * @param string $string - option name to be deleted.
 */
function mowc_( $string ) {
	$string = preg_replace( '/\s+/S', ' ', $string );
	return is_scalar( $string )
			? ( MoUtility::is_polylang_installed() && MOV_WC_USE_POLYLANG ? pll__( $string ) : __( $string, 'miniorange-otp-verification' ) ) // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText --common function for string translation.
			: $string;
}

/**
 * Updates the option set in the wp_option table.
 *
 * @param string $string - option name to be deleted.
 * @param string $type - value of the option.
 */
function mowc_esc_string( $string, $type ) {

	if ( 'attr' === $type ) {
		return esc_attr( $string );
	} elseif ( 'url' === $type ) {
		return esc_url( $string );
	}

	return esc_attr( $string );
}

/**
 * Retrieved the value of the option in the wp_option table.
 *
 * @param string $string - option name to be deleted.
 * @param string $prefix - prefix of the option.
 */
function get_mo_wc_option( $string, $prefix = null ) {
	$string = ( null === $prefix ? 'mo_wc_customer_validation_' : $prefix ) . $string;
	return apply_filters( 'get_mo_wc_option', get_site_option( $string ) );
}

/**
 * Updates the option set in the wp_option table.
 *
 * @param string $string - option name to be deleted.
 * @param string $value - value of the option.
 * @param string $prefix - prefix of the option.
 */
function update_mo_wc_option( $string, $value, $prefix = null ) {
	$string = ( null === $prefix ? 'mo_wc_customer_validation_' : $prefix ) . $string;
	update_site_option( $string, apply_filters( 'update_mo_wc_option', $value, $string ) );
}

/**
 * Deletes the option set in the wp_option table.
 *
 * @param string $string - option name to be deleted.
 * @param string $prefix - prefix of the option.
 */
function delete_mo_wc_option( $string, $prefix = null ) {
	$string = ( null === $prefix ? 'mo_wc_customer_validation_' : $prefix ) . $string;
	delete_site_option( $string );
}

/**
 * Returns the plugin details like version, plan name.
 *
 * @param object $obj - object of the class.
 */
function get_mo_wc_class( $obj ) {
	$namespace_class = get_class( $obj );
	return substr( $namespace_class, strrpos( $namespace_class, '\\' ) + 1 );
}

/**
 * Returns the plugin details like version, plan name.
 */
function initialize_wc_package_json() {
			$package = wp_json_encode(
				array(
					'name'         => 'miniorange-otp-verification',
					'version'      => '4.3.4',
					'type'         => 'MiniOrangeGateway',
					'testmode'     => false,
					'failmode'     => false,
					'hostname'     => 'https://login.xecurify.com',
					'dcustomerkey' => '16555',
					'dapikey'      => 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq',
					'sslverify'    => false,
					'session'      => 'TRANSIENT',
					'typeplan'     => 'wp_otp_verification_basic_plan',
					'licensename'  => 'WP_OTP_VERIFICATION_PLUGIN',
				)
			);
			return $package;
}
