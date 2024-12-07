<?php
/**Load Abstract Class Template
 *
 * @package miniorange-order-notifications-woocommerce/objects
 */

namespace WCSMSOTP\Objects;

use WCSMSOTP\Helper\MoConstants;
use WCSMSOTP\Helper\MoMessages;
use WCSMSOTP\Helper\MoUtility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This super class is the super class which defines some common
 * functionality for all of our html templates. This should
 * be extended by every template class that we make.
 *
 * @todo Add a reset template functionality
 */
if ( ! class_exists( 'Template' ) ) {
	/**
	 * Template class
	 */
	abstract class Template extends BaseActionHandler implements MoITemplate {


		/**
		 * The key for the template which will uniquely
		 * differentiate the template from other templates
		 *
		 * @var string
		 */
		protected $key;

		/**
		 * The template editor Id unique for each template
		 *
		 * @var string
		 */
		protected $template_editor_id;

		/**
		 * The nonce for the popup template form
		 *
		 * @var string
		 */
		protected $nonce;

		/**
		 * Is preview mode on or not
		 *
		 * @var bool
		 */
		protected $preview = false;

		/**
		 * The HTML skeleton for the jquery URL that can be used in any template
		 *
		 * @var string
		 */
		protected $jquery_url;

		/**
		 * The loader img HTML skeleton
		 *
		 * @var string
		 */
		protected $img;

		/**
		 * The common HTML code for the IFRAME so that the
		 * content is centered on the page
		 *
		 * @var string
		 */
		public $pane_content;

		/**
		 * The common HTML code for showing messages
		 * in the IFRAME
		 *
		 * @var string
		 */
		public $message_div;

		/**
		 * The common HTML code for showing success messages in the IFRAME
		 *
		 * @var string
		 */
		protected $success_message_div;

		/**
		 * Common settings for all the editors of the template.
		 *
		 * @var array
		 */
		public static $template_editor = array(
			'wpautop'           => false,
			'media_buttons'     => false,
			'textarea_rows'     => 20,
			'tabindex'          => '',
			'tabfocus_elements' => ':prev,:next',
			'editor_css'        => '',
			'editor_class'      => '',
			'teeny'             => false,
			'dfw'               => false,
			'tinymce'           => false,
			'quicktags'         => true,
		);

		/**
		 * The tags necessary to be present in the popup template.
		 * Can be overriden by base class for more tags
		 *
		 * @var array
		 */
		protected $required_tags = array(
			'{{JQUERY}}',
			'{{GO_BACK_ACTION_CALL}}',
			'{{FORM_ID}}',
			'{{REQUIRED_FIELDS}}',
			'{{REQUIRED_FORMS_SCRIPTS}}',
		);


		/**
		 * ----------------------------------------------------------------
		 * constructor
		 * ----------------------------------------------------------------
		 */
		protected function __construct() {
			parent::__construct();

			$this->jquery_url = '';

			$this->img = "<div style='display:table;text-align:center;'>" .
						"<img src='{{LOADER_CSV}}'>" .
					'</div>';

			$this->pane_content = "<div style='text-align:center;width: 100%;height: 450px;display: block;" .
										"margin-top: 40%;vertical-align: middle;'>" .
										'{{CONTENT}}' .
							'</div>';

			$this->message_div = "<div style='font-style: italic;font-weight: 600;color: #23282d;" .
										'font-family:Segoe UI,Helvetica Neue,sans-serif;' .
										"color:#942828;'>" .
									'{{MESSAGE}}' .
							'</div>';

			$this->success_message_div = "<div style='font-style: italic;font-weight: 600;color: #23282d;" .
										"font-family:Segoe UI,Helvetica Neue,sans-serif;color:#138a3d;'>" .
											'{{MESSAGE}}' .
							'</div>';

			$this->img   = str_replace( '{{LOADER_CSV}}', MOV_WC_LOADER_URL, $this->img );
			$this->nonce = 'mo_popup_options';
			add_filter( 'mo_template_defaults', array( $this, 'get_defaults' ), 1, 1 );
			add_filter( 'mo_template_build', array( $this, 'build' ), 1, 5 );
			add_action( 'admin_post_mo_preview_popup', array( $this, 'show_preview' ) );
			add_action( 'admin_post_mo_popup_save', array( $this, 'save_popup' ) );
		}


		/**
		 * This function is used to preview the template based on the type passed
		 * to the filter. This function is called when the filter admin_post_mo_preview_popup
		 * filter is called. The filter can be used by other users to modify the
		 * template if they choose to do so.
		 */
		public function show_preview() {
			if ( array_key_exists( 'popuptype', $_POST ) && $_POST['popuptype'] !== $this->get_template_key() ) {
				return;
			}
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
				wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
			}
			$data     = MoUtility::mowc_sanitize_array( $_POST );
			$message  = '<i>' . mowc_( 'PopUp Message shows up here.' ) . '</i>';
			$otp_type = VerificationType::TEST;
			$template = stripslashes( $data[ $this->get_template_editor_id() ] );
			$this->validateRequiredFields( $template );
			$from_both     = false;
			$this->preview = true;
			wp_send_json(
				MoUtility::create_json(
					$this->parse( $template, $message, $otp_type, $from_both ),
					MoConstants::SUCCESS_JSON_TYPE
				)
			);
		}

		/**
		 * This function is called to save the pop up in the database that the admin
		 * has set in the settings. Called using the admin_post_mo_popup_save action.
		 * The action can be used by other users to modify the template before it is
		 * saved in the database if they choose to do so.
		 */
		public function save_popup() {
			if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $this->nonce ) ) {
				wp_die( esc_attr( MoMessages::showMessage( MoMessages::INVALID_OP ) ) );
				return;
			}
			$data = MoUtility::mowc_sanitize_array( $_POST );
			if ( ! $this->istemplate_type( $data ) ) {
				return;
			}
			$template = stripslashes( $data[ $this->get_template_editor_id() ] );
			$this->validateRequiredFields( $template );
			$email_templates                              = maybe_unserialize( get_mo_wc_option( 'custom_popups' ) );
			$email_templates[ $this->get_template_key() ] = $template;
			update_mo_wc_option( 'custom_popups', $email_templates );
			wp_send_json(
				MoUtility::create_json(
					$this->showSuccessMessage( MoMessages::showMessage( MoMessages::TEMPLATE_SAVED ) ),
					MoConstants::SUCCESS_JSON_TYPE
				)
			);
		}


		/**
		 * This function is used to build the template based on the type passed
		 * to the filter. This function is called when the filter mo_template_build
		 * filter is called. The filter can be used by other users to modify the
		 * template if they choose to do so.
		 *
		 * @param string $template      the template content to be modified.
		 * @param string $template_type  the template type.
		 * @param string $message       the message to be show in the popup.
		 * @param string $otp_type      the otp type invoked.
		 * @param string $from_both     does user have the option to choose b/w email and sms verification.
		 * @return string
		 */
		public function build( $template, $template_type, $message, $otp_type, $from_both ) {
			if ( strcasecmp( $template_type, $this->get_template_key() ) !== 0 ) {
				return $template;
			}
			$email_templates = maybe_unserialize( get_mo_wc_option( 'custom_popups' ) );
			$template        = $email_templates[ $this->get_template_key() ];
			return $this->parse( $template, $message, $otp_type, $from_both );
		}


		/**
		 * This function checks if the template passed to it has the required
		 * tags necessary for the popup to work. If not then return false or
		 * return true.
		 *
		 * @param mixed $template template.
		 */
		protected function validateRequiredFields( $template ) {
			foreach ( $this->required_tags as $tag ) {
				if ( strpos( $template, $tag ) === false ) {
					$message = str_replace(
						'{{MESSAGE}}',
						MoMessages::showMessage( MoMessages::REQUIRED_TAGS, array( 'TAG' => $tag ) ),
						$this->message_div
					);
					wp_send_json(
						MoUtility::create_json(
							str_replace( '{{CONTENT}}', $message, $this->pane_content ),
							MoConstants::ERROR_JSON_TYPE
						)
					);
				}
			}
			if ( MoUtility::check_for_script_tags( $template ) ) {
				$message = str_replace(
					'{{MESSAGE}}',
					MoMessages::showMessage( MoMessages::INVALID_SCRIPTS ),
					$this->message_div
				);
				wp_send_json(
					MoUtility::create_json(
						str_replace( '{{CONTENT}}', $message, $this->pane_content ),
						MoConstants::ERROR_JSON_TYPE
					)
				);
			}
		}



		/**
		 * This function is used to show message on the screen for the popup
		 * as an indication to the admin/user that the process was
		 * successful.
		 *
		 * @param string $message - message to be shown to the admin.
		 * @return mixed
		 */
		protected function showSuccessMessage( $message ) {
			$message = str_replace( '{{MESSAGE}}', $message, $this->success_message_div );
			return str_replace( '{{CONTENT}}', $message, $this->pane_content );
		}


		/**
		 * This function is used to normal message on the screen for the popup
		 * as an indication to the admin/user that the process was
		 * successful.
		 *
		 * @param string $message - message to be shown to the admin.
		 * @return mixed
		 */
		protected function showMessage( $message ) {
			$message = str_replace( '{{MESSAGE}}', $message, $this->message_div );
			return str_replace( '{{CONTENT}}', $message, $this->pane_content );
		}


		/**
		 * This function detects if the form setting being saved or the preview
		 * request being made is for this popup or other templates/popups.
		 * Checks of the popuptype parameter in the request.
		 *
		 * @param array $data - $_POST.
		 *
		 * @return bool
		 */
		protected function istemplate_type( $data ) {
			return array_key_exists( 'popuptype', $data ) && strcasecmp( $data['popuptype'], $this->get_template_key() ) === 0;
		}



		/*
		|-------------------------------------------------------------------------
		| Getters
		|-------------------------------------------------------------------------
		 */

		/** This function returns the current Templates Key */
		public function get_template_key() {
			return $this->key; }

			/** This function returns the current Templates Editor Id */
		public function get_template_editor_id() {
			return $this->template_editor_id; }
	}
}
