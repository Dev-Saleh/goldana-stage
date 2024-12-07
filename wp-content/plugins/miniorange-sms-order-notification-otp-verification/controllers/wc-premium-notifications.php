<?php
/**
 * Load view for premium SMS Notifications List
 *
 * @package miniorange-otp-verification/controllers
 */

use WCSMSOTP\Helper\MoUtility;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

	$premium_notifications = array(
		'dokannotif' => array(
			'subtab'      => 'dokanNotifSubTab',
			'filename'    => 'dokannotif',
			'discription' => 'Enable Order Status Notifications for the Vendors On Dokan Platform. <br><br><b> Dokan Notifications</b> is a premium plan feature.  Check <a class="font-semibold text-yellow-500" href="' . esc_url( $license_url ) . '">Licensing Tab</a> to learn more.</a>',
		),
		'wcfmnotif'  => array(
			'subtab'      => 'wcfmNotifSubTab',
			'filename'    => 'wcfmsmsnotification',
			'discription' => 'Enable Order Status Notifications for the Vendors On WCFM Platform. <br><br> <b>WCFM Notifications</b> is a premium plan feature.  Check <a class="font-semibold text-yellow-500" href="' . esc_url( $license_url ) . '">Licensing Tab</a> to learn more.</a>',
		),
		'formNotif'  => array(
			'subtab'      => 'formNotifSubTab',
			'filename'    => 'formsmsnotification',
			'discription' => 'Enable SMS Notifications on submission of Login, Registration and Contact Forms. <br><br> <b>Forms Notifications</b> is a premium feature. Contact us at <a style="cursor:pointer;" onClick="otpSupportOnClickWC(\'Hi, I am Interested in the Forms Notification feature. Provide me with the more information.\');"><u> otpsupport@xecurify.com</u></a> to know more',
		),
	);

	foreach ( $premium_notifications as $notif => $notif_subtab ) {

			$premium_notif_hidden = $notif_subtab['subtab'] !== $subtab ? 'hidden' : '';
			$premium_notif_id     = $notif_subtab['subtab'] . 'Container';
		if ( is_dir( MOV_WC_DIR . '/notifications/' . $notif_subtab['filename'] ) ) {
			require_once MOV_WC_DIR . 'notifications/' . $notif_subtab['filename'] . '/controllers/main-controller.php';
		} else {
			require MOV_WC_DIR . '/views/wc-premium-notifications.php';
		}
	}
