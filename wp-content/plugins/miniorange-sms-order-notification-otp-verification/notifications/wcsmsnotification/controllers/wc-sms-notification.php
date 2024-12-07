<?php
/**
 * Load view for SMS Notifications List
 *
 * @package miniorange-order-notifications-woocommerce
 */

use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnMessages;
use WCSMSOTP\Notifications\WcSMSNotification\Helper\WooCommerceNotificationsList;
use WCSMSOTP\Helper\MoUtility;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$notification_settings = get_mowc_option( 'notification_settings' );
$notification_settings = $notification_settings ? maybe_unserialize( $notification_settings )
												: WooCommerceNotificationsList::instance();
$sms                   = '';
$wc_hidden             = 'WcNotifSubTab' !== $subtab ? 'hidden' : '';

require_once WC_MSN_DIR . '/views/wc-sms-notification.php';

/**
 * This function is used to display rows in the notification table for the admin to get an
 * overview of all the SMS notifications that are going out because of the plugin. It displays
 * if the notification is enabled, who the recipient is , the type of SMS notification etc.
 *
 * @param WooCommerceNotificationsList $notifications The list of all notifications for WooCommerce.
 */
function show_wc_notifications_table( WooCommerceNotificationsList $notifications ) {
	foreach ( $notifications as $notification => $property ) {
		echo '	<div style="display:flex;"><div>
                    
                    <tr >
                        <td>
                            <a class="mowc-title text-primary text-blue-600">' . esc_attr( $property->title ) . '</a>';

		echo '		    </td>

                        <td class="msn-table-list-recipient" style="word-wrap: break-word;">
                            ' . esc_attr( $property->notification_type ) . '
                        </td>
                    

                        <td class="msn-table-list-status-actions">
                            <label class="mowc-switch">
                              <input class="input" name="' . esc_attr( $notification ) . '" id="' . esc_attr( $notification ) . '"  onclick="wcfm_save_notif(this)" type="checkbox" ' . ( $property->is_enabled ? 'checked' : '' ) . '/>
                              <span class="mowc-slider mowc-round"></span>
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
										include WC_MSN_DIR . $path;

		echo '                       </div>
                                </td>
                            </tr>

                    </td>';

	}

	echo '	<div style="display:flex;"><div>         
                    <tr >
                        <td>
                            <a class="mowc-title text-primary text-blue-600">Stock Notifications</a>';
		echo '		    </td>
                        <td class="msn-table-list-recipient" style="word-wrap: break-word;">Administrator</td>
                        <td class="msn-table-list-status-actions">   
                        </td>';

		echo '           <td class="msn-table-edit-body mo_showcontainer">
							<div data-show="false" class="before:content-none flex items-center justify-center">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
										<g id="d4a43e0162b45f718f49244b403ea8f4">
											<g id="4ea4c3dca364b4cff4fba75ac98abb38">
													<g id="2413972edc07f152c2356073861cb269">
													<path id="2deabe5f8681ff270d3f37797985a977" d="M20.8007 20.5644H3.19925C2.94954 20.5644 2.73449 20.3887 2.68487 20.144L0.194867 7.94109C0.153118 7.73681 0.236091 7.52728 0.406503 7.40702C0.576651 7.28649 0.801941 7.27862 0.980492 7.38627L7.69847 11.4354L11.5297 3.72677C11.6177 3.54979 11.7978 3.43688 11.9955 3.43531C12.1817 3.43452 12.3749 3.54323 12.466 3.71889L16.4244 11.3598L23.0197 7.38654C23.1985 7.27888 23.4233 7.28702 23.5937 7.40728C23.7641 7.52754 23.8471 7.73707 23.8056 7.94136L21.3156 20.1443C21.2652 20.3887 21.0501 20.5644 20.8007 20.5644Z" fill="orange"></path>
												</g>
												</g>
											</g>        
							</div>  
				       		<label id="mowc_premium_notif" style="color:#facc15;" onClick="otpSupportOnClickWC(\'Hi! I am interested in the WooCommerce stock notifications. I would like to know more about this feature. \');">Premium Feature</label>
						</td>
			</div>';

}

