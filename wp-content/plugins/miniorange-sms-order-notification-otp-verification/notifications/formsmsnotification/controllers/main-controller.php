<?php
/**
 * Main Controller of Ultimate member SMS notifications.
 *
 * @package miniorange-otp-verification/notifications/formsmsnotification/controllers
 */

use WCSMSOTP\Notifications\FormSMSNotification\Handler\FormSMSNotificationsHandler;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$handler         = FormSMSNotificationsHandler::instance();
$registerd       = $handler->moAddOnV();
$disabled        = ! $registerd ? 'disabled' : '';
$mo_current_user = wp_get_current_user();
$form_controller = FMSN_DIR . 'controllers/';
$addon           = add_query_arg( array( 'page' => 'addon' ), remove_query_arg( 'addon', ( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ) ) );
require $form_controller . 'form-sms-notification.php';
