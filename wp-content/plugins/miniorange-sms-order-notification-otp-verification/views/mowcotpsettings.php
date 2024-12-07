<?php
/**
 * Load admin view for OTP Settings Tab.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


echo '<div>
        <form name="f" method="post" action="" id="mowc_otp_verification_settings">
			    <input type="hidden" name="option" value="mowc_otp_extra_settings" />';
				wp_nonce_field( $nonce );


echo '<div class="mowc-header">

        <p class="mowc-heading flex-1">' . esc_html( mowc_( 'OTP Settings' ) ) . '</p>
			<input type="submit" name="save" id="save" 
				class="mowc-button primary" value="' . esc_attr( mowc_( 'Save Settings' ) ) . '">
	  </div>

    <div class="border-b flex flex-col gap-mowc-6 px-mowc-4 mowc-section">
        <div class="w-full flex">
        <div class="flex-1">
            <h5 class="mowc-title"><b>Country Code Dropdown</b></h5>
             <p class="mowc-caption mt-mowc-2">Country code will be appended to the phone number field</p>
        </div>
        <div class="flex-1">
            <div id="country_code_settings" class="flex my-mowc-3">
                <div class="my-mowc-3">' . esc_html( mowc_( 'Select Default Country Code' ) ) . ':</div>
                    <div>';
						get_wc_country_code_dropdown();
echo '  	        </div>
                </div>
                <div class="flex">
                    <div>' . esc_html( mowc_( 'Country Code' ) ) . ': </div>
                        <span id="country_code"></span>
                    </div>
                <div class="my-mowc-3">
                     <input  type="checkbox" ' . esc_attr( $disabled ) . '
                    name="show_dropdown_on_form"
                    id="dropdownEnable"
                    value="1"' . esc_attr( $show_dropdown_on_form ) . ' />
            ' . esc_html( mowc_( 'Show a country code dropdown on the phone field.' ) ) . '
                </div>
            </div>
        </div>
</form>
</div>';
