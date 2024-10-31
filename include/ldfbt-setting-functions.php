<?php
/**
 * Gets sales price if set else regural price for a product.
 *
 * @param int $pid .
 * @return float
 */
function ldfbt_get_woo_sale_reg_price( $pid ) {
	$product = wc_get_product( $pid );

	// Check if the product is on sale .
	if ( $product->is_on_sale() ) {
		$price = $product->get_sale_price();
	} else {
		$price = $product->get_regular_price();
	}

	return $price;
}

/**
 * Check woocommerce plugin installation.
 *
 * @return bool
 */
function ldfbt_is_woocom_active() {
	$chk = class_exists( 'WC_Auth' ) ? true : false;
	return $chk;
}

/**
 * Check wocom-ld plugin is installed.
 *
 * @return bool
 */
function ldfbt_is_woocom_ld_active() {
	$chk = class_exists( 'Learndash_WooCommerce' ) ? true : false;
	return $chk;
}

/**
 * Check edd plugin installation.
 *
 * @return bool
 */
function ldfbt_is_edd_active() {
	$chk = class_exists( 'Easy_Digital_Downloads' ) ? true : false;
	return $chk;
}

/**
 * Check edd-ld plugin installation.
 *
 * @return bool
 */
function ldfbt_is_edd_ld_active() {
	$chk = class_exists( 'LearnDash_EDD' ) ? true : false;
	return $chk;
}

/** Choose default option */
function ldfbt_make_default_option() {
	$option_array    = get_option( 'ldfbt_upsells' );
	$selected_option = '';
	if ( ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ) {
		if ( ! $option_array['ldfbt_plugin_type'] ) {
			$selected_option = 'woocom';
		}
	} elseif ( ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ) {
		if ( ! $option_array['ldfbt_plugin_type'] ) {
			$selected_option = 'edd';
		}
	}
	return $selected_option;
}

/**
 * Gets option status.
 *
 * @return string
 */
function ldfbt_check_option_status() {
	$option_array = get_option( 'ldfbt_upsells' );
	if ( isset( $option_array['ldfbt_plugin_type'] ) ) {
		return $option_array['ldfbt_plugin_type'];
	} else {
		return '';
	}
}

/**
 * Gets enroller count option.
 *
 * @return string
 */
function ldfbt_get_enroller_count_status() {
	$option_array = get_option( 'ldfbt_upsells' );
	if ( isset( $option_array['ldfbt_enroller_enable'] ) ) {
		return $option_array['ldfbt_enroller_enable'];
	} else {
		return 'no';
	}
}

/**
 * Gets upsells widget heading.
 *
 * @return string
 */
function ldfbt_check_widget_heading() {
	$option_array = get_option( 'ldfbt_upsells' );
	if ( isset( $option_array['ldfbt_widget_heading'] ) ) {
		return $option_array['ldfbt_widget_heading'];
	} else {
		return 'Frequently Bought Together';
	}
}

/**
 * Show notice on checking write permission to the file.
 */
function ldfbt_admin_notices_callback() {
	$plugin_status = ldfbt_check_option_status(); // gets plugin install status.

	$link = '<a href="' . admin_url( 'admin.php?page=ldfbt_upsell_settings' ) . '">' . __( 'Settings', 'ldfbt-frequently-bought-products' ) . '</a>';

	// if both plugin is not installed then display message.
	if ( ( ! ( ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ) ) && ( ! ( ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ) ) ) {
		?>
		<div class="notice notice-info is-dismissible">
			<p><strong><?php esc_html_e( 'Found EDD and WooCommerce and its integration plugin missing install any one to work with plugin ', 'ldfbt-frequently-bought-products' ); ?></strong></p>
		</div>
		<?php
	} else {
		if ( 'woocom' === $plugin_status && ! ( ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ) ) {
			?>
			<div class="notice notice-info is-dismissible"> 
				<p><strong><?php esc_html_e( "WooCommerce and / or LearnDash WooCommerce Integration plugin' are found to be deactivated or not installed. Please activate / install it to make 'Frequently Bought Together For LearnDash' plugin work", 'ldfbt-frequently-bought-products' ); ?><?php echo esc_url( $link ); ?></strong></p>
			</div>
			<?php
		} elseif ( 'edd' === $plugin_status && ! ( ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ) ) {
			?>
			<div class="notice notice-info is-dismissible"> 
				<p><strong><?php esc_html_e( "Easy Digital Downloads(EDD) and / or LearnDash EDD Integration plugin  are found to be deactivated or not installed. Please activate / install it to make 'Frequently Bought Together For LearnDash' plugin work.", 'ldfbt-frequently-bought-products' ); ?><?php echo esc_url( $link ); ?></strong></p>
			</div>
			<?php
		}
	}
}
add_action( 'admin_notices', 'ldfbt_admin_notices_callback' );

/**
 * Update rate Notice.
 */
function ldfbt_ajax_update_notice() {
	global $current_user;
	if ( isset( $_POST['nonce'] ) && ! empty( $_POST['nonce'] ) ) {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ldfbt-frequently-bought-products' ) ) {
			wp_die( esc_html__( 'Permission Denied.', 'ldfbt-frequently-bought-products' ) );
		}
		update_user_meta( $current_user->ID, 'ldfbt_rate_notices', 'rated' );
		echo esc_url( network_admin_url() );
	}
	die();
}
add_action( 'wp_ajax_ldfbt_update', 'ldfbt_ajax_update_notice' );
add_action( 'wp_ajax_nopriv_ldfbt_update', 'ldfbt_ajax_update_notice' );

/**
 * Rating notice widget.
 * Save the date to display notice after 10 days.
 */
function ldfbt_plugin_rating_notice() {
	global $current_user;

	$today_date = strtotime( 'now' ); // gets the current timestamp.

	// Add 10 day to the current timestamp.
	if ( ! get_user_meta( $current_user->ID, 'ldfbt_notices_time' ) ) {
		$after_10_day = strtotime( '+10 day', $today_date );
		update_user_meta( $current_user->ID, 'ldfbt_notices_time', $after_10_day );
	}

	// gets the option of user rating status and week status.
	$rate_status = get_user_meta( $current_user->ID, 'ldfbt_rate_notices', true );
	$next_w_date = get_user_meta( $current_user->ID, 'ldfbt_notices_time', true );

	// show if user has not rated the plugin and it has been 1 week.
	if ( 'rated' !== $rate_status && $today_date > $next_w_date ) {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><span><?php esc_html_e( "Awesome, you've been using", 'ldfbt-frequently-bought-products' ); ?></span><span><?php echo '<strong> Frequently Bought Together For LearnDash </strong>'; ?><span><?php esc_html_e( 'for more than 1 week', 'ldfbt-frequently-bought-products' ); ?></span></p>
			<p><?php esc_html_e( 'If you like our plugin Would you like to rate our plugin at WordPress.org ?', 'ldfbt-frequently-bought-products' ); ?></p>
			<span><a href="https://wordpress.org/plugins/saffire-frequently-bought-together-learndash/#reviews" target="_blank"><?php esc_html_e( "Yes, I'd like to rate it!", 'ldfbt-frequently-bought-products' ); ?></a></span>&nbsp; - &nbsp;<span><a class="ldfbt_hide_rate" href="#"><?php esc_html_e( 'I already did!', 'ldfbt-frequently-bought-products' ); ?></a></span>
			<br/><br/>
		</div>
		<?php
	}
}
add_action( 'admin_notices', 'ldfbt_plugin_rating_notice' );
