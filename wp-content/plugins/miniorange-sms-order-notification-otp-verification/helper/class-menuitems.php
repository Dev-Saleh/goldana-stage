<?php
/**Load administrator changes for MenuItems
 *
 * @package miniorange-order-notifications-woocommerce/helper
 */

namespace WCSMSOTP\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WCSMSOTP\MoWcInit;
use WCSMSOTP\Objects\PluginPageDetails;
use WCSMSOTP\Objects\TabDetails;
use WCSMSOTP\Traits\Instance;

/**
 * This class simply adds menu items for the plugin
 * in the WordPress dashboard.
 */
if ( ! class_exists( 'MenuItems' ) ) {
	/**
	 * MenuItems class
	 */
	final class MenuItems {

		use Instance;

		/**
		 * The URL for the plugin icon to be shown in the dashboard
		 *
		 * @var string
		 */
		private $callback;

		/**
		 * The call back function for the menu items
		 *
		 * @var string
		 */
		private $menu_slug;

		/**
		 * The slug for the main menu
		 *
		 * @var string
		 */
		private $menu_logo;

		/**
		 * Array of PluginPageDetails Object detailing
		 * all the page menu options.
		 *
		 * @var array $tab_details
		 */
		private $tab_details;

		/**
		 * MenuItems constructor.
		 */
		private function __construct() {
			$this->callback  = array( MoWcInit::instance(), 'mo_wc_customer_validation_options' );
			$this->menu_logo = MOV_WC_ICON;

			$tab_details       = tabDetails::instance();
			$this->tab_details = $tab_details->tab_details;
			$this->menu_slug   = $tab_details->parent_slug;
			$this->add_main_menu();
			$this->add_sub_menus();
		}
		/**
		 * Adding MainMenu.
		 */
		private function add_main_menu() {
			add_menu_page(
				'WC Notifications',
				'WC Notifications',
				'manage_options',
				$this->menu_slug,
				$this->callback,
				$this->menu_logo
			);
		}
		/**
		 * Adding MainMenu.
		 */
		private function add_sub_menus() {
			foreach ( $this->tab_details as $tab_detail ) {

				add_submenu_page(
					$this->menu_slug,
					$tab_detail->page_title,
					$tab_detail->menu_title,
					'manage_options',
					$tab_detail->menu_slug,
					$this->callback
				);
			}
		}
	}
}
