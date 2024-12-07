<?php
/**
 * Load admin view for WooCommerceSocialLoginForm.
 *
 * @package miniorange-order-notifications-woocommerce/views/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo ' 	<div class="mowc-formsettings-container" id="' . esc_attr( get_mo_wc_class( $handler ) ) . '">

			<div class="flex">
              	<label class="mowc-checkbox-container flex-1">
 	            <input type="checkbox" ' . esc_attr( $disabled ) . ' 
                    id="wc_social" 
                    class="app_enable sr-only" 
                    name="mo_customer_validation_wc_social_login_enable" 
                    value="1"
			        ' . esc_attr( $wc_social_login ) . ' />
                	<span class="mowc-checkmark"></span>
                	<p class="mowc-title">' . esc_attr( $form_name ) . '</p>
              	</label>        
            </div>';

echo ' </div>';

