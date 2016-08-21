<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin\' uh?' );
}

if ( ! function_exists( 'woocommerce_make_my_pack_add_to_cart' ) ) {

    /**
     * Output the make_my_pack product add to cart area.
     *
     * @subpackage  Product
     */
    function woocommerce_make_my_pack_add_to_cart() {
        wc_get_template( 'single-product/add-to-cart/make_my_pack.php' );
    }
}
?>