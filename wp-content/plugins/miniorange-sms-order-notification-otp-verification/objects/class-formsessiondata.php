<?php
/**Load Interface FormSessionData
 *
 * @package miniorange-order-notifications-woocommerce/objects
 */

namespace WCSMSOTP\Objects;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FormSessionData' ) ) {
	/**
	 * Interface class that needs to be extended by each form class.
	 * It defines some of the common actions and functions for each form
	 * class.
	 */
	class FormSessionData {
		/**
		 * Variable declaration
		 *
		 * @var string
		 */
		private $is_initialized = false;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $email_submitted;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $phone_submitted;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $email_verified;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $phone_verified;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $email_verification_status;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $phone_verification_status;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $field_or_form_id;
		/** Variable declaration
		 *
		 * @var string
		 */
		private $user_submitted;


		/** Constructor */
		public function __construct() {}

		/**MoInternal Function
		 *
		 * @return $this
		 */
		public function init() {
			$this->is_initialized = true;
			return $this;
		}

		/**MoInternal Function to initialized the process.
		 *
		 * @return mixed
		 */
		public function getis_initialized() {
			return $this->is_initialized;
		}

		/**MoInternal Function to submit the email.
		 *
		 * @return mixed
		 */
		public function get_email_submitted() {
			return $this->email_submitted;
		}


		/**MoInternal Function
		 *
		 * @param mixed $email_submitted email submittion status.
		 */
		public function set_email_submitted( $email_submitted ) {
			$this->email_submitted = $email_submitted;
		}

		/**MoInternal Function to submit the phone.
		 *
		 * @return mixed
		 */
		public function get_phone_submitted() {
			return $this->phone_submitted;
		}

		/**MoInternal Function to check phone submittion status.
		 *
		 * @param mixed $phone_submitted phone submittion status.
		 */
		public function set_phone_submitted( $phone_submitted ) {
			$this->phone_submitted = $phone_submitted;
		}

		/**MoInternal Function to check if email is verified.
		 *
		 * @return mixed
		 */
		public function get_email_verified() {
			return $this->email_verified;
		}

		/**MoInternal Function to set the email status as verified.
		 *
		 * @param mixed $email_verified email status.
		 */
		public function set_email_verified( $email_verified ) {
			$this->email_verified = $email_verified;
		}

		/**MoInternal Function to check if phone is verified.
		 *
		 * @return mixed
		 */
		public function get_phone_verified() {
			return $this->phone_verified;
		}


		/**MoInternal Function to set the phone status as verified.
		 *
		 * @param mixed $phone_verified phone staus.
		 */
		public function set_phone_verified( $phone_verified ) {
			$this->phone_verified = $phone_verified;
		}

		/**MoInternal Function to get the email verification status.
		 *
		 * @return mixed
		 */
		public function get_email_verification_status() {
			return $this->email_verification_status;
		}

		/**MoInternal Function to set the email verification status.
		 *
		 * @param mixed $email_verification_status email status.
		 */
		public function set_email_verification_status( $email_verification_status ) {
			$this->email_verification_status = $email_verification_status;
		}

		/**MoInternal Function to get the phone verification status.
		 *
		 * @return mixed
		 */
		public function get_phone_verification_status() {
			return $this->phone_verification_status;
		}

		/**MoInternal Function to set the phone verification status.
		 *
		 * @param mixed $phone_verification_status status.
		 */
		public function set_phone_verification_status( $phone_verification_status ) {
			$this->phone_verification_status = $phone_verification_status;
		}

		/**MoInternal Function to get the form id.
		 *
		 * @return mixed
		 */
		public function getfield_or_form_id() {
			return $this->field_or_form_id;
		}

		/**MoInternal Function to set the form id.
		 *
		 * @param mixed $field_or_form_id id.
		 */
		public function setfield_or_form_id( $field_or_form_id ) {
			$this->field_or_form_id = $field_or_form_id;
		}

		/**MoInternal Function to check if user data is submitted.
		 *
		 * @return mixed
		 */
		public function get_user_submitted() {
			return $this->user_submitted;
		}

		/**MoInternal Function
		 *
		 * @param mixed $user_submitted user details.
		 */
		public function set_user_submitted( $user_submitted ) {
			$this->user_submitted = $user_submitted;
		}
	}
}
