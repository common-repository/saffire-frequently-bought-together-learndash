<?php
add_action( 'wp_ajax_ldfbt_free_cart', 'ldfbt_ajax_add_free_cart_copy' );
add_action( 'wp_ajax_nopriv_ldfbt_free_cart', 'ldfbt_ajax_add_free_cart_copy' );

/**
 * Add fbt produts to cart.
 */
function ldfbt_ajax_add_free_cart_copy() {

	// Nonce verification .
	if ( isset( $_POST['ldfbtNonce'] ) ) {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ldfbtNonce'] ) ), 'ldfbt-frequently-bought-products' ) ) {
			die();
		}
	}

	// Get the products that need to be added to cart .
	$fbt_products_added = json_decode( isset( $_POST['userFBTProductsKey'] ) ? sanitize_text_field( ( wp_unslash( $_POST['userFBTProductsKey'] ) ) ) : '' );

	$cart_product_ids = array();

	// Check what is previously present in cart .
	if ( 'woocom' === ldfbt_check_option_status() ) {
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$cart_product_ids[] = intval( $cart_item['product_id'] );
		}
	} else {
		foreach ( edd_get_cart_contents() as $cart_item ) {
			$cart_product_ids[] = $cart_item['id'];
		}
	}

	// Add products to cart if they are not in cart .
	foreach ( $fbt_products_added as $pid ) {
		if ( 'woocom' === ldfbt_check_option_status() ) {
			if ( ! in_array( intval( $pid ), $cart_product_ids, true ) ) {

				WC()->cart->add_to_cart( $pid, 1, 0, null, array() );

			}
		} elseif ( 'edd' === ldfbt_check_option_status() ) {

			if ( ! in_array( intval( $pid ), $cart_product_ids, true ) ) {
				EDD()->cart->add( $pid, array() );
			}
		}
	}

	// Redirect URL.
	if ( 'woocom' === ldfbt_check_option_status() ) {

		$wc_cart_url = wc_get_cart_url();
		echo wp_json_encode( array( 'woocom' => $wc_cart_url ) );
	} elseif ( 'edd' === ldfbt_check_option_status() ) {
		$edd_cart_url = edd_get_checkout_uri();
		echo wp_json_encode( array( 'edd' => $edd_cart_url ) );
	}

	die();
}
