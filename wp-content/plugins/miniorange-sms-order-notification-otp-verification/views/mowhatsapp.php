<?php
/**
 * Load admin view for WhatsApp Tab.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Helper\MoUtility;



	$circle_icon = '
		<svg class="min-w-[8px] min-h-[8px]" width="8" height="8" viewBox="0 0 18 18" fill="none">
			<circle id="a89fc99c6ce659f06983e2283c1865f1" cx="9" cy="9" r="7" stroke="rgb(99 102 241)" stroke-width="4"></circle>
		</svg>
	';

echo '	<div class="mowc-header">
			<p class="mowc-heading flex-1">' . esc_html( mowc_( 'WhastApp For OTP Verification And Notifications' ) ) . '</p>
		</div>

		<div class="mowc-alert" style="background-color:#EFF6FF; justify-content:flex-start; font-weight:600;">
			<span><style="color:#1261d8;">
				Send OTPs and Order Notifications over WhatsApp. To know more about WhatsApp Integration contact us or email us at <a style="cursor:pointer;" onClick="otpSupportOnClickWC(\'Hi! I want to integrate WhatsApp OTP verification & SMS Notifications. Can you please help me with more information?\');"><u> otpsupport@xecurify.com</u></a>.
			</span>
		</div>
					
		<div class="border-b flex flex-col gap-mo-6 px-mo-4 mowc-section">
			<div class="mo-whatsapp-snippet-grid">
				<div class="mowc-whatsapp-card" >
					<div class="mowc-whatsapp-header" style="font-size: 1.125rem;line-height: 1.75rem;" >
						<h5>WhatsApp Premium Plan</h5>
							<div class="mt-mowc-4 flex gap-mowc-2">
								<div class="font-bold">$49</div><span> + (transaction-based pricing)</span>
							</div>
					</div> 

					<ul class="mt-mowc-4 grow">

							<li class="feature-snippet"  style="margin-top:15px;">
								<span>' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '</span>
								<p class="m-mowc-0">Use the default <b>miniOrange business account</b>. Need to purchase WhatsApp transactions from <b>miniOrange</b>.</p>
							</li>

							<li class="feature-snippet"  style="margin-top:15px;">
								<span>' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '</span>
								<p class="m-mowc-0">Use your <b>personal business account</b>. Need to purchase WhatsApp transactions from <b>Meta(Facebook)</b></p>
							</li>

							<li class="feature-snippet"  style="margin-top:15px;">
								<span>' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '</span>
								<p class="m-mowc-0">WhatsApp <b>OTP Verification</b></p>
							</li> 

							<li class="feature-snippet"  style="margin-top:15px;">
								<span>' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '</span>
								<p class="m-mowc-0"><b>SMS Notifications</b> via WhatsApp.</p>
							</li> 

							<li class="feature-snippet"  style="margin-top:15px;">
								<span>' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '</span>
								<p class="m-mowc-0"><b>Fallback to SMS</b> OTP for non-WhatsApp numbers.</p>
							</li>

						</ul>

						<a class="w-full mowc-button primary"   style="margin-top:15px;" onclick="otpSupportOnClickWC(\'Hi! I am interested in using WhatsApp OTP & Notifications. Can you please provide more information?\');">Upgrade Now</a><br>
					</div>
			</div>
		</div>';
