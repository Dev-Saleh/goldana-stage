<?php
/**
 * Load admin view for settings of Configured Forms.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Helper\MoMessages;

echo '	<divj>
			<form name="f" method="post" action="' . esc_url( $moaction ) . '" id="mo_otp_verification_settings">
			    <input type="hidden" id="error_message" name="error_message" value="">
				<input type="hidden" name="option" value="mowc_customer_validation_settings" />';

					wp_nonce_field( $nonce );
					require MOV_WC_DIR . 'views/configuredforms.php';

echo '		</form>
		</div>';
