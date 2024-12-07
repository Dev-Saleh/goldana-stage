<?php
/**Load administrator changes for ExternalPopup
 *
 * @package miniorange-order-notifications-woocommerce/helper/templates
 */

namespace WCSMSOTP\Helper\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WCSMSOTP\Objects\MoITemplate;
use WCSMSOTP\Objects\Template;
use WCSMSOTP\Traits\Instance;

/**
 * This is the External Popup class. This class handles all the
 * functionality related to External popup functionality of the plugin. It extends the Template
 * and implements the MoITemplate class to implement some much needed functions.
 */
if ( ! class_exists( 'ExternalPopup' ) ) {
	/**
	 * ExternalPopup class
	 */
	class ExternalPopup extends Template implements MoITemplate {

		use Instance;
		/**
		 * Constructor to declare variables of the class on initialization
		 **/
		protected function __construct() {
			$this->key                = 'EXTERNAL';
			$this->template_editor_id = 'customEmailMsgEditor3';
			$this->required_tags      = array_merge(
				$this->required_tags,
				array(
					'{{PHONE_FIELD_NAME}}',
					'{{SEND_OTP_BTN_ID}}',
					'{{VERIFICATION_FIELD_NAME}}',
					'{{VALIDATE_BTN_ID}}',
					'{{SEND_OTP_BTN_ID}}',
					'{{VERIFY_CODE_BOX}}',
				)
			);
			parent::__construct();
		}

		/**
		 * This function initializes the default HTML of the PopUp Template
		 * to be used by the plugin. This function is called only during
		 * plugin activation or when user resets the templates. In Both
		 * cases the plugin initializes the template to the default value
		 * that the plugin ships with.
		 *
		 * @param array $templates - the template string to be parsed.
		 *
		 * @note: The html content has been minified Check helper/templates/templates.html
		 * @return array
		 */
		public function get_defaults( $templates ) {
			if ( ! is_array( $templates ) ) {
				$templates = array();
			}
			$pop_up_templates_request = wp_remote_get( MOV_WC_URL . 'includes/html/externalphone.min.html' );

			if ( is_wp_error( $pop_up_templates_request ) ) {
				return $templates;
			}
			$templates[ $this->get_template_key() ] = wp_remote_retrieve_body( $pop_up_templates_request );
			return $templates;
		}
		/**
		 * This function is used to parse the template and replace the
		 * tags with the appropriate content. Some of the contents are
		 * not shown if the admin/user is just previewing the pop-up.
		 *
		 * @param string $template the HTML Template.
		 * @param string $message the message to be show in the popup.
		 * @param string $otp_type the otp type invoked.
		 * @param string $from_both does user have the option to choose b/w email and sms verification.
		 * @return mixed|string
		 */
		public function parse( $template, $message, $otp_type, $from_both ) {
			$required_scripts  = $this->getRequiredScripts();
			$extra_post_data   = $this->preview ? '' : extra_wc_post_data();
			$extra_form_fields = '<input type="hidden" name="mo_external_popup_option" value="mo_ajax_form_validate" />';
			$extra_form_fields = '<input type="hidden" id="mopopup_wpnonce" name="mopopup_wpnonce" value="' . wp_create_nonce( $this->nonce ) . '"/>';

			$template  = str_replace( '{{JQUERY}}', $this->jquery_url, $template );
			$template  = str_replace( '{{FORM_ID}}', 'mo_validate_form', $template );
			$template  = str_replace( '{{GO_BACK_ACTION_CALL}}', 'mo_validation_goback();', $template );
			$template  = str_replace( '{{MO_CSS_URL}}', MOV_WC_CSS_URL, $template );
			$template  = str_replace( '{{OTP_MESSAGE_BOX}}', 'mo_message', $template );
			$template  = str_replace( '{{REQUIRED_FORMS_SCRIPTS}}', $required_scripts, $template );
			$template  = str_replace( '{{HEADER}}', mowc_( 'Validate OTP (One Time Passcode)' ), $template );
			$template  = str_replace( '{{GO_BACK}}', mowc_( '&larr; Go Back' ), $template );
			$template  = str_replace( '{{MESSAGE}}', mowc_( $message ), $template );
			$template  = str_replace( '{{REQUIRED_FIELDS}}', $extra_form_fields, $template );
			$template  = str_replace( '{{PHONE_FIELD_NAME}}', 'mo_phone_number', $template );
			$template  = str_replace( '{{OTP_FIELD_TITLE}}', mowc_( 'Enter Code' ), $template );
			$template  = str_replace( '{{VERIFY_CODE_BOX}}', 'mowc_validate_otp', $template );
			$template  = str_replace( '{{VERIFICATION_FIELD_NAME}}', 'mo_otp_token', $template );
			$template  = str_replace( '{{VALIDATE_BTN_ID}}', 'validate_otp', $template );
			$template  = str_replace( '{{VALIDATE_BUTTON_TEXT}}', mowc_( 'Validate' ), $template );
			$template  = str_replace( '{{SEND_OTP_TEXT}}', mowc_( 'Send OTP' ), $template );
			$template  = str_replace( '{{SEND_OTP_BTN_ID}}', 'send_otp', $template );
			$template  = str_replace( '{{EXTRA_POST_DATA}}', $extra_post_data, $template );
			$template .= $this->getExtraFormFields();

			return $template;
		}

		/**
		 * Returns necessary form elements for the template.
		 * Includes mostly hidden forms/fields.
		 *
		 * @return string
		 */
		private function getExtraFormFields() {
			$ffields = '<form name="f" method="post" action="" id="validation_goBack_form">
                            <input id="validation_goBack" name="option" value="validation_goBack" type="hidden"/>
                        </form>';
			return $ffields;
		}

		/**
		 * This function is used to replace the {{SCRIPTS}} in the template
		 * with the appropriate scripts. These scripts are required
		 * for the popup to work. Scripts are not added if the form is in
		 * preview mode.
		 */
		private function getRequiredScripts() {
			$scripts = '<style>.mo_customer_validation-modal{display:block!important}</style>';
			if ( ! $this->preview ) {
				$scripts .=
					'<script>function mo_validation_goback(){
                   document.getElementById("validation_goBack_form").submit()};' .
						'jQuery(document).ready(function(){' .
							'$mo=jQuery,' .
							'$mo("#send_otp").click(function(o){' .
								'var e=$mo("input[name=mo_phone_number]").val();' .
								'$mo("#mo_message").empty(),' .
								'$mo("#mo_message").append("' . $this->img . '"),' .
								'$mo("#mo_message").show(),' .
								'$mo.ajax({' .
									'url:"' . site_url() . '/?option=miniorange-ajax-otp-generate",' .
									'type:"POST",' .
									'data:{user_phone:e},' .
									'crossDomain:!0,' .
									'dataType:"json",
                                    success:function(o){' .
										'"success"==o.result?(' .
											'$mo("#mo_message").empty(),' .
											'$mo("#mo_message").append(o.message),' .
											'$mo("#mo_message").css("background-color","#8eed8e"),' .
											'$mo("#validate_otp").show(),' .
											'$mo("#send_otp").val("' . mowc_( 'Resend OTP' ) . '"),' .
											'$mo("#mowc_validate_otp").show(),' .
											'$mo("input[name=mowc_validate_otp]").focus()' .
										'):(' .
											'$mo("#mo_message").empty(),' .
											'$mo("#mo_message").append(o.message),' .
											'$mo("#mo_message").css("background-color","#eda58e"),' .
											'$mo("input[name=mo_phone_number]").focus()' .
										')' .
									'},' .
									'error:function(o,e,m){}' .
								'})' .
							'}),' .
							'$mo("#validate_otp").click(function(o){' .
								'var e=$mo("input[name=mo_otp_token]").val(),' .
								'm=$mo("input[name=mo_phone_number]").val();' .
								'$mo("#mo_message").empty(),' .
								'$mo("#mo_message").append("' . $this->img . '"),' .
								'$mo("#mo_message").show(),' .
								'$mo.ajax({' .
									'url:"' . site_url() . '/?option=miniorange-ajax-otp-validate",' .
									'type:"POST",' .
									'data:{mo_otp_token:e,user_phone:m},' .
									'crossDomain:!0,' .
									'dataType:"json",' .
									'success:function(o){' .
										'"success"==o.result?(' .
											'$mo("#mo_message").empty(),' .
											'$mo("#mo_validate_form").submit()' .
										'):(' .
											'$mo("#mo_message").empty(),' .
											'$mo("#mo_message").append(o.message),' .
											'$mo("#mo_message").css("background-color","#eda58e"),' .
											'$mo("input[name=validate_otp]").focus()' .
										')' .
									'},' .
									'error:function(o,e,m){}' .
								'})' .
							'})' .
						'});' .
					'</script>';
			} else {
				$scripts .= '<script>' .
								'$mo=jQuery,' .
								'$mo("#mo_validate_form").submit(function(e){' .
									'e.preventDefault();' .
								'});' .
							'</script>';
			}
			return $scripts;
		}
	}

}
