<?php
/**
 * Load admin view for Form SMS Notification addon.
 *
 * @package miniorange-otp-verification/addons/formsmsnotification/handler
 */

namespace WCSMSOTP\Notifications\FormSMSNotification\Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationsList;
	use WCSMSOTP\Objects\BaseAddOnHandler;
	use WCSMSOTP\Helper\MoMessages;
	use WCSMSOTP\Objects\BaseMessages;
	use WCSMSOTP\Helper\MoConstants;
	use WCSMSOTP\Traits\Instance;
	use WCSMSOTP\Helper\MoFormDocs;
	use WCSMSOTP\Helper\MoUtility;


/**
 * The class is used to handle all Form SMS Notifications related functionality.
 * This class hooks into all the available form submission hooks and filters
 * to provide the possibility of SMS notifications.
 */
if ( ! class_exists( 'FormSMSNotificationsHandler' ) ) {
	/**
	 * FormSMSNotificationsHandler class
	 */
	class FormSMSNotificationsHandler extends BaseAddOnHandler {

		use Instance;

		/**
		 * Instance of the FormSMSNotificationsList Class.
		 *
		 * @var FormSMSNotificationsList instance of the FormSMSNotificationsList Class */
		private $notification_settings;


		/**
		 * Constructor checks if notifications has been enabled by the admin and initializes
		 * all the class variables. This function also defines all the hooks to
		 * hook into to make the add-on functionality work.
		 */
		protected function __construct() {
			parent::__construct();

			$this->notification_settings = get_fmsn_option( 'notification_settings_option' )
			? get_fmsn_option( 'notification_settings_option' ) : FormSMSNotificationsList::instance();

			if ( empty( get_fmsn_option( 'notification_settings_option' ) ) && ! empty( get_fmsn_option( 'notification_settings' ) ) ) {
				$old_notification_settings = get_option( 'mo_form_sms_notification_settings' );
				foreach ( $old_notification_settings as $notification_name => $property ) {
					$sms_settings             = $this->notification_settings->$notification_name;
					$sms_settings->is_enabled = $property['is_enabled'];
					$sms_settings->sms_body   = $property['sms_body'];
					$sms_settings->recipient  = $property['recipient'];
				}
					update_fmsn_option( 'notification_settings_option', $this->notification_settings );
			}

			add_action( 'wpcf7_before_send_mail', array( $this, 'mo_send_new_customer_sms_notif' ), 10, 1 );
			add_action( 'wpforms_ajax_submit_completed', array( $this, 'mo_wpform_send_sms_notif' ), 10, 2 );
			add_action( 'ninja_forms_after_submission', array( $this, 'mo_ninjaform_send_sms_notif' ), 10, 1 );
			add_action( 'admin_init', array( $this, 'check_admin_notifications_options' ) );
		}


		/**
		 * This function hooks into the wpcf7_before_send_mail hook
		 * to send an SMS notification to the user when he submit the Contact form.
		 *
		 * @param array $args     the extra arguments passed by the hook.
		 */
		public function mo_send_new_customer_sms_notif( $args ) {
			$this->notification_settings->get_mo_contact_form7_notif()->send_sms( $args );
		}

		/**
		 * This function hooks into the wpforms_ajax_submit_completed hook
		 * to send an SMS notification to the user when he submit the Contact form.
		 *
		 * @param array $form_id - Form id.
		 * @param array $response - Reponse message text.
		 * @return void
		 */
		public function mo_wpform_send_sms_notif( $form_id, $response ) {
			$this->notification_settings->get_mo_wpform_notif()->send_sms( array_merge( array( 'form_id' => $form_id ), $response ) );
		}

		/**
		 * This function hooks into the gform_after_submission hook
		 * to send an SMS notification to the user when he submit the Contact form.
		 *
		 * @param array $form_data The entry currently being processed.
		 */
		public function mo_ninjaform_send_sms_notif( $form_data ) {
			$this->notification_settings->get_mo_ninja_form_notif()->send_sms( $form_data );
		}

		/**
		 * Checks and updates the notification options.
		 */
		public function check_admin_notifications_options() {
			if ( ! ( isset( $_POST['option'] ) && 'mo_form_sms_notif_settings' === $_POST['option'] ) ) {
				return;
			}
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mo_admin_actions' ) ) {
				wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
			}

			foreach ( $this->notification_settings as $notification_name => $notification_setting ) {
				$textarea_tag  = $notification_name . '_smsbody';
				$recipient_tag = $notification_name . '_recipient';
				$notification  = $this->notification_settings->$notification_name;

				$textar_tag            = isset( $_POST [ $textarea_tag ] ) ? sanitize_textarea_field( wp_unslash( $_POST [ $textarea_tag ] ) ) : null;
				$sms                   = MoUtility::is_blank( $textar_tag ) ? $notification->default_sms_body : MoUtility::sanitize_check( $textarea_tag, $_POST );
				$recipient_value       = MoUtility::sanitize_check( $recipient_tag, $_POST );
				$admin_recipient_value = MoUtility::sanitize_check( 'mo_form_admin_notif_recipient', $_POST );

				$notification = $this->notification_settings->$notification_name;
				$notification->setis_enabled( isset( $_POST[ $notification_name ] ) );
				$notification->set_recipient( $recipient_value );
				$notification->set_admin_recipient( $admin_recipient_value );
				$notification->set_sms_body( $sms );
			}
				update_fmsn_option( 'notification_settings_option', $this->notification_settings );
				update_fmsn_option( 'moform_notif_admin_recipient', $admin_recipient_value );
		}

		/**
		 * Unhook all the emails that we will be sending sms notifications for.
		 */
		private function unhook() {
			remove_action( 'um_registration_complete', 'um_send_registration_notification' );
		}


		/** Set Addon Key */
		public function set_addon_key() {
			$this->add_on_key = 'form_sms_notification_addon';
		}

		/** Set AddOn Desc */
		public function set_add_on_desc() {
			$this->add_on_desc = mowc_(
				'Allows your site to send custom SMS notifications to your customers.'
				. 'Click on the settings button to the right to see the list of notifications that go out.'
			);
		}

		/** Set an AddOnName */
		public function set_add_on_name() {
			$this->addon_name = mowc_( 'Forms SMS Notification' );
		}

		/** Set an Addon Docs link */
		public function set_add_on_docs() {
		}

		/** Set an Addon Video link */
		public function set_add_on_video() {
		}
		/** Set Settings Page URL */
		public function set_settings_url() {
			$this->settings_url = add_query_arg( array( 'addon' => 'form_notif' ), isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : null );
		}
	}
}
