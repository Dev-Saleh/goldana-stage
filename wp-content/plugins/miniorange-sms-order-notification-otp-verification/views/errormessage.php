<?php
/**
 * Load admin view for error messages tab.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! $registered ) {
	echo '<section class="mowc-alert mowc-message border-b" style="background-color:#fba36157;" >
                    <p class="flex-1">Login or Register to enable OTP Verification & SMS Notifications. ( Note: Contact us at <a style="cursor:pointer;" onClick="otpSupportOnClickWC(\'Hi, I could you please provide 10 free SMS transactions for testing purpose.\');"><u> otpsupport@xecurify.com</u></a> to get one-time 10 Free SMS transactions for testing purpose)</p>
                    <button class="mowc-button inverted" id="mo_wc_login">Login / Register</button>
                  </section>';
} elseif ( ! $activated ) {
	echo '<section class="mowc-alert mowc-error">
                    ' . esc_attr( $activation_msg ) . '
                  </section>';
} elseif ( ! $gatewayconfigured ) {
	echo '<section class="mowc-alert mowc-error">
                    <h2>' . esc_html( $gateway_msg ) . '</h2>
                  </section>';
} elseif ($sms_remaining === '0' && $registered ) {
    echo '<section class="mowc-alert mowc-message border-b" style="background-color:#fba36157;" >
            <p class="flex-1">You will face issues while sending the SMS OTPs. Contact us at <a style="cursor:pointer;" onClick="otpSupportOnClickWC(\'Hi, I could you please provide 10 free SMS transactions for testing purpose.\');"><u> otpsupport@xecurify.com</u></a> to get one-time 10 Free SMS transactions for testing purpose)</p>
          </section>';
}
