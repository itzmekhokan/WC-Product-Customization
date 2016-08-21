<?php
class WCPc_Ajax {
	

	public function __construct() {
		add_action( 'wp_ajax_package_total', array( $this, 'package_total_callback') );
		add_action( 'wp_ajax_nopriv_package_total', array( $this, 'package_total_callback') );
		
	}

	function package_total_callback() {
		global $WCPc,$woocommerce;

		$pkg_total_price = $_POST['pkg_total_price'];
		$pkg_quantity = $_POST['pkg_quantity'];
		$pkg_product = $_POST['pkg_product'];
		//$this->change_pkg_price()
		$product = get_product( $pkg_product );
		
		if( $product->product_type == 'make_my_pack'  ){
			if ( $pkg_total_price !='0.00' && $pkg_total_price >= $product->get_price() ) {
				update_post_meta( $pkg_product, '_package_modified_price', $pkg_total_price);
				WC()->cart->add_to_cart($pkg_product, $pkg_quantity);

				
				$title = '"'.get_the_title( $pkg_product ).'"';
				$added_text = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', sizeof( $title ), 'woocommerce' ), $title );
				
				$message  = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'woocommerce' ), esc_html( $added_text ) );
				$data = array(
					'error'       => false,
					'message' => $message
				);
				wp_send_json( $data );
				die;
				
			}else{
				/*update_post_meta( $pkg_product, '_package_modified_price', $product->get_price());
				WC()->cart->add_to_cart($pkg_product, $pkg_quantity);
				$title = '"'.get_the_title( $pkg_product ).'"';
				$added_text = sprintf( _n( '%s has been added to your cart.', '%s have been added to your cart.', sizeof( $title ), 'woocommerce' ), $title );
				
				$message  = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'woocommerce' ), esc_html( $added_text ) );*/
				$data = array(
					'error'       => true,
					'message' => 'You have to Make your Package minimum '.$product->get_price_html()
				);
				wp_send_json( $data );
				die;
			}
		}

	}
	
}
?>