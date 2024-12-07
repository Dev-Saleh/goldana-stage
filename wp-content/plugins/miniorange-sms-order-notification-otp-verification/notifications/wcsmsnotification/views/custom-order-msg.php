<?php
/**
 * View file for Customer Order Message
 *
 * @package miniorange-order-notifications-woocommerce
 */

use WCSMSOTP\Helper\MoUtility;

	echo '
			<div id="custom_order_sms_meta_box">
				<input type="hidden" id="post_ID" name="post_ID" value="' . esc_attr( get_the_ID() ) . '">
				<div id="jsonMsg" hidden></div>

				<b>' . esc_html( mowc_( 'Billing Phone' ) ) . ': </b><br>
				<input type="text" id="billing_phone" name="billing_phone" value="' . esc_attr( $phone_numbers ) . '" style="width:100%"/><br><br>';


	echo ' <b>' . esc_html( mowc_( 'SMS Template: ' ) ) . '</b><br>
                <p>
					<textarea type="text" name="mo_wc_custom_order_msg" id="mo_wc_custom_order_msg" class="mowc-textarea w-full mowc_remaining_characters" 
						value="" placeholder=" Write your message here.."></textarea>
					<span id="characters" style="font-size:12px;">Remaining Characters : <span id="remaining_mo_wc_custom_order_msg">160</span> </span>
				</p>
				<p>
					<a class="mowc-button primary" id="mo_custom_order_send_message">' . esc_html( mowc_( 'Send SMS' ) ) . '</a>
	        	</p>
			</div>

			<div class="mo_otp_note">
				' . esc_html( mowc_( 'Note for Indian customers :' ) ) . '
				' . esc_html( mowc_( 'Please contact us on' ) ) . '
				<u>  ' . esc_html( mowc_( 'mfasupport@xecurify.com' ) ) . '</u>      
				' . esc_html( mowc_( 'for sending the Custom SMS to users.' ) ) . '   
            </div>';

	echo '           

			<script>
				jQuery(document).ready(function () {  
					window.intlTelInput(document.querySelector("#billing_phone"));
				});
			</script>';
