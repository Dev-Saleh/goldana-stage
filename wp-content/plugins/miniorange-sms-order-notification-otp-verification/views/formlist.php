<?php
/**
 * Load admin view for header of forms.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoMessages;

$class_name  = 'YourOwnForm';
$class_name  = $class_name . '#' . $class_name;
$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
$url         = add_query_arg(
	array(
		'page' => 'mowcsettings',
		'form' => $class_name,
	),
	$request_uri
);

echo '			<div class="mowc_registration_table_layout"';
					echo esc_attr( $form_name ) ? 'hidden' : '';
echo '		         id="form_search">
					<div style="width:100%">
						<div>
							<div colspan="2">
								<h2>
								    ' . esc_html( mowc_( 'SELECT YOUR FORM FROM THE LIST BELOW' ) ) . ':';
echo '							    
							        <span style="float:right;margin-top:-10px;">
							            <a  class="show_configured_forms button button-primary button-large" 
                                            href="' . esc_url( $moaction ) . '">
                                            ' . esc_html( mowc_( 'Show All Enabled Forms' ) ) . '
                                        </a>

                                        <span   class="mo-dashicons dashicons dashicons-arrow-up mowc-toggle-div" 
                                                data-show="false" 
                                                data-toggle="modropdown"></span>
                                    </span>
                                    <input  name="save" id="ov_settings_button_config"
                                            class="button button-primary button-large" ' . esc_attr( $disabled ) . '
                                            value="' . esc_html( mowc_( 'Save Settings' ) ) . '" type="submit">
                                </h2> ';
					echo '<b><font color="#0085ba"><a style = "text-decoration: none;" href="' . esc_url( $url ) . '" data-form="YourOwnForm#YourOwnForm">Not able to find your form.</a></font></b>';
echo '<span class="tooltip">
                                                <span class="dashicons dashicons-editor-help"></span>
                                                <span class="tooltiptext">
                                                    <span class="header"><b><i>' . esc_html( MoMessages::showMessage( MoMessages::FORM_NOT_AVAIL_HEAD ) ) . '</i></b></span><br/><br/>
                                                    <span class="body">We are actively adding support for more forms. Please contact us using the support form on your right or email us at <a onClick="otpSupportOnClickWC();""><font color="white"><u>' . esc_html( MoConstants::FEEDBACK_EMAIL ) . '</u>.</font></a> While contacting us please include enough information about your registration form and how you intend to use this plugin. We will respond promptly.</span>
                                                </span>
                                              </span>';

echo '</div>
						</div>
						<div>
							<div colspan="2">';
								get_wc_otp_verification_form_dropdown( $controller );
echo '							
							</div>
						</div>
					</div>
				</div>';
