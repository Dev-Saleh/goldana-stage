<?php
/**
 * Load admin view for miniorange profile details.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '
<div>
	
	<div class="mowc-header">
		<p class="mowc-heading flex-1">Account Details</p>
		<div class="flex gap-mowc-3">
            <input  type="button" ' . esc_attr( $disabled ) . ' 
                    name="check_btn" 
                    id="check_btn" 
                    class="mowc-button inverted" 
                    value="' . esc_attr( mowc_( 'Check License' ) ) . '"/>
            <input  type="button" ' . esc_attr( $disabled ) . ' 
                    name="remove_accnt" 
                    id="remove_accnt" 
                    class="mowc-button secondary" 
                    value="' . esc_attr( mowc_( 'Logout' ) ) . '"/>
		</div>
	</div>
    


	<div class="p-mowc-6">
		<table class="mowc-table">
			<tr>
				<td>' . esc_html( mowc_( 'Registered Email' ) ) . '</td>
				<td>' . esc_html( $email ) . '</td>
			</tr>
			<tr>
				<td>' . esc_html( mowc_( 'Customer ID' ) ) . '</td>
				<td>' . esc_html( $customer_id ) . '</td>
			</tr>
			<tr>
				<td>' . esc_html( mowc_( 'API Key' ) ) . '</td>
				<td>' . esc_html( $api_key ) . '</td>
			</tr>
			<tr>
				<td>' . esc_html( mowc_( 'Token Key' ) ) . '</td>
				<td>' . esc_html( $token ) . '</td>
			</tr>
		</table>
	</div>
	
	<form id="mo_ln_form" style="display:none;" action="" method="post">';
			wp_nonce_field( $nonce );
echo '<input type="hidden" name="option" value="check_mowc_ln" />
	</form>
	
	<form id="remove_accnt_form" style="display:none;" action="" method="post">';
			wp_nonce_field( $regnonce );
echo '		<input type="hidden" name="option" value="mowc_remove_account" />
	</form>

</div>';
