<?php
/**Load administrator changes for MoMessages
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

use WCSMSOTP\Objects\BaseMessages;
use WCSMSOTP\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This is the constant class which lists all the messages
 * to be shown in the plugin.
 */
if ( ! class_exists( 'MoMessages' ) ) {
	/**
	 * MoMessages class
	 */
	final class MoMessages extends BaseMessages {

		use Instance;

		/**Constructor
		 */
		public function __construct() {
			define(
				'MO_WC_MESSAGES',
				maybe_serialize(
					array(

						self::NEED_TO_REGISTER          => mowc_( 'You need to login with the miniOrange account in the plugin in order to send the OTP Code.' ),
						self::INVALID_SCRIPTS           => mowc_( 'You cannot add script tags in the pop up template.' ),

						self::OTP_SENT_PHONE            => mowc_(
							'A OTP (One Time Passcode) has been sent to ##phone##.
															Please enter the OTP in the field below to verify your phone.'
						),

						self::OTP_SENT_EMAIL            => mowc_(
							'A One Time Passcode has been sent to ##email##.
															Please enter the OTP below to verify your Email Address.
															If you cannot see the email in your inbox, make sure to check
															your SPAM folder.'
						),

						self::ERROR_OTP_EMAIL           => mowc_(
							'There was an error in sending the OTP.
															Please enter a valid email id or contact site Admin.'
						),

						self::ERROR_OTP_PHONE           => mowc_(
							'There was an error in sending the OTP to the given Phone.
															Number. Please Try Again or contact site Admin.'
						),

						self::ERROR_PHONE_FORMAT        => mowc_(
							'##phone## is not a valid phone number.
															Please enter a valid Phone Number. E.g:+1XXXXXXXXXX'
						),

						self::ERROR_EMAIL_FORMAT        => mowc_(
							'##email## is not a valid email address.
															Please enter a valid Email Address. E.g:abc@abc.abc'
						),

						self::CHOOSE_METHOD             => mowc_(
							'Please select one of the methods below to verify your account.
															A One time passcode will be sent to the selected method.'
						),

						self::PLEASE_VALIDATE           => mowc_( 'You need to verify yourself in order to submit this form' ),

						self::ERROR_PHONE_BLOCKED       => mowc_(
							'##phone## has been blocked by the admin.
															Please Try a different number or Contact site Admin.'
						),

						self::ERROR_EMAIL_BLOCKED       => mowc_(
							'##email## has been blocked by the admin.
															Please Try a different email or Contact site Admin.'
						),

						self::REGISTER_WITH_US          => mowc_(
							"<a href='{{url}}'>Register or Login with miniOrange&nbsp</a>
															to get the free SMS/Email Transactions and enable OTP Verification"
						),

						self::ACTIVATE_PLUGIN           => mowc_(
							"<a href='{{url}}'>Complete plugin activation process</a>
															to enable OTP Verification"
						),

						self::FORM_NOT_AVAIL_HEAD       => mowc_( 'MY FORM IS NOT IN THE LIST' ),

						self::FORM_NOT_AVAIL_BODY       => mowc_(
							"We are actively adding support for more forms. Please contact 
                                                        us using the support form on your right or email us at 
                                                        <a style='cursor:pointer;' onClick='otpSupportOnClick();'><span style=\"color:white\"><u>" . MoConstants::FEEDBACK_EMAIL . '</u>.</span></a> While contacting us please include
                                                        enough information about your registration form and how you
                                                        intend to use this plugin. We will respond promptly.'
						),

						self::SUPPORT_FORM_VALUES       => mowc_( 'Please submit your query along with email.' ),

						self::SUPPORT_FORM_SENT         => mowc_( 'Thanks for getting in touch! We shall get back to you shortly.' ),

						self::SUPPORT_FORM_ERROR        => mowc_( 'Your query could not be submitted. Please try again.' ),

						self::FEEDBACK_SENT             => mowc_( 'Thank you for your feedback.' ),

						self::FEEDBACK_ERROR            => mowc_( "Your feedback couldn't be submitted. Please try again" ),

						self::MSG_TEMPLATE_SAVED        => mowc_( 'Settings saved successfully.' ),

						self::SMS_TEMPLATE_SAVED        => mowc_( 'Your SMS configurations are saved successfully.' ),

						self::SMS_TEMPLATE_ERROR        => mowc_( 'Please configure your gateway URL correctly.' ),

						self::EXTRA_SETTINGS_SAVED      => mowc_( 'Settings saved successfully.' ),

						self::EMAIL_MISMATCH            => mowc_(
							'The email OTP was sent to and the email in contact
															submission do not match.'
						),

						self::PHONE_MISMATCH            => mowc_(
							'The phone number OTP was sent to and the phone number in
															contact submission do not match.'
						),

						self::ENTER_PHONE               => mowc_( 'You will have to provide a Phone Number before you can verify it.' ),

						self::ENTER_EMAIL               => mowc_( 'You will have to provide an Email Address before you can verify it.' ),

						self::ENTER_PHONE_CODE          => mowc_( 'Please enter the verification code sent to your phone' ),

						self::ENTER_EMAIL_CODE          => mowc_( 'Please enter the verification code sent to your email address' ),

						self::ENTER_VERIFY_CODE         => mowc_( 'Please verify yourself before submitting the form.' ),

						self::PHONE_VALIDATION_MSG      => mowc_( 'Enter your mobile number below for verification :' ),

						self::PASS_LENGTH               => mowc_( 'Choose a password with minimum length 6.' ),

						self::PASS_MISMATCH             => mowc_( 'Password and Confirm Password do not match.' ),

						self::OTP_SENT                  => mowc_(
							'A passcode has been sent to {{method}}. Please enter the otp
															below to verify your account.'
						),

						self::ERR_OTP                   => mowc_(
							'There was an error in sending OTP. Please click on Resend
															OTP link to resend the OTP.'
						),

						self::REG_SUCCESS               => mowc_( 'Your account has been retrieved successfully.' ),

						self::ACCOUNT_EXISTS            => mowc_(
							'You already have an account with miniOrange.
															Please enter a valid password.'
						),

						self::REG_COMPLETE              => mowc_( 'Registration complete!' ),

						self::INVALID_OTP               => mowc_( 'Invalid one time passcode. Please enter a valid passcode.' ),

						self::RESET_PASS                => mowc_(
							'You password has been reset successfully and sent to your
															registered email. Please check your mailbox.'
						),

						self::REQUIRED_FIELDS           => mowc_( 'Please enter all the required fields' ),

						self::REQUIRED_OTP              => mowc_( 'Please enter a value in OTP field.' ),

						self::INVALID_SMS_OTP           => mowc_( 'There was an error in sending sms. Please Check your phone number.' ),

						self::UNKNOWN_ERROR             => mowc_( 'Error processing your request. Please try again.' ),
						self::INVALID_OP                => mowc_( 'Invalid Operation. Please Try Again' ),

						self::MO_REG_ENTER_PHONE        => mowc_( 'Phone with country code eg. +1xxxxxxxxxx' ),

						self::UPGRADE_MSG               => mowc_( 'Thank you. You have upgraded to {{plan}}.' ),
						self::REMAINING_TRANSACTION_MSG => mowc_( 'Thank you. You have upgraded to {{plan}}. <br>You have <b>{{sms}}</b> SMS and <b>{{email}}</b> Email remaining.' ),

						self::FREE_PLAN_MSG             => mowc_( 'You are on our FREE plan. Check Licensing Plans to upgrade or recharge your account.' ),

						self::TRANS_LEFT_MSG            => mowc_(
							'You have <b><i>{{email}} Email Transactions</i></b> and
															<b><i>{{sms}} Phone Transactions</i></b> remaining.'
						),
						self::INSTALL_PREMIUM_PLUGIN    => mowc_(
							"You have Upgraded to the Custom Gateway Plugin. You will need to 
			                                            install the premium plugin from the 
			                                            <a href='" . MoConstants::HOSTNAME . "/moas/viewpaymenthistory'>
			                                            miniOrange dashboard</a>."
						),

						self::PHONE_NOT_FOUND           => mowc_( "Sorry, but you don't have a registered phone number." ),

						self::REGISTER_PHONE_LOGIN      => mowc_(
							'A new security system has been enabled for you. Please
															register your phone to continue.'
						),

						self::PHONE_EXISTS              => mowc_( 'Phone Number is already in use. Please use another number.' ),

						self::EMAIL_EXISTS              => mowc_( 'Email is already in use. Please use another email.' ),

						self::INVALID_USERNAME          => mowc_( 'Please enter a valid username or email.' ),

						self::DEFAULT_SMS_TEMPLATE      => mowc_(
							'Dear Customer, Your OTP is ##otp##. Use this Passcode to
															complete your transaction. Thank you.'
						),

						self::EMAIL_SUBJECT             => mowc_( 'Your Requested One Time Passcode' ),

						self::DEFAULT_EMAIL_TEMPLATE    => mowc_(
							"Dear Customer, \n\nYour One Time Passcode for completing
															your transaction is: ##otp##\nPlease use this Passcode to
															complete your transaction. Do not share this Passcode with
															anyone.\n\nThank You,\nminiOrange Team."
						),

						self::INVALID_PHONE             => mowc_( 'Please enter a valid phone number' ),

						self::ERROR_SENDING_SMS         => mowc_( 'There was an error sending SMS to the user' ),

						self::SMS_SENT_SUCCESS          => mowc_( 'SMS was sent successfully.' ),

						self::WC_BILLING_CHOOSE         => mowc_( 'Please Choose a verification method for Woocommerce Billing Form' ),
						self::REGISTRATION_ERROR        => mowc_( "There is some issue proccessing the request. Please try again or contact us at <b><i><a onClick='otpSupportOnClickWC();'> <u>otpsupport@xecurify.com</u></a></i></b> to know more. " ),
						self::FORGOT_PASSWORD_MESSAGE   => mowc_( "Please<a href='https://login.xecurify.com/moas/idp/resetpassword ' target='_blank'> Click here </a>to reset your password" ),

						self::ENTER_PHONE_VERIFY_CODE   => mowc_( 'Please verify your phone number before submitting the form.' ),
						self::ENTER_EMAIL_VERIFY_CODE   => mowc_( 'Please verify your email address before submitting the form.' ),

					)
				)
			);
		}



		/**
		 * This function is used to fetch and process the Messages to
		 * be shown to the user. It was created to mostly show dynamic
		 * messages to the user.
		 *
		 * @param string $message_keys    Message Key.
		 * @param array  $data           The  key value pair to be replaced in the message.
		 *
		 * @return string
		 */
		public static function showMessage( $message_keys, $data = array() ) {
			$display_message = '';
			$message_keys    = explode( ' ', $message_keys );
			$messages        = maybe_unserialize( MO_WC_MESSAGES );
			foreach ( $message_keys as $message_key ) {
				if ( MoUtility::is_blank( $message_key ) ) {
					return $display_message;
				}
				$format_message = mowc_( $messages[ $message_key ] );
				foreach ( $data as $key => $value ) {
					$format_message = str_replace( '{{' . $key . '}}', $value, $format_message );
				}
				$display_message .= $format_message;
			}
			return $display_message;
		}
	}
}
