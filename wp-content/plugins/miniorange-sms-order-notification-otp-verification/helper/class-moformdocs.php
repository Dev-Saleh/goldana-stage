<?php
/**Load administrator changes for MoFormDocs
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function is used to show docs links for forms in WordPress.
 */
if ( ! class_exists( 'MoFormDocs' ) ) {
	/**
	 * MoFormDocs class
	 */
	class MoFormDocs {

		const WOCOMMERCE_SMS_NOTIFICATION_LINK = array(
			'guideLink' => 'https://plugins.miniorange.com/wordpress-otp-verification',
			'videoLink' => 'https://www.youtube.com/watch?v=atlyqTy8RHI&ab_channel=miniOrange',
		);

		const WC_FORM_LINK     = array(
			'formLink'  => 'https://wordpress.org/plugins/woocommerce/',
			'guideLink' => 'https://plugins.miniorange.com/otp-verification-woocommerce-registration-form/',
			'videoLink' => 'https://youtu.be/IpsJ9cRTYSI',
		);
		const WC_CHECKOUT_LINK = array(
			'formLink'  => 'https://wordpress.org/plugins/woocommerce/',
			'guideLink' => 'https://plugins.miniorange.com/otp-verification-woocommerce-checkout-form/',
			'videoLink' => 'https://youtu.be/atlyqTy8RHI',
		);
		const WC_BILLING_LINK  = array(
			'formLink'  => 'https://wordpress.org/plugins/woocommerce/',
			'guideLink' => 'https://plugins.miniorange.com/otp-verification-woocommerce-billing-address-update-form/',
			'videoLink' => 'https://youtu.be/4q1rKeiPKvw',
		);

		const WC_SOCIAL_LOGIN = array(
			'formLink'  => 'https://woocommerce.com/products/woocommerce-social-login/',
			'guideLink' => 'https://plugins.miniorange.com/otp-verification-for-woocommerce-social-login',
			'videoLink' => '',
		);

		const WC_PRODUCT_VENDOR = array(
			'formLink'  => 'https://woocommerce.com/products/product-vendors/',
			'guideLink' => 'https://plugins.miniorange.com/otp-verification-for-woocommerce-product-vendor-registration-form',
			'videoLink' => '',
		);

		const LOGIN_FORM           = array(
			'formLink'  => '',
			'guideLink' => 'https://plugins.miniorange.com/otp-verification-login-form',
			'videoLink' => 'https://www.youtube.com/watch?v=RyNWI-t2kDo',
		);
		const WCFM_FORM            = array(
			'formLink'  => 'https://wordpress.org/plugins/wc-frontend-manager/',
			'guideLink' => 'https://plugins.miniorange.com/otp-verification-for-woocommerce-frontend-manager-form',
			'videoLink' => '',
		);
		const WC_NEW_CHECKOUT_LINK = array(
			'formLink'  => 'https://wordpress.org/plugins/woocommerce/',
			'guideLink' => '',
			'videoLink' => '',
		);
	}
}
