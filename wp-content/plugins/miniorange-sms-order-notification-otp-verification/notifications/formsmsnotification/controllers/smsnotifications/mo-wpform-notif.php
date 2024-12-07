<?php
/**
 * Load admin view for WPForm Submission SMS notifications.
 *
 * @package miniorange-otp-verification/notifications/formsmsnotification/controllers/smsnotifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationsList;

$notification_settings = maybe_unserialize( get_fmsn_option( 'notification_settings_option' ) );
$notification_settings = $notification_settings ? $notification_settings : FormSMSNotificationsList::instance();
$sms                   = '';

$sms_settings       = $notification_settings->get_mo_wpform_notif();
$enable_disable_tag = $sms_settings->page;
$textarea_tag       = $sms_settings->page . '_smsbody';
$recipient_tag      = $sms_settings->page . '_recipient';
$recipient_value    = maybe_unserialize( $sms_settings->recipient );
$recipient_value    = is_array( $recipient_value ) ? implode( ';', $recipient_value ) : $recipient_value;
$enable_disable     = $sms_settings->is_enabled ? 'checked' : '';

require FMSN_DIR . '/views/smsnotifications/form-customer-sms-template.php';
