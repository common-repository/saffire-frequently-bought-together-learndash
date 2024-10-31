<?php
/**
 * Create frequent bought together course metabox on course page.
 *
 * @param object $post .
 */
function ldfbt_frequent_course_metabox_callback( $post ) {
	global $wpdb;
	$current_course_id = $post->ID; // course id of current course.
	$plugin_type       = ldfbt_check_option_status(); // selected plugin to work with.

	// edd & woocommerce install status.
	$wc_installed  = ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ? 1 : 0;
	$edd_installed = ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ? 1 : 0;

	// gets product ids from edd or wooCommerce depend on plugin selected.
	if ( 'woocom' === $plugin_type && ( $wc_installed ) ) {
		$product_ids      = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_type IN ('product', 'product_variation') AND post_status = %s",
				'publish'
			)
		);
		$temp_product_ids = array();
		foreach ( $product_ids as $pid ) {
			$product = wc_get_product( $pid );

			if ( $product && ! ( $product->is_type( 'variable' ) ) ) {
				array_push( $temp_product_ids, $pid );
			}
		}
		$product_ids = $temp_product_ids;
	} elseif ( 'edd' === $plugin_type && ( $edd_installed ) ) {
		$product_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s AND post_status = %s", 'download', 'publish' ) );
	} else {
		$product_ids = array();
	}

	$product_ids = ldfbt_filter_courses_from_products( $product_ids );
	// gets selected course id from metabox.
	if ( metadata_exists( 'post', $current_course_id, 'ldfbt-frequent-course' ) ) {
		$selected_course_array = get_post_meta( $current_course_id, 'ldfbt-frequent-course', true );
	} else {
		$selected_course_array = array();
	}

	// Change any course id to product id.

	if ( get_post_type( $selected_course_array[0] ) === 'sfwd-courses' ) {

		$plugin_type           = ldfbt_check_option_status(); // selected plugin to work with.
		$wc_installed          = ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ? 1 : 0;
		$edd_installed         = ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ? 1 : 0;
		$converted_product_ids = array();
		if ( 'woocom' === $plugin_type && ( $wc_installed ) ) {
			$courses_with_product_ids = ldfbt_get_woo_course_id( $product_ids );
		}

		if ( 'edd' === $plugin_type && ( $edd_installed ) ) {
			$courses_with_product_ids = ldfbt_get_woo_course_id( $product_ids );
		}

		foreach ( $selected_course_array as $cid ) {
			if ( array_key_exists( $cid, $courses_with_product_ids ) ) {
				$value = $courses_with_product_ids[ $cid ];
				array_push( $converted_product_ids, $value );
			}
		}
		if ( array_key_exists( $current_course_id, $courses_with_product_ids ) ) {
			$value = $courses_with_product_ids[ $current_course_id ];
			array_push( $converted_product_ids, $value );
		}
		update_post_meta( $current_course_id, 'ldfbt-frequent-course', $converted_product_ids );
		$selected_course_array = $converted_product_ids;
	}
	?>

	<!-- Display selectbox in metabox -->
	<select class="ldfbt-learndash-select2" name="ldfbt-ld-frequent-course[]" multiple="multiple" style="width:100%">
		<?php
		foreach ( $product_ids as $id ) {

			if ( 'woocom' === $plugin_type && ( $wc_installed ) ) {
				$product_price = ldfbt_get_woo_sale_reg_price( $id );
			}

			if ( 'edd' === $plugin_type && ( $edd_installed ) ) {
				$product_price = edd_get_download_price( $id );
			}

			if ( ! empty( $selected_course_array ) ) {

				if ( in_array( intval( $id ), $selected_course_array ) ) {
					?>
							<option selected="selected" value="<?php echo esc_attr( $id ); ?>" price-attr="<?php echo esc_attr( $product_price ); ?>" ><?php echo esc_html( get_the_title( $id ) ); ?> (<?php echo '#' . esc_html( $id ); ?>)</option>
					<?php } else { ?>
							<option value="<?php echo esc_attr( $id ); ?>" price-attr="<?php echo esc_attr( $product_price ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?> (<?php echo '#' . esc_html( $id ); ?>) </option>
							<?php
					}
			} else {
				?>
					<option value="<?php echo esc_attr( $id ); ?>" price-attr="<?php echo esc_attr( $product_price ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?> (<?php echo '#' . esc_html( $id ); ?>) </option>
					<?php
			}
		}
		?>
	</select>
	<?php
}

/**
 * Add frequent course metabox.
 */
function ldfbt_meta_frequent_boxes_callback() {
	add_meta_box( 'ldfbt-frequent-course', __( 'Frequently Bought Together Widget', 'ldfbt-frequently-bought-products' ), 'ldfbt_frequent_course_metabox_callback', array( 'sfwd-courses' ), 'advanced', 'low' );
}
add_action( 'add_meta_boxes', 'ldfbt_meta_frequent_boxes_callback' );

/**
 * Save value in frequent-course meta field that is selected from the metabox.
 */
function ldfbt_update_metafield() {

	// Take the current page post editor type skip updating to metafield when updating with elementor.
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['action'] ) ) {

		if ( 'elementor_ajax' !== $_REQUEST['action'] ) {

			// stores all selected metabox course id in 'ldfbt-frequent-course' post meta.
			if ( isset( $_POST['ldfbt-ld-frequent-course'] ) ) {
				update_post_meta( get_the_ID(), 'ldfbt-frequent-course', map_deep( $_POST['ldfbt-ld-frequent-course'], 'intval' ) );
			} else {
				update_post_meta( get_the_ID(), 'ldfbt-frequent-course', null );
			}
		}
	}
}
add_action( 'save_post_sfwd-courses', 'ldfbt_update_metafield' );
