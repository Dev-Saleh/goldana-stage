<?php
/*
Plugin Name: Update Options API
Description: A simple API to update wp_options values.
Version: 1.0
Author: Dev saleh
*/

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/update-option', array(
        'methods' => 'POST',
        'callback' => 'update_option_callback',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        }
    ));
});

function update_option_callback($request) {
    $option_name = $request->get_param('option_name');
    $option_value = $request->get_param('option_value');

    if (!$option_name || !$option_value) {
        return new WP_Error('missing_parameters', 'Option name and value are required.', array('status' => 400));
    }

    if (update_option($option_name, $option_value)) {
        return rest_ensure_response(array('success' => true, 'message' => 'Option updated successfully.'));
    } else {
        return rest_ensure_response(array('success' => false, 'message' => 'Failed to update option.'));
    }
}
