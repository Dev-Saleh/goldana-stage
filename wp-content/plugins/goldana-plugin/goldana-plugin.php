<?php
/*
Plugin Name: Goldana Plugin
Description: A simple plugin for adding custom functions to the Goldana site.
Version: 1.0
Author: Dev Saleh
*/




function my_custom_plugin_enqueue_scripts() {
    $scripts = array(
        'index' => 'index.js',
        // 'helper' => 'helper.js',
        // 'extra' => 'extra.js',
    );

    foreach ($scripts as $handle => $file) {
        // Dynamically enqueue each script
        wp_enqueue_script(
            "my-custom-plugin-$handle",                     // Script handle
            plugin_dir_url(__FILE__) . $file,               // Script URL
            array('jquery'),                                // Dependencies
            '19.2',                                        // Version
            false                                            // Load in the footer
        );

        // Mark the script as a module
        // wp_script_add_data("my-custom-plugin-$handle", 'type', 'module');
    }
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');

/*-----------------------------------------------------------------------------------------------*/


/**
 * Helper function to get attribute value more efficiently.
 */
function get_attribute_value_from_attributes($attributes, $product_id, $attribute_name) {
  
    foreach ($attributes as $attribute_key => $attribute) {
     $label = urldecode(wc_attribute_label($attribute_key)); 
        
      
        if ($label === $attribute_name) {
            if ($attribute->is_taxonomy()) {
                // If it's a taxonomy attribute (like "pa_gold_carat")
                $terms = wp_get_post_terms($product_id, $attribute_key, array('fields' => 'names'));
                return !empty($terms) ? $terms[0] : ''; 
            } else {
                // If it's a custom attribute (non-taxonomy)
                $options = $attribute->get_options();
                return !empty($options) ? $options[0] : ''; 
            }
        }
    }

    return ''; 
	
   
}

add_filter('woocommerce_get_price_html', 'add_custom_data_attributes_to_price_html', 10, 2);
function add_custom_data_attributes_to_price_html($price, $product) {

    if (!$product) {
        return $price; 
    }

    $product_id = $product->get_id(); 
    $fixed_price = get_option('fixed-livePrice'); // Retrieve fixed price option

    // Cache product attributes and weight for better performance
    $attributes = $product->get_attributes();
    $weight = $product->get_weight();

    // Define attribute names
    $attribute_gold_carat_name = 'العيار'; 
    $attribute_manufacturing_fees_name = 'المصنعيه'; 

    // Get the values for these attributes
    $gold_carat = get_attribute_value_from_attributes($attributes, $product_id, $attribute_gold_carat_name);
    $manufacturing_fees = get_attribute_value_from_attributes($attributes, $product_id, $attribute_manufacturing_fees_name);

    // Fallbacks for missing data
    $weight = !empty($weight) ? $weight : 'N/A';
    $gold_carat = !empty($gold_carat) ? $gold_carat : 'N/A';
    $manufacturing_fees = !empty($manufacturing_fees) ? $manufacturing_fees : 'N/A';
    $fixed_price = !empty($fixed_price) ? $fixed_price : 'N/A';

    // Append custom data attributes to the price HTML
    return sprintf(
        '<div class="wc-block-components-product-price" id="livePriceEl" 
            data-product-id="%1$s" 
            data-product-weight="%2$s" 
            data-product-gold-carat="%3$s" 
            data-product-manufacturing-fees="%4$s"
            data-fixed-price="%5$s">%6$s</div>',
        esc_attr($product_id),               // Product ID
        esc_attr($weight),                   // Product weight
        esc_attr($gold_carat),               // Gold carat value
        esc_attr($manufacturing_fees),       // Manufacturing fees
        esc_attr($fixed_price),              // Fixed price
        $price                               // Original price HTML from WooCommerce
    );
}
/*-----------------------------------------------------------------------------------------------*/

function calculate_price($c, $live_price_24, $weight = 1, $manufacturingFees = 0) {
    switch ($c) {
        case 18:
            $livePrice_18 = $live_price_24 * 0.75;
            return $weight * ($manufacturingFees + $livePrice_18) * 1.15;

        case 21:
            $livePrice_21 = $live_price_24 * 0.875;
            return $weight * ($manufacturingFees + $livePrice_21) * 1.15;

        case 24:
            return $live_price_24 * $weight;

        default:
            return null; // or handle the default case if needed
    }
}

add_filter('woocommerce_add_to_cart_validation', 'validate_live_price_for_cart', 10, 3);

function validate_live_price_for_cart($passed, $product_id, $quantity) {
   
    if (empty(get_option('fixed-livePrice'))) {
        
        wc_add_notice('لا يمكن اضافته للسله,لم يتم جلب السعر العالمي بنجاح', 'error');
        
       
        return false;
    }

  
    return $passed;
}


add_action('woocommerce_ajax_added_to_cart', 'set_product_with_live_price');

function set_product_with_live_price($product_id) {
    // Avoid running the function more than once
    if ( did_action( 'woocommerce_ajax_added_to_cart' ) >= 2 ) {
        return;
    }
    if(empty(get_option('fixed-livePrice')))
    {
          wc_add_notice('x1','error');
         return false;
    }
    
    $live_price_24 =  get_option('fixed-livePrice') ;
    
    $product = wc_get_product( $product_id );
    
    if ( !$product ) {
        return false; 
    }

    // Cache product attributes and weight for better performance
    $attributes = $product->get_attributes();
    $weight = $product->get_weight();

    // Logging for debugging purposes
    error_log( 'attributes: ' . print_r( $attributes, true ) );
    error_log( 'weight: ' . $weight );

        // Define attribute names
    $attribute_gold_carat_name = 'العيار'; 
    $attribute_manufacturing_fees_name = 'المصنعيه'; 

    // Get the values for these attributes
    $gold_carat = get_attribute_value_from_attributes($attributes, $product_id, $attribute_gold_carat_name);
    $manufacturing_fees = get_attribute_value_from_attributes($attributes, $product_id, $attribute_manufacturing_fees_name);

    error_log( 'gold_carat: ' . $gold_carat );
    error_log( 'manufacturing_fees: ' . $manufacturing_fees );
    error_log( 'weight: ' . $weight );

    // Fallbacks for missing data
    $weight = !empty($weight) ? $weight : 0;
    $gold_carat = !empty($gold_carat) ? $gold_carat : 0;
    $manufacturing_fees = !empty($manufacturing_fees) ? $manufacturing_fees : 0;

    error_log( 'gold_carat: ' . $gold_carat );
    error_log( 'manufacturing_fees: ' . $manufacturing_fees );
    error_log( 'weight: ' . $weight );


      	 $xx = calculate_price($gold_carat,$live_price_24,$weight,$manufacturing_fees);



        // Get the cart object
        $cart = WC()->cart;

        // Get the cart item key of the item that was just added
        $cart_item_key = $cart->generate_cart_id( $product_id );

        // Get the cart item using the cart item key
        $cart_item = $cart->get_cart_item( $cart_item_key );

			if ( $cart_item ) {
				// Update the specific cart item's metadata with the live price
				$cart_item['LIVE-PRICE'] = $xx;

				// Update the cart with the modified item
				$cart->cart_contents[ $cart_item_key ] = $cart_item;

				// Save the cart session to persist changes
				WC()->cart->set_session();

			
			} else {
				error_log( 'Cart item not found for product ID: ' . $product_id );
			}
 	}
	
	 



















 


add_action( 'woocommerce_before_mini_cart', 'update_cart_item_price_for_mini_cart', 5 );

function update_cart_item_price_for_mini_cart() {
    //   error_log('update_cart_item_price_for_mini_cart ');
    if ( ! WC()->cart ) return;
     
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['LIVE-PRICE'] ) && ! empty( $cart_item['LIVE-PRICE'] ) ) {
            $new_price = floatval( $cart_item['LIVE-PRICE'] );
            $cart_item['data']->set_price( $new_price );
            $cart_item['data']->set_sale_price( $new_price * 1.3 );
            $cart_item['data']->set_regular_price( $new_price );
        }
    }
    WC()->cart->calculate_totals();
}



add_action( 'woocommerce_before_calculate_totals', 'update_cart_item_price_based_on_live_price', 10, 1 );
add_action( 'woocommerce_cart_loaded_from_session', 'update_cart_item_price_based_on_live_price', 5, 1 );

function update_cart_item_price_based_on_live_price( $cart ) {
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
        return;
    }
  
    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['LIVE-PRICE'] ) && ! empty( $cart_item['LIVE-PRICE'] ) ) {
            $new_price = floatval( $cart_item['LIVE-PRICE'] );
            $cart_item['data']->set_price( $new_price );
            $cart_item['data']->set_sale_price( $new_price * 1.3 );
            $cart_item['data']->set_regular_price( $new_price );
        }
    }
  
    // Force WooCommerce to recalculate totals and prevent caching issues
    WC()->cart->calculate_totals();
}




