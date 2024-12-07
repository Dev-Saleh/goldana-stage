<?php
/**
 * Load admin view for Gateway settings Tab.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '
		<div class="mowc-header">
			<p class="mowc-heading flex-1">' . esc_html( mowc_( 'Gateway Settings' ) ) . '</p>
							<input type="submit" name="save" id="save" 
										class="mowc-button primary" disabled value="' . esc_attr( mowc_( 'Save Settings' ) ) . '">
		</div>
		<div class="mowc-alert" style="background-color:#EFF6FF; justify-content:flex-start; font-weight:600;">
				<span><style="color:#1261d8;">To send the OTPs over SMS and Email you need to have an SMS/Email Gateways. If you dont have an Gateway you can use miniOrange Gateway.<br> Your default Gateway is set as miniOrange in the Free plugin. To use your own gateway or any queries please contact us or email us at <a style="cursor:pointer;" onClick="otpSupportOnClickWC(\'Hi! I want to use my own Gateway for sending OTPs. Can you please help me with more information?\');"><u> otpsupport@xecurify.com</u></a>.</span>
				</div>
				<div class="border-b flex flex-col gap-mo-6 px-mo-4 mowc-section">

				<div class="w-full flex m-mo-4">
						<div class="flex-1">
							<h5 class="mo-title"><b>' . esc_html( mowc_( 'SMS Gateway Configurations' ) ) . '</b></h5>
							<p class="mo-caption mt-mowc-2">' . esc_html( mowc_( 'SMS Gateway is a service provider for sending SMS to the users users.' ) ) . '</p>
							<p class="mo-caption mt-mowc-2"><i>' . esc_html( mowc_( '(Your default Gateway is selected as miniOrange Gateway.)' ) ) . '</i></p>	
						</div>
						<div class="flex-1 pr-mo-4 pl-mo-2 py-mo-4">
							<div class="flex">
								<div class="w-[46%] my-mo-2">' . esc_html( mowc_( 'Select Gateway type' ) ) . ': </div>
								<div class="mo-select-wrapper w-[46%]">
									<select id="custom_gateway_type" name="mowc_customer_validation_custom_gateway_type">
										<option value="MoGateway">miniOrange Gateway</option>
										<option value="Twilio">Twilio Gateway</option>
										<option value="HTTP-Based">HTTP based Gateway</option>
										<option value="MSG-91">MSG 91 Gateway</option>
										<option value="Amazon-SNS">Amazon SNS Gateway</option>
									</select>									
								</div>
							</div>
							<div class="flex-1">
								<div class="pb-mo-2 pr-mo-10">
									<div class="mo_otp_note flex gap-mo-1 my-mo-4">
									<h5 class="mo-title" id="gateway_note" >When you are using miniOrange gateway, you need to purchase SMS/Email transactions for sending the OTPs. You can check the country wise 
									<a class="font-semibold text-yellow-500" href="' . esc_url( $pricing_url ) . '" target="_blank"> SMS Pricing </a>here.</h5>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="border-b flex flex-col gap-mo-6 px-mo-4  mowc-section">
					<input type="hidden" name="option" value="mo_customer_validation_sms_configuration" />
					<div class="w-full flex m-mo-4">
						<div class="flex-1">
							<h5 class="mo-title"><b>' . esc_html( mowc_( 'Email Gateway(SMTP) Configurations' ) ) . '</b></h5>
							<p class="mo-caption mt-mowc-2">' . esc_html( mowc_( 'SMTP Gateway is a service provider for sending Email OTPs to the users.' ) ) . '</p>
							
						</div>
						<div class="flex-1 pr-mo-4 pl-mo-2 py-mo-4">
							<div class="flex-1">
								<div class="pb-mo-2 pr-mo-10">
									<div class="mo_otp_note flex gap-mo-1 my-mo-4">
										<svg width="18" class="ml-mo-4" height="18" viewBox="0 0 24 24" fill="none">
												<g id="d4a43e0162b45f718f49244b403ea8f4">
													<g id="4ea4c3dca364b4cff4fba75ac98abb38">
														<g id="2413972edc07f152c2356073861cb269">
															<path id="2deabe5f8681ff270d3f37797985a977" d="M20.8007 20.5644H3.19925C2.94954 20.5644 2.73449 20.3887 2.68487 20.144L0.194867 7.94109C0.153118 7.73681 0.236091 7.52728 0.406503 7.40702C0.576651 7.28649 0.801941 7.27862 0.980492 7.38627L7.69847 11.4354L11.5297 3.72677C11.6177 3.54979 11.7978 3.43688 11.9955 3.43531C12.1817 3.43452 12.3749 3.54323 12.466 3.71889L16.4244 11.3598L23.0197 7.38654C23.1985 7.27888 23.4233 7.28702 23.5937 7.40728C23.7641 7.52754 23.8471 7.73707 23.8056 7.94136L21.3156 20.1443C21.2652 20.3887 21.0501 20.5644 20.8007 20.5644Z" fill="orange"></path>
														</g>
													</g>
												</g>
											</svg>
										<div class="my-mo-5 mr-mo-4">This is a Premium plan feature for sending the Email messages using your SMTP Gateway.<br/>
													Check <a class="font-semibold text-yellow-500" href="' . esc_url( $license_url ) . '" target="_blank"> Licensing Plan </a> to learn more.
													</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>';

