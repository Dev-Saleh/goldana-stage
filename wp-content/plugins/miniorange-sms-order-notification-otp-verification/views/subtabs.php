<?php
/**
 * Load admin view for subtabs.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( isset( $sub_tab_details->sub_tab_details[ $active_tab ] ) ) {
	$sub_tab_list = $sub_tab_details->sub_tab_details[ $active_tab ];
	echo '	<div id="mowc_subtab" class="mowc-subtabs-container">';

	foreach ( $sub_tab_list as $sub_tabs ) {
		if ( $sub_tabs->show_in_nav ) {
			echo '
                    <div class="mowc-subtab-item">
                        <span class="mowc-subtab-title" 
                            id="' . esc_attr( $sub_tabs->id ) . '">
                            ' . esc_html( $sub_tabs->tab_name ) . '
                        </span>
                    </div>';
		}
	}
	echo '</div>';
}
