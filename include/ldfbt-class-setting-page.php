<?php
/**
 * LearnDash Settings Page Advanced.
 *
 * @since 3.6.0
 * @package LearnDash\Settings\Pages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( ! class_exists( 'LearnDash_Settings_Page_FBT' ) ) ) {
	/**
	 * Class LearnDash Settings Page Advanced.
	 */
	class LearnDash_Settings_Page_FBT extends LearnDash_Settings_Page {
		/**
		 * Public constructor for class
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=learndash_lms_settings';
			$this->menu_page_capability  = LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'ldfbt_upsell_settings';
			$this->settings_page_title   = __( 'Frequently Bought Together', 'ldfbt-frequently-bought-products' );
			$this->settings_tab_priority = 100;
			add_action( 'learndash_settings_page_init', array( $this, 'learndash_fbt_settings_page_init' ), 10, 1 );
			parent::__construct();
		}

		/**
		 * Settings page init.
		 *
		 * Called from `learndash_settings_page_init` action.
		 *
		 * @param string $settings_page_id   Settings Page ID.
		 */
		public function learndash_fbt_settings_page_init( $settings_page_id ) {
			$this->show_submit_meta      = true;
			$this->show_quick_links_meta = false;
			$this->settings_columns      = 2;
		}
	}
}
add_action(
	'learndash_settings_pages_init',
	function() {
		LearnDash_Settings_Page_FBT::add_page_instance();
	}
);
