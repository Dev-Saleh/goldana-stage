<?php
/**Load administrator changes for PremiumAddonList
 *
 * @package miniorange-otp-verification/helper
 */

namespace WCSMSOTP\Helper;

use WCSMSOTP\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This is the constant class which lists all the texts
 * that need to be supported for the Premium addon List.
 */
if ( ! class_exists( 'PremiumAddonList' ) ) {
	/**
	 * PremiumAddonList class
	 */
	final class PremiumAddonList {

		use Instance;
		/** Variable declaration
		 *
		 * @var $premium_addon
		 */
		private $premium_addon;

		/** Variable declaration
		 *
		 * @var $addon_name
		 */
		private $addon_name;

		/**Constructor
		 **/
		private function __construct() {
			$this->premium_addon = array(
				'reg_only_phone_addon'       => array(
					'name'        => 'Register Using Only Phone Number',
					'description' => array(
						mowc_( 'Register with phone number and OTP' ),
						mowc_( 'No email required' ),
						mowc_( 'Register user on Woocommerce Registration form using Phone number only!' ),
					),
					'svg'         => '<svg width="50" height="50" viewBox="0 0 100 100" fill="none">
										<g id="5702c03fcabb879bfe0641db68c0bd60">
										<g id="fc53fd539e2f2cc4097cb8e3ecf1606d">
											<path id="0b5274ab205e435baa8eb19539234e50" d="M90 0H10C4.47715 0 0 4.47715 0 10V90C0 95.5229 4.47715 100 10 100H90C95.5229 100 100 95.5229 100 90V10C100 4.47715 95.5229 0 90 0Z" fill="url(#b166b3f6e9757cea71b85b13ea51b3dd)"></path>
										</g>
										<g id="a68f7d5e1a6388ee04e4eaab3bdff766">
											<g id="7f362f4b2891cb2c9c684ac05e53bb73">
											<path id="b6f186ef9482fd3626edade6146f28c7" fill-rule="evenodd" clip-rule="evenodd" d="M70.25 62.0466V65.75C70.25 68.2353 68.2353 70.25 65.75 70.25C45.8678 70.25 29.75 54.1322 29.75 34.25C29.75 31.7647 31.7647 29.75 34.25 29.75H37.9534C39.7934 29.75 41.4481 30.8703 42.1315 32.5787L43.9622 37.1556C44.8314 39.3286 43.8899 41.8051 41.7965 42.8517L41 43.25C41 43.25 42.125 48.875 46.625 53.375C51.125 57.875 56.75 59 56.75 59L57.1483 58.2035C58.1949 56.1101 60.6714 55.1686 62.8444 56.0378L67.4213 57.8685C69.1297 58.5519 70.25 60.2066 70.25 62.0466ZM66.875 34.25C66.875 36.7353 64.8603 38.75 62.375 38.75C59.8897 38.75 57.875 36.7353 57.875 34.25C57.875 31.7647 59.8897 29.75 62.375 29.75C64.8603 29.75 66.875 31.7647 66.875 34.25ZM66.2 41C68.4368 41 70.25 42.8132 70.25 45.05C70.25 46.5412 69.0412 47.75 67.55 47.75H57.2C55.7088 47.75 54.5 46.5412 54.5 45.05C54.5 42.8132 56.3132 41 58.55 41H66.2Z" fill="white"></path>
											</g>
										</g>
										</g>
										<defs>
										<linearGradient id="b166b3f6e9757cea71b85b13ea51b3dd" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
											<stop stop-color="#EB57E5"></stop>
											<stop offset="1" stop-color="#3FFFFF"></stop>
										</linearGradient>
										</defs>
									</svg>',
					'price'         => '$49',
					'guide_link'    => 'https://plugins.miniorange.com/how-to-configure-register-using-only-phone-addon',
					'purchase_link' => 'https://portal.miniorange.com/initializePayment?requestOrigin=wp_otp_register_with_phone_addon_plan',
					'support_msg'   => 'Hi I am interested in the Register Using Only Phone Number addon, could you please tell me more about this addon?',
				),
				'wc_pass_reset_addon'        => array(
					'name'        => 'WooCommerce Password Reset Over OTP ',
					'description' => array(
						mowc_( 'Reset WooCommerce password using OTP' ),
						mowc_( 'Reset password using Phone Number' ),
						mowc_( 'User Friendly Password Reset' ),
					),
					'svg'         => '<svg width="50" height="50" viewBox="0 0 100 100" fill="none" >
											<g id="563f69671e04ffeef3b83ea51e866208">
											<rect width="100" height="100" rx="10" fill="url(#7df1013e6d8f719f861e1022a164bc7d)"></rect>
											<g id="34454dd908cc56c9684f81974317ce7c">
												<g id="38645efbc9a6fbc2f72deb9c7ceb7067">
												<path id="0db6786045b6f3ac2f30450d4a1abca3" fill-rule="evenodd" clip-rule="evenodd" d="M50 75C63.8071 75 75 63.8071 75 50C75 36.1929 63.8071 25 50 25C36.1929 25 25 36.1929 25 50C25 63.8071 36.1929 75 50 75ZM56.2069 34.308C55.8248 33.3455 54.7348 32.8751 53.7724 33.2572C52.8099 33.6394 52.3395 34.7294 52.7216 35.6918L53.156 36.7858C52.139 36.5712 51.0827 36.4582 50 36.4582C42.1848 36.4582 35.625 42.4013 35.625 49.9999C35.625 51.2128 35.7947 52.3909 36.1136 53.5126C36.3968 54.5087 37.4338 55.0866 38.4299 54.8034C39.4259 54.5203 40.0038 53.4832 39.7207 52.4872C39.4953 51.6944 39.375 50.8613 39.375 49.9999C39.375 44.7118 44.008 40.2082 50 40.2082C52.035 40.2082 53.9261 40.7342 55.5312 41.6388C56.2234 42.0288 57.0864 41.9402 57.6849 41.4176C58.2834 40.895 58.4875 40.0519 58.1943 39.3133L56.2069 34.308ZM63.8864 46.4872C63.6032 45.4911 62.5662 44.9132 61.5701 45.1964C60.5741 45.4795 59.9962 46.5165 60.2793 47.5126C60.5047 48.3054 60.625 49.1385 60.625 49.9999C60.625 55.2879 55.992 59.7916 50 59.7916C47.965 59.7916 46.0739 59.2655 44.4688 58.361C43.7766 57.9709 42.9136 58.0595 42.315 58.5821C41.7165 59.1048 41.5124 59.9479 41.8056 60.6864L43.7931 65.6918C44.1752 66.6543 45.2652 67.1247 46.2276 66.7425C47.1901 66.3604 47.6605 65.2704 47.2784 64.308L46.844 63.2139C47.861 63.4286 48.9173 63.5416 50 63.5416C57.8152 63.5416 64.375 57.5985 64.375 49.9999C64.375 48.787 64.2053 47.6089 63.8864 46.4872Z" fill="white"></path>
												</g>
											</g>
											</g>
											<defs>
											<linearGradient id="7df1013e6d8f719f861e1022a164bc7d" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
												<stop stop-color="#FF7AB2"></stop>
												<stop offset="1" stop-color="#EA6CFF"></stop>
											</linearGradient>
											</defs>
										</svg>',
					'price'        => '$19',
					'guide_link'   => 'https://plugins.miniorange.com/how-to-configure-woocommerce-password-reset-addon',
					'purchase_link'=> 'https://portal.miniorange.com/initializePayment?requestOrigin=wp_otp_wc_password_reset_addon_plan',
					'support_msg'  => 'Hi! I am interested in the WooCommerce Password Reset Over OTP addon, could you please tell me more about this addon?',
				),
				'otp_control'                => array(
					'name'        => 'Limit OTP Request (Spam Preventor) ',
					'description' => array(
						mowc_( 'Set timer to resend OTP' ),
						mowc_( 'Block Sending OTP Until set timer out' ),
						mowc_( 'Limit OTPs based on IP' ),
						mowc_( 'Restrict user from multiple OTP attempts' ),
					),
					'svg'         => '<svg width="50" height="50" viewBox="0 0 100 100" fill="none">
										<g id="033b90d886830bac50b11c6b379dcafe">
										<rect width="100" height="100" rx="10" fill="url(#3495f85936cfe87c48ae6be73d1ec048)"></rect>
										<g id="910adee180532c09d094ca011b854458">
											<path id="88000f903c64892e337b114c0f69607c" fill-rule="evenodd" clip-rule="evenodd" d="M50 72.5C62.4264 72.5 72.5 62.4264 72.5 50C72.5 37.5736 62.4264 27.5 50 27.5C37.5736 27.5 27.5 37.5736 27.5 50C27.5 62.4264 37.5736 72.5 50 72.5ZM59 51.6875C59.932 51.6875 60.6875 50.932 60.6875 50C60.6875 49.068 59.932 48.3125 59 48.3125H41C40.068 48.3125 39.3125 49.068 39.3125 50C39.3125 50.932 40.068 51.6875 41 51.6875H59Z" fill="white"></path>
										</g>
										</g>
										<defs>
										<linearGradient id="3495f85936cfe87c48ae6be73d1ec048" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
											<stop stop-color="#FF8C8C"></stop>
											<stop offset="1" stop-color="#FF3F3F"></stop>
										</linearGradient>
										</defs>
									</svg>',
					'price'        => '$49',
					'guide_link'   => 'https://plugins.miniorange.com/how-to-configure-limit-otp-request-addon',
					'purchase_link'=> 'https://portal.miniorange.com/initializePayment?requestOrigin=wp_otp_limit_otp_addon_plan',
					'support_msg' => 'Hi I am interested in the Limit OTP Request addon, could you please tell me more about this addon?',
				),
				'otp_selected_product_addon' => array(
					'name'              => 'OTP on Selected WooCoomerce Product Category',
					'description'       => array(
						mowc_( 'OTP verification will be enabled on the selected product category' ),
						mowc_( 'All Woocommerce categories supported' ),
					),
					'svg'               => '<svg width="50" height="50" viewBox="0 0 100 100" fill="none" >
										<g id="678c9a11">
										<rect width="100" height="100" rx="10" fill="url(#1f829a77)"></rect>
										<g id="c8456309">
											<path id="9cbc5e53" fill-rule="evenodd" clip-rule="evenodd" d="M28.5879 28.9171C28.2121 28.7636 27.7444 28.7501 26.3794 28.75L25.0001 28.75H25L24.0001 28.7501C23.5859 28.7502 23.2501 28.4144 23.25 28.0002C23.2499 27.586 23.5857 27.2502 23.9999 27.2501L24.9999 27.25H25L26.3795 27.25L26.5362 27.25C27.6814 27.2495 28.471 27.2491 29.155 27.5285C29.7566 27.7742 30.2841 28.1721 30.6856 28.683C31.1421 29.264 31.3587 30.0233 31.6728 31.1246L31.7158 31.2753L32.28 33.2501H61.7156H61.7521C62.7894 33.2501 63.6182 33.2501 64.276 33.3086C64.9466 33.3682 65.5297 33.4942 66.0359 33.8053C66.8273 34.2918 67.4101 35.0547 67.6711 35.9463C67.8381 36.5165 67.8063 37.1122 67.6873 37.7748C67.5707 38.4248 67.3526 39.2244 67.0797 40.2251L67.07 40.2604L64.0098 51.4814L63.964 51.6494C63.5481 53.176 63.281 54.1567 62.7042 54.9035C62.1963 55.5611 61.5248 56.074 60.7567 56.391C59.8844 56.7509 58.8681 56.7506 57.2858 56.7502L57.1117 56.7501H42.8275L42.6556 56.7502H42.6556C41.0914 56.7506 40.0865 56.7509 39.2212 56.3975C38.4591 56.0862 37.791 55.5823 37.2824 54.9351C36.7049 54.2002 36.4291 53.2339 35.9998 51.7297L35.9526 51.5644L30.9931 34.2062L30.2735 31.6874C29.8985 30.375 29.757 29.929 29.5062 29.6098C29.2653 29.3033 28.9488 29.0645 28.5879 28.9171ZM37.25 66C37.25 63.3766 39.3766 61.25 42 61.25C44.6234 61.25 46.75 63.3766 46.75 66C46.75 68.6233 44.6234 70.75 42 70.75C39.3766 70.75 37.25 68.6233 37.25 66ZM58 61.25C55.3766 61.25 53.25 63.3766 53.25 66C53.25 68.6233 55.3766 70.75 58 70.75C60.6234 70.75 62.75 68.6233 62.75 66C62.75 63.3766 60.6234 61.25 58 61.25Z" fill="white"></path>
										</g>
										</g>
										<defs>
										<linearGradient id="1f829a77" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
											<stop stop-color="#5D94FF"></stop>
											<stop offset="1" stop-color="#1CE7E7"></stop>
										</linearGradient>
										</defs>
									</svg>',
					'price'             => '$49',
					'guide_link'        => '',
					'purchase_link'     => '',
					'guide_request_msg' => 'Hi I am interested in the Selected Product Category addon, could you please share the payment details for this addon?',
					'support_msg'       => 'Hi I am interested in the Selected Product Category addon, could you please share the payment details for this addon?',
				),
				'selected_country_addon'     => array(
					'name'        => 'OTP Verification for Selected Countries Only',
					'description' => array(
						mowc_( 'Add countries for which you wish to enable OTP Verification' ),
						mowc_( 'Country code dropdown will be altered accordingly' ),
						mowc_( 'Block OTP for selected countries' ),
					),
					'svg'         => '<svg width="50" height="50" viewBox="0 0 100 100" fill="none">
										<g id="c804ab86e06907df4ede7c5996a51eee">
										<rect width="100" height="100" rx="10" fill="url(#c631fb2424936253666f90eb3c760e43)"></rect>
										<g id="215da64cca5e778eae54f3e9be6f6171">
											<g id="255431f8fe3786cf28667958c83417de">
											<path id="103e2c5498800c8069ab033c4ae3fd13" opacity="0.4" d="M61.25 50C61.25 52.4853 59.2353 54.5 56.75 54.5H38.75V61.25C38.75 63.7353 40.7647 65.75 43.25 65.75H65.75C68.2353 65.75 70.25 63.7353 70.25 61.25V43.25C70.25 40.7647 68.2353 38.75 65.75 38.75H61.25V50Z" fill="white"></path>
											<path id="e12d10051d83e3666c506d0426968ce2" d="M31.4375 56.75H56.9912C59.3434 56.75 61.2502 54.7353 61.2502 52.25V34.25C61.2502 31.7647 59.3434 29.75 56.9912 29.75H31.4375V56.75Z" fill="white"></path>
											<path id="96d243b1d831672d9fb31e0117b0435b" opacity="0.4" fill-rule="evenodd" clip-rule="evenodd" d="M29.75 25.8125C30.682 25.8125 31.4375 26.568 31.4375 27.5V72.5C31.4375 73.432 30.682 74.1875 29.75 74.1875C28.818 74.1875 28.0625 73.432 28.0625 72.5V27.5C28.0625 26.568 28.818 25.8125 29.75 25.8125Z" fill="white"></path>
											</g>
										</g>
										</g>
										<defs>
										<linearGradient id="c631fb2424936253666f90eb3c760e43" x1="0" y1="0" x2="100" y2="100" gradientUnits="userSpaceOnUse">
											<stop stop-color="#EDEF83"></stop>
											<stop offset="1" stop-color="#00D6AF"></stop>
										</linearGradient>
										</defs>
									</svg>',
					'price'         => '$39',
					'guide_link'    => 'https://plugins.miniorange.com/otp-verification-for-selected-countries',
					'purchase_link' => 'https://portal.miniorange.com/initializePayment?requestOrigin=wp_otp_selected_country_addon_plan',
					'support_msg'   => 'Hi I am interested in the OTP Verification for Selected Countries Only addon, could you please tell me more about this addon?',
				),
			);

		}


		/**
		 * Function called to get the addon names
		 */
		public function get_add_on_name() {
			return $this->addon_name; }
		/**
		 * Function called to get the premium addon list
		 */
		public function get_premium_add_on_list() {
			return $this->premium_addon; }

	}
}
