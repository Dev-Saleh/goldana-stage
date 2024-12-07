<?php
/**
 * Controller of Form SMS notifications.
 *
 * @package miniorange-otp-verification/notifications/formsmsnotification/controllers
 */

use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationsList;
use WCSMSOTP\Notifications\FormSMSNotification\Helper\FormSMSNotificationUtility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$notification_settings = maybe_unserialize( get_fmsn_option( 'notification_settings_option' ) );
$notification_settings = $notification_settings ? $notification_settings : FormSMSNotificationsList::instance();
$sms                   = '';
$formnotif_hidden      = 'formNotifSubTab' !== $subtab ? 'hidden' : '';

$admin_recipient       = get_fmsn_option( 'moform_notif_admin_recipient' );
$admin_recipient_value = maybe_unserialize( $admin_recipient );
$admin_recipient_value = is_array( $admin_recipient_value ) ? implode( ';', $admin_recipient_value ) : $admin_recipient_value;

require_once FMSN_DIR . '/views/form-sms-notification.php';


/**
 * Display the Ultimate Member SMS Notification table
 *
 * @param FormSMSNotificationsList $notifications - contains all the data of the ultimate member notifications notifications.
 */
function show_form_notifications_table( FormSMSNotificationsList $notifications ) {

	foreach ( $notifications as $notification => $property ) {
		echo '	<div style="display:flex;"><div>
					
					<tr >
						<td class="mowc-wcnotif-table bg-white">
							<a class="mowc-title text-primary text-blue-600">' . esc_attr( $property->title ) . '</a>';

		echo '		    </td>

						<td class="msn-table-list-recipient" style="word-wrap: break-word;">
							' . esc_attr( $property->notification_type ) . '
						</td>
					

						<td class="msn-table-list-status-actions">
							<label class="mowc-switch">
							  <input class="input" name="' . esc_attr( $notification ) . '" id="' . esc_attr( $notification ) . '" type="checkbox" ' . ( $property->is_enabled ? 'checked' : '' ) . '/>
							  <span class="mowc-slider"></span>
							</label>
						</td>';

						$var = $notification;

						$id    = 'sms-body-' . $var;
						$btnid = 'btn-' . $var;

		echo '           <td class="msn-table-edit-body mo_showcontainer">
							<button id="' . esc_attr( $btnid ) . '" type="button" class="mowc-button secondary" onClick="edit_button(this)">Edit</button>

							<tr>
								<td colspan="4">
									<div id="' . esc_attr( $id ) . '" style="display:none;" class="p-mowc-8">';

										$notif        = $var;
										$len_of_notif = strlen( $notif );
		for ( $i = 0; $i < $len_of_notif; $i++ ) {

			if ( '_' === $notif[ $i ] ) {
				$notif[ $i ] = '-';
			}
		}
										$path = '/controllers/smsnotifications/' . $notif . '.php';
										include FMSN_DIR . $path;

		echo '                       </div>
								</td>
							</tr>

					</td>';

	}
}
