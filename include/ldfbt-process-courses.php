<?php
/**
 * Sends product ids or download ids corresponding to a course id.
 *
 * @param array $data .
 * @return array .
 */
function ldfbt_get_woo_course_id( $data = array() ) {
	$valid_courses = array(); // all valid course id.
	$plugin_type   = ldfbt_check_option_status(); // selected plugin to work with.

	$wc_installed  = ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ? 1 : 0;
	$edd_installed = ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ? 1 : 0;

	foreach ( $data as $id ) {

		if ( 'woocom' === $plugin_type && ( $wc_installed ) ) {

			$wc_linked_course_id = metadata_exists( 'post', $id, '_related_course' ) ? get_post_meta( $id, '_related_course', true ) : null;
			if ( ! empty( $wc_linked_course_id ) ) {

				$conv_course_id = array_shift( $wc_linked_course_id );
				if ( 'publish' === get_post_status( $conv_course_id ) ) {

						$valid_courses[ $conv_course_id ] = $id;

				}
			}
		} elseif ( 'edd' === $plugin_type && ( $edd_installed ) ) {

			$edd_linked_course_id = metadata_exists( 'post', $id, '_edd_learndash_course' ) ? get_post_meta( $id, '_edd_learndash_course', true ) : null;
			if ( ! empty( $edd_linked_course_id ) ) {

				$conv_course_id = array_shift( $edd_linked_course_id );
				if ( 'publish' === get_post_status( $conv_course_id ) ) {

						$valid_courses[ $conv_course_id ] = $id;
				}
			}
		}
	}
	return $valid_courses;
}

/**
 * Gets vaild items to show in FBT widget for user
 *
 * @param array $data course id data.
 * @param int   $current_user_id course id data.
 * @return array
 */
function ldfbt_get_valid_widget_items( $data = array(), $current_user_id ) {

	$current_user_enrolled_courses = learndash_user_get_enrolled_courses( $current_user_id );
	$product_course                = array();
	$valid_items                   = array(); // all valid course id.
	$plugin_type                   = ldfbt_check_option_status(); // selected plugin to work with.
	$wc_installed                  = ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ? 1 : 0;
	$edd_installed                 = ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ? 1 : 0;

	foreach ( $data as $id ) {

		if ( 'woocom' === $plugin_type && ( $wc_installed ) ) {

			$wc_linked_course_id = metadata_exists( 'post', $id, '_related_course' ) ? get_post_meta( $id, '_related_course', true ) : null;
			if ( ! empty( $wc_linked_course_id ) ) {

				$conv_course_id = array_shift( $wc_linked_course_id );
				array_push( $product_course, $conv_course_id );
				if ( 'publish' === get_post_status( $conv_course_id ) ) {
					if ( ! in_array( intval( $conv_course_id ), $current_user_enrolled_courses, true ) ) {
						array_push( $valid_items, intval( $id ) );
					} elseif ( current_user_can( 'administrator' ) ) {
						array_push( $valid_items, intval( $id ) );

					}
				}
			} else {
				array_push( $valid_items, intval( $id ) );
			}
		} elseif ( 'edd' === $plugin_type && ( $edd_installed ) ) {

			$edd_linked_course_id = metadata_exists( 'post', $id, '_edd_learndash_course' ) ? get_post_meta( $id, '_edd_learndash_course', true ) : null;
			if ( ! empty( $edd_linked_course_id ) ) {

				$conv_course_id = array_shift( $edd_linked_course_id );
				if ( 'publish' === get_post_status( $conv_course_id ) ) {
					if ( ! in_array( $conv_course_id, $current_user_enrolled_courses, true ) ) {
						array_push( $valid_items, intval( $id ) );
					} elseif ( current_user_can( 'administrator' ) ) {
						array_push( $valid_items, intval( $id ) );

					}
				}
			} else {
				array_push( $valid_items, intval( $id ) );
			}
		}
	}
	return $valid_items;
}

/**
 * Gets courses from all products.
 *
 * @param array $product_ids .
 * @return array
 */
function ldfbt_filter_courses_from_products( $product_ids ) {
	$course_from_products = array();
	$plugin_type          = ldfbt_check_option_status(); // selected plugin to work with.
	$wc_installed         = ldfbt_is_woocom_active() && ldfbt_is_woocom_ld_active() ? 1 : 0;
	$edd_installed        = ldfbt_is_edd_active() && ldfbt_is_edd_ld_active() ? 1 : 0;

	foreach ( $product_ids as $pid ) {

		if ( 'woocom' === $plugin_type && ( $wc_installed ) ) {
			$wc_linked_course_id = metadata_exists( 'post', $pid, '_related_course' ) ? get_post_meta( $pid, '_related_course', true ) : null;
			if ( $wc_linked_course_id ) {
				array_push( $course_from_products, $pid );
			}
		} elseif ( 'edd' === $plugin_type && ( $edd_installed ) ) {
			$edd_linked_course_id = metadata_exists( 'post', $pid, '_edd_learndash_course' ) ? get_post_meta( $pid, '_edd_learndash_course', true ) : null;
			if ( $edd_linked_course_id ) {
				array_push( $course_from_products, $pid );
			}
		}
	}

	return $course_from_products;
}
/**
 * Gets the unique course ids with current course.
 *
 * @param array $course_ids .
 * @param int   $course_id .
 * @return array
 */
function ldfbt_get_fbt_widget_courses( $course_ids, $course_id ) {
	$page_course_ids = array();
	array_push( $page_course_ids, $course_id ); // add current page course id to array.

	// merge array of current course id and all selected course ids.
	$fbt_widget_course_ids = array_merge( $course_ids, $page_course_ids );

	// if array contain only one course.
	if ( count( $fbt_widget_course_ids ) < 2 ) {

		// if found only current course ten return empty array.
		if ( in_array( $course_id, $fbt_widget_course_ids, true ) ) {
			return array();
		}
	} else {
		return $fbt_widget_course_ids;
	}
}
