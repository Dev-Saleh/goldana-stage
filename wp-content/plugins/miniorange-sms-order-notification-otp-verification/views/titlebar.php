<?php
/**
 * Load admin view of the title bar.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '	<div class="flex items-center">
            
            <div class="w-full mowc-header">
                <p class="mowc-heading flex-1">' . esc_html( mowc_( 'Woocommerce Notifications and OTP Verification' ) ) . '</p>';

if ( $is_logged_in ) {
	echo '
            <div id="show_transactions">        
                <a id="mowc_check_transactions" class="mowc-button inverted" style="width:170px; margin-right:10px;">' . esc_attr( mowc_( 'View Transactions' ) ) . ' </a>
                <form id="mowc_check_transactions_form" style="display:none;" action="" method="post">';

			wp_nonce_field( 'mowc_check_transactions_form', '_nonce' );
		echo '<input type="hidden" name="option" value="mowc_check_transactions" />
                </form>  
            </div>

            <div> 
            <a type="button" class="mowc-button secondary" style="width:100px; margin-right:5px;" href="' . esc_url( $pricing_url ) . '" target="_blank">Recharge</a>
            </div>';
}

echo '      </div>
        </div>';
