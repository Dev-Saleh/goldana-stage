<?php
/**
 * Comman handler to check the registration.
 *
 * @package miniorange-order-notifications-woocommerce/handler
 */

namespace WCSMSOTP\Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\GatewayFunctions;
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MocURLCall;
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\BaseActionHandler;
use WCSMSOTP\Traits\Instance;


if ( ! class_exists( 'MoRegistrationHandler' ) ) {
	/**
	 * This class handles all the Registration related functionality.
	 *
	 * @todo need to modularize the code further
	 */
	class MoRegistrationHandler extends BaseActionHandler {

		use Instance;

		/**
		 * Initializes values
		 */
		protected function __construct() {
			parent::__construct();
			$this->nonce = 'mo_reg_actions';
			add_action( 'admin_init', array( $this, 'handle_customer_registration' ) );
		}


		/**
		 * This function hooks into the admin_init hook and routes the data
		 * to the correct functions for processsing. Makes sure the user
		 * has enough capabilities to be able to register.
		 */
		public function handle_customer_registration() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'mo_reg_actions' ) ) {
				return;
			}
			if ( ! isset( $_POST['option'] ) ) {
				return;
			}
			$data   = MoUtility::mowc_sanitize_array( $_POST );
			$option = $data['option'];
			switch ( $option ) {
				case 'mowc_registration_register_customer':
					$this->mo_register_customer( $data );
					break;
				case 'mowc_registration_connect_verify_customer':
					$this->mo_verify_customer( $data );
					break;
				case 'mowc_registration_go_back':
					$this->mo_revert_back_registration();
					break;
				case 'mowc_registration_forgot_password':
					$this->mo_reset_password();
					break;
				case 'mowc_go_to_login_page':
				case 'mowc_remove_account':
					$this->removeAccount();
					break;
				case 'mowc_registration_verify_license':
					$this->vlk( $_POST );
					break;
			}
		}


		/**
		 * Process the registration form and register the user. Checks if the password
		 * and confirm password match the correct format and email and password fields
		 * are not empty or null. First checks if a customer exists in the system Based
		 * on that decides if a new user needs to be created or fetch user info instead.
		 *
		 * @param array $post - the posted data.
		 */
		private function mo_register_customer( $post ) {
			$this->is_valid_request();
			$email            = sanitize_email( $post['email'] );
			$company          = sanitize_text_field( $post['company'] );
			$first_name       = sanitize_text_field( $post['fname'] );
			$last_name        = sanitize_text_field( $post['lname'] );
			$password         = sanitize_text_field( $post['password'] );
			$confirm_password = sanitize_text_field( $post['confirm_password'] );

			if ( strlen( $password ) < 6 || strlen( $confirm_password ) < 6 ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::PASS_LENGTH ), 'ERROR' );
				return;
			}

			if ( $password !== $confirm_password ) {
				delete_mo_wc_option( 'verify_customer' );
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::PASS_MISMATCH ), 'ERROR' );
				return;
			}

			if ( MoUtility::is_blank( $email ) || MoUtility::is_blank( $password )
				|| MoUtility::is_blank( $confirm_password ) ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::REQUIRED_FIELDS ), 'ERROR' );
				return;
			}

			update_mo_wc_option( 'company_name', $company );
			update_mo_wc_option( 'first_name', $first_name );
			update_mo_wc_option( 'last_name', $last_name );
			update_mo_wc_option( 'admin_email', $email );
			update_mo_wc_option( 'admin_password', $password );

			$content = json_decode( MocURLCall::check_customer( $email ), true );
			switch ( $content['status'] ) {
				case 'CUSTOMER_NOT_FOUND':
					$this->handle_without_ckey_cid_regisgtration( $email, $company, $password, '', $first_name, $last_name );
					break;
				default:
					$this->mo_get_current_customer( $email, $password );
					break;
			}
		}


		/**
		 * Function to fetch customer details from the server and save in
		 * the local WordPress Database.
		 *
		 * @param string $email email of the user.
		 * @param string $company company of the user.
		 * @param string $password password of the user.
		 * @param string $phone phone of the user.
		 * @param string $first_name first_name of the user.
		 * @param string $last_name last_name of the user.
		 */
		private function handle_without_ckey_cid_regisgtration( $email, $company, $password, $phone, $first_name, $last_name ) {
			$customer_key = json_decode( MocURLCall::create_customer( $email, $company, $password, $phone, $first_name, $last_name ), true );
			if ( strcasecmp( $customer_key['status'], 'INVALID_EMAIL_QUICK_EMAIL' ) === 0 ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::ENTERPRIZE_EMAIL ), 'ERROR' );
			}
			if ( strcasecmp( $customer_key['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) === 0 ) {
				$this->mo_get_current_customer( $email, $password );
			} elseif ( strcasecmp( $customer_key['status'], 'ENDUSER_EMAIL_EXISTS' ) === 0 ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::ACCOUNT_EXISTS ), 'ERROR' );
			} elseif ( strcasecmp( $customer_key['status'], 'EMAIL_BLOCKED' ) === 0 && 'error.enterprise.email' === $customer_key['message'] ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::ENTERPRIZE_EMAIL ), 'ERROR' );
			} elseif ( strcasecmp( $customer_key['status'], 'FAILED' ) === 0 ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::REGISTRATION_ERROR ), 'ERROR' );
			} elseif ( strcasecmp( $customer_key['status'], 'SUCCESS' ) === 0 ) {
				$this->save_success_customer_config( $customer_key['id'], $customer_key['apiKey'], $customer_key['token'], $customer_key['appSecret'] );
				update_mowc_option( 'registration_status', 'MO_CUSTOMER_VALIDATION_REGISTRATION_COMPLETE' );
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::REG_COMPLETE ), 'SUCCESS' );
				header( 'Location: admin.php?page=mowcsettings' );
			}
		}

			/**
			 * Function to fetch customer details from the server and save in
			 * the local WordPress Database.
			 *
			 * @param string $email email of the user.
			 * @param string $password password of the user.
			 */
		private function mo_get_current_customer( $email, $password ) {
			$content      = MocURLCall::get_customer_key( $email, $password );
			$customer_key = json_decode( $content, true );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				update_mo_wc_option( 'admin_email', $email );
				if ( isset( $customer_key['phone'] ) ) {
					update_mo_wc_option( 'admin_phone', $customer_key['phone'] );
				}

				$this->save_success_customer_config(
					$customer_key['id'],
					$customer_key['apiKey'],
					$customer_key['token'],
					$customer_key['appSecret']
				);
				MoUtility::handle_mo_check_ln( false, $customer_key['id'], $customer_key['apiKey'] );
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::REG_SUCCESS ), 'SUCCESS' );
			} else {
				update_mo_wc_option( 'admin_email', $email );
				update_mo_wc_option( 'verify_customer', 'true' );
				delete_mo_wc_option( 'new_registration' );
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::ACCOUNT_EXISTS ), 'ERROR' );
			}
		}

		/**
		 * Save all required fields on customer registration/retrieval complete.
		 *
		 * @param array $id .
		 * @param array $api_key .
		 * @param array $token .
		 * @param array $app_secret .
		 * @return void
		 */
		private function save_success_customer_config( $id, $api_key, $token, $app_secret ) {
			update_mo_wc_option( 'admin_customer_key', $id );
			update_mo_wc_option( 'admin_api_key', $api_key );
			update_mo_wc_option( 'customer_token', $token );
			update_mo_wc_option( 'plugin_activation_date', gmdate( 'Y-m-d h:i:sa' ) );
			delete_mo_wc_option( 'verify_customer' );
			delete_mo_wc_option( 'new_registration' );
			delete_mo_wc_option( 'admin_password' );
		}


		/**
		 * Function to verify customer details. Checks if email and
		 * password has been submitted and then fetches customer info.
		 *
		 * @param array $post .
		 */
		private function mo_verify_customer( $post ) {
			$this->is_valid_request();
			$email    = sanitize_email( $post['email'] );
			$password = stripslashes( $post['password'] );

			if ( MoUtility::is_blank( $email ) || MoUtility::is_blank( $password ) ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::REQUIRED_FIELDS ), 'ERROR' );
				return;
			}
			$this->mo_get_current_customer( $email, $password );
		}


		/**
		 * Reset Administrator's miniOrange password.
		 * This calls the server to send a forgot password email.
		 */
		private function mo_reset_password() {
			$this->is_valid_request();
			$email = get_mo_wc_option( 'admin_email' );
			if ( ! $email ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::FORGOT_PASSWORD_MESSAGE ), 'SUCCESS' );
			} else {
				$forgot_password_response = json_decode( MocURLCall::forgot_password( $email ) );
				if ( 'SUCCESS' === $forgot_password_response->status ) {
					do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::RESET_PASS ), 'SUCCESS' );
				} else {
					do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::UNKNOWN_ERROR ), 'ERROR' );
				}
			}
		}


		/**
		 * In case of an error delete all option values to revert back
		 * all the data as was at the beginning of the registration process.
		 */
		private function mo_revert_back_registration() {
			$this->is_valid_request();
			update_mo_wc_option( 'registration_status', '' );
			delete_mo_wc_option( 'new_registration' );
			delete_mo_wc_option( 'verify_customer' );
			delete_mo_wc_option( 'admin_email' );
			delete_mo_wc_option( 'sms_otp_count' );
			delete_mo_wc_option( 'email_otp_count' );
			delete_mo_wc_option( 'plugin_activation_date' );
		}


		/**
		 * This function runs when the user wants to remove his account. Used to delete
		 * a few values so that the user has to login again when he wishes to.
		 */
		private function removeAccount() {
			$this->is_valid_request();
			$this->flush_cache();
			wp_clear_scheduled_hook( 'hourlySync' );
			delete_mo_wc_option( 'transactionId' );
			delete_mo_wc_option( 'admin_password' );
			delete_mo_wc_option( 'registration_status' );
			delete_mo_wc_option( 'admin_phone' );
			delete_mo_wc_option( 'new_registration' );
			delete_mo_wc_option( 'admin_customer_key' );
			delete_mo_wc_option( 'admin_api_key' );
			delete_mo_wc_option( 'customer_token' );
			delete_mo_wc_option( 'verify_customer' );
			delete_mo_wc_option( 'message' );
			delete_mo_wc_option( 'check_ln' );
			delete_mo_wc_option( 'site_email_ckl' );
			delete_mo_wc_option( 'email_verification_lk' );
			update_mo_wc_option( 'verify_customer', true );
			delete_mo_wc_option( 'plugin_activation_date' );
		}

		/**
		 * Function checks if there is an existing license on the site.
		 * If so then update the status of the key on the server so
		 * that it can be reused again.
		 */
		private function flush_cache() {
			$gateway = GatewayFunctions::instance();
			$gateway->flush_cache();
		}

		/**
		 * This function is used to verify the license key entered
		 * by the user while activating the plugin.
		 *
		 * @param array $post - all the data sent in form post to validate the license key.
		 */
		private function vlk( $post ) {
			$gateway = GatewayFunctions::instance();
			$gateway->vlk( $post );
		}
	}
}
