<?php
/**
 * Load admin view for Feedback Pop Up.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '
            <div class="mowc_deactivation_popup_container" id="mowc_support_form"  style="display: none;" >
                <div id="mowc_deactivation_popup_wrapper" class="mowc_deactivation_popup_wrapper" tabindex="-1" role="dialog" style="display: none;" >
                            <div class="mowc-header" >
                                <h4 class="font-bold grow" >Feedback</h4>
                                <a class ="mowc-button secondary" id="mowc_feedback_cancel_btn" href="#" onclick="mowc_otp_feedback_goback()">' . esc_html( mowc_( 'Go Back' ) ) . '</a>
                              
                            </div>
                            <form class="p-mowc-6 flex flex-col gap-mowc-6" id="mowc_otp_feedback_form" name="f" method="post" action="">
                                    <div class="deactivation_message">' . esc_attr( $message ) . '</div>
                                    <div class="flex flex-col gap-mowc-3">';

foreach ( $deactivationreasons as $code => $name ) {
	echo '
                                                    <label class="mowc-checkbox-container flex">
                                                      <input  type="checkbox" name="reason[]" id="dropdownEnable" value=' . esc_attr( $code ) . ' class="sr-only"/>
                                                      <div class="mowc-checkmark"></div>
                                                      <p class="text-base font-normal">' . esc_html( $name ) . '</p>
                                                    </label> 
                                                ';
}
echo '                                  
                                    </div>
                                        <input type="hidden" name="option" value="mowc_otp_feedback_option"/>
                                        <input type="hidden" value="false" id="wc_feedback_type" name="plugin_deactivated"/>';

										wp_nonce_field( $nonce );

echo '                                   <textarea id="query_feedback"
                                                    class="mowc-textarea"
                                                    name="query_feedback" 
                                                    style="width:100%" 
                                                    rows="4" 
                                                    placeholder="Type your feedback here"></textarea>
                                        <div class="mowc_otp_note" hidden id="feedback_message" style="padding:10px;color:darkred;"></div>
                                        <textarea hidden id="feedback" name="feedback" style="width:100%" rows="2" placeholder="Type your feedback here"></textarea>
        ';
echo '                           <div class="mowc_customer_validation-modal-footer" >    
                                    <input type="submit" name="miniorange_wc_feedback_submit" class="mowc-button primary"  style="float: right;"
                                        data-sm="' . esc_attr( $submit_and_deactivate_message ) . '" data-sm2="' . esc_attr( $submit_message ) . '" value="' . esc_attr( $submit_and_deactivate_message ) . '" />';
echo '                                        
                                </div>
                            </form>    
                        
                </div>
            </div>
       ';
