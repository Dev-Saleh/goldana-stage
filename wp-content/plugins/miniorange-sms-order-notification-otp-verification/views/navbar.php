<?php
/**
 * Load admin view for Account Tab.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$setup_guide_url = 'https://plugins.miniorange.com/woocommerce-order-notifications-plugin-setup-wordpress';

echo '	<div id="tab" class="w-max md:min-w-[268px] md:w-[268px] border-r p-mowc-4">
			
            <div class="flex flex-col gap-mowc-2">';

foreach ( $tab_details->tab_details as $mo_tabs ) {
	if ( $mo_tabs->show_in_nav ) {
				echo '
                <div>
                    <a  class="mowc-tab-item ' . ( $active_tab === $mo_tabs->menu_slug ? 'bg-slate-100' : '' ) . '"
                        href="' . esc_url( $mo_tabs->url ) . '"
                        id="' . esc_attr( $mo_tabs->id ) . '">
                        <svg
                          width="22"
                          height="22"
                          fill="none"
                          viewBox="0 0 24 24"
                        >
                          <path
                            fillRule="evenodd"
                            clipRule="evenodd"
                            d="' . esc_attr( $mo_tabs->icon ) . '"
                            fill="#000"
                          />
                        </svg>
                        
                        <p>' . esc_attr( $mo_tabs->tab_name ) . '</p>
                    
                    </a>

                </div>';
	}
}

				echo '
                <div>
                    <hr> 
                    <a class="mt-mowc-2 mowc-tab-item" id="LicensingPlanButton" href="' . esc_url( $license_url ) . '">' . esc_html( mowc_( 'Licensing Plans' ) ) . '</a>       
                    <a class="mt-mowc-2 mowc-tab-item" id="Setup_guide" href="' . esc_url( $setup_guide_url ) . '"  target="_blank ">' . esc_html( mowc_( 'Setup Guide' ) ) . '</a>                  
                    <a><span class="mowc-tab-item" onClick="otpSupportOnClickWC(\'Hi! I am interested in using your plugin and would like to get a demo of the features and functionality. Please schedule a demo for the plugin. \');" id="demoButton">
                        ' . esc_html( mowc_( 'Need a Demo?' ) ) . '
                    </span>
                        </a>
                    <a class="mowc-tab-item" id="faqButton" href="' . esc_url( $help_url ) . '" target="_blank">' . esc_html( mowc_( 'FAQs' ) ) . '</a>
                </div>';

			echo '
            </div>

        </div>';

