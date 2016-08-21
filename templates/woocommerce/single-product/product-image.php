<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;

?>
<div class="images">
	<?php 
	if($product->product_type == 'make_my_pack'){ ?>
        <form action="#" method="post" class="makepkgForm" id="makepkgForm">
    		<div id="makepackage"> 
            <?php 
                $package_product_ids = get_post_meta( $post->ID, '_package_product_ids', true );
                foreach($package_product_ids as $product_id){
                    $pack_product = new WC_Product( $product_id );
                    $price = $pack_product->get_price();
                    $title = $pack_product->get_title();
                    $image = get_the_post_thumbnail( $product_id, 'shop_thumbnail', array(
                    'title' => $title ));
                    echo '<div class="pack_pro"><input type="checkbox" name="'.$title.'" value="'.$price.'" />'.$image.'<p class="pack_pro_title">'.$title.'</p><p class="pack_pro_price">'.$pack_product->get_price_html().'</p></div>';
                }
            ?>
            </div>
            <p class="price">Total Price: <?php echo get_woocommerce_currency_symbol(get_option($product->ID, 'woocommerce_currency'));?> <input type="text" name="total" id="package_total" class="package_total" size="6" value="0.00" readonly="readonly" /></p>
        </form>
	<?php }else{ 
		if ( has_post_thumbnail() ) {
			$image_caption = get_post( get_post_thumbnail_id() )->post_excerpt;
			$image_link    = wp_get_attachment_url( get_post_thumbnail_id() );
			$image         = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title'	=> get_the_title( get_post_thumbnail_id() )
			) );

			$attachment_count = count( $product->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			}

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_caption, $image ), $post->ID );

		} else {

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
		}
		?>
		<?php do_action( 'woocommerce_product_thumbnails' ); ?>

	<?php } ?>
</div>
