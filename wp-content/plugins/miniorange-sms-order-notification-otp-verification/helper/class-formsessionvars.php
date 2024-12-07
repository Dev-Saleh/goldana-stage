<?php
/**Load administrator changes for FormSessionVars
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function lists down all the session variable names
 * being used in the plugin and turn them into constants.
 */
if ( ! class_exists( 'FormSessionVars' ) ) {
	/**
	 * FormSessionVars class
	 */
	class FormSessionVars {

		const TX_SESSION_ID      = 'mo_otp_site_txID';
		const WC_DEFAULT_REG     = 'woocommerce_registration';
		const WC_CHECKOUT        = 'woocommerce_checkout_page';
		const WC_SOCIAL_LOGIN    = 'wc_social_login';
		const WP_DEFAULT_LOGIN   = 'default_wp_login';
		const WP_LOGIN_REG_PHONE = 'default_wp_reg_phone';
		const WC_PRODUCT_VENDOR  = 'wc_product_vendor';
		const WC_BILLING         = 'wc_billing';
		const WC_CHECKOUT_NEW    = 'woocommerce_checkout_new';
		const WC_PROFILE_UPDATE  = 'wc_profile';
	}

}

