<?php
/**
 * Handler functions for Woocommerce Notifications
 *
 * @package miniorange-order-notifications-woocommerce
 */

namespace WCSMSOTP\Notifications\WcSMSNotification\Handler;

use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnMessages;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnUtility;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\WcOrderStatus;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\WooCommerceNotificationsList;
use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoFormDocs;
use WCSMSOTP\Objects\BaseAddOnHandler;
use WCSMSOTP\Objects\BaseMessages;
use WCSMSOTP\Objects\SMSNotification;
use WCSMSOTP\Traits\Instance;
use WC_Emails;
use WC_Order;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerceNotifications class
 */
class WooCommerceNotifications extends BaseAddOnHandler {

	use Instance;

	/**
	 * The list of all the Notification Settings
	 *
	 * @var WooCommerceNotificationsList
	 */
	private $notification_settings;

	/**
	 * The list of all the enabled notifications
	 *
	 * @var WooCommerceNotificationsList
	 */
	private $enable_notification_action;

	/**
	 * Constructor checks if add-on has been enabled by the admin and initializes
	 * all the class variables. This function also defines all the hooks to
	 * hook into to make the add-on functionality work.
	 */
	protected function __construct() {
		parent::__construct();

		add_action( 'admin_enqueue_scripts', array( $this, 'mo_wc_sms_notif_settings_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'mo_wc_sms_notif_settings_script' ) );

		$this->notification_settings = get_mowc_option( 'notification_settings' )
			? get_mowc_option( 'notification_settings' ) : WooCommerceNotificationsList::instance();

		$this->enable_notification_action = 'mo_wc_notification_enable';

		add_action( 'wp_ajax_nopriv_' . $this->enable_notification_action, array( $this, 'enable_wc_sms_notification' ) );
		add_action( 'wp_ajax_' . $this->enable_notification_action, array( $this, 'enable_wc_sms_notification' ) );

		add_action( 'woocommerce_created_customer_notification', array( $this, 'mo_send_new_customer_sms_notif' ), 1, 3 );
		add_action( 'woocommerce_new_customer_note_notification', array( $this, 'mo_send_new_customer_sms_note' ), 1, 1 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'mo_send_admin_order_sms_notif' ), 1, 3 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'mo_customer_order_hold_sms_notif' ), 1, 3 );

		add_action( 'admin_init', array( $this, 'handle_admin_actions' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_custom_msg_meta_box' ), 1 );
	}


	/**
	 * This function hooks into the admin_init WordPress hook. This function
	 * checks the form being posted and routes the data to the correct function
	 * for processing. The 'option' value in the form post is checked to make
	 * the diversion.
	 */
	public function handle_admin_actions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( array_key_exists( 'mo_send_custome_msg_option', $_POST ) && ! check_ajax_referer( $this->nonce, $this->nonce_key ) ) {
			wp_send_json(
				MoUtility::create_json(
					MoMessages::showMessage( BaseMessages::INVALID_OP ),
					MoConstants::ERROR_JSON_TYPE
				)
			);
			exit;
		}
		$data    = MoUtility::mowc_sanitize_array( $_POST );
		$getdata = MoUtility::mowc_sanitize_array( $_GET );

		if ( isset( $getdata['option'] ) && sanitize_text_field( $getdata['option'] ) === 'mo_send_order_custom_msg' ) {
			$this->send_custom_order_msg( $data );
		}
	}

	/**
	 * Enqueue the CSS stylesheet
	 *
	 * @return void
	 */
	public function mo_wc_sms_notif_settings_style() {
		wp_enqueue_style( 'wcfm_sn_notif_admin_settings_style', WC_MSN_CSS_URL, array(), WC_MSN_VERSION );
	}
	/**
	 * Function to register a new script
	 *
	 * @return void
	 */
	public function mo_wc_sms_notif_settings_script() {
				wp_register_script( 'settingswcnotif', MOWC_MSN_JS_URL, array( 'jquery' ), WC_MSN_VERSION, false );
				wp_localize_script(
					'settingswcnotif',
					'mowcsmsvar',
					array(
						'siteURL' => wp_wc_ajax_url(),
						'action'  => $this->enable_notification_action,
					)
				);
				wp_enqueue_script( 'settingswcnotif' );
	}

	/**
	 * Enable and disable toggle and update in database
	 *
	 * @return void
	 */
	public function enable_wc_sms_notification() {
		if ( array_key_exists( 'option', $_POST ) && ! check_ajax_referer( $this->nonce, $this->nonce_key ) ) {
			wp_send_json(
				MoUtility::create_json(
					MoMessages::showMessage( BaseMessages::INVALID_OP ),
					MoConstants::ERROR_JSON_TYPE
				)
			);
			exit;
		}
		if ( isset( $_POST['notification'] ) ) {
			$wc_notification_type = sanitize_text_field( wp_unslash( $_POST['notification'] ) );
		}
		switch ( $wc_notification_type ) {
			case 'wc_new_customer_notif':
				$wcfm_notif = $this->notification_settings->get_wc_new_customer_notif();
				break;
			case 'wc_customer_note_notif':
				$wcfm_notif = $this->notification_settings->get_wc_customer_note_notif();
				break;
			case 'wc_admin_order_status_notif':
				$wcfm_notif = $this->notification_settings->get_wc_admin_order_status_notif();
				break;
			case 'wc_order_on_hold_notif':
				$wcfm_notif = $this->notification_settings->get_wc_order_on_hold_notif();
				break;
			case 'wc_order_processing_notif':
				$wcfm_notif = $this->notification_settings->get_wc_order_processing_notif();
				break;
			case 'wc_order_completed_notif':
				$wcfm_notif = $this->notification_settings->get_wc_order_completed_notif();
				break;
			case 'wc_order_refunded_notif':
				$wcfm_notif = $this->notification_settings->get_wc_order_refunded_notif();
				break;
			case 'wc_order_cancelled_notif':
				$wcfm_notif = $this->notification_settings->get_wc_order_cancelled_notif();
				break;
			case 'wc_order_failed_notif':
				$wcfm_notif = $this->notification_settings->get_wc_order_failed_notif();
				break;
			case 'wc_order_pending_notif':
				$wcfm_notif = $this->notification_settings->get_wc_order_pending_notif();
				break;
		}
		if ( $wcfm_notif->is_enabled ) {
			$wcfm_notif->setis_enabled( false );
		} else {
			$wcfm_notif->setis_enabled( true );
		}

		update_mowc_option( 'notification_settings', $this->notification_settings );
	}

	/**
	 * This function hooks into the woocommerce_created_customer_notification hook
	 * to send an SMS notification to the user when he successfully creates an
	 * account using the checkout or registration page.
	 *
	 * @param  string $customer_id        id of the customer.
	 * @param  array  $new_customer_data  array of customer data.
	 * @param  bool   $password_generated was password is automatically generated.
	 */
	public function mo_send_new_customer_sms_notif( $customer_id, $new_customer_data = array(), $password_generated = false ) {
		$this->notification_settings->get_wc_new_customer_notif()->send_sms(
			array(
				'customer_id'        => $customer_id,
				'new_customer_data'  => $new_customer_data,
				'password_generated' => $password_generated,
			)
		);
	}

	/**
	 * This function hooks into the woocommerce_new_customer_note_notification hook
	 * to send an SMS notification to the user when the admin successfully adds a
	 * note to the order that the user has ordered.
	 *
	 * @param  array $args array Customer note details.
	 */
	public function mo_send_new_customer_sms_note( $args ) {
		$this->notification_settings->get_wc_customer_note_notif()->send_sms(
			array( 'orderDetails' => wc_get_order( $args['order_id'] ) )
		);
	}

	/**
	 * This function hooks into woocommerce_order_status_changed hook
	 * to send an SMS notification to the admin that a order needs to be
	 * processed as its status has changed.
	 *
	 * @param int    $order_id string the id of the order.
	 * @param string $old_status string the old status of the order.
	 * @param string $new_status string the new status of the order.
	 */
	public function mo_send_admin_order_sms_notif( $order_id, $old_status, $new_status ) {
		$order = new WC_Order( $order_id );
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}
		$this->notification_settings->get_wc_admin_order_status_notif()->send_sms(
			array(
				'orderDetails' => $order,
				'new_status'   => $new_status,
				'old_status'   => $old_status,
			)
		);
	}

	/**
	 * This function hooks into all of the On-Hold notification Woocommerce
	 * hook to send an SMS notification to the customer that a order has been
	 * put on hold
	 *
	 * @param int    $order_id string the id of the order.
	 * @param string $old_status string the old status of the order.
	 * @param string $new_status string the new status of the order.
	 */
	public function mo_customer_order_hold_sms_notif( $order_id, $old_status, $new_status ) {
		$order = new WC_Order( $order_id );
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		if ( strcasecmp( $new_status, WcOrderStatus::ON_HOLD ) === 0 ) {
			$notification = $this->notification_settings->get_wc_order_on_hold_notif();
		} elseif ( strcasecmp( $new_status, WcOrderStatus::PROCESSING ) === 0 ) {
			$notification = $this->notification_settings->get_wc_order_processing_notif();
		} elseif ( strcasecmp( $new_status, WcOrderStatus::COMPLETED ) === 0 ) {
			$notification = $this->notification_settings->get_wc_order_completed_notif();
		} elseif ( strcasecmp( $new_status, WcOrderStatus::REFUNDED ) === 0 ) {
			$notification = $this->notification_settings->get_wc_order_refunded_notif();
		} elseif ( strcasecmp( $new_status, WcOrderStatus::CANCELLED ) === 0 ) {
			$notification = $this->notification_settings->get_wc_order_cancelled_notif();
		} elseif ( strcasecmp( $new_status, WcOrderStatus::FAILED ) === 0 ) {
			$notification = $this->notification_settings->get_wc_order_failed_notif();
		} elseif ( strcasecmp( $new_status, WcOrderStatus::PENDING ) === 0 ) {
			$notification = $this->notification_settings->get_wc_order_pending_notif();
		} else {
			return;
		}
		$notification->send_sms( array( 'orderDetails' => $order ) );
	}

	/**
	 * Unhook all the emails that we will be sending sms notifications for.
	 *
	 * @param WC_Emails $email_class the class to disable notification for.
	 */
	private function unhook( $email_class ) {
		$new_order_email  = array( $email_class->emails['WC_Email_New_Order'], 'trigger' );
		$processing_order = array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' );
		$completed_order  = array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' );
		$new_customer     = array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' );

		remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
		remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
		remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', $new_order_email );
		remove_action( 'woocommerce_order_status_pending_to_completed_notification', $new_order_email );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', $new_order_email );
		remove_action( 'woocommerce_order_status_failed_to_processing_notification', $new_order_email );
		remove_action( 'woocommerce_order_status_failed_to_completed_notification', $new_order_email );
		remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', $new_order_email );
		remove_action( 'woocommerce_order_status_pending_to_processing_notification', $processing_order );
		remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', $processing_order );
		remove_action( 'woocommerce_order_status_completed_notification', $completed_order );
		remove_action( 'woocommerce_new_customer_note_notification', $new_customer );
	}

	/**
	 * Add a meta box in the order page so that customer can send custom messages to
	 * the users if he wants to.
	 */
	public function add_custom_msg_meta_box() {
		if ( class_exists( 'WooCommerce' ) ) {
			$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
			? wc_get_page_screen_id( 'shop-order' )
			: 'shop_order';

			add_meta_box(
				'mo_wc_custom_sms_meta_box',
				'Send Custom SMS',
				array( $this, 'mo_show_send_custom_msg_box' ),
				$screen,
				'side',
				'default'
			);
		}
	}

	/**
	 * This function is a call back to our meta box hook so that we
	 * can design our metabox and provide our own frontend to the
	 * metabox. In this cases its being used to show a box where
	 * admin can send custom messages to the user.
	 *
	 * @param  array $post_or_order_object - the post or order object passed by WordPress.
	 */
	public function mo_show_send_custom_msg_box( $post_or_order_object ) {
		$order_details = is_a( $post_or_order_object, 'WP_Post' ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		$phone_numbers = MoWcAddOnUtility::get_customer_number_from_order( $order_details );
		include WC_MSN_DIR . 'views/custom-order-msg.php';
	}

	/**
	 * This function is used to send custom SMS messages to the user
	 * from the order page of WooCommer using our meta box.
	 *
	 * @param array $data the posted data.
	 */
	private function send_custom_order_msg( $data ) {

		if ( ! array_key_exists( 'numbers', $data ) || MoUtility::is_blank( sanitize_text_field( $data['numbers'] ) ) ) {
			MoUtility::create_json(
				MoWcAddOnMessages::showMessage( MoWcAddOnMessages::INVALID_PHONE ),
				MoConstants::ERROR_JSON_TYPE
			);
		} else {
			foreach ( explode( ';', $data['numbers'] ) as $number ) {
				if ( MoUtility::send_phone_notif( $number, $data['msg'] ) ) {
					wp_send_json(
						MoUtility::create_json(
							MoWcAddOnMessages::showMessage( MoWcAddOnMessages::SMS_SENT_SUCCESS ),
							MoConstants::SUCCESS_JSON_TYPE
						)
					);
				} else {
					wp_send_json(
						MoUtility::create_json(
							MoWcAddOnMessages::showMessage( MoWcAddOnMessages::ERROR_SENDING_SMS ),
							MoConstants::ERROR_JSON_TYPE
						)
					);
				}
			}
		}
	}


	/** Set Addon Key */
	public function set_addon_key() {
		$this->add_on_key = 'wc_sms_notification_addon';
	}

	/** Set AddOn Desc */
	public function set_add_on_desc() {
		$this->add_on_desc = mowc_(
			'Allows your site to send order and WooCommerce notifications to buyers, '
			. 'sellers and admins. Click on the settings button to the right to see the list of notifications '
			. 'that go out.'
		);
	}

	/** Set an AddOnName */
	public function set_add_on_name() {
		$this->addon_name = mowc_( 'WooCommerce SMS Notification' );
	}

	/** Set an Addon Docs link */
	public function set_add_on_docs() {
		$this->add_on_docs = MoFormDocs::WOCOMMERCE_SMS_NOTIFICATION_LINK['guideLink'];
	}

	/** Set an Addon Video link */
	public function set_add_on_video() {
		$this->add_on_video = MoFormDocs::WOCOMMERCE_SMS_NOTIFICATION_LINK['videoLink'];
	}

	/** Set Settings Page URL */
	public function set_settings_url() {
		$request_url        = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$this->settings_url = add_query_arg( array( 'addon' => 'wc_notif' ), $request_url );
	}
}
