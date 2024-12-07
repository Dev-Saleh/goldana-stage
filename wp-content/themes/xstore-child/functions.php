<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 1001 );
function theme_enqueue_styles() {
	if (function_exists('etheme_child_styles')){
		etheme_child_styles();
	}
}


function enqueue_custom_inline_script() {
   
    wp_enqueue_script('jquery');
    
  
    $custom_js = "
    const updateProductPrices = () => {
      const elements = document.querySelectorAll(
        '.wc-block-components-product-price:not([data-updated])'
      );
      elements.forEach((element) => {
        element.innerHTML = `<p>يتم جلب السعر...</p>`;
        element.setAttribute('data-updated', 'true');
      });
    };
    
        jQuery(document).ready(function($){ 
         $(document).on('et_ajax_element_loaded', function (event, data) {
                if (data.element == 'etheme_products'){
                    console.log('custom code started to show tap');
					updateProductPrices();
                }
                });
    /*-------------------------------------------------------------------*/
			$(document).on('etheme_product_grid_ajax_loaded', function() {
            	console.log('etheme_product_grid_ajax_loaded is appliedddd');
        	})
		});
    ";
    wp_add_inline_script('jquery', $custom_js); // Attach the inline script after jQuery
}
add_action('wp_enqueue_scripts', 'enqueue_custom_inline_script');


// add_action('template_redirect', 'redirect_non_logged_users_to_custom_login');
// function redirect_non_logged_users_to_custom_login() {
//     if (is_checkout() && !is_user_logged_in()) {
//         // Replace 'your-custom-login-page-url' with the actual URL of your login page
//         $custom_login_url = site_url('my-account/'); 

//         // Append the redirect URL to send the user back to checkout after login
//         $redirect_url = add_query_arg('redirect_to', wc_get_checkout_url(), $custom_login_url);

//         wp_redirect($redirect_url);
//         exit;
//     }
// }



// function block_non_mobile_users() {
   
//     if (!wp_is_mobile()) {
//         wp_redirect('https://google.com/'); 
//         exit();
//     }
// }
// add_action('template_redirect', 'block_non_mobile_users');