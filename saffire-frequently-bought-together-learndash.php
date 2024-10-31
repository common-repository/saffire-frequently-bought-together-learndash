<?php
/**
 * Plugin Name: Frequently Bought Together For LearnDash
 * Description: Frequently Bought Together for LearnDash is a plugin that allows you to display a section that shows courses most usually bought together with the course listing watched by customers.
 * Plugin URI:  https://www.saffiretech.com/frequently-bought-together-for-learndash
 * Author URI:  https://www.saffiretech.com
 * Author:      SaffireTech
 * Text Domain: ldfbt-frequently-bought-products
 * Domain Path: /languages
 * Stable Tag : 2.0.3
 * Requires at least: 5.3
 * Tested up to: 6.6.1
 * Requires PHP: 7.2
 * LD Requires at least: 3.6.0.3
 * LD tested up to: 4.15.2
 * License:    GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Version:     2.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Check the installation of pro version.
 *
 * @return bool
 */
function ldfbt_check_pro_version() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'frequently-bought-together-learndash-pro/frequently-bought-together-learndash-pro.php' ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Display notice if pro plugin found.
 */
function ldfbt_free_plugin_install() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	// if pro plugin found deactivate free plugin.
	if ( ldfbt_check_pro_version() ) {

		deactivate_plugins( plugin_basename( __FILE__ ), true ); // deactivate free plugin if pro found.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( defined( 'LDFBT_PRO_PLUGIN' ) ) {
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
			add_action( 'admin_notices', 'ldfbt_install_free_admin_notice' );
		}
	}
}
add_action( 'plugins_loaded', 'ldfbt_free_plugin_install' );

/**
 * Add message if pro version is installed.
 */
function ldfbt_install_free_admin_notice() {    ?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Free version deactivated Pro version Installed', 'ldfbt-frequently-bought-products' ); ?></p>
	</div>
	<?php
}

/**
 * Loads FBT settings files.
 */
function ldfbt_free_load_settings() {
	if ( ! ldfbt_check_pro_version() ) {
		require dirname( __FILE__ ) . '/include/ldfbt-class-setting-page.php'; // gets course ids from product id.
		require dirname( __FILE__ ) . '/include/ldfbt-class-setting-fields.php'; // gets course ids from product id.
	}
}
add_action( 'plugins_loaded', 'ldfbt_free_load_settings' );

/**
 * Loads all plugin files.
 */
function ldfbt_free_load_required_files() {
	if ( ! ldfbt_check_pro_version() ) {
		require_once dirname( __FILE__ ) . '/include/ldfbt-setting-functions.php'; // code for setting function.
		require_once dirname( __FILE__ ) . '/include/ldfbt-widget-metabox.php'; // metabox page.
		require_once dirname( __FILE__ ) . '/include/ldfbt-process-add-cart.php'; // code for add to cart.
		require_once dirname( __FILE__ ) . '/include/ldfbt-frequently-bought-widget.php'; // code for frequent product widget.
		require_once dirname( __FILE__ ) . '/include/ldfbt-process-courses.php'; // gets courses id.
	}
}

add_action( 'init', 'ldfbt_free_load_required_files' );

/**
 * Including select2.
 */
function ldfbt_free_include_selecet2() {

	if ( 'sfwd-courses' === get_post_type() ) {
		wp_enqueue_style( 'ldfbt-sweetalert-css', plugins_url( 'assets/css/sweetalert2.min.css', __FILE__ ), array(), '1.0.0' );
		wp_enqueue_script( 'ldfbt-select2-js', plugins_url( 'assets/js/select2.min.js', __FILE__ ), array( 'jquery' ), '1.1.0', false );
		wp_enqueue_script( 'ldfbt-backend-js', plugins_url( 'assets/js/ldfbt-backend.js', __FILE__ ), array( 'jquery' ), '1.1.0', false );
	}
}

add_action( 'admin_enqueue_scripts', 'ldfbt_free_include_selecet2' );

/**
 * Loads required js & css file.
 */
function ldfbt_free_load_required_css_js_files() {
	if ( ! ldfbt_check_pro_version() ) {
		wp_enqueue_script( 'jquery' );
		load_plugin_textdomain( 'ldfbt-frequently-bought-products', false, basename( dirname( __FILE__ ) ) . '/languages/' );
		wp_enqueue_style( 'ldfbt-course-css', plugins_url( 'assets/css/ldfbt-courses-style.css', __FILE__ ), array(), '1.0.0' );
		wp_enqueue_style( 'ldfbt-course-css', plugins_url( 'assets/css/font-awesome.min', __FILE__ ), array(), '4.7.0' );
		wp_enqueue_style( 'ldfbt-select2-css', plugins_url( 'assets/css/select2.min.css', __FILE__ ), array(), '1.1.0' );
		wp_enqueue_script( 'ldfbt-free-course-js', plugins_url( 'assets/js/ldfbt-frequent-courses.js', __FILE__ ), array( 'jquery' ), '1.0.0', false );
		wp_enqueue_script( 'ldfbt-sweetalert-js', plugins_url( 'assets/js/sweetalert2.all.min.js', __FILE__ ), array( 'jquery' ), '1.1.0', false );
		wp_localize_script(
			'ldfbt-free-course-js',
			'ldfbt_data',
			array(
				'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
				'nonce'                     => wp_create_nonce( 'ldfbt-frequently-bought-products' ),
				'ldfbt_plugin_wc_install'   => ldfbt_is_woocom_active() + ldfbt_is_woocom_ld_active(),
				'ldfbt_plugin_edd_install'  => ldfbt_is_edd_active() + ldfbt_is_edd_ld_active(),

				// Translation for free to pro alert message.
				'ldfbt_pro_alert_title'     => __( 'Looking for this cool feature? Go Pro!', 'ldfbt-frequently-bought-products' ),
				'ldfbt_pro_alert_sub_title' => __( 'Go with our premium version to unlock the following features:', 'ldfbt-frequently-bought-products' ),
				'ldfbt_popup_point1'        => __( 'Bundle & Save unlock exclusive savings with fixed or percentage discounts on course bundles.', 'ldfbt-frequently-bought-products' ),
				'ldfbt_popup_point2'        => __( 'Effortlessly integrate the FBT widget wherever you like, post-course description or via shortcode.', 'ldfbt-frequently-bought-products' ),
				'ldfbt_popup_point3'        => __( 'Spotlight your FBT offers on WooCommerce product pages and EDD downloads for increased engagement.', 'ldfbt-frequently-bought-products' ),
				'ldfbt_popup_point4'        => __( 'Ensure your learners see their savings everywhere, from emails to receipts and orders, with WooCommerce EDD.', 'ldfbt-frequently-bought-products' ),
				'ldfbt_popup_point5'        => __( 'Bundle Standard WooCommerce Product Types (Simple and Variation) along with your courses for a more comprehensive offering!', 'ldfbt-frequently-bought-products' ),
				'ldfbt_popup_title'         => __( 'Pro Field Alert!', 'ldfbt-frequently-bought-products' ),
				'ldfbt_upgarade_now'        => __( 'Upgrade Now!', 'ldfbt-frequently-bought-products' ),
				'ldfbt_nonce'               => wp_create_nonce( 'ldfbt-frequently-bought-products' ),
			)
		);
	}
	wp_set_script_translations( 'ldfbt-free-course-js', 'ldfbt-frequently-bought-products', plugin_dir_path( __FILE__ ) . '/languages/' );
}
add_action( 'init', 'ldfbt_free_load_required_css_js_files' );

/**
 * Settings link in plugin page.
 *
 * @param array $links links Plugin links on plugins.php.
 * @return array
 */
function ldfbt_free_action_links_callback( $links ) {
	if ( ! ldfbt_check_pro_version() ) {
		$settinglinks = array(
			'<a href="' . admin_url( 'admin.php?page=ldfbt_upsell_settings' ) . '">' . __( 'setting', 'ldfbt-frequently-bought-products' ) . '</a>',
			'<a class="ldfbt-setting-upgrade" href="https://www.saffiretech.com/frequently-bought-together-for-learndash/?utm_source=wp_plugin&utm_medium=plugins_archive&utm_campaign=free2pro&utm_id=c1&utm_term=upgrade_now&utm_content=ldfbt" target="_blank">' . __( 'UpGrade to Pro !', 'ldfbt-frequently-bought-products' ) . '</a>',
		);
		return array_merge( $settinglinks, $links );
	} else {
		return $links;
	}
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ldfbt_free_action_links_callback', 10, 1 );

// HPOS Compatibility.
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

add_action( 'admin_notices', 'ldfbt_update_admin_notice' );
/**
 * 2.0.0 Update notice .
 *
 * @return void
 */
function ldfbt_update_admin_notice() {
	?>
	<div class="notice notice-warning is-dismissible">
		<p> <strong><?php esc_html_e( 'Important Update for Frequently Bought Together (FBT) :', 'ldfbt-frequently-bought-products' ); ?> </strong> <?php esc_html_e( 'We have made a significant change in how the Frequently Bought Together (FBT) works. It now uses Product IDs / Download IDs instead of course IDs.', 'ldfbt-frequently-bought-products' ); ?> <br> <?php esc_html_e( 'While we have ensured that your existing configurations remain unaffected, we strongly recommend that you review your courses FBT metabox settings.', 'ldfbt-frequently-bought-products' ); ?><br><?php esc_html_e( 'If you find the FBT metabox empty, you might have to re-add the products to ensure everything works as expected.  If you have any queries or need assistance please reach out to us on ', 'ldfbt-frequently-bought-products' ); ?> <a href="https://wordpress.org/support/plugin/saffire-frequently-bought-together-learndash/"><?php esc_html_e( 'plugin support ', 'ldfbt-frequently-bought-products' ); ?></a></p>
	</div>
	<?php
}
