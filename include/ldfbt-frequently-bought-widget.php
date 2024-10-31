<?php
add_action( 'learndash-course-content-list-after', 'ldfbt_learndash_frequent_course' );

/**
 * Show frequent upsell products on learndash course page.
 */
function ldfbt_learndash_frequent_course() {
	if ( ( ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ) || ( ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ) ) {
		ldfbt_frequently_bought_course( get_the_ID() );
	}
}

/**
 * Returns course id after converting from product id.
 *
 * @return int course id.
 */
function ldfbt_get_post_id() {
	global $post;
	if ( get_post_type() === 'sfwd-courses' ) { // if course.
		return $post->ID;
	} elseif ( get_post_type() === 'product' ) { // if product.
		$wc_linked_course_id = metadata_exists( 'post', $post->ID, '_related_course' ) ? get_post_meta( $post->ID, '_related_course', true ) : null;
		if ( ! empty( $wc_linked_course_id ) ) {
			$course_id = array_shift( $wc_linked_course_id );
			return $course_id;
		}
	} elseif ( get_post_type() === 'download' ) { // if edd product.
		$edd_linked_course_id = metadata_exists( 'post', $post->ID, '_edd_learndash_course' ) ? get_post_meta( $post->ID, '_edd_learndash_course', true ) : null;
		if ( ! empty( $edd_linked_course_id ) ) {
			$course_id = array_shift( $edd_linked_course_id );
			return $course_id;
		}
	}
}


/**
 * Display frequently bought together widget.
 */
function ldfbt_frequently_bought_course() {

	$course_id             = ldfbt_get_post_id(); // current course id.
	$current_user_id       = get_current_user_id();
	$fbt_selected_products = get_post_meta( $course_id, 'ldfbt-frequent-course', true );
	global $wpdb;

	// Code for replacing old course ids with with new product ids .
	if ( get_post_type( $fbt_selected_products[0] ) === 'sfwd-courses' ) {

		$plugin_type   = ldfbt_check_option_status(); // selected plugin to work with.
		$wc_installed  = ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ? 1 : 0;
		$edd_installed = ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ? 1 : 0;

		// getting all product ids .
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

		$converted_product_ids = array();
		if ( 'woocom' === $plugin_type && ( $wc_installed ) ) {
			$courses_with_product_ids = ldfbt_get_woo_course_id( $product_ids );
		}

		if ( 'edd' === $plugin_type && ( $edd_installed ) ) {
			$courses_with_product_ids = ldfbt_get_woo_course_id( $product_ids );
		}

		foreach ( $fbt_selected_products as $cid ) {
			if ( array_key_exists( $cid, $courses_with_product_ids ) ) {
				$value = $courses_with_product_ids[ $cid ];
				array_push( $converted_product_ids, $value );
			}
		}
		if ( array_key_exists( $course_id, $courses_with_product_ids ) ) {
			$value = $courses_with_product_ids[ $course_id ];
			array_push( $converted_product_ids, $value );
		}
		update_post_meta( $course_id, 'ldfbt-frequent-course', $converted_product_ids );
		$vaild_widget_items = ldfbt_get_valid_widget_items( $converted_product_ids, $current_user_id );
	} else {
		$vaild_widget_items = ldfbt_get_valid_widget_items( $fbt_selected_products, $current_user_id );
	}

	$vaild_items_json = wp_json_encode( $vaild_widget_items );

	if ( 'woocom' === ldfbt_check_option_status() ) {
		$currency = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';
	} elseif ( 'edd' === ldfbt_check_option_status() ) {
		$currency = function_exists( 'edd_currency_symbol' ) ? edd_currency_symbol() : '';
	}

	$ldfbt_total_price = 0; // Total price.
	$ldfbt_sign        = 0; // plus sign.
	$wc_installed      = ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ? 1 : 0;
	$edd_installed     = ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ? 1 : 0;

	// gets currency symbol based on option selected.
	if ( 'woocom' === ldfbt_check_option_status() ) {
		$currency = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';
	} elseif ( 'edd' === ldfbt_check_option_status() ) {
		$currency = function_exists( 'edd_currency_symbol' ) ? edd_currency_symbol() : '';
	}

	if ( count( $vaild_widget_items ) > 1 ) {
		?>
		<div id="ldfbt-upsells-widget-container">
			<h2 id="ldfbt-upsells-widget-container-heading"><?php echo esc_html( ldfbt_check_widget_heading() ); ?></h2>
			<?php
			foreach ( $vaild_widget_items as $cid ) {

				$show_enrolment = false;
				if ( 'woocom' === ldfbt_check_option_status() ) {
					$product_price = ldfbt_get_woo_sale_reg_price( $cid );
					$product       = wc_get_product( $cid );

					if ( $product && ( $product->is_type( 'course' ) ) ) {
						$wc_linked_course_id = metadata_exists( 'post', $cid, '_related_course' ) ? get_post_meta( $cid, '_related_course', true ) : null;
						if ( ! empty( $wc_linked_course_id ) ) {

							$conv_course_id = array_shift( $wc_linked_course_id );
							if ( 'publish' === get_post_status( $conv_course_id ) ) {
								$enrolled_courses = learndash_get_users_for_course( $conv_course_id, array(), true );
								$enrolled_count   = isset( $enrolled_courses->total_users ) ? $enrolled_courses->total_users : 0;
								$show_enrolment   = true;
							}
						}
					}
				} elseif ( 'edd' === ldfbt_check_option_status() ) {
					$product_price        = edd_get_download_price( $cid );
					$edd_linked_course_id = metadata_exists( 'post', $cid, '_edd_learndash_course' ) ? get_post_meta( $cid, '_edd_learndash_course', true ) : null;
					if ( ! empty( $edd_linked_course_id ) ) {

						$conv_course_id = array_shift( $edd_linked_course_id );
						if ( 'publish' === get_post_status( $conv_course_id ) ) {
								$enrolled_courses = learndash_get_users_for_course( $conv_course_id, array(), true );
								$enrolled_count   = isset( $enrolled_courses->total_users ) ? $enrolled_courses->total_users : 0;
								$show_enrolment   = true;
						}
					}
				}

				$ldfbt_sign++;
				$author_id          = get_post_field( 'post_author', $cid );
				$display_name       = get_the_author_meta( 'display_name', $author_id );
				$course_image       = wp_get_attachment_image_src( get_post_thumbnail_id( $cid ), array( 100, 100 ), false );
				$course_img_src     = ! empty( $course_image ) ? $course_image[0] : '#';
				$ldfbt_total_price += floatval( $product_price );
				$ldfbt_total_price  = round( $ldfbt_total_price, 2 );
				$currency_symbol    = apply_filters( 'ldfbt_course_currency_change', $currency, $cid );
				?>

				<!-- All fbt courses -->
				<div class="ldfbt-upsells-widget-course-lists">
					<div class="ldfbt-course-list-data">

						<!-- FBT course Image rendering-->
						<div class="ldfbt-course-image-wrapper">
							<a href="<?php echo esc_url( get_the_guid( $cid ) ); ?>">
								<img src="<?php echo esc_url( $course_img_src ); ?>" class="ldfbt-course-img"/>
							</a>
						</div>

						<!-- contain course data -->
						<div class="ldfbt-course-card">
							<div class="leftCarddata">
								<a href="<?php echo esc_url( get_the_guid( $cid ) ); ?>">
									<h3 class="ldfbt-course-title"><?php echo esc_html( get_the_title( $cid ) ); ?></h3>
								</a>

								<!-- Author name container -->
								<div class="ldfbt-course-author-container">
									<span class="ldfbt-course-author-title"><?php echo esc_html( $display_name ); ?></span>
								</div>

								<!-- below course title hook container -->
								<div class="ldfbt-below-course-title-container">
									<span class="ldfbt-below-course-title">
									<?php do_action( 'ldfbt_below_course_title', $cid ); ?>
									</span>
								</div>
							</div>

							<!-- Enroller count container -->
							<div class="ldfbt-course-enroller-count">
							<?php
							if ( 'yes' === ldfbt_get_enroller_count_status() && $show_enrolment ) {
								?>
								<span class="ldfbt-course-enrolled">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M352 128c0 70.7-57.3 128-128 128s-128-57.3-128-128S153.3 0 224 0s128 57.3 128 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM609.3 512H471.4c5.4-9.4 8.6-20.3 8.6-32v-8c0-60.7-27.1-115.2-69.8-151.8c2.4-.1 4.7-.2 7.1-.2h61.4C567.8 320 640 392.2 640 481.3c0 17-13.8 30.7-30.7 30.7zM432 256c-31 0-59-12.6-79.3-32.9C372.4 196.5 384 163.6 384 128c0-26.8-6.6-52.1-18.3-74.3C384.3 40.1 407.2 32 432 32c61.9 0 112 50.1 112 112s-50.1 112-112 112z"></path></svg>
									<span class="ldfbt-course-enrolled-count"><?php echo esc_html( intval( $enrolled_count ) ); ?></span>
								</span>
								<?php } ?>
							</div>

							<!-- Show course price container -->
							<div class="ldfbt-course-price-container">
								<span class="ldfbt-course-price">
									<?php echo esc_html( $currency_symbol ); ?><?php echo esc_html( $product_price ); ?>
								</span>
							</div>
						</div>
					</div>
					<?php
					// render plus sign skipping last div.
					if ( ( $ldfbt_sign ) !== count( $vaild_widget_items ) ) {
						?>
						<div class="ldfbt-course-plus-icon-container imageWidth-100">+</div>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>
			<div class="ldfbtPriceFtrwrap">
				<div class="ldfbtPriceFtrwrap__left">
					<p class="ldfbt-course-total-price"> <span>Total:</span>  <strong><?php echo esc_html( $currency_symbol ); ?><?php echo esc_html( floatval( $ldfbt_total_price ) ); ?></strong></p>
				</div>
				<button id="ldfbt-course-add-to-cart" data-course-id = <?php echo esc_attr( intval( $course_id ) ); ?> ><?php echo esc_html_e( 'Add all item to Cart', 'ldfbt-frequently-bought-products' ); ?></button>
			</div>
		</div>
		<input type="hidden" id="ldfbt-products-added" products-added="<?php echo esc_attr( $vaild_items_json ); ?>">
		<?php
	}
}
