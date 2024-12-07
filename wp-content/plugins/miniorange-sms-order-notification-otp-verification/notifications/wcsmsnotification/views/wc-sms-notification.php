<?php
/**
 * View file to show Customer SMS Notifications List
 *
 * @package miniorange-order-notifications-woocommerce
 */

use WCSMSOTP\Notifications\WcSMSNotification\Helper\MoWcAddOnUtility;

echo '		
			<div id="WcNotifSubTabContainer" class="mowc-subpage-container ' . esc_attr( $wc_hidden ) . '">';

					MoWcAddOnUtility::is_addon_activated();

echo '
					<form name="f" method="post" action="" id="wc_sms_notif_settings">
						<input type="hidden" name="option" value="wc_sms_notif_settings" />
						<div class="mowc-header">
							<p class="mowc-heading flex-1">' . esc_html( mowc_( 'WooCommerce Notification Settings' ) ) . '</p>
							<input type="submit" name="save" id="save" 
										class="mowc-button primary" value="' . esc_attr( mowc_( 'Save Settings' ) ) . '">
						</div>
						<div>
							<table class="mowc-wcnotif-table bg-white">
								<thead>
									<tr>
										<th>SMS Type</th>
										<th>Recipient</th>
										<th></th>
										<th>SMS Body</th>			
									</tr>
								</thead>
								<tbody>';
									show_wc_notifications_table( $notification_settings );
echo '							</tbody>
							</table>
						</div>
					</form>	
			</div>';
