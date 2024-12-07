<?php
/**Load administrator changes for DefaultPopup
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
 * This is the Default Popup class. This class handles all the
 * functionality related to Default popup functionality of the plugin. It extends the Template
 * and implements the MoITemplate class to implement some much needed functions.
 */
if ( ! class_exists( 'DefaultPopup' ) ) {
	/**
	 * DefaultPopup class
	 */
	class DefaultPopup extends Template implements MoITemplate {

		use Instance;
		/**
		 * Constructor to declare variables of the class on initialization
		 **/
		protected function __construct() {
			$this->key                = 'DEFAULT';
			$this->template_editor_id = 'customEmailMsgEditor';
			$this->required_tags      = array_merge( $this->required_tags, array( '{{OTP_FIELD_NAME}}' ) );
			parent::__construct();
		}

		/**
		 * This function initializes the default HTML of the PopUp Template
		 * to be used by the plugin. This function is called only during
		 * plugin activation or when user resets the templates. In Both
		 * cases the plugin initializes the template to the default value
		 * that the plugin ships with.
		 *
		 * @param string $templates - the template string to be parsed.
		 *
		 * @note: The html content has been minified for public release
		 * @return array
		 */
		public function get_defaults( $templates ) {
			if ( ! is_array( $templates ) ) {
				$templates = array();
			}
			$pop_up_templates_request = wp_remote_get( MOV_WC_URL . 'includes/html/default.min.html' );

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
			$from_both          = $from_both ? 'true' : 'false';
			$required_scripts   = $this->getRequiredFormsSkeleton( $otp_type, $from_both );
			$extra_post_data    = $this->preview ? '' : extra_wc_post_data();
			$extra_form_fields  = $this->getExtraFormFields( $otp_type, $from_both );
			$extra_form_fields .= '<input type="hidden" name="option" value="miniorange-validate-otp-form" />';
			$extra_form_fields .= '<input type="hidden" id="mopopup_wpnonce" name="mopopup_wpnonce" value="' . wp_create_nonce( $this->nonce ) . '"/>';

			$template = str_replace( '{{JQUERY}}', $this->jquery_url, $template );
			$template = str_replace( '{{FORM_ID}}', 'mo_validate_form', $template );
			$template = str_replace( '{{GO_BACK_ACTION_CALL}}', 'mo_validation_goback();', $template );
			$template = str_replace( '{{OTP_MESSAGE_BOX}}', 'mo_message', $template );
			$template = str_replace( '{{MO_CSS_URL}}', MOV_WC_CSS_URL, $template );
			$template = str_replace( '{{REQUIRED_FORMS_SCRIPTS}}', $required_scripts, $template );
			$template = str_replace( '{{HEADER}}', mowc_( 'Validate OTP (One Time Passcode)' ), $template );
			$template = str_replace( '{{GO_BACK}}', mowc_( '&larr; Go Back' ), $template );
			$template = str_replace( '{{MESSAGE}}', mowc_( $message ), $template );
			$template = str_replace( '{{OTP_FIELD_NAME}}', 'mo_otp_token', $template );
			$template = str_replace( '{{OTP_FIELD_TITLE}}', mowc_( 'Enter Code' ), $template );
			$template = str_replace( '{{BUTTON_TEXT}}', mowc_( 'Validate OTP' ), $template );
			$template = str_replace( '{{REQUIRED_FIELDS}}', $extra_form_fields, $template );
			$template = str_replace( '{{LOADER_IMG}}', $this->img, $template );
			$template = str_replace( '{{EXTRA_POST_DATA}}', $extra_post_data, $template );
			$template = str_replace( '{{RESEND_OTP}}', mowc_( 'Resend OTP' ), $template );

			$template .= apply_filters( 'mo_add_script', $template );
			return $template;
		}

		/**
		 * This function is used to replace the {{REQUIRED_FORMS_SCRIPTS}} in the template
		 * with the appropriate scripts and forms. These forms and scripts are required
		 * for the popup to work.
		 *
		 * @param string $otp_type - the otp type invoked.
		 * @param string $from_both - does user have the option to choose b/w email and sms verification.
		 * @return mixed|string
		 */
		private function getRequiredFormsSkeleton( $otp_type, $from_both ) {
			$required_fields = '<form name="f" method="post" action="" id="validation_goBack_form">
			<input id="validation_goBack" name="option" value="validation_goBack" type="hidden"/>
		</form>
		<form name="f" method="post" action="" id="verification_resend_otp_form">
			<input id="verification_resend_otp" name="option" value="verification_resend_otp" type="hidden"/>
			<input name="otp_type" value="' . $otp_type . '" type="hidden"/>
			<input type="hidden" id="from_both" name="from_both" value="' . $from_both . '"/> {{EXTRA_POST_DATA}}
		</form>
		<form name="f" method="post" action="" id="goBack_choice_otp_form">
			<input id="verification_resend_otp" name="option" value="verification_resend_otp_both" type="hidden"/>
			<input type="hidden" id="from_both" name="from_both" value="true">{{EXTRA_POST_DATA}}</form>
		{{SCRIPTS}}';
			$required_fields = str_replace( '{{SCRIPTS}}', $this->getRequiredScripts(), $required_fields );
			return $required_fields;
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
				$scripts .= '<script>function mo_validation_goback(){
				document.getElementById("validation_goBack_form").submit()}function mo_otp_verification_resend(){
					document.getElementById("verification_resend_otp_form").submit()}function mo_select_goback(){
						document.getElementById("goBack_choice_otp_form").submit()}jQuery(document).ready(function(){
							$mo=jQuery;$mo("#mo_validate_form").submit(function(){$mo(this).hide();$mo("#mo_message").show()})})</script>';
			} else {
				$scripts .= '<script>$mo=jQuery;$mo("#mo_validate_form").submit(function(e){e.preventDefault();});</script>';
			}
			return $scripts;
		}

		/**
		 * This function is used to add the required input fields to the main otp form.
		 *
		 * @param string $otp_type - the otp type invoked.
		 * @param string $from_both - does user have the option to choose b/w email and sms verification.
		 * @return string
		 */
		private function getExtraFormFields( $otp_type, $from_both ) {
			return ' <input type="hidden" name="otp_type" value="' . $otp_type . '">
                 <input type="hidden" id="from_both" name="from_both" value="' . $from_both . '">
                 {{EXTRA_POST_DATA}}';
		}
	}

}
