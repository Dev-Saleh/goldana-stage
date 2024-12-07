<?php
/**
 * Loads View for List of all the addons.
 *
 * @package miniorange-otp-verification
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo '	
			<div id="addOnsTable">
				<form name="f" method="post" action="" id="mo_add_on_settings">
					<input type="hidden" name="option" value="mo_add_on_settings" />
						<div class="mowc-header">
							<p class="mowc-heading flex-1">' . esc_html( mowc_( 'Addons:' ) ) . '</p>
						</div>
						<div id="addons-grid" class="mo-addon-section-container">';
						show_woocommerce_addon_list();
	echo '      		</div>
				</form>
			</div>';

