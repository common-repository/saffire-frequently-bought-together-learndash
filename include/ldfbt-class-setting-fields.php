<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // exit if access directly.
}

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_FBT_Fields' ) ) ) {
	/**
	 * Class LearnDash Settings Section for Custom Labels Metabox.
	 */
	class LearnDash_Settings_FBT_Fields extends LearnDash_Settings_Section {

		/**
		 * Protected constructor for class
		 */
		protected function __construct() {

			// setting page id.
			$this->settings_page_id = 'ldfbt_upsell_settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'ldfbt_upsells';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'ldfbt_upsells';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'ldfbt_upsells_fields';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Frequently Bought Together', 'ldfbt-frequently-bought-products' );

			// Section discription.
			$this->settings_section_description = esc_html__( 'Frequently Bought Together for LearnDash Settings', 'ldfbt-frequently-bought-products' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {
			parent::load_settings_values();

			if ( false === $this->setting_option_values ) {
				$this->setting_option_values = get_option( 'learndash_custom_label_settings' );
			}

			if ( ( isset( $_GET['action'] ) ) && ( 'ld_reset_settings' === $_GET['action'] ) && ( isset( $_GET['page'] ) ) && ( $_GET['page'] === $this->settings_page_id ) ) {
				if ( ( isset( $_GET['ld_wpnonce'] ) ) && ( ! empty( $_GET['ld_wpnonce'] ) ) ) {
					if ( wp_verify_nonce( $_GET['ld_wpnonce'], get_current_user_id() . '-' . $this->setting_option_key ) ) {
						if ( ! empty( $this->setting_option_values ) ) {
							foreach ( $this->setting_option_values as $key => $val ) {
								$this->setting_option_values[ $key ] = '';
							}
							$this->save_settings_values();
						}
						$reload_url = remove_query_arg( array( 'action', 'ld_wpnonce' ) );
						learndash_safe_redirect( $reload_url );
					}
				}
			}
		}

		/**
		 * Initialize the metabox settings fields.
		 */
		public function load_settings_fields() {
			$this->setting_option_fields = array(

				// widget heading.
				'ldfbt_widget_heading'  => array(
					'name'      => 'ldfbt_widget_heading',
					'type'      => 'text',
					'default'   => 'Frequently Bought Together',
					'label'     => esc_html__( 'FBT Widget Title', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'Use this setting to give a suitable heading to your Frequently Bough Together widget', 'ldfbt-frequently-bought-products' ),
					'value'     => $this->setting_option_values['ldfbt_widget_heading'] ? $this->setting_option_values['ldfbt_widget_heading'] : 'Frequently Bought Together',
					'class'     => 'regular-text',
				),

				// plugin type.
				'ldfbt_plugin_type'     => array(
					'name'      => 'ldfbt_plugin_type',
					'label'     => esc_html__( 'Choose Frequently Bought Together (FBT) Configuration', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'Please choose the plugin with which you intend to sell courses on this site.', 'ldfbt-frequently-bought-products' ),
					'type'      => 'radio',
					'value'     => $this->setting_option_values['ldfbt_plugin_type'] ? $this->setting_option_values['ldfbt_plugin_type'] : ldfbt_make_default_option(),
					'options'   => array(
						'woocom' => array(
							'label' => esc_html__( 'WooCommerce', 'ldfbt-frequently-bought-products' ),
						),
						'edd'    => array(
							'label' => esc_html__( 'Easy Digital Downloads (EDD)', 'ldfbt-frequently-bought-products' ),
						),
					),
				),

				// wc fbt enable.
				'ldfbt_enroller_enable' => array(
					'name'      => 'ldfbt_enroller_enable',
					'type'      => 'checkbox-switch',
					'label'     => esc_html__( 'Display enroll count for the course', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'Shows a count of users (who enrolled for that course) next to each course in the FBT widget.', 'ldfbt-frequently-bought-products' ),
					'value'     => isset( $this->setting_option_values['ldfbt_enroller_enable'] ) ? $this->setting_option_values['ldfbt_enroller_enable'] : '',
					'options'   => array(
						'yes' => '',
					),
				),

				// widget position.
				'ldfbt_widget_position' => array(
					'name'      => 'ldfbt_widget_position',
					'type'      => 'select',
					'label'     => esc_html__( 'FBT Widget Display', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'This setting helps you select the way in which you want to show your Frequently Bough Together widget on the course page. The default setting is "After Course Description" . This will show the widget  just below the course description. If you want to show the widget at a different position on the course page, then you can choose "Using Shortcode" option. You can copy the shortcode and place it anywhere on the LearnDash course page.', 'ldfbt-frequently-bought-products' ),
					'value'     => $this->setting_option_values['ldfbt_widget_position'],
					'default'   => 'aftercontent',
					'options'   => array(
						'aftercontent' => esc_html__( 'After Course Description', 'ldfbt-frequently-bought-products' ),
						'shortcode'    => esc_html__( 'Using Shortcode [ldfbt_frequently_bought_product]', 'ldfbt-frequently-bought-products' ),
					),
				),

				// course image size.
				'ldfbt_widget_img_size' => array(
					'name'      => 'ldfbt_widget_img_size',
					'type'      => 'select',
					'label'     => esc_html__( 'Image size for products in FBT Widget', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'Please choose the size of the image of product to be shown in the Frequently Bough Together widget.', 'ldfbt-frequently-bought-products' ),
					'value'     => $this->setting_option_values['ldfbt_widget_img_size'],
					'default'   => 'small',
					'options'   => array(
						'small'  => esc_html__( 'Small ( 100 X 100 )', 'ldfbt-frequently-bought-products' ),
						'medium' => esc_html__( 'Medium ( 150 X 150 )', 'ldfbt-frequently-bought-products' ),
					),
				),

				'fbt_wocom_section'     => array(
					'name'  => 'fbt_wocom_section',
					'type'  => 'html',
					'label' => esc_html__( 'WooCommerce FBT Setting', 'ldfbt-frequently-bought-products' ),
				),
				// woocommerce discount label.
				'ldfbt_discount_label'  => array(
					'name'      => 'ldfbt_discount_label',
					'type'      => 'text',
					'default'   => 'Total Discount',
					'label'     => esc_html__( 'Discount Label', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'Use this setting to give a suitable discount label in woocommerce cart', 'ldfbt-frequently-bought-products' ),
					'value'     => $this->setting_option_values['ldfbt_discount_label'] ? $this->setting_option_values['ldfbt_discount_label'] : 'Total Discount',
					'class'     => 'regular-text',
					'attrs'     => 'woocom' !== ldfbt_check_option_status() ? array(
						'disabled' => 'disabled',
					) : '',
				),

				'fbt_wocom_section'     => array(
					'name'  => 'fbt_wocom_section',
					'type'  => 'html',
					'label' => esc_html__( 'WooCommerce FBT Setting', 'ldfbt-frequently-bought-products' ),
				),
				// wc fbt enable.
				'wc_fbt_enable'         => array(
					'name'      => 'wc_fbt_enable',
					'type'      => 'checkbox-switch',
					'label'     => esc_html__( 'Show FBT Widget on WooCommerce Product page', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'If you enable this setting FBT widget will also show on the WooCommerce product page.', 'ldfbt-frequently-bought-products' ),
					'value'     => isset( $this->setting_option_values['wc_fbt_enable'] ) ? $this->setting_option_values['wc_fbt_enable'] : '',
					'options'   => array(
						'yes' => '',
					),
					'attrs'     => 'woocom' !== ldfbt_check_option_status() ? array() : '',
				),

				'fbt_edd_section'       => array(
					'name'  => 'fbt_edd_section',
					'type'  => 'html',
					'label' => esc_html__( 'Easy Digital Downloads (EDD) FBT Setting', 'ldfbt-frequently-bought-products' ),
				),
				// edd op enable.
				'edd_fbt_enable'        => array(
					'name'      => 'edd_fbt_enable',
					'type'      => 'checkbox-switch',
					'label'     => esc_html__( 'Show FBT Widget on EDD product page', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( 'If you enable this setting FBT widget will also show up on the EDD dowload page.', 'ldfbt-frequently-bought-products' ),
					'value'     => isset( $this->setting_option_values['edd_fbt_enable'] ) ? $this->setting_option_values['edd_fbt_enable'] : '',
					'options'   => array(
						'yes' => '',
					),
					'attrs'     => 'edd' !== ldfbt_check_option_status() ? array() : '',
				),

				// edd ero enable.
				'edd_fbt_ero_enable'    => array(
					'name'      => 'edd_fbt_ero_enable',
					'type'      => 'checkbox-switch',
					'label'     => esc_html__( 'Show FBT Discount on Emails, Reciepts & Orders', 'ldfbt-frequently-bought-products' ),
					'help_text' => esc_html__( "By default, EDD doesn't show discount seperatly on email, recipts and orders. If you enable this setting, then you'll be able to show FBT discounts seperately on all emails, recipts and order dashbaord.", 'ldfbt-frequently-bought-products' ),
					'value'     => isset( $this->setting_option_values['edd_fbt_ero_enable'] ) ? $this->setting_option_values['edd_fbt_ero_enable'] : '',
					'options'   => array(
						'yes' => '',
					),
					'attrs'     => 'edd' !== ldfbt_check_option_status() ? array() : '',
				),
			);

			/**
			 * Filters custom labels setting fields.
			 *
			 * @param array $setting_option_fields Associative array of Setting field details like name,type,label,value.
			 */
			$this->setting_option_fields = apply_filters( 'learndash_custom_label_fields', $this->setting_option_fields );
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			parent::load_settings_fields();
		}

		/**
		 * Save settings
		 *
		 * @param array  $new_values         Array of section fields values.
		 * @param array  $old_values         Array of old values.
		 * @param string $setting_option_key Section option key should match $this->setting_option_key.
		 */
		public function section_pre_update_option( $new_values = '', $old_values = '', $setting_option_key = '' ) {
			if ( $setting_option_key === $this->setting_option_key ) {
				$new_values = parent::section_pre_update_option( $new_values, $old_values, $setting_option_key );

				// cheks the index of all the fields and update the default value.
				if ( $new_values !== $old_values ) {

					if ( isset( $new_values['ldfbt_plugin_type'] ) && ( ! isset( $old_values['ldfbt_plugin_type'] ) || $new_values['ldfbt_plugin_type'] !== $old_values['ldfbt_plugin_type'] ) ) {
						// Clear the postmeta for all courses .
						$args       = array(
							'post_type'      => 'sfwd-courses',
							'posts_per_page' => -1,
							'fields'         => 'ids',
						);
						$course_ids = get_posts( $args );

						// Loop through each course and update the postmeta key.
						foreach ( $course_ids as $course_id ) {
							update_post_meta( $course_id, 'ldfbt-frequent-course', '' );
						}
					}
					// sets the plugin type default.
					if ( ! isset( $new_values['ldfbt_plugin_type'] ) ) {
						$new_values['ldfbt_plugin_type'] = 'woocom';
					}

					// sets the default option for enroller count.
					if ( ! isset( $new_values['ldfbt_enroller_enable'] ) ) {
						$new_values['ldfbt_enroller_enable'] = '';
					}

					// set the default course limit.
					if ( ! isset( $new_values['ldfbt_widget_img_size'] ) ) {
						$new_values['ldfbt_widget_img_size'] = 'small';
					}

					// set the default upsell heading.
					if ( ! isset( $new_values['ldfbt_widget_heading'] ) ) {
						$new_values['ldfbt_widget_heading'] = 'Students also bought';
					}
				}
			}
			return $new_values;
		}
	}
}
add_action(
	'learndash_settings_sections_init',
	function () {
		LearnDash_Settings_FBT_Fields::add_section_instance();
	}
);


add_action( 'in_admin_footer', 'ldfbt_footer_banner' );

/**
 * Settings footer banner.
 */
function ldfbt_footer_banner() {

	if ( isset( $_GET['page'] ) && 'ldfbt_upsell_settings' === $_GET['page'] ) {
		echo '<div class="ldfbt-footer-upgrade">
		<div class="sft-logo">
		<a href="' . esc_url( plugins_url( '../assets/images/saffiretech_logo.png', __FILE__ ) ) . '">
		<img src="' . esc_url( plugins_url( '../assets/images/saffiretech_logo.png', __FILE__ ) ) . '">
		</a>
		</div>
		<div class="ldfbt-upgrade-col1">
		<h3>' . esc_html__( 'Unlock Advanced Features For Frequently Bought Together for LearnDash', 'ldfbt-frequently-bought-products' ) . '</h3>
		<div class="ldfbt-moneyback-badge">
		<div>
		<a href="' . esc_url( plugins_url( '../assets/images/moneyback-badge.png', __FILE__ ) ) . '">
		<img src="' . esc_url( plugins_url( '../assets/images/moneyback-badge.png', __FILE__ ) ) . '">
		</a>
		</div>
		<div class="ldfbt-cashback-text">
		<h3>' . esc_html__( '100% Risk-Free Money Back Guarantee!', 'ldfbt-frequently-bought-products' ) . '</h3>
		<p>' . esc_html__( 'We guarantee you a complete refund for new purchases or renewals if a request is made within 15 Days of purchase.', 'ldfbt-frequently-bought-products' ) . '</p>
		<input type="button" value="' . esc_attr__( 'Upgrade To Pro !', 'ldfbt-frequently-bought-products' ) . '" class="btn" onclick="window.open(\'https://www.saffiretech.com/frequently-bought-together-for-learndash/?utm_source=wp_plugin&utm_medium=footer&utm_campaign=free2pro&utm_id=c1&utm_term=upgrade_now&utm_content=ldfbt\', \'_blank\');" />
		</div>
		</div>

		</div>
		<div class="ldfbt-upgrade-col">
		<ul>
		<li><i class="fa fa-check" aria-hidden="true"></i><strong>' . esc_html__( 'Bundle Standard WooCommerce Product Types with Courses', 'ldfbt-frequently-bought-products' ) . '</strong> : ' . esc_html__( 'Now,  you can club Simple and Variation Product types along with your courses for a more comprehensive offering!', 'ldfbt-frequently-bought-products' ) . '</li>
		<li><i class="fa fa-check" aria-hidden="true"></i><strong>' . esc_html__( 'Bundle & Save', 'ldfbt-frequently-bought-products' ) . ' </strong> : ' . esc_html__( 'Unlock exclusive savings with fixed or percentage discounts on course bundles.', 'ldfbt-frequently-bought-products' ) . '</li>
		<li><i class="fa fa-check" aria-hidden="true"></i><strong>' . esc_html__( 'Flexible Widget Placement', 'ldfbt-frequently-bought-products' ) . ' </strong> :' . esc_html__( 'Effortlessly integrate the FBT widget wherever you like, post-course description or via shortcode.', 'ldfbt-frequently-bought-products' ) . '</li>
		<li><i class="fa fa-check" aria-hidden="true"></i><strong>' . esc_html__( 'Enhanced Visibility', 'ldfbt-frequently-bought-products' ) . '</strong> :' . esc_html__( 'Spotlight your FBT offers on WooCommerce product pages and EDD downloads for increased engagement.', 'ldfbt-frequently-bought-products' ) . '</li>
		<li><i class="fa fa-check" aria-hidden="true"></i><strong>' . esc_html__( 'Discounts on Display', 'ldfbt-frequently-bought-products' ) . ' </strong> : ' . esc_html__( 'Ensure your learners see their savings everywhere, from emails to receipts and orders, with WooCommerce EDD.', 'ldfbt-frequently-bought-products' ) . '</li>
		<li><i class="fa fa-check" aria-hidden="true"></i><strong>' . esc_html__( 'Customizable Image Sizing', 'ldfbt-frequently-bought-products' ) . '</strong> : ' . esc_html__( 'Tailor the FBT widget\'s visuals with adjustable image sizes to fit your site\'s style perfectly.', 'ldfbt-frequently-bought-products' ) . '</li>
		</ul>
		</div>
		</div> ';
	}
}
