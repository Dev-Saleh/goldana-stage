<?php
/**
 * Load admin view for Form list.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '<div class="mowc_registration_table_layout" id="selected_form_details">
					<table id="mo_forms" style="width: 100%;">
						<tr>
							<td>
								<h2>
									<i>' . esc_html( mowc_( 'FORM SETTINGS' ) ) . '</i>
									<span style="float:right;margin-top:-10px;">
									    <a  class="show_configured_forms button button-primary button-large" 
                                            href="' . esc_url( $moaction ) . '">
                                            ' . esc_html( mowc_( 'Show All Enabled Forms' ) ) . '
                                        </a>
									    <a class="show_form_list button button-primary button-large"
									        href="' . esc_url( $forms_list_page ) . '">
									        ' . esc_html( mowc_( 'Show Forms List' ) ) . '
                                        </a>
                                        <input  name="save" id="ov_settings_button" ' . esc_attr( $disabled ) . ' 
                                                class="button button-primary button-large" 
                                                value="' . esc_attr( mowc_( 'Save Settings' ) ) . '" type="submit">
                                        <span   class="mo-dashicons dashicons dashicons-arrow-up mowc-toggle-div" 
                                                data-show="false" 
                                                data-toggle="new_form_settings"></span>
                                    </span>									
								</h2><hr>
							</td>
						</tr>
						<tr>
							<td>
								<div id="new_form_settings">
									<div id="form_details">';
										require $controller . 'forms/' . strtolower( $form_name ) . '.php';

echo '</div>
								</div>
							</td>
						</tr>
					</table>
				</div>';
