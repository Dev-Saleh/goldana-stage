<?php
/**Load administrator changes for ErrorPopup
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
 * This is the Error Popup class. This class handles all the
 * functionality related to Error popup functionality of the plugin. It extends the Template
 * and implements the MoITemplate class to implement some much needed functions.
 */
if ( ! class_exists( 'ErrorPopup' ) ) {
	/**
	 * ErrorPopup class
	 */
	class ErrorPopup extends Template implements MoITemplate {

		use Instance;
		/**
		 * Constructor to declare variables of the class on initialization
		 **/
		protected function __construct() {
			$this->key                = 'ERROR';
			$this->template_editor_id = 'customEmailMsgEditor4';
			$this->required_tags      = array_diff( $this->required_tags, array( '{{FORM_ID}}', '{{REQUIRED_FIELDS}}' ) );
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
			$pop_up_templates_request = wp_remote_get( MOV_WC_URL . 'includes/html/error.min.html' );

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
			$from_both        = $from_both ? 'true' : 'false';
			$required_scripts = $this->getRequiredFormsSkeleton( $otp_type, $from_both );

			$template = str_replace( '{{JQUERY}}', $this->jquery_url, $template );
			$template = str_replace( '{{GO_BACK_ACTION_CALL}}', 'mo_validation_goback();', $template );
			$template = str_replace( '{{MO_CSS_URL}}', MOV_WC_CSS_URL, $template );
			$template = str_replace( '{{REQUIRED_FORMS_SCRIPTS}}', $required_scripts, $template );
			$template = str_replace( '{{HEADER}}', mowc_( 'Validate OTP (One Time Passcode)' ), $template );
			$template = str_replace( '{{GO_BACK}}', mowc_( '&larr; Go Back' ), $template );
			$template = str_replace( '{{MESSAGE}}', mowc_( $message ), $template );
			return $template;
		}

		/**
		 * This function is used to replace the {{REQUIRED_FORMS_SCRIPTS}} in the template
		 * with the appropriate scripts and forms. These forms and scripts are required
		 * for the popup to work.
		 *
		 * @param string $otp_type the otp type invoked.
		 * @param string $from_both does user have the option to choose b/w email and sms verification.
		 * @return mixed|string
		 */
		private function getRequiredFormsSkeleton( $otp_type, $from_both ) {
			$required_fields = '<form name="f" method="post" action="" id="validation_goBack_form">
				<input id="validation_goBack" name="option" value="validation_goBack" type="hidden"/>
			</form>{{SCRIPTS}}';
			$required_fields = str_replace( '{{SCRIPTS}}', $this->getRequiredScripts(), $required_fields );
			return $required_fields;
		}

		/**
		 * This function is used to replace the {{SCRIPTS}} in the template
		 * with the appropriate scripts. These scripts are required
		 * for the popup to work. Scripts are not added if the form is in
		 * preview mode.
		 *
		 * @return string
		 */
		private function getRequiredScripts() {
			$scripts  = '<style>.mo_customer_validation-modal{display:block!important}</style>';
			$scripts .= '<script>function mo_validation_goback(){document.getElementById("validation_goBack_form").submit()}</script>';
			return $scripts;
		}
	}


}
