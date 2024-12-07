<?php
/**
 * Load admin view for miniorange Login Form.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '	<form name="f" method="post" action="">';
			wp_nonce_field( $nonce );
echo '		<input type="hidden" name="option" value="mowc_registration_connect_verify_customer" />

			<div class="mowc-header">
				<p class="mowc-heading flex-1">Login with Miniorange</p>
			</div>
			
			<div class="w-full h-full flex justify-center items-center p-mowc-8">
				
				<div class="w-[528px] h-full flex flex-col gap-mowc-6">
					<div class="w-full mowc-input-wrapper group group">
                		<label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">Email</label>
                		<input class="w-full mowc-input" type="email" name="email"
						required placeholder="person@example.com"
						value="' . esc_attr( $admin_email ) . '" />
            		</div>
					
					<div class="w-full mowc-input-wrapper group group">
                		<label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">Password</label>
                		<input class="w-full mowc-input" required type="password"
						name="password" placeholder="' . esc_attr( mowc_( 'Enter your miniOrange password' ) ) . '" />
            		</div>
					<a href="#forgot_password" class="text-right font-semibold">Forgot Password</a>
					
					<div>
						<input type="submit" class="w-full mowc-button primary" value="Login"/>
						<a href="#goBackButton" class="mt-mowc-2 mowc-button secondary">Go back to Registration Page</a>
					</div>
				</div>
			</div>
		</form>

		<form id="forgotpasswordform" method="post" action="">';
			wp_nonce_field( $nonce );
echo '		<input type="hidden" name="option" value="mowc_registration_forgot_password" />
		</form>

		<form id="goBacktoRegistrationPage" method="post" action="">';
			wp_nonce_field( $nonce );
echo '		<input type="hidden" name="option" value="mowc_registration_go_back" />
		</form>

		<script>
			jQuery(document).ready(function(){
				$mo(\'a[href="#forgot_password"]\').click(function(){
					$mo("#forgotpasswordform").submit();
				});

				$mo(\'a[href="#goBackButton"]\').click(function(){
					$mo("#goBacktoRegistrationPage").submit();
				});
			});
		</script>';
