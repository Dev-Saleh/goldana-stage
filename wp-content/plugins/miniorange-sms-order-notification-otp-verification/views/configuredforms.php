<?php
/**
 * Load admin view for Configured Forms.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Objects\TabDetails;
use WCSMSOTP\Objects\Tabs;

$tab_details    = TabDetails::instance();
$form_notif_tab = $tab_details->tab_details[ Tabs::WOOCOMERCE_NOTIF ];

echo '
                <div class="mowc-header">
		            <p class="mowc-heading flex-1">' . esc_html( mowc_( 'Configure Forms' ) ) . '</p>
                    <input  name="save" id="ov_settings_button_config" 
                        class="mowc-button primary" ' . esc_attr( $disabled ) . ' 
                        value="' . esc_attr( mowc_( 'Save Settings' ) ) . '" type="submit">
	            </div>
                <div id="configured_mo_forms">';
					show_wc_configured_form_details( $controller, $disabled, $page_list );
echo '		    </div>';
