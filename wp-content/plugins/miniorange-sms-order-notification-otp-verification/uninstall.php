<?php
/**
 * Deletes options saved in the plugin.
 *
 * @package miniorange-order-notifications-woocommerce
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

	do_action( 'mowc_otp_verification_delete_addon_options' );

	delete_site_option( 'mo_wc_customer_validation_admin_email' );
	delete_site_option( 'mo_wc_customer_validation_company_name' );
	delete_site_option( 'mo_wc_customer_validation_first_name' );
	delete_site_option( 'mo_wc_customer_validation_last_name' );
	delete_site_option( 'mo_wc_customer_validation_wc_default_enable' );
	delete_site_option( 'mo_wc_customer_validation_wc_enable_type' );
	delete_site_option( 'mo_wc_customer_validation_wc_social_login_enable' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_enable' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_type' );
	delete_site_option( 'mo_wc_customer_validation_wc_redirect' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_button' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_guest' );
	delete_site_option( 'mo_wc_customer_validation_check_ln' );
	delete_site_option( 'mo_wc_customer_validation_wp_login_enable' );
	delete_site_option( 'mo_wc_customer_validation_wp_login_register_phone' );
	delete_site_option( 'mo_wc_customer_validation_wp_login_bypass_admin' );
	delete_site_option( 'mo_wc_customer_validation_wp_login_key' );
	delete_site_option( 'mo_wc_customer_validation_wp_member_reg_enable' );
	delete_site_option( 'mo_wc_customer_validation_wp_member_reg_enable_type' );
	delete_site_option( 'mo_wc_customer_validation_default_country_code' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_popup' );
	delete_site_option( 'mo_wc_customer_validation_wp_login_allow_phone_login' );
	delete_site_option( 'mo_wc_customer_validation_wp_login_restrict_duplicates' );
	delete_site_option( 'mo_wc_customer_validation_blocked_domains' );
	delete_site_option( 'mo_wc_customer_validation_blocked_phone_numbers' );
	delete_site_option( 'mo_wc_customer_validation_wp_reg_restrict_duplicates' );
	delete_site_option( 'mo_wc_customer_validation_show_remaining_trans' );
	delete_site_option( 'mo_wc_customer_validation_show_dropdown_on_form' );
	delete_site_option( 'mo_wc_customer_validation_email_verification_lk' );
	delete_site_option( 'mo_wc_customer_validation_site_email_ckl' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_payment_type' );
	delete_site_option( 'mo_wc_customer_validation_otp_length' );
	delete_site_option( 'mo_wc_customer_validation_otp_validity' );
	delete_site_option( 'mo_wc_customer_validation_generate_alphanumeric_otp' );
	delete_site_option( 'mo_wc_customer_validation_globally_banned_phone' );
	delete_site_option( 'mo_wc_customer_validation_masterotp_validity' );
	delete_site_option( 'mo_wc_customer_validation_masterotp_admin' );
	delete_site_option( 'mo_wc_customer_validation_masterotp_user' );
	delete_site_option( 'mo_wc_customer_validation_masterotp_admins' );
	delete_site_option( 'mo_wc_customer_validation_masterotp_specific_user' );
	delete_site_option( 'mo_wc_customer_validation_masterotp_specific_user_details' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_selective_payment' );
	delete_site_option( 'mo_wc_customer_validation_custom_popups' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_button_link_text' );
	delete_site_option( 'mo_wc_customer_validation_wc_billing_enable' );
	delete_site_option( 'mo_wc_customer_validation_wc_billing_type_enabled' );
	delete_site_option( 'mo_wc_customer_validation_wc_billing_restrict_duplicates' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_restrict_duplicates' );
	delete_site_option( 'mo_wc_customer_validation_wc_checkout_disable_auto_login' );

	delete_site_option( 'mo_wc_otp_success_email_message' );
	delete_site_option( 'mo_wc_otp_success_phone_message' );
	delete_site_option( 'mo_wc_otp_error_phone_message' );
	delete_site_option( 'mo_wc_otp_error_email_message' );
	delete_site_option( 'mo_wc_otp_invalid_phone_message' );
	delete_site_option( 'mo_wc_otp_invalid_email_message' );
	delete_site_option( 'mo_wc_otp_blocked_phone_message' );
	delete_site_option( 'mo_wc_otp_blocked_email_message' );
	delete_site_option( 'mo_wc_otp_invalid_message' );

