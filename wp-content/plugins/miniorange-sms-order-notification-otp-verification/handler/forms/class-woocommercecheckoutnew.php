<?php
/**
 * Load the backend functionality for OTP Verification process for Latest Woocommerce Checkout Form(New UI) form.
 *
 * @package miniorange-order-notifications-woocommerce/handler/forms/
 */

namespace WCSMSOTP\Handler\Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Helper\FormSessionVars;
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoFormDocs;
use WCSMSOTP\Helper\MoPHPSessions;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Helper\SessionUtils;
use WCSMSOTP\Objects\FormHandler;
use WCSMSOTP\Objects\IFormHandler;
use WCSMSOTP\Objects\VerificationType;
use WCSMSOTP\Traits\Instance;
use ReflectionException;

/**
 * This is the WooCommerce CheckOut form class. This class handles all the
 * functionality related to WooCommerce CheckOut form. It extends the FormHandler
 * and implements the IFormHandler class to implement some much needed functions.
 *
 * @todo scripts needs to be better managed
 * @todo disable autologin after checkout needs to be better managed
 */
if ( ! class_exists( 'WooCommerceCheckoutNew' ) ) {
	/**
	 * WooCommerceCheckoutNew class
	 */
	class WooCommerceCheckoutNew extends FormHandler implements IFormHandler {

		use Instance;

		/**
		 * Initializes values
		 */
		protected function __construct() {
			$this->is_login_or_social_form = false;
			$this->is_ajax_form            = true;
			$this->form_session_var        = FormSessionVars::WC_CHECKOUT_NEW;
			$this->type_phone_tag          = 'mo_wc_phone_enable';
			$this->type_email_tag          = 'mo_wc_email_enable';
			$this->phone_form_id           = '#shipping-phone';
			$this->form_key                = 'WC_NEW_CHECKOUT_FORM';
			$this->form_name               = mowc_( 'Woocommerce Checkout Form( above 8.3.0 Version)' );
			$this->is_form_enabled         = get_mo_wc_option( 'wc_new_checkout_enable' );
			$this->form_documents          = MoFormDocs::WC_NEW_CHECKOUT_LINK;
			$this->generate_otp_action     = 'mowc_new_wc_send_otp';
			$this->validate_otp_action     = 'mowc_new_wc_verify_otp';
			parent::__construct();
		}

		/**
		 * Function checks if form has been enabled by the admin and initializes
		 * all the class variables. This function also defines all the hooks to
		 * hook into to make OTP Verification possible.
		 *
		 * @throws ReflectionException .
		 */
		public function handle_form() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				return;
			}
			$this->otp_type = get_mo_wc_option( 'wc_new_checkout_type' );

			add_action( "wp_ajax_{$this->generate_otp_action}", array( $this, 'send_otp' ) );
			add_action( "wp_ajax_nopriv_{$this->generate_otp_action}", array( $this, 'send_otp' ) );

			add_action( "wp_ajax_{$this->validate_otp_action}", array( $this, 'processFormAndValidateOTP' ) );
			add_action( "wp_ajax_nopriv_{$this->validate_otp_action}", array( $this, 'processFormAndValidateOTP' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script_on_page' ) );
			add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'my_custom_checkout_field_process' ), 99, 1 );
		}

		/**
		 * The function is used to process the email or phone number provided
		 * and send OTP to it for verification. This is called from the form
		 * using AJAX calls.
		 */
		public function send_otp() {
			if ( ! check_ajax_referer( $this->nonce, $this->nonce_key ) ) {
				wp_send_json(
					MoUtility::create_json(
						MoMessages::showMessage( MoMessages::INVALID_OP ),
						MoConstants::ERROR_JSON_TYPE
					)
				);
				exit;
			}
			$data = MoUtility::mowc_sanitize_array( $_POST );
			MoPHPSessions::check_session();
			MoUtility::initialize_transaction( $this->form_session_var );

			if ( MoUtility::sanitize_check( 'otpType', $data ) === VerificationType::PHONE ) {
				$this->process_phone_and_send_otp( $data );
			}
			if ( MoUtility::sanitize_check( 'otpType', $data ) === VerificationType::EMAIL ) {
				$this->process_email_and_send_otp( $data );
			}
		}

		/**
		 * Validates phone entered by the user and calls function for sending OTP.
		 *
		 * @param array $data - Post data submitted on the send OTP ajax call.
		 */
		private function process_phone_and_send_otp( $data ) {
			if ( ! MoUtility::sanitize_check( 'user_phone', $data ) ) {
				wp_send_json(
					MoUtility::create_json(
						MoMessages::showMessage( MoMessages::ENTER_PHONE ),
						MoConstants::ERROR_JSON_TYPE
					)
				);
			} else {
				$user_phone = sanitize_text_field( $data['user_phone'] );
				SessionUtils::add_phone_verified( $this->form_session_var, $user_phone );
				$this->send_challenge( '', null, null, $user_phone, VerificationType::PHONE );
			}
		}

		/**
		 * Validates email entered by the user and calls function for sending OTP.
		 *
		 * @param array $data - Post data submitted on the send OTP ajax call.
		 */
		private function process_email_and_send_otp( $data ) {
			MoPHPSessions::check_session();
			if ( ! MoUtility::sanitize_check( 'user_email', $data ) ) {
				wp_send_json(
					MoUtility::create_json(
						MoMessages::showMessage( MoMessages::ENTER_EMAIL ),
						MoConstants::ERROR_JSON_TYPE
					)
				);
			} else {
				$user_email = sanitize_email( $data['user_email'] );
				SessionUtils::add_email_verified( $this->form_session_var, $user_email );
				$this->send_challenge( '', $user_email, null, null, VerificationType::EMAIL );
			}
		}

		/**
		 * Checks if OTP is entered and validates the OTP.
		 */
		public function processFormAndValidateOTP() {
			if ( ! check_ajax_referer( $this->nonce, $this->nonce_key ) ) {
				wp_send_json(
					MoUtility::create_json(
						MoMessages::showMessage( MoMessages::INVALID_OP ),
						MoConstants::ERROR_JSON_TYPE
					)
				);
				exit;
			}
			$data = MoUtility::mowc_sanitize_array( $_POST );
			$this->checkIntegrityAndValidateOTP( $data );
		}

		/**
		 * Checks if email or phone is altered after the OTP is sent.
		 * Also, verifies the OTP.
		 *
		 * @param array $data - post data submitted on validate OTP button.
		 */
		private function checkIntegrityAndValidateOTP( $data ) {
			$this->checkIntegrity( $data );
			$this->validate_challenge( sanitize_text_field( $data['otpType'] ), null, sanitize_text_field( $data['otp_token'] ) );

			if ( SessionUtils::is_status_match( $this->form_session_var, self::VALIDATED, $data['otpType'] ) ) {
				MoPHPSessions::add_session_var( 'is_otp_verified_' . $data['otpType'], true );

				wp_send_json(
					MoUtility::create_json(
						MoConstants::SUCCESS_JSON_TYPE,
						MoConstants::SUCCESS_JSON_TYPE
					)
				);
			} else {
				wp_send_json(
					MoUtility::create_json(
						MoMessages::showMessage( MoMessages::INVALID_OTP ),
						MoConstants::ERROR_JSON_TYPE
					)
				);
			}
		}

		/**
		 * Checks if email or phone is altered after the OTP is sent.
		 *
		 * @param array $data - post data submitted on validate OTP button.
		 */
		private function checkIntegrity( $data ) {
			if ( VerificationType::PHONE === $data['otpType'] ) {
				if ( ! SessionUtils::is_phone_verified_match( $this->form_session_var, sanitize_text_field( $data['user_phone'] ) ) ) {
					wp_send_json(
						MoUtility::create_json(
							MoMessages::showMessage( MoMessages::PHONE_MISMATCH ),
							MoConstants::ERROR_JSON_TYPE
						)
					);
				}
			}
			if ( VerificationType::EMAIL === $data['otpType'] ) {
				if ( ! SessionUtils::is_email_verified_match( $this->form_session_var, sanitize_email( $data['user_email'] ) ) ) {
					wp_send_json(
						MoUtility::create_json(
							MoMessages::showMessage( MoMessages::EMAIL_MISMATCH ),
							MoConstants::ERROR_JSON_TYPE
						)
					);
				}
			}
		}

		/**
		 * Process the checkout form being submitted. Validate if
		 * OTP has been sent and the form has been submitted with an OTP.
		 *
		 * @param object $order The details submitted by the form.
		 */
		public function my_custom_checkout_field_process( $order ) {
			$order_details   = $order->get_data();
			$billing_details = $order_details['billing'];
			$message         = $this->handle_otp_token_submitted( $billing_details ) ? $this->handle_otp_token_submitted( $billing_details ) : null;
			if ( ! empty( $message ) ) {
				$notices = WC()->session->get( 'wc_notices', array() );// phpcs:ignore intelephense.diagnostics.undefinedFunctions -- Default function of Woocommerce.

				$message = apply_filters( 'woocommerce_add_error', $message );
				if ( ! empty( $message ) ) {
					$notices['error'][] = array(
						'notice' => $message,
						'data'   => $order,
					);
				}
				return WC()->session->set( 'wc_notices', $notices );// phpcs:ignore intelephense.diagnostics.undefinedFunctions -- Default function of Woocommerce.
			} else {
				$this->unset_otp_session_variables();
			}
		}

		/**
		 * Validate if the phone number or email otp was sent to and
		 * the phone number and email in the final submission are the
		 * same. If not then throw an error.
		 *
		 *  @param array $data - sanitized data from post request.
		 */
		public function handle_otp_token_submitted( $data ) {
			if ( strcasecmp( $this->otp_type, $this->type_phone_tag ) === 0 ) {
				return $this->process_phone_number( $data );
			} else {
				return $this->processEmail( $data );
			}
		}

		/**
		 * Check to see if email address OTP was sent to and the phone number
		 * submitted in the final form submission are the same.
		 *
		 * @param array $data post data.
		 */
		public function process_phone_number( $data ) {
			if ( ! SessionUtils::is_otp_initialized( $this->form_session_var ) || ! MoPHPSessions::get_session_var( 'is_otp_verified_phone' ) ) {
				$message = MoMessages::showMessage( MoMessages::ENTER_PHONE_VERIFY_CODE );
				return $message;
			}
			if ( ! SessionUtils::is_phone_verified_match( $this->form_session_var, sanitize_text_field( $data['phone'] ) ) ) {
				$message = MoMessages::showMessage( MoMessages::PHONE_MISMATCH );
				return $message;
			}
		}

		/**
		 * Check to see if email address OTP was sent to and the phone number
		 * submitted in the final form submission are the same.
		 *
		 * @param array $data post data.
		 */
		public function processEmail( $data ) {
			if ( ! SessionUtils::is_otp_initialized( $this->form_session_var ) || ! MoPHPSessions::get_session_var( 'is_otp_verified_email' ) ) {
				$message = MoMessages::showMessage( MoMessages::ENTER_EMAIL_VERIFY_CODE );
				return $message;
			}
			if ( ! SessionUtils::is_email_verified_match( $this->form_session_var, sanitize_email( $data['email'] ) ) ) {
				$message = MoMessages::showMessage( MoMessages::EMAIL_MISMATCH );
				return $message;
			}
		}

		/**
		 * This function hooks into the otp_verification_failed hook. This function
		 * details what is done if the OTP verification fails.
		 *
		 * @param string $user_login the username posted by the user.
		 * @param string $user_email the email posted by the user.
		 * @param string $phone_number the phone number posted by the user.
		 * @param string $otp_type the verification type.
		 */
		public function handle_failed_verification( $user_login, $user_email, $phone_number, $otp_type ) {
			SessionUtils::add_status( $this->form_session_var, self::VERIFICATION_FAILED, $otp_type );
		}


		/**
		 * This function hooks into the otp_verification_successful hook. This function is
		 * details what needs to be done if OTP Verification is successful.
		 *
		 * @param string $redirect_to the redirect to URL after new user registration.
		 * @param string $user_login the username posted by the user.
		 * @param string $user_email the email posted by the user.
		 * @param string $password the password posted by the user.
		 * @param string $phone_number the phone number posted by the user.
		 * @param string $extra_data any extra data posted by the user.
		 * @param string $otp_type the verification type.
		 */
		public function handle_post_verification( $redirect_to, $user_login, $user_email, $password, $phone_number, $extra_data, $otp_type ) {
			SessionUtils::add_status( $this->form_session_var, self::VALIDATED, $otp_type );

		}


		/**
		 * This function is used to enqueue script on the frontend to facilitate
		 * OTP Verification for the FormCraft form. This function
		 * also localizes certain values required by the script.
		 */
		public function enqueue_script_on_page() {
			$script_url = MOV_WC_URL . 'includes/js/mowcnewcheckout.min.js?version=' . MOV_WC_VERSION;
			wp_register_script( 'wccheckout', $script_url, array( 'jquery' ), MOV_WC_VERSION, true );
			wp_localize_script(
				'wccheckout',
				'mowccheckoutnew',
				array(
					'siteURL' => wp_wc_ajax_url(),
					'otpType' => strcasecmp( $this->otp_type, $this->type_phone_tag ) === 0 ? 'phone' : 'email',
					'field'   => strcasecmp( $this->otp_type, $this->type_phone_tag ) === 0 ? 'shipping-phone' : 'email',
					'gaction' => $this->generate_otp_action,
					'vaction' => $this->validate_otp_action,
					'imgURL'  => MOV_WC_LOADER_URL,
					'nonce'   => wp_create_nonce( $this->nonce ),
				)
			);
			wp_enqueue_script( 'wccheckout' );
		}


		/**
		 * Unset all the session variables so that a new form submission starts
		 * a fresh process of OTP verification.
		 */
		public function unset_otp_session_variables() {
			SessionUtils::unset_session( array( $this->tx_session_id, $this->form_session_var ) );
		}

		/**
		 * This function is called by the filter mowc_phone_dropdown_selector
		 * to return the Jquery selector of the phone field. The function will
		 * push the formID to the selector array if OTP Verification for the
		 * form has been enabled.
		 *
		 * @param  array $selector the Jquery selector to be modified.
		 * @return array
		 */
		public function get_phone_number_selector( $selector ) {

			if ( $this->is_form_enabled() && ( $this->otp_type === $this->type_phone_tag ) ) {
				array_push( $selector, $this->phone_form_id );
			}
			return $selector;
		}


		/**
		 * Handles saving all the woocommerce checkout form related options by the admin.
		 */
		public function handle_form_options() {
			if ( ! MoUtility::are_form_options_being_saved( $this->get_form_option() ) || ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->admin_nonce ) ) {
				return;
			}
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				return;
			}
			$data = MoUtility::mowc_sanitize_array( $_POST );
			if ( isset( $data['mo_customer_validation_wc_new_checkout_enable'] ) ) {
				if ( isset( $data['mo_customer_validation_wc_checkout_enable'] ) && $data['mo_customer_validation_wc_checkout_enable'] ) {
					do_action( 'mowc_registration_show_message', 'Enable only one checkout form at a time. Select according to your latest WooCommerce version', 'ERROR' );
					return;
				}
			}

			$this->is_form_enabled = $this->sanitize_form_post( 'wc_new_checkout_enable' );
			$this->otp_type        = $this->sanitize_form_post( 'wc_new_checkout_type' );

			update_mo_wc_option( 'wc_new_checkout_enable', $this->is_form_enabled );
			update_mo_wc_option( 'wc_new_checkout_type', $this->otp_type );

		}
	}
}
