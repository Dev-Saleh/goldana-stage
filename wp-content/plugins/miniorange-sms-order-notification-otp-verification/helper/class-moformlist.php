<?php
/**Load administrator changes for MoFormList.
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
 * This class lists down all the forms supported by the plugin.
 */
if ( ! class_exists( 'MoFormList' ) ) {
	/**
	 * FormList class
	 */
	final class MoFormList {

		use Instance;

		/**
		 * Key value pair associative array. This holds all the
		 * form Object which is initialized.
		 *
		 * @var array
		 */
		private $forms;

		/**
		 * Key Value pair associative array. This holds all the form
		 * Object which is active.
		 *
		 * @var array
		 */
		private $enabled_forms;

		/** Constructor */
		private function __construct() {
			$this->forms = array(); }

		/**
		 * Add Form to the FormList
		 *
		 * @param string      $key    the form key.
		 * @param FormHandler $form   the formHandler Object of the Form.
		 */
		public function add( $key, $form ) {
			$this->forms[ $key ] = $form;
			if ( $form->is_form_enabled() ) {
				$this->enabled_forms[ $key ] = $form;
			}
		}


		/*
		|---------------------------------------------------------------------------
		| Getters
		|---------------------------------------------------------------------------
		 */

		/** Function to return list
		 *
		 * @return array
		 */
		public function get_list() {
			return $this->forms; }

		/**Function to return list
		 *
		 * @return array
		 */
		public function get_enabled_forms() {
			return $this->enabled_forms; }

	}
}
