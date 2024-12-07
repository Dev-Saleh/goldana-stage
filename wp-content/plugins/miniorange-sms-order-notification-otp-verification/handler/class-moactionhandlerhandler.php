<?php
/**
 * Form Action Handler.
 *
 * @package miniorange-order-notifications-woocommerce/handler.
 */

namespace WCSMSOTP\Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\CountryList;
use WCSMSOTP\Helper\GatewayFunctions;
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MocURLCall;
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Objects\BaseActionHandler;
use WCSMSOTP\Objects\PluginPageDetails;
use WCSMSOTP\Objects\TabDetails;
use WCSMSOTP\Objects\Tabs;
use WCSMSOTP\Traits\Instance;

if ( ! class_exists( 'MoActionHandlerHandler' ) ) {
	/**
	 * This class handles all the Admin related actions of the user related to the
	 * OTP Verification Plugin.
	 */
	class MoActionHandlerHandler extends BaseActionHandler {

		use Instance;

		/**
		 * Initializes values
		 */
		protected function __construct() {
			parent::__construct();
			$this->nonce = 'mo_admin_actions';
			add_action( 'admin_init', array( $this, 'mo_handle_admin_actions' ), 1 );
			add_action( 'admin_init', array( $this, 'moScheduleTransactionSync' ), 1 );
			add_action( 'admin_init', array( $this, 'checkIfPopupTemplateAreSet' ), 1 );
			add_filter( 'dashboard_glance_items', array( $this, 'otp_transactions_glance_counter' ), 10, 1 );
			add_action( 'admin_post_miniorange_get_form_details', array( $this, 'showFormHTMLData' ) );
			add_action( 'admin_post_miniorange_get_gateway_config', array( $this, 'show_gateway_config' ) );
			add_action( 'admin_notices', array( $this, 'showNotice' ) );
			add_action( 'wp_ajax_mo_dismiss_notice', array( $this, 'dismiss_notice' ) );
		}

		/**
		 * This function shows the Enterprise plan notificaton on the admin site only at once.
		 * Once you click on the close notice it will not displayed again.
		 * After deactivation of plugin again the notification will get display.
		 **/
		public function showNotice() {
			$license_page_url = admin_url() . 'admin.php?page=mowcpricing';
			$addon_page_url   = admin_url() . 'admin.php?page=addon';
			$query_string     = isset( $_SERVER['QUERY_STRING'] ) ? sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) ) : '';
			$current_url      = admin_url() . 'admin.php?' . $query_string;
			$is_notice_closed = get_mo_wc_option( 'mowc_hide_notice' );
			if ( 'mowc_hide_notice' !== $is_notice_closed ) {
				if ( ( ! strcmp( MOV_WC_TYPE, 'EnterpriseGatewayWithAddons' ) !== 0 ) && ( $current_url !== $license_page_url ) ) {
					echo '<div class="mowc_notice updated notice is-dismissible" style="padding-bottom:7px;">
        <p><img src="' . esc_attr( MOV_WC_FEATURES_GRAPHIC ) . '" class="show_mo_icon_form" style="width:3%;margin-bottom: -1%;">&ensp; We support OTP Verification on WooCommerce Login, Registration, Checkout, Account details, Password Reset & WCFM/ Dokan Vendor Registration Forms. <br>WooCommerce Order Notifications for Admins, Vendors & Customers | OTP & Notification over WhatsApp | Custom SMS & Email Gateways supported. <br> Check out more about the feature list here : <a href=' . esc_url( $license_page_url ) . '>Plan Details</a>.</b></p>
         </div>';
				}
			}
		}

		/**
		 * This function we used to update the value on click of hide admin notice.
		 * This is the check for notification on click of close notification.
		 */
		public function dismiss_notice() {
			update_mo_wc_option( 'mowc_hide_notice', 'mowc_hide_notice' );
		}

		/**
		 * This function hooks into the admin_init WordPress hook. This function
		 * checks the form being posted and routes the data to the correct function
		 * for processing. The 'option' value in the form post is checked to make
		 * the diversion.
		 */
		public function mo_handle_admin_actions() {
			if ( ! isset( $_POST['option'] ) ) {
				return;
			}
			switch ( $_POST['option'] ) {
				case 'mowc_customer_validation_settings':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mo_save_settings( MoUtility::mowc_sanitize_array( $_POST ), MoUtility::mowc_sanitize_array( $_GET ) );
					break;
				case 'mowc_customer_validation_messages':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->handle_custom_messages_form_submit( MoUtility::mowc_sanitize_array( $_POST ) );
					break;
				case 'mowc_validation_contact_us_query_option':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mo_validation_support_query( MoUtility::mowc_sanitize_array( $_POST ) );
					break;
				case 'mowc_otp_extra_settings':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mo_save_extra_settings( MoUtility::mowc_sanitize_array( $_POST ) );
					break;
				case 'mowc_otp_feedback_option':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mo_validation_feedback_query( MoUtility::mowc_sanitize_array( $_POST ) );
					break;
				case 'check_mowc_ln':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mo_check_l();
					break;
				case 'mowc_check_transactions':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mowc_check_transactions_form', '_nonce' ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mowc_check_transactions( MoUtility::mowc_sanitize_array( $_POST ) );
					break;
				case 'mowc_customer_validation_sms_configuration':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mo_configure_sms_template( MoUtility::mowc_sanitize_array( $_POST ) );
					break;
				case 'mowc_customer_validation_email_configuration':
					if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
						wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
					}
					$this->mo_configure_email_template( MoUtility::mowc_sanitize_array( $_POST ) );
					break;
			}
		}


		/**
		 * This function is used to process and save the custom messages .
		 * set by the admin. These messages are user facing messages.
		 *
		 * @param array $post - The post data containing all the messaging information to be processed .
		 */
		public function handle_custom_messages_form_submit( $post ) {
			$this->is_valid_request();
			update_mo_wc_option( 'success_email_message', MoUtility::sanitize_check( 'otp_success_email', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'success_phone_message', MoUtility::sanitize_check( 'otp_success_phone', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'error_phone_message', MoUtility::sanitize_check( 'otp_error_phone', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'error_email_message', MoUtility::sanitize_check( 'otp_error_email', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'invalid_phone_message', MoUtility::sanitize_check( 'otp_invalid_phone', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'invalid_email_message', MoUtility::sanitize_check( 'otp_invalid_email', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'invalid_message', MoUtility::sanitize_check( 'invalid_otp', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'blocked_email_message', MoUtility::sanitize_check( 'otp_blocked_email', $post ), 'mo_wc_otp_' );
			update_mo_wc_option( 'blocked_phone_message', MoUtility::sanitize_check( 'otp_blocked_phone', $post ), 'mo_wc_otp_' );

			do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::MSG_TEMPLATE_SAVED ), 'SUCCESS' );
		}


		/**
		 * All form related data to be saved are saved in the form's
		 * handleFormOptions function. This function checks if there's
		 * a javascript error and show the appropriate message.
		 *
		 * @param array $post_data   the post data containing all settings data admin saved.
		 * @param array $get_data   the get data.
		 */
		private function mo_save_settings( $post_data, $get_data ) {
			$tab_details = TabDetails::instance();

			$form_settings_tab = $tab_details->tab_details[ Tabs::FORMS ];
			$this->is_valid_request();
			if ( MoUtility::sanitize_check( 'page', $get_data ) !== $form_settings_tab->menu_slug
			&& sanitize_text_field( $post_data['error_message'] ) ) {
				do_action(
					'mowc_registration_show_message',
					MoMessages::showMessage( sanitize_text_field( $post_data['error_message'] ) ),
					'ERROR'
				);
			}
		}


		/**
		 * This function sets the extra OTP related settings in the
		 * plugin.
		 *
		 * @param array $posted   the post data containing all settings data admin saved.
		 */
		private function mo_save_extra_settings( $posted ) {
			$this->is_valid_request();

			delete_site_option( 'default_country_code' );
			$default_country = isset( $posted['default_country_code'] ) ? sanitize_text_field( $posted['default_country_code'] ) : '';

			update_mo_wc_option( 'default_country', maybe_serialize( CountryList::$countries[ $default_country ] ) );
			update_mo_wc_option( 'blocked_domains', MoUtility::sanitize_check( 'mo_otp_blocked_email_domains', $posted ) );
			update_mo_wc_option( 'blocked_phone_numbers', MoUtility::sanitize_check( 'mo_otp_blocked_phone_numbers', $posted ) );
			update_mo_wc_option( 'show_remaining_trans', MoUtility::sanitize_check( 'mo_show_remaining_trans', $posted ) );
			update_mo_wc_option( 'show_dropdown_on_form', MoUtility::sanitize_check( 'show_dropdown_on_form', $posted ) );
			update_mo_wc_option( 'otp_length', MoUtility::sanitize_check( 'mo_otp_length', $posted ) );
			update_mo_wc_option( 'otp_validity', MoUtility::sanitize_check( 'mo_otp_validity', $posted ) );
			update_mo_wc_option( 'generate_alphanumeric_otp', MoUtility::sanitize_check( 'mo_generate_alphanumeric_otp', $posted ) );
			update_mo_wc_option( 'globally_banned_phone', MoUtility::sanitize_check( 'mo_globally_banned_phone', $posted ) );
			update_mo_wc_option( 'masterotp_validity', MoUtility::sanitize_check( 'mo_masterotp_validity', $posted ) );
			update_mo_wc_option( 'masterotp_admin', MoUtility::sanitize_check( 'mo_masterotp_admin', $posted ) );
			update_mo_wc_option( 'masterotp_user', MoUtility::sanitize_check( 'mo_masterotp_user', $posted ) );
			update_mo_wc_option( 'masterotp_admins', MoUtility::sanitize_check( 'mo_masterotp_admins', $posted ) );
			update_mo_wc_option( 'masterotp_specific_user', MoUtility::sanitize_check( 'mo_masterotp_specific_user', $posted ) );
			update_mo_wc_option( 'masterotp_specific_user_details', MoUtility::sanitize_check( 'masterotp_specific_user_details', $posted ) );

			do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::EXTRA_SETTINGS_SAVED ), 'SUCCESS' );
		}


		/**
		 * This function processes the support form data before sending it to the server.
		 *
		 * @param array $post_data .
		 */
		private function mo_validation_support_query( $post_data ) {
			$email = MoUtility::sanitize_check( 'query_email', $post_data );
			$query = MoUtility::sanitize_check( 'query', $post_data );
			$phone = MoUtility::sanitize_check( 'query_phone', $post_data );

			if ( ! $email || ! $query ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::SUPPORT_FORM_VALUES ), 'ERROR' );
				return;
			}

			$submitted = MocURLCall::submit_contact_us( $email, $phone, $query );

			if ( json_last_error() === JSON_ERROR_NONE && $submitted ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::SUPPORT_FORM_SENT ), 'SUCCESS' );
				return;
			}

			do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::SUPPORT_FORM_ERROR ), 'ERROR' );
		}


		/**
		 * This function hooks into the dashboard_glance_items filter to show remaining transactions
		 * on the dashboard.
		 */
		public function otp_transactions_glance_counter() {
			if ( ! MoUtility::micr() || ! MoUtility::is_mg() ) {
				return;
			}
			$email = get_mo_wc_option( 'email_transactions_remaining' );
			$phone = get_mo_wc_option( 'phone_transactions_remaining' );
			echo "<li class='mo-trans-count'><a href='" . esc_url( admin_url() ) . "admin.php?page=mowcsettings'>"
				. esc_html(
					MoMessages::showMessage(
						MoMessages::TRANS_LEFT_MSG,
						array(
							'email' => $email,
							'phone' => $phone,
						)
					)
				) . '</a></li>';
		}


		/**
		 * This function checks if the popup templates have been set in the
		 * database. If not then set the templates up and save them in the
		 * database.
		 */
		public function checkIfPopupTemplateAreSet() {
			$email_templates = maybe_unserialize( get_mo_wc_option( 'custom_popups' ) );
			if ( empty( $email_templates ) ) {
				$templates = apply_filters( 'mo_template_defaults', array() );
				update_mo_wc_option( 'custom_popups', maybe_serialize( $templates ) );
			}
		}


		/**
		 * Show Form Data in the Admin Dashboard. Calls the controller of the form
		 * in question to directly get HTML content of the form. This is sent back
		 * in a JSON format which can be used to show data to the admin in the
		 * dashboard.
		 *
		 * @deprecated Deprecated as of version 3.2.80
		 */
		public function showFormHTMLData() {
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
				wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
			}
			$data      = MoUtility::mowc_sanitize_array( $_POST );
			$form_name = sanitize_text_field( $data['formname'] );

			$this->is_valid_request();

			$controller = MOV_WC_DIR . 'controllers/';
			$disabled   = ! MoUtility::micr() ? 'disabled' : '';
			$page_list  = add_query_arg( 'post_type', 'page', admin_url() . 'edit.php' );
			ob_start();
			include $controller . 'forms/' . $form_name . '.php';
			$string = ob_get_clean();
			wp_send_json( MoUtility::create_json( $string, MoConstants::SUCCESS_JSON_TYPE ) );
		}

		/**
		 * Show the gateway configuration fields as per the gateway name.
		 * return a json format view of the page.
		 */
		public function showGatewayConfig() {
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
				wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
			}
			$data = MoUtility::mowc_sanitize_array( $_POST );
			$this->is_valid_request();
			$gateway_type        = $data['gateway_type'];
			$gateway_class       = 'WCSMSOTP\Helper\Gateway\\' . $gateway_type;
			$disabled            = ! MoUtility::micr() ? 'disabled' : '';
			$gateway_url         = get_mo_wc_option( 'custom_sms_gateway' )
										? get_mo_wc_option( 'custom_sms_gateway' )
										: '';
			$gateway_config_view = $gateway_class::instance()->getGatewayConfigView( $disabled, $gateway_url );
			wp_send_json( MoUtility::create_json( $gateway_config_view, MoConstants::SUCCESS_JSON_TYPE ) );
		}

		/**
		 * Show the supported gateway names as per the plan name.
		 * return a json format view of the page.
		 */
		public function show_gateway_config() {
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
				wp_die( esc_attr( MoMessages::showMessage( MoMessages::UNKNOWN_ERROR ) ) );
			}
			$data             = MoUtility::mowc_sanitize_array( $_POST );
			$license_page_url = admin_url() . 'admin.php?page=mowcpricing';
			$pricing_url      = MOV_WC_PORTAL . '/initializepayment?requestOrigin=wp_otp_verification_basic_plan';

			if ( 'MoGateway' === $data['gateway_type'] ) {
				$gateway_note = 'When you are using miniOrange gateway, you need to purchase SMS/Email transactions for sending the OTPs. You can check the country wise <a class="font-semibold text-yellow-500"  href="' . esc_url( $pricing_url ) . '" target="_blank"> SMS Pricing </a> here.';
			} else {
				$gateway_note = 'We provide ' . esc_attr( $data['gateway_type'] ) . ' Gateway integration in the<a class="font-semibold text-yellow-500" href="' . esc_url( $license_page_url ) . '" target="_blank"> Premium Plan </a> of our plguin.';
			}
			wp_send_json( MoUtility::create_json( $gateway_note, MoConstants::SUCCESS_JSON_TYPE ) );
		}

		/**
		 * This function hooks into the WordPress init hook to
		 * start the daily sync schedule. This function starts
		 * a daily schedule to sync the email and sms transactions
		 * from the server.
		 *
		 * @note - this might say hourlySync but it's actually a daily sync
		 */
		public function moScheduleTransactionSync() {
			if ( ! wp_next_scheduled( 'hourlySync' ) && MoUtility::micr() ) {
				wp_schedule_event( time(), 'daily', 'hourlySync' );
			}
		}

		/**
		 * This function provides the feedback reasons to the users
		 * on the deactivation of the plugin.
		 */
		public function mo_feedback_reasons() {
			$deactivationreasons = array(
				'unable_to_setup_plugin'   => 'Unable to setup plugin',
				'not_the_feture_i_wanted'  => 'Features I wanted are missing',
				'temporarily_deactivation' => 'Temporarily deactivation to debug an issue',
				'cost_is_too_high'         => 'Cost is too high',
				'found_a_better_plugin'    => 'Found a better plugin',
			);

			return $deactivationreasons;
		}

		/**
		 * This function returns the list of form enabled during deactivation
		 */
		public function mowc_enabled_form_list() {
			global $wpdb;
			$enabled_form_list = $wpdb->get_results( $wpdb->prepare( 'SELECT option_name FROM wp_options WHERE option_value = 1 AND option_name LIKE %s', array( 'mo_wc%enable' ) ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, Direct database call without caching detected -- DB Direct Query is necessary here.
			$enabled_forms     = '';
			foreach ( $enabled_form_list as $form_name ) {
				$current_form_name = str_replace( '_enable', '', $form_name->option_name );
				$current_form_name = str_replace( 'mo_wc_customer_validation_', '', $current_form_name );
				$enabled_forms    .= $current_form_name . ' , ';
			}
			return $enabled_forms;
		}

		/**
		 * Function to fetch the HTML body of the feedback template.
		 *
		 * @return string
		 */
		private function mowc_get_feedback_html() {
			$template =
			'<html><head><title></title></head><body> <div> First Name :{{FIRST_NAME}}<br/><br/> Last Name :{{LAST_NAME}}<br/><br/> Server Name :{{SERVER}}<br/><br/> Email :{{EMAIL}}<br/><br/>Plugin Type : {{PLUGIN_TYPE}}<br/><br/> {{TYPE}}: [{{PLUGIN}} - {{VERSION}}] : <br/><br/><strong><em>Feedback : </em></strong>{{FEEDBACK}}<br/><br/><b>Enabled Forms : </b>{{ENABLED_FORMS}}</div></body></html>';
			return $template;
		}

		/**
		 * Process and send the feedback
		 *
		 * @param array $posted $_POST.
		 */
		private function mo_validation_feedback_query( $posted ) {
			$this->is_valid_request();
			$submit_type = sanitize_text_field( $posted['miniorange_wc_feedback_submit'] );

			$deactivating_plugin = strcasecmp( sanitize_text_field( $posted['plugin_deactivated'] ), 'true' ) === 0;
			$type                = ! $deactivating_plugin ? mowc_( '[ Plugin Feedback ] : ' ) : mowc_( '[ Plugin Deactivated ]' );

			$views               = array();
			$deactivationreasons = $this->mo_feedback_reasons();
			if ( isset( $posted['miniorange_wc_feedback_submit'] ) ) {
				if ( ! empty( $posted['reason'] ) ) {
					foreach ( $posted['reason'] as $value ) {
						$views[] = $deactivationreasons[ $value ];
					}
				}
			}
			$feedback          = implode( ' , ', $views ) . ' , ' . sanitize_text_field( $posted['query_feedback'] );
			$feedback_template = $this->mowc_get_feedback_html();

			$current_user         = wp_get_current_user();
			$customer_type        = MoUtility::micv() ? 'Premium' : 'Free';
			$email                = get_mo_wc_option( 'admin_email' );
			$activation_date      = get_mo_wc_option( 'plugin_activation_date' );
			$activation_days      = round( ( strtotime( gmdate( 'Y-m-d h:i:sa' ) ) - strtotime( $activation_date ) ) / ( 60 * 60 * 24 ) );
			$activation_date_html = '<br><br>Days since Activated: ' . $activation_days;
			$feedback_template    = str_replace( '{{FIRST_NAME}}', $current_user->first_name, $feedback_template );
			$feedback_template    = str_replace( '{{LAST_NAME}}', $current_user->last_name, $feedback_template );
			$feedback_template    = str_replace( '{{PLUGIN_TYPE}}', MOV_WC_TYPE . ':' . $customer_type . $activation_date_html, $feedback_template );
			$server_name          = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
			$feedback_template    = str_replace( '{{SERVER}}', $server_name, $feedback_template );
			$feedback_template    = str_replace( '{{EMAIL}}', $email, $feedback_template );
			$feedback_template    = str_replace( '{{PLUGIN}}', MoConstants::QUERY_NAME, $feedback_template );
			$feedback_template    = str_replace( '{{VERSION}}', MOV_WC_VERSION, $feedback_template );

			$feedback_template = str_replace( '{{TYPE}}', $type, $feedback_template );
			$feedback_template = str_replace( '{{FEEDBACK}}', $feedback, $feedback_template );
			$feedback_template = str_replace( '{{ENABLED_FORMS}}', $this->mowc_enabled_form_list(), $feedback_template );
			$notif             = MoUtility::send_email_notif(
				$email,
				'Xecurify',
				MoConstants::FEEDBACK_EMAIL,
				'WordPress WC OTP & Notifications Plugin Feedback',
				$feedback_template
			);
			if ( $notif ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::FEEDBACK_SENT ), 'SUCCESS' );
			} else {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::FEEDBACK_ERROR ), 'ERROR' );
			}

			if ( $deactivating_plugin ) {
				deactivate_plugins( array( MOVWC_PLUGIN_NAME ) );
			}
			delete_mo_wc_option( 'mowc_hide_notice' );
		}


		/**
		 * Checks the number of transactions available in user's account.
		 * We can change the is_valid_request() by adding a nonce param to make it generic.
		 *
		 * @param array $post_data $_POST.
		 */
		private function mowc_check_transactions( $post_data ) {
			if ( ! empty( $post_data ) && check_admin_referer( 'mowc_check_transactions_form', '_nonce' ) ) {
				MoUtility::handle_mo_check_ln(
					true,
					get_mo_wc_option( 'admin_customer_key' ),
					get_mo_wc_option( 'admin_api_key' )
				);

			}
		}

		/**
		 * Check the license of the user and update the transaction count in WordPress
		 * so that it can be shown to the users on the At a Glance section of WordPress.
		 * This endpoint is called from the licensing tab or the account page in the
		 * WordPress Plugin.
		 */
		private function mo_check_l() {
			$this->is_valid_request();
			MoUtility::handle_mo_check_ln(
				true,
				get_mo_wc_option( 'admin_customer_key' ),
				get_mo_wc_option( 'admin_api_key' )
			);
		}

		/**
		 * Check when users changes the SMS template.
		 *
		 * @param array $posted .
		 * @return void
		 */
		private function mo_configure_sms_template( $posted ) {
			if ( isset( $posted['mo_customer_validation_custom_sms_gateway'] ) && empty( sanitize_text_field( $posted['mo_customer_validation_custom_sms_gateway'] ) ) ) {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::SMS_TEMPLATE_ERROR ), 'ERROR' );

			} else {
				do_action( 'mowc_registration_show_message', MoMessages::showMessage( MoMessages::SMS_TEMPLATE_SAVED ), 'SUCCESS' );
			}

			$gateway = GatewayFunctions::instance();
			$gateway->mo_configure_sms_template( $posted );
		}

		/**
		 * Configure the email template from the admin panel.
		 *
		 * @param array $posted .
		 * @return void
		 */
		private function mo_configure_email_template( $posted ) {
			$gateway = GatewayFunctions::instance();
			$gateway->mo_configure_email_template( $posted );
		}
	}
}
