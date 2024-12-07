<?php
/**
 * Load user view for admin panel.
 *
 * @package miniorange-order-notifications-woocommerce/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use WCSMSOTP\Helper\CountryList;
use WCSMSOTP\Helper\MoFormList;
use WCSMSOTP\Helper\PremiumFormList;
use WCSMSOTP\Helper\GatewayFunctions;
use WCSMSOTP\Helper\MoUtility;
use WCSMSOTP\Helper\Templates\DefaultPopup;
use WCSMSOTP\Helper\Templates\ErrorPopup;
use WCSMSOTP\Helper\Templates\ExternalPopup;
use WCSMSOTP\Helper\Templates\UserChoicePopup;
use WCSMSOTP\Objects\FormHandler;
use WCSMSOTP\Objects\TabDetails;
use WCSMSOTP\Objects\Tabs;
use WCSMSOTP\Helper\PremiumAddonList;


/**
 * This displays a link next to the name of each of the forms under the
 * forms tab so that user can see if the form in question is the correct
 * form.
 * Also adds A link to Guide and Video Tutorial if any.
 *
 * @param  array $formalink -   array of the link to the forms main page['formLink'],
 *                              guide Link['guideLink] and Video Tutotial['videoLink].
 */
function get_wc_plugin_form_link( $formalink ) {
	if ( MoUtility::sanitize_check( 'formLink', $formalink ) ) {
		echo '<a    class="dashicons mo-form-links dashicons-feedback mo_form_icon"
                    href="' . esc_url( $formalink['formLink'] ) . '"
                    title="' . esc_url( $formalink['formLink'] ) . '"
                    id="formLink"  
                    target="_blank">' .
				'<span class="mo-link-text">' . esc_html( mowc_( 'FormLink' ) ) . '</span>' .
			'</a>';
	}
	if ( MoUtility::sanitize_check( 'guideLink', $formalink ) ) {
		echo '<a    class="dashicons mo-form-links dashicons-book-alt mo_book_icon"
                    href="' . esc_url( $formalink['guideLink'] ) . '"
                    title="Instruction Guide"
                    id="guideLink" 
                    target="_blank">' .
			'<span class="mo-link-text">' . esc_html( mowc_( 'Setup Guide' ) ) . '</span>' .
		'</a>';
	}
	if ( MoUtility::sanitize_check( 'videoLink', $formalink ) ) {
		echo '<a    class="dashicons mo-form-links dashicons-video-alt3 mo_video_icon"
                    href="' . esc_url( $formalink['videoLink'] ) . '"
                    title="Tutorial Video"
                    id="videoLink"  
                    target="_blank">' .
			'<span class="mo-link-text">' . esc_html( mowc_( 'Video Tutorial' ) ) . '</span>' .
		'</a>';
	}
	echo '<br/><br/>';
}


/**
 * Display a tooltip with the appropriate header and message on the page
 *
 * @param  string $header  - the header of the tooltip.
 * @param  string $message - the body of the tooltip message.
 */
function mowc_draw_tooltip( $header, $message ) {
	echo '<span class="tooltip">
            <span class="dashicons dashicons-editor-help"></span>
            <span class="tooltiptext">
                <span class="header"><b><i>' . esc_html( mowc_( $header ) ) . '</i></b></span><br/><br/>
                <span class="body">' . esc_html( mowc_( $message ) ) . '</span>
            </span>
          </span>';
}


/**
 * This is used to display extra post data as hidden fields in the verification
 * page so that it can used later on for processing form data after verification
 * is complete and successful.
 *
 * @param array $data - the data posted by the user using the form.
 * @return string
 */
function extra_wc_post_data( $data = null ) {
	$ignore_fields = array(
		'moFields'          => array(
			'option',
			'mo_otp_token',
			'miniorange_wc_otp_token_submit',
			'miniorange-validate-otp-choice-form',
			'submit',
			'mo_customer_validation_otp_choice',
			'register_nonce',
			'timestamp',
		),
		'loginOrSocialForm' => array(
			'user_login',
			'user_email',
			'register_nonce',
			'option',
			'register_tml_nonce',
			'mo_otp_token',
		),
	);

	$extra_post_data      = '';
	$login_or_social_form = false;
	$login_or_social_form = apply_filters( 'mowc_is_login_or_social_form', $login_or_social_form );
	$fields               = ! $login_or_social_form ? 'moFields' : 'loginOrSocialForm';
	foreach ( $_POST as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need for nonce verification as the function is called on third party plugin hook.
		$extra_post_data .= ! in_array( $key, $ignore_fields[ $fields ], true ) ? get_wc_hidden_fields( $key, $value ) : '';
	}
	return $extra_post_data;
}


/**
 * Show hidden fields. Makes hidden input fields on the page.
 *
 * @param  string $key   - the name attribute of the hidden field.
 * @param  string $value - the value of the input field.
 * @return string
 */
function get_wc_hidden_fields( $key, $value ) {
	if ( 'wordfence_userDat' === $key ) {
		return;
	}
	$hidden_val = '';
	if ( is_array( $value ) ) {
		foreach ( $value as $t => $val ) {
			$hidden_val .= get_wc_hidden_fields( $key . '[' . $t . ']', $val );
		}
	} else {
		$hidden_val .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
	}
	return $hidden_val;
}


/**
 * The HTML code to display the OTP Verification pop up with appropriate messaging
 * and hidden fields for later processing.
 *
 * @param string $user_login the username posted by the user.
 * @param string $user_email the email posted by the user.
 * @param string $phone_number the phone number posted by the user.
 * @param string $message message posted by the user.
 * @param string $otp_type the verification type.
 * @param string $from_both any extra data posted by the user.
 */
function miniorange_wc_site_otp_validation_form( $user_login, $user_email, $phone_number, $message, $otp_type, $from_both ) {
	if ( ! headers_sent() ) {
		header( 'Content-Type: text/html; charset=utf-8' );
	}

	$error_popup_handler = ErrorPopup::instance();

	$default_popup_handler = DefaultPopup::instance();
	$html_content          = MoUtility::is_blank( $user_email ) && MoUtility::is_blank( $phone_number ) ?
					apply_filters( 'mo_template_build', '', $error_popup_handler->get_template_key(), $message, $otp_type, $from_both )
					: apply_filters( 'mo_template_build', '', $default_popup_handler->get_template_key(), $message, $otp_type, $from_both );
	echo wp_kses( mowc_( $html_content ), MoUtility::mowc_allow_html_array() );
	exit();
}


/**
 * Display the user choice popup where user can choose between email or
 * sms verification.
 *
 * @param string $user_login the username posted by the user.
 * @param string $user_email the email posted by the user.
 * @param string $phone_number the phone number posted by the user.
 * @param string $message message posted by the user.
 * @param string $otp_type the verification type.
 */
function miniorange_wc_verification_user_choice( $user_login, $user_email, $phone_number, $message, $otp_type ) {
	if ( ! headers_sent() ) {
		header( 'Content-Type: text/html; charset=utf-8' );
	}
	$user_choice_popup = UserChoicePopup::instance();
	$htmlcontent       = apply_filters( 'mo_template_build', '', $user_choice_popup->get_template_key(), $message, $otp_type, true );
	echo wp_kses( mowc_( $htmlcontent ), MoUtility::mowc_allow_html_array() );
	exit();
}


/**
 * Display the popup where user has to enter his phone number and then
 * validate the OTP sent to it. This phone number is later stored in the
 *
 * @param string $go_back_url the redirection url on click of go back button.
 * @param string $user_email the email posted by the user.
 * @param string $message message posted by the user.
 * @param string $form the form details posted by the user.
 * @param string $usermeta the user meta.
 * database.
 */
function mowc_external_phone_validation_form( $go_back_url, $user_email, $message, $form, $usermeta ) {
	if ( ! headers_sent() ) {
		header( 'Content-Type: text/html; charset=utf-8' );
	}
	$external_pop_up = ExternalPopup::instance();
	$htmlcontent     = apply_filters( 'mo_template_build', '', $external_pop_up->get_template_key(), $message, null, false );
	echo wp_kses( mowc_( $htmlcontent ), MoUtility::mowc_allow_html_array() );
	exit();
}

/**
 * Display a dropdown on the page with list of all plugins that are supported.
 */
function get_wc_otp_verification_form_dropdown() {
	$count        = 0;
	$form_handler = MoFormList::instance();
	$tab_details  = TabDetails::instance();
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	echo '
        <div class="modropdown" id="modropdown">
            <span class="dashicons dashicons-search"></span>
                <input type="text" id="searchForm" class="dropbtn" placeholder="' . esc_attr( mowc_( 'Search and select your Form.' ) ) . '" />
            <div class="modropdown-content" id="formList">';

	foreach ( $form_handler->get_list() as $key => $form ) {
		$count++;
		$class_name = get_mo_wc_class( $form );
		$class_name = $form->is_form_enabled() ? 'configured_forms#' . $class_name : $class_name . '#' . $class_name;
		$url        = add_query_arg(
			array(
				'page' => $tab_details->tab_details[ Tabs::FORMS ]->menu_slug,
				'form' => $class_name,
			),
			$request_uri
		);
		if ( ! $form->is_add_on_form() ) {
			echo '<div class="search_box">';
			echo '<a class="mo_search"';
			echo ' href="' . esc_url( $url ) . '" ';
			echo ' data-value="' . esc_attr( $form->get_form_name() ) . '" data-form="' . esc_attr( $class_name ) . '">';
			echo ' <span class="';
			echo $form->is_form_enabled() ? 'enabled">' : '">';
			if ( strrpos( $class_name, 'YourOwnForm' ) === 0 ) {
				echo esc_attr( $count ) . '.&nbsp';
			}
			echo $form->is_form_enabled() ? '( ENABLED ) ' : '';
			echo wp_kses(
				$form->get_form_name(),
				array(
					'b'    => array(),
					'span' => array(
						'style' => array(),
					),
				)
			) . '</span></a></div>';
		}
	}
	echo '</div>
        </div>';
}


/**
 * Display a dropdown with country and it's respective country code.
 */
function get_wc_country_code_dropdown() {
	echo '<select name="default_country_code" id="mo_country_code">';
	echo '<option value="" disabled selected="selected">
            --------- ' . esc_html( mowc_( 'Select your Country' ) ) . ' -------
          </option>';
	foreach ( CountryList::get_countrycode_list() as $key => $country ) {
		echo '<option data-countrycode="' . esc_attr( $country['countryCode'] ) . '" value="' . esc_attr( $key ) . '"';
		echo CountryList::is_country_selected( mowc_esc_string( $country['countryCode'], 'attr' ), mowc_esc_string( $country['alphacode'], 'attr' ) ) ? 'selected' : '';
		echo '>' . esc_attr( $country['name'] ) . '</option>';
	}
	echo '</select>';
}


/**
 * Display a multiselect dropdown to select countries to show in the
 * dropdown.
 *
 * @todo : This is for a future plugin update which allows user to select list of countries to be shown in the dropdown
 */
function get_wc_country_code_multiple_dropdown() {
	echo '<select multiple size="5" name="allow_countries[]" id="mo_country_code">';
	echo '<option value="" disabled selected="selected">
            --------- ' . esc_html( mowc_( 'Select your Countries' ) ) . ' -------
          </option>';

	echo '</select>';
}

/**
 * Function to get the Class name of the Forms based on the priority.
 *
 * @return array
 */
function get_priority_wise_class_name() {
	$important_forms = array(
		'WooCommerceRegistrationForm',
		'WPLoginForm',
		'WooCommerceCheckOutForm',
		'WooCommerceCheckoutNew',
		'WooCommerceBilling',
		'WooCommerceProductVendors',
	);
	return $important_forms;
}


/**
 * Loop through and show only configured form list
 *
 * @param string $controller -controller attributes.
 * @param string $disabled  -disabled attributes.
 * @param string $page_list  -List of pages.
 */
function show_wc_configured_form_details( $controller, $disabled, $page_list ) {
	$form_handler = MoFormList::instance();

	$premium_form_handler = PremiumFormList::instance();
	$premium_form_handler = $premium_form_handler->get_premium_form_list();
	$license_page_url     = admin_url() . 'admin.php?page=mowcpricing';

	$class_name_list = get_priority_wise_class_name();
	foreach ( $class_name_list as $key => $value ) {
		include $controller . 'forms/class-' . strtolower( $value ) . '.php';
	}

	foreach ( $form_handler->get_list() as $form ) {
		if ( ! $form->is_add_on_form() ) {
			$namespace_class = get_class( $form );
			$class_name      = substr( $namespace_class, strrpos( $namespace_class, '\\' ) + 1 );
			if ( ! in_array( $class_name, $class_name_list, true ) ) {
				include $controller . 'forms/class-' . strtolower( $class_name ) . '.php';
			}
		}
	}

	foreach ( $premium_form_handler as $class_name => $form_name ) {

		echo '	<div class="mowc-formsettings-container" id="mowc_premium_forms">

			<div class="w-full flex items-center">
              	<label class="mowc-checkbox-container flex-1">
                	<input  type="checkbox" disabled
	            	    id="premium_forms" 
	            	    class="sr-only mowc-checkmark"
						name="mowc_premium-forms_enable" 
						value=""/>
                	<span class="mowc-checkmark"></span>
                	<p class="mowc-title"> <a class="flex mowc-title gap-mowc-2" href="' . esc_url( $license_page_url ) . '" target="_blank">' . esc_html( $form_name ) . '</a></p>
              	</label>
				<div data-show="false" class=" before:content-none flex items-center justify-center">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
				                       <g id="d4a43e0162b45f718f49244b403ea8f4">
				                           <g id="4ea4c3dca364b4cff4fba75ac98abb38">
				                                <g id="2413972edc07f152c2356073861cb269">
				                                   <path id="2deabe5f8681ff270d3f37797985a977" d="M20.8007 20.5644H3.19925C2.94954 20.5644 2.73449 20.3887 2.68487 20.144L0.194867 7.94109C0.153118 7.73681 0.236091 7.52728 0.406503 7.40702C0.576651 7.28649 0.801941 7.27862 0.980492 7.38627L7.69847 11.4354L11.5297 3.72677C11.6177 3.54979 11.7978 3.43688 11.9955 3.43531C12.1817 3.43452 12.3749 3.54323 12.466 3.71889L16.4244 11.3598L23.0197 7.38654C23.1985 7.27888 23.4233 7.28702 23.5937 7.40728C23.7641 7.52754 23.8471 7.73707 23.8056 7.94136L21.3156 20.1443C21.2652 20.3887 21.0501 20.5644 20.8007 20.5644Z" fill="orange"></path>
				                               </g>
				                            </g>
				                        </g>
				                    </svg><span class="text-yellow-500" onClick="otpSupportOnClickWC(\'Hi, I want to use OTP Verification on ' . esc_html( $form_name ) . '.\');">Pro Feature</span>
				</div>          
            </div>
			</div>';
	}

	echo '<div class="mowc-alert w-max" style="background-color:#d6e8e8;">
            <span>We are actively adding support for more forms. If you want to upgrade to premium plan or have any queries please contact us or email us at <a style="cursor:pointer;" onClick="otpSupportOnClickWC(\'Hi! I want to integrate OTP verification on my WooCommerce Form. Can you please help me with more information?\');"><u> otpsupport@xecurify.com</u></a>.</span>
            </div>';
}


/**
 * This function is used to show a multi-select dropdown of WooCommerce
 * Checkout Page.
 *
 * @param string $disabled  -disabled attributes.
 * @param array  $checkout_payment_plans -checkout payment plans.
 */
function get_mowc_payment_dropdown( $disabled, $checkout_payment_plans ) {
	if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		echo esc_html( mowc_( '[ Please activate the WooCommerce Plugin ]' ) );
		return;
	}
	$payment_plans = WC()->payment_gateways->payment_gateways(); // phpcs:ignore intelephense.diagnostics.undefinedFunctions -- Default function of Woocommerce
	echo '<select multiple size="5" name="wc_payment[]" id="wc_payment">';
	echo '<option value="">' . esc_html( mowc_( 'Select your Payment Methods' ) ) . '</option>';
	foreach ( $payment_plans as $payment_plan ) {
		echo '<option ';
		if ( $checkout_payment_plans && array_key_exists( $payment_plan->id, $checkout_payment_plans ) ) {
			echo 'selected';
		} elseif ( ! $checkout_payment_plans ) {
			echo 'selected';
		}
		echo ' value="' . esc_attr( $payment_plan->id ) . '">' . esc_html( $payment_plan->title ) . '</option>';
	}
	echo '</select>';
}


/**
 * This function is called to generate the form details fields for a form.
 *
 * @param array  $form_details the details posted by the user.
 * @param string $show_verify_field show verify fields.
 * @param string $show_email_and_phone_field show email and phone field.
 * @param string $disabled disabled attribute.
 * @param string $key the name attribute of the hidden field.
 * @param string $form_name the name of the form.
 * @param string $key_type the type of the key.
 * @return mixed
 */
function get_wc_multiple_form_select( $form_details, $show_verify_field, $show_email_and_phone_field, $disabled, $key, $form_name, $key_type ) {
	$row_template = "<div id='row{FORM}{KEY}_{INDEX}'>
                            %s : 
                            <input  id='{FORM}_form_{KEY}_{INDEX}' 
                                    class='field_data' 
                                    name='{FORM}_form[form][]' 
                                    type='text' 
                                    value='{FORM_ID_VAL}'>
                                    {EMAIL_AND_PHONE_FIELD}
                                    {VERIFY_FIELD}
                        </div>";

	$email_and_phone_field = " <span {HIDDEN1}>
                                    %s: 
                                    <input  id='{FORM}_form_email_{KEY}_{INDEX}' 
                                            class='field_data' 
                                            name='{FORM}_form[emailkey][]' 
                                            type='text' 
                                            value='{EMAIL_KEY_VAL}'>
                                </span>
                                <span {HIDDEN2}>
                                    %s: 
                                    <input  id='{FORM}_form_phone_{KEY}_{INDEX}' 
                                            class='field_data'  
                                            name='{FORM}_form[phonekey][]' 
                                            type='text' value='{PHONE_KEY_VAL}'>
                                </span>";

	$verify_field = "<span>
                            %s: 
                            <input  class='field_data' 
                                    id='{FORM}_form_verify_{KEY}_{INDEX}' 
                                    name='{FORM}_form[verifyKey][]' 
                                    type='text' value='{VERIFY_KEY_VAL}'>
                        </span>";

	$verify_field = $show_verify_field ? $verify_field : '';

	$email_and_phone_field = $show_email_and_phone_field ? $email_and_phone_field : '';

	$row_template = MoUtility::replace_string(
		array(
			'VERIFY_FIELD'          => $verify_field,
			'EMAIL_AND_PHONE_FIELD' => $email_and_phone_field,
		),
		$row_template
	);

	$row_template = sprintf(
		$row_template,
		mowc_( 'Form ID' ),
		mowc_( "Email Field $key_type" ),
		mowc_( "Phone Field $key_type" ),
		mowc_( "Verification Field $key_type" )
	);

	$counter = 0;
	if ( MoUtility::is_blank( $form_details ) ) {
		$details = array(
			'KEY'            => $key,
			'INDEX'          => 0,
			'FORM'           => $form_name,
			'HIDDEN1'        => 2 === $key ? 'hidden' : '',
			'HIDDEN2'        => 1 === $key ? 'hidden' : '',
			'FORM_ID_VAL'    => '',
			'EMAIL_KEY_VAL'  => '',
			'PHONE_KEY_VAL'  => '',
			'VERIFY_KEY_VAL' => '',
		);
		echo wp_kses(
			MoUtility::replace_string( $details, $row_template ),
			array(
				'div'   => array( 'id' => array() ),
				'input' => array(
					'id'    => array(),
					'class' => array(),
					'name'  => array(),
					'type'  => array(),
					'value' => array(),
				),
				'span'  => array(
					'hidden' => array(),
				),
			)
		);
	} else {
		foreach ( $form_details as $form_key => $form_detail ) {
			$details = array(
				'KEY'            => $key,
				'INDEX'          => $counter,
				'FORM'           => $form_name,
				'HIDDEN1'        => 2 === $key ? 'hidden' : '',
				'HIDDEN2'        => 1 === $key ? 'hidden' : '',
				'FORM_ID_VAL'    => $show_email_and_phone_field ? $form_key : $form_detail,
				'EMAIL_KEY_VAL'  => $show_email_and_phone_field ? $form_detail['email_show'] : '',
				'PHONE_KEY_VAL'  => $show_email_and_phone_field ? $form_detail['phone_show'] : '',
				'VERIFY_KEY_VAL' => $show_verify_field ? $form_detail['verify_show'] : '',
			);
			echo wp_kses(
				MoUtility::replace_string( $details, $row_template ),
				array(
					'div'   => array( 'id' => array() ),
					'input' => array(
						'id'    => array(),
						'class' => array(),
						'name'  => array(),
						'type'  => array(),
						'value' => array(),
					),
					'span'  => array(
						'hidden' => array(),
					),
				)
			);
			$counter++;
		}
	}
	$result['counter'] = $counter;
	return $result;
}


/**
 * This function is used to generate the scripts necessary to add or remove
 * fields for taking form details from the admin.
 *
 * @param string $show_verify_field show verify fields.
 * @param string $show_email_and_phone_field show email and phone field.
 * @param string $form_name the name of the form.
 * @param string $key_type the type of the key.
 * @param string $counters the counters.
 */
function multiple_wc_from_select_script_generator( $show_verify_field, $show_email_and_phone_field, $form_name, $key_type, $counters ) {
	$row_template = "<div id='row{FORM}{KEY}_{INDEX}'>
                            %s : 
                            <input  id='{FORM}_form_{KEY}_{INDEX}' 
                                    class='field_data' 
                                    name='{FORM}_form[form][]' 
                                    type='text' 
                                    value=''> 
                                    {EMAIL_AND_PHONE_FIELD}{VERIFY_FIELD} 
                        </div>";

	$verify_field          = "<span> %s:
                            <input  class='field_data' 
                                    id='{FORM}_form_verify_{KEY}_{INDEX}' 
                                    name='{FORM}_form[verifyKey][]' 
                                    type='text' value=''>
                        </span>";
	$email_and_phone_field = "<span {HIDDEN1}> %s:
                                    <input  id='{FORM}_form_email_{KEY}_{INDEX}' 
                                            class='field_data' 
                                            name='{FORM}_form[emailkey][]' 
                                            type='text' value=''>
                                </span>
                                <span {HIDDEN2}> %s: 
                                    <input  id='{FORM}_form_phone_{KEY}_{INDEX}' 
                                            class='field_data'  
                                            name='{FORM}_form[phonekey][]' 
                                            type='text' 
                                            value=''>
                                </span>";

	$verify_field          = $show_verify_field ? $verify_field : '';
	$email_and_phone_field = $show_email_and_phone_field ? $email_and_phone_field : '';

	$row_template = MoUtility::replace_string(
		array(
			'VERIFY_FIELD'          => $verify_field,
			'EMAIL_AND_PHONE_FIELD' => $email_and_phone_field,
		),
		$row_template
	);

	$row_template = sprintf(
		$row_template,
		mowc_( 'Form ID' ),
		mowc_( "Email Field $key_type" ),
		mowc_( "Phone Field $key_type" ),
		mowc_( "Verification Field $key_type" )
	);

	$row_template = trim( preg_replace( '/\s\s+/', ' ', $row_template ) );

	$script_template = ' <script>
                                var {FORM}_counter1, {FORM}_counter2, {FORM}_counter3;
                                jQuery(document).ready(function(){  
                                    {FORM}_counter1 = ' . $counters[0] . '; {FORM}_counter2 = ' . $counters[1] . '; {FORM}_counter3 = ' . $counters[2] . ";
                                });
                            </script>
                            <script>
                                function add_{FORM}(t,n)
                                {
                                    var count = this['{FORM}_counter'+n];
                                    var hidden1='',hidden2='',both='';
                                    var html = \"" . $row_template . "\";
                                    if(n===1) hidden2 = 'hidden';
                                    if(n===2) hidden1 = 'hidden';
                                    if(n===3) both = 'both_';
                                    count++;
                                    html = html.replace('{KEY}', n).replace('{INDEX}',count).replace('{HIDDEN1}',hidden1).replace('{HIDDEN2}',hidden2);
                                    if(count!==0) {
                                        \$mo(html).insertAfter(\$mo('#row{FORM}'+n+'_'+(count-1)+''));
                                    }
                                    this['{FORM}_counter'+n]=count;
                                }
                            
                                function remove_{FORM}(n)
                                {
                                    var count =   Math.max(this['{FORM}_counter1'],this['{FORM}_counter2'],this['{FORM}_counter3']);
                                    if(count !== 0) {
                                        \$mo('#row{FORM}1_' + count).remove();
                                        \$mo('#row{FORM}2_' + count).remove();
                                        \$mo('#row{FORM}3_' + count).remove();
                                        count--;
                                        this['{FORM}_counter3']=this['{FORM}_counter1']=this['{FORM}_counter2']=count;
                                    }       
                                }
                            </script>";
	$script_template = MoUtility::replace_string( array( 'FORM' => $form_name ), $script_template );
	echo wp_kses(
		$script_template,
		array(
			'div'    => array(
				'name'   => array(),
				'id'     => array(),
				'class'  => array(),
				'title'  => array(),
				'style'  => array(),
				'hidden' => array(),
			),
			'script' => array(),
			'span'   => array(
				'class'  => array(),
				'title'  => array(),
				'style'  => array(),
				'hidden' => array(),
			),
			'input'  => array(
				'type'        => array(),
				'id'          => array(),
				'name'        => array(),
				'value'       => array(),
				'class'       => array(),
				'size '       => array(),
				'tabindex'    => array(),
				'hidden'      => array(),
				'style'       => array(),
				'placeholder' => array(),
				'disabled'    => array(),
			),
		)
	);

}

/**Function for showing addonlist
 * */
function show_woocommerce_addon_list() {

	$circle_icon = '
							<svg class="min-w-[8px] min-h-[8px]" width="8" height="8" viewBox="0 0 18 18" fill="none">
								<circle id="a89fc99c6ce659f06983e2283c1865f1" cx="9" cy="9" r="7" stroke="rgb(99 102 241)" stroke-width="4"></circle>
							</svg>
						';

	$premium_feature_list = PremiumAddonList::instance();
	$premium_addon_list   = $premium_feature_list->get_premium_add_on_list();

	foreach ( $premium_addon_list as $key => $value ) {
			echo '			<div class="mo-addon-card">
										<div class="grow">
											<div class="flex">';
			echo '                 	' . wp_kses( $value['svg'], MoUtility::mowc_allow_svg_array() ) . '
												<div class="flex-1"><span style="float:right;margin-top:15px; font-size:160%; font-weight:bold;">' . esc_html( $value['price'] ) . '</span></div>
											</div>
											<p class="my-mowc-6 font-semibold text-md">' . esc_html( $value['name'] ) . '</p>
						';
		foreach ( $value['description'] as $f_key ) {
			echo '			<li class="feature-snippet">
											<span class="mt-mowc-1">' . wp_kses( $circle_icon, MoUtility::mowc_allow_svg_array() ) . '</span>
											<p class="m-mowc-0">' . esc_html( $f_key ) . '</p>
										</li>';
		}
			echo '			</div>
									<div class="flex w-full mt-mowc-4 justify-center item-center">';
		if ( '' !== $value['guide_link'] ) {
			echo '       	<a href="' . esc_url( $value['guide_link'] ) . '" target="_blank" class="flex-1 mr-mo-1  mowc-button secondary">  Know More </a>';
		} else {
			echo '			<a class="flex-1 mowc-button secondary mr-mo-1" style="cursor:pointer;" onClick="otpSupportOnClickWC(\'' . esc_html( $value['guide_request_msg'] ) . '\');" > Know More</a>';
		}
			echo '			<a class="flex-1 mowc-button inverted ml-mo-1" style="cursor:pointer;"  onclick="otpSupportOnClickWC(\'' . esc_html( $value['support_msg'] ) . '\')"> Get Addon</a>';

			
		echo'</div>
		</div>';
		
	}
}
