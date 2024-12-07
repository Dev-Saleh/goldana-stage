<?php
/**
 * Load admin view for Form SMS Notification.
 *
 * @package miniorange-otp-verification/formsmsnotification/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
echo '				<div id="formNotifSubTabContainer" class="mowc-subpage-container ' . esc_attr( $formnotif_hidden ) . '">		
						<form name="f" method="post" action="" id="mo_form_sms_notif_settings">
							<input type="hidden" name="option" value="mo_form_sms_notif_settings" />';
							wp_nonce_field( $nonce );
echo '						<div class="mowc-header">
								<p class="mowc-heading flex-1">' . esc_html( mowc_( 'Form Notification Settings' ) ) . '</p>
								<input type="submit" name="save" id="save" ' . esc_attr( $disabled ) . '
											class="mowc-button inverted" value="' . esc_attr( mowc_( 'Save Settings' ) ) . '">
							</div>

							<div id="mo_admin_phone_settings" class="p-mowc-8 border-b">
									<div class="flex flex-col gap-mowc-6 px-mowc-4">
									<div class="w-full flex m-mowc-4">
										<div class="flex-1">
											<h5 class="mowc-title">Admin Phone Numbers</h5>
											<p class="mowc-caption mt-mowc-2">Enter the Admin Phone Numbers on which Notifications will be received.</p>
										</div>
										<div class="flex-1 flex flex-wrap">
									<input type="text" name="mo_form_admin_notif_recipient" id="mo_form_admin_notif_recipient" value="' . esc_attr( $admin_recipient_value ) . '"  class="w-full mowc-input" placeholder="' . esc_html( mowc_( 'Enter semi-colon (;) to separate multiple phone numbers.' ) ) . '"/>
								';

						echo '	</div>
									</div>
								</div>
							</div>
							
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
									show_form_notifications_table( $notification_settings );
echo '							</tbody>
							</table>

							<div class="mowc-alert" style="background-color:#EFF6FF; justify-content:flex-start; font-weight:600;">
								<span><style="color:#1261d8;">Contact us or email us at <a style="cursor:pointer;" onClick="otpSupportOnClickWC(\'Hi! I want to send the SMS Notifications on form submission.\');"><u> otpsupport@xecurify.com</u></a> to integrate SMS Notifications on your form</span>
							</div>
						</form>
					</div>';
