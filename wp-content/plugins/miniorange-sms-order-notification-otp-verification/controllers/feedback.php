<?php
/**
 * Loads deactivation feedback form.
 *
 * @package miniorange-order-notifications-woocommerce/controller/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use WCSMSOTP\Handler\MoActionHandlerHandler;

$message = mowc_(
	'We are sad to see you go :( Have you found a bug? Did you feel something was missing? 
                Whatever it is, we\'d love to hear from you and get better.'
);

$submit_and_deactivate_message = mowc_( 'Submit & Deactivate' );
$submit_message                = mowc_( 'Submit' );

$admin_handler       = MoActionHandlerHandler::instance();
$nonce               = $admin_handler->get_nonce_value();
$deactivationreasons = $admin_handler->mo_feedback_reasons();

require MOV_WC_DIR . 'views/feedback.php';



