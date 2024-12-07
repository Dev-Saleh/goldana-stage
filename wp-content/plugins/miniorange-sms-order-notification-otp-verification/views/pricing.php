<?php
/**
 * Load admin view for Licensing Tab.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoUtility;

$circle_icon  = '
    <svg class="min-w-[8px] min-h-[8px]" width="8" height="8" viewBox="0 0 18 18" fill="none">
            <circle id="a89fc99c6ce659f06983e2283c1865f1" cx="9" cy="9" r="7" stroke="rgb(99 102 241)" stroke-width="4"></circle>
        </svg>
    ';
$country_list = MOWC_SMS_PRICING;

echo '
    <div class="w-full">
		<div class="mowc-header h-full">
			<p class="mowc-heading flex-1">Plans &amp; Pricing</p>
		</div>

        <div class="mowc-section">
            <div class="grid grid-cols-pricingPlans duration-150">
                <div class="mowc-section border-r flex flex-col" >
                    
                    <div class="flex items-center gap-mowc-2">
                        <h5 class="mowc-display small">Free Plan</h5>
                    </div>
                    <div class="h-mowc-14 my-mowc-3 flex items-center">
                        <h2 class="mowc-display small"></h2>
                    </div>
                   
                
                        <ul id="mowc-gateway-features" class="mowc-features">
                            <div class="h-mowc-14 mowc-alert mowc-success">
                                <p>10 Free Emails & SMS*</p>
                            </div>';

foreach ( $free_plan_features as $features ) {
					echo '   <li class="feature-snippet">
                        ' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '
                                <p class="m-mowc-0">' . wp_kses( $features, MoUtility::mowc_allow_html_array() ) . '</p>
                            </li>';
}

echo '                   </ul>
                </div>

                <div class="mowc-section border-r flex flex-col" >
                    <div>
                        <h5 class="mowc-display small">Starter Plan</h5>
                    </div>
                    <div class="h-mowc-14 my-mowc-3 flex items-center">
                        <b><h3 class="" style="font-size:1rem" >Transaction Based Pricing</h3></b>
                    </div>

                        <ul id="mowc-gateway-features" class="mowc-features">
                            <li class="feature-snippet">
                                <p class="border rounded-smooth p-mowc-2 bg-slate-50 text-xs">
                                <span><b>Note:</b> Need to purchase the SMS/Email transactions from miniOrange.</span></p>
                            </li>';

foreach ( $starter_plan_features as $features ) {
					echo '   <li class="feature-snippet">
                        ' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '
                                <p class="m-mowc-0">' . wp_kses( $features, MoUtility::mowc_allow_html_array() ) . '</p>
                            </li>';
}

echo '                       <li class="feature-snippet" style="display: table;margin: 0 auto;width: 100%;">
									<form action="" method="post" id="mo_sms_pricing" >';
									wp_nonce_field( 'mosmsnonce', 'mo_sms_pricing_nonce' );
echo '									<select name="languages" style="margin-bottom:0.5rem;width:100%;" id="mochoosecountry">
											<option>Select your target country</option>';
foreach ( $country_list  as $key => $value ) {
	echo '									<option value="' . esc_attr( $key ) . '">' . esc_attr( $key ) . '</option>';
}
echo '									</select>
										<select name="transactions" id="mosmspricing" style="width:100%;">
											<option id="moloading">Check SMS Transaction Pricing<option>
											<option id="moloading">Select the target country to check pricing</option>
										</select>
                                        <select class="mt-mowc-2 w-full" name="email_transactions" id="moemailpricing" >
											<option >Check Email Transaction Pricing<option>
											<option >100 transactions- $2</option>
                                            <option >500 transactions- $5</option>
                                            <option >1000 transactions- $7</option>
                                            <option >5000 transactions- $20</option>
                                            <option >10000 transactions- $30</option>
                                            <option >50000 transactions- $45</option>
										</select>
									</form>
								</li>
                        
                        <span class="flex-1"></span>
                        <button id="see-pricing-btn" class="mt-mowc-4 w-full mowc-button primary" onclick="mowc_upgradeform(\'wp_otp_verification_basic_plan\')">Select</button>                         
                    </ul>
                    
                </div>

                <div class="mowc-section border-r flex flex-col" >
                    <div>
                       <div class="flex items-center gap-mowc-2">
                           <h5 class="mowc-display small">Standard Plan</h5>
                        </div>
                        <div class="h-mowc-14 my-mowc-3 flex items-center">
                            <h2 class="mowc-display small">$49 (<del>$79</del>)</h2><span style="font-size:1rem; margin-top:2%"><i>/Year</i></span>
                        </div>
                    </div>
                
                    <ul id="mowc-gateway-features" class="mowc-features">
                
                    <li class="feature-snippet">
                        <p class="border rounded-smooth p-mowc-2 bg-slate-50 text-xs">
                        <span><b>Note:</b> Purchase the SMS/Email transactions from your Gateway/miniOrange Gateway. </span></p>
                    </li>';

foreach ( $standard_plan_features as $features ) {
				echo '<li class="feature-snippet">
                    ' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '
                            <p class="m-mowc-0">' . wp_kses( $features, MoUtility::mowc_allow_html_array() ) . '</p>
                     </li>';
}

echo '               <span class="flex-1"></span>  
                        <button id="see-pricing-btn" class="mt-mowc-4 w-full mowc-button primary" onclick="mowc_upgradeform(\'wp_notification_intranet_standard_plan\')">Select</button>                         
                    </span>
                    </a>
                </ul>
                </div>


                <div class="mowc-section flex flex-col" >
                    <div>
                       <div class="flex items-center gap-mowc-2">
                           <h5 class="mowc-display small">Enterprise Plan</h5>
                        </div>
                        <div class="h-mowc-14 my-mowc-3 flex items-center">
                            <h2 class="mowc-display small">$99 (<del>$149</del>)</h2><span style="font-size:1rem; margin-top:2%"><i>/Year</i></span>
                        </div>
                    </div>
            
                <ul id="mowc-gateway-features" class="mowc-features">
                    <li class="feature-snippet">
                        <p class="border rounded-smooth p-mowc-2 bg-slate-50 text-xs">
                        <span><b>Note:</b> Purchase the SMS/Email transactions from your Gateway/ miniOrange Gateway. </span></p>
                    </li>';

foreach ( $enterprise_plan_features as $features ) {
			echo '  <li class="feature-snippet">
                ' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '
                        <p class="m-mowc-0">' . wp_kses( $features, MoUtility::mowc_allow_html_array() ) . '</p>
                    </li>';
}

echo '               <span class="flex-1"></span>  
                    <a class="mt-mowc-4 w-full mowc-button primary" onclick="mowc_upgradeform(\'wp_notification_intranet_enterprise_plan\')">Select</a>                         
                </ul>
                </div>

            </div>
        
            <div class="mowc-alert w-max">
            <span>*We support HTTP API based or SDK based gateways.     <u><i><a href=' . esc_url( 'https://plugins.miniorange.com/supported-sms-email-gateways' ) . ' target="_blank">Click here to check all Supported Gateways.</a></u><i></span>
            </div>
        </div>';

	echo '
         <div class="m-mowc-0 border dark:border-gray-700" id="otp_payment">
            <div id="otp_pay_method">
                <div class="mowc-header">
                    <p class="mowc-heading flex-1 mt-mowc-2">' . esc_html( mowc_( 'Supported Payment Methods' ) ) . '</p>              
                </div>
                <div class="mowc-pricing-container">
                    <div class="mo-card-pricing-deck">
                        <div class="mo-card-pricing mo-animation">
                            <div class="mo-card-pricing-header">
                                <img  src="' . esc_url( MOV_WC_CARD ) . '"  style="size: landscape;width: 100px; height: 27px; margin-bottom: 4px;margin-top: 4px;opacity: 1;padding-left: 8px;">
                            </div>
                            <hr style="border-top: 4px solid #fff;">
                            <div class="mo-card-pricing-body">
                                <p>If payment is made through Intenational Credit Card/Debit card, the license will be created automatically once payment is completed.</p>
                                <p style="margin-top: 20%;"><i><b><a class="mo_links" href=' . esc_url( MoConstants::FAQ_PAY_URL ) . ' target="blank">Click Here</a> to know more.</b></i></p>
                            </div>
                        </div>
                        <div class="mo-card-pricing mo-animation">
                            <div class="mo-card-pricing-header">
                                <img  src="' . esc_url( MOV_WC_NETBANK ) . '"  style="size: landscape;width: 100px; height: 27px; margin-bottom: 4px;margin-top: 4px;opacity: 1;padding-left: 8px;">
                            </div>
                            <hr style="border-top: 4px solid #fff;">
                            <div class="mo-card-pricing-body">
                                <p>If you want to use net banking for payment then contact us at <i><b style="color:#1261d8">' . esc_html( MoConstants::SUPPORT_EMAIL ) . '</b></i> so that we can provide you bank details. </i></p>
                                <p style="margin-top: 32%;"><i><b>Note:</b> There is an additional 18% GST applicable via Bank Transfer.</i></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mo_otp_note px-mowc-8 my-mowc-3" style="margin-left:4rem; margin-right:4rem;">
                    <p><b>Note :</b> Once you have paid through Net Banking, please inform us so that we can confirm and update your License.</p>
                    <p>For more information about payment methods visit <i><u><a href=' . esc_url( MoConstants::FAQ_PAY_URL ) . ' target="_blank">Supported Payment Methods.</a></u></i></p></p>
                </div>
            </div>
        </div>
            <div class="m-mo-4 border dark:border-gray-700" >
                 <div class="mowc-header">
                    <p class="mowc-heading flex-1 mt-mowc-2">' . esc_html( mowc_( 'Refund and Privacy Policy' ) ) . '</p>        
                </div>
                <div class="mo_otp_note px-mowc-4 my-mowc-3" style="margin-left:4rem; margin-right:4rem;">
                    <p><b>Note :</b> Please read the <i><u><a class="font-semibold" href="https://plugins.miniorange.com/end-user-license-agreement" target="_blank">Refund Policy</a></u></i>  and <i><u><a class="font-semibold" href="https://plugins.miniorange.com/wp-content/uploads/2023/08/Plugins-Privacy-Policy.pdf" target="_blank">Plugin Privacy Policy</a></u></i> before upgrading to any plan.</p>
                </div>
            </div>
    </div>
    <form style="display:none;" id="mocf_loginform" action="' . esc_url( $form_action ) . '" target="_blank" method="post">
        <input type="email" name="username" value="' . esc_attr( $email ) . '" />
        <input type="text" name="redirectUrl" value="' . esc_url( $redirect_url ) . '" />
        <input type="text" name="requestOrigin" id="requestOrigin"  />
    </form>
    <form style="display:none;" id="mowc_upgrade_form" action="' . esc_url( $portal_host ) . '" target="_blank" method="post">
        <input type="text" name="payment_referer" value="' . esc_url( admin_url() ) . 'admin.php?page=mowcpricing">
        <input type="text" name="requestOrigin" id="requestOriginUpgrade"  />
    </form>
    <script>
        const mowcSMSPricing = document.getElementById("mowc-sms-pricing");
        const mowcGatewayFeatures = document.getElementById("mowc-gateway-features");

        const seePricingBtn = document.getElementById("see-pricing-btn");
        const backtoFeaturesBtn = document.getElementById("backto-features-btn");

        mowcSMSPricing.style.display = "none";

        seePricingBtn.addEventListener("click",(e)=>{
            mowcGatewayFeatures.style.display = "none";
            mowcSMSPricing.style.display = "block";
        });

        backtoFeaturesBtn.addEventListener("click",(e)=>{
            mowcGatewayFeatures.style.display = "flex";
            mowcSMSPricing.style.display = "none";
        });

        function mowc_upgradeform(planType){
            jQuery("input[name=\'requestOrigin\']").val(planType);
            jQuery("#mowc_upgrade_form").submit();
        }

    </script>
';
