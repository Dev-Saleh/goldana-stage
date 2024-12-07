<?php
/**Load administrator changes for PremiumFormList
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

use WCSMSOTP\Objects\FormHandler;
use WCSMSOTP\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This is the constant class which lists all the texts
 * that need to be supported for the Premium form List.
 */
if ( ! class_exists( 'PremiumFormList' ) ) {
	/**
	 * PremiumFormList class
	 */
	final class PremiumFormList {

		use Instance;

		/** Variable declaration
		 *
		 * @var $premium_forms
		 */
		private $premium_forms;

		/** Variable declaration
		 *
		 * @var $enabled_forms
		 */
		private $enabled_forms;

		/**
		 * Key value pair associative array. This holds all the
		 * form Object which is initialized.
		 *
		 * @var array
		 */
		private $forms;


		/**Constructor
		 **/
		private function __construct() {
			$this->premium_forms = array(
				'WCFM'              => 'WooCommerce Frontend Manager Form (WCFM)',
				'CHECKOUT_WC'       => 'Checkout WC Form',
				'WC_PASS_RESET'     => 'WooCommerce Password Reset Form',
			);
		}

		/**
		 * Function called to add the form name
		 *
		 * @param string $key - unique key for the form.
		 * @param object $form - name of the form.
		 */
		public function add( $key, $form ) {
			$this->forms[ $key ] = $form;
			if ( $form->is_form_enabled() ) {
				$this->enabled_forms[ $key ] = $form;
			}
		}

		/**
		 * Function called to get the premium form list
		 */
		public function get_premium_form_list() {
			return $this->premium_forms; }

	}


}
