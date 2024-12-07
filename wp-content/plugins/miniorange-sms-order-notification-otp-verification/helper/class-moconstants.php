<?php
/**Load administrator changes for MoConstants
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class lists down all the OTP Constant variables.
 */
if ( ! class_exists( 'MoConstants' ) ) {
	/**
	 * MoConstants class
	 */
	class MoConstants {

		const HOSTNAME              = MOV_WC_HOST;
		const DEFAULT_CUSTOMER_KEY  = MOV_WC_DEFAULT_CUSTOMERKEY;
		const DEFAULT_API_KEY       = MOV_WC_DEFAULT_APIKEY;
		const FROM_EMAIL            = 'no-reply@xecurify.com';
		const SUPPORT_EMAIL         = 'info@xecurify.com';
		const FEEDBACK_EMAIL        = 'otpsupport@xecurify.com';
		const SUCCESS               = 'SUCCESS';
		const ERROR                 = 'ERROR';
		const FAILURE               = 'FAILURE';
		const AREA_OF_INTEREST      = 'WP OTP Verification Plugin';
		const QUERY_NAME            = 'WC OTP & Notifications';
		const PLUGIN_TYPE           = MOV_WC_TYPE;
		const PATTERN_PHONE         = '/^[\+]\d{1,4}\d{7,12}$|^[\+]\d{1,4}[\s]\d{7,12}$/';
		const PATTERN_COUNTRY_CODE  = '/^[\+]\d{1,4}.*/';
		const PATTERN_SPACES_HYPEN  = '/([\(\) \-]+)/';
		const ERROR_JSON_TYPE       = 'error';
		const SUCCESS_JSON_TYPE     = 'success';
		const EMAIL_TRANS_REMAINING = 10;
		const PHONE_TRANS_REMAINING = 10;
		const FAQ_URL               = 'https://faq.miniorange.com/kb/otp-verification/';
		const VIEW_TRANSACTIONS     = '/moas/viewtransactions';
		const FAQ_PAY_URL           = 'https://faq.miniorange.com/knowledgebase/how-to-make-payment-for-the-otp-verification-plugin';
		const MOCOUNTRY             = 'India';
	}
}
