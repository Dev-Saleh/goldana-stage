<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 1001 );
function theme_enqueue_styles() {
	if (function_exists('etheme_child_styles')){
		etheme_child_styles();
	}
}


function add_text_and_icon_after_price($price, $product) {
    // Only add the HTML if we're on the single product page
    if (is_product()) {
        $custom_html = '
        <span style="display: inline-flex; margin-top: 5px; align-items: center; background-color: #ebf8ff; padding: 10px 5px; border-radius: 4px; font-size: 12px; font-weight: 		bold; color: #3182ce; border: 1px solid rgba(59, 130, 246, 0.1); width: 100%;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px; margin-left: 				5px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            سعر مناسب للشراء  
        </span>';

        $price .= $custom_html; // Append the custom HTML to the price
    }
    return $price;
}
add_filter('woocommerce_get_price_html', 'add_text_and_icon_after_price', 10, 2);


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