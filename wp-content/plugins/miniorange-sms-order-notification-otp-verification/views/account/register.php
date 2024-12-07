<?php
/**
 * Load admin view for miniorange Login Form.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '
	<div class="w-full">
		<form name="f" method="post" action="" id="register-form">';
			wp_nonce_field( $nonce );
			echo '	
			<input type="hidden" name="option" value="mowc_registration_register_customer" />
			
			<div class="mowc-header">
				<p class="mowc-heading flex-1">Register with Miniorange</p>
			</div>

			<div class="w-full h-full flex justify-center items-center p-mowc-8">
				<div class="w-[528px] flex flex-col gap-mowc-6">

					<div class="w-full mowc-input-wrapper group group">
                	    <label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">Email</label>
                	    <input class="w-full mowc-input" type="email" name="email"
									required placeholder="person@example.com"
									value="' . esc_attr( $mo_current_user->user_email ) . '" />
                	</div>

					<div class="w-full mowc-input-wrapper group group">
                	    <label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">Website/Company Name</label>
                	    <input class="w-full mowc-input" type="text" name="company"
								required placeholder="' . esc_attr( mowc_( 'Enter your companyName' ) ) . '"
								value="' . esc_attr( isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '' ) . '" />
                	</div>
					
					<div class="flex items-center gap-mowc-6">
						<div class="w-full mowc-input-wrapper group group">
                		    <label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">First Name</label>
                		    <input class="w-full mowc-input" type="text" name="fname"
									placeholder="' . esc_attr( mowc_( 'Enter your First Name' ) ) . '"
									value="' . esc_attr( $mo_current_user->user_firstname ) . '" />
                		</div>

						<div class="w-full mowc-input-wrapper group group">
                		    <label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">Last Name</label>
                		    <input class="w-full mowc-input" type="text" name="lname"
									placeholder="' . esc_attr( mowc_( 'Enter your Last Name' ) ) . '"
									value="' . esc_attr( $mo_current_user->user_lastname ) . '" />
                		</div>
					</div>

					<div class="flex items-center gap-mowc-6">
						<div class="w-full mowc-input-wrapper group group">
                		    <label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">Password</label>
                		    <input class="w-full mowc-input" required type="password"
									name="password" placeholder="' . esc_attr( mowc_( 'Choose your password (Min. length 6)' ) ) . '" />
                		</div>

						<div class="w-full mowc-input-wrapper group group">
                		    <label for="mo_firebase_gateway_databaseurl" class="mowc-input-label">Confirm Password</label>
                		    <input class="w-full mowc-input" required type="password"
									name="confirm_password" placeholder="' . esc_attr( mowc_( 'Confirm your password' ) ) . '" />
                		</div>
					</div>

					<div>
						<input class="w-full mowc-button primary" type="submit" name="submit" value="' . esc_attr( mowc_( 'Register' ) ) . '" class="button button-primary button-large" />
						<a href="#goToLoginPage" class="mt-mowc-2 mowc-button secondary">' . esc_html( mowc_( 'Already Have an Account? Sign In' ) ) . '</a>
					</div>
				</div>
			</div>
		</form>
	</div>

	<form id="goToLoginPageForm" method="post" action="">';
		wp_nonce_field( $nonce );
echo '	<input type="hidden" name="option" value="mowc_go_to_login_page" />
	</form>
	
	<script>
		jQuery(document).ready(function(){
			$mo(\'a[href="#forgot_password"]\').click(function(){
				$mo("#forgotpasswordform").submit();
			});

			$mo(\'a[href="#goToLoginPage"]\').click(function(){
				$mo("#goToLoginPageForm").submit();
			});
		});
	</script>';
