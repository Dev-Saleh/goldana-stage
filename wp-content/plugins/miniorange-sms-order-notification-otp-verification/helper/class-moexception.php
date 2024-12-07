<?php
/**Load administrator changes for MoException
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MoException' ) ) {
	/**
	 * MoException class
	 */
	class MoException extends \Exception {
		/**Global Variable
		 *
		 * @var message to show
		 */
		private $mo_code;

		/**Constructor to declare variables of the class on initialization
		 *
		 * @param string $mo_code exception code.
		 * @param string $message message to show.
		 * @param string $code code of message to show.
		 **/
		public function __construct( $mo_code, $message, $code ) {
			$this->mo_code = $mo_code;
			parent::__construct( $message, $code, null );
		}

		/** Function for Exception codes
		 *
		 * @return mixed */
		public function getmo_code() {
			return $this->mo_code; }
	}
}
