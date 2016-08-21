<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WCPc_Product {
	public $product_360_list;
	private $general_settings;
	public function __construct() {
		$this->general_settings = get_option( "wcpc_general_settings_name" );
		// add product slider meta box
		add_action('add_meta_boxes', array($this,'add_new_product_slider_meta_box'));
		// save product slider meta box
		add_action('save_post', array($this,'save_product_slider_metabox'));

		// apply product customize image changes on product image filter
		add_filter('woocommerce_single_product_image_html', array(&$this, 'woocommerce_show_product_slider'), 20);

		// add Product video Tabs
		add_action(	'woocommerce_product_write_panel_tabs', array( &$this, 'add_product_video_tab' ), 30);
		add_action(	'woocommerce_product_write_panels', array( &$this, 'output_product_video_tab'), 30);
		add_action(	'save_post', array( &$this, 'process_product_video_data' ) );		
		add_filter( 'woocommerce_product_tabs', array( &$this, 'product_video_tab' ) );
		// register custom wc product type 'make my pack'
		register_make_my_pack_product_type();
		add_filter( 'product_type_selector', array( &$this, 'add_make_my_pack_product_type') );


		add_action( 'admin_footer', array( &$this, 'make_my_pack_custom_js') );
		add_filter( 'woocommerce_product_data_tabs', array( &$this, 'make_my_pack_product_tabs') );
		add_action( 'woocommerce_product_data_panels', array( &$this, 'make_my_pack_product_tab_content') );
		add_action( 'woocommerce_process_product_meta_make_my_pack', array( &$this, 'save_make_my_pack_field') );
		// Override WooCommerce Template within our plugin
		add_filter( 'woocommerce_locate_template', array( &$this, 'WCPc_woocommerce_locate_template'), 10, 3 );
		add_action( 'woocommerce_make_my_pack_add_to_cart', array( &$this, 'woocommerce_make_my_pack_add_to_cart_func'), 30 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'change_pkg_price') );
		add_filter( 'woocommerce_get_price_html', array( $this, 'change_pkg_default_price_display'),10,2);
		
	}

	function change_pkg_default_price_display( $price, $product ) {
		if($product->product_type == 'make_my_pack'){
			$price .= ' base price';
			return $price;
		}else{
			return $price;
		}
	}


	function change_pkg_price($cart_object){
		global $WCPc;
		 foreach ( $cart_object->cart_contents as $key => $value ) {
		 	if($value['data']->product_type == 'make_my_pack'){
		 		$pkg_mdfy_price = get_post_meta( $value['data']->id, '_package_modified_price', true );
		 		$value['data']->price = $pkg_mdfy_price;
		 	}
	        
	    }
	}


  	function add_new_product_slider_meta_box(){
  		if($this->general_settings['is_product_360'] == true){
  			global $WCPc;
			$id = 'woocommerce_product_slider'; // it should be unique
			$heading = 'Product 360 Slider'; // meta box heading
			$callback = array($this,'product_slider_metabox_content');// the name of the callback function
			$post_type = 'product';
			$position = 'side';
			$pri = 'default'; // priority, 'default' is good for us
			add_meta_box( $id, $heading, $callback, $post_type, $position, $pri );
		}
	}


	function product_slider_metabox_content($post='') {  
 		global $WCPc,$post;
	   ?>
		<div id="product_slider_container">
			<ul class="product_sliders">
				<?php
					if ( metadata_exists( 'post', $post->ID, '_product_slider_gallery' ) ) {
						$product_slider_gallery = get_post_meta( $post->ID, '_product_slider_gallery', true );
					} else {
						// Backwards compat
						$attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
						$attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
						$product_slider_gallery = implode( ',', $attachment_ids );
					}

					$attachments = array_filter( explode( ',', $product_slider_gallery ) );

					$update_meta = false;

					if ( ! empty( $attachments ) ) {
						foreach ( $attachments as $attachment_id ) {
							$attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

							// if attachment is empty skip
							if ( empty( $attachment ) ) {
								$update_meta = true;

								continue;
							}

							echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
								' . $attachment . '
								<ul class="actions">
									<li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'woocommerce' ) . '">' . __( 'Delete', 'woocommerce' ) . '</a></li>
								</ul>
							</li>';

							// rebuild ids to be saved
							$updated_gallery_ids[] = $attachment_id;
						}

						// need to update product meta to set new gallery ids
						if ( $update_meta ) {
							update_post_meta( $post->ID, '_product_slider_gallery', implode( ',', $updated_gallery_ids ) );
						}
					}
				?>
			</ul>

			<input type="hidden" id="product_slider_gallery" name="product_slider_gallery" value="<?php echo esc_attr( $product_slider_gallery ); ?>" />

		</div>
		<p class="add_product_sliders hide-if-no-js">
			<a href="#" data-choose="<?php esc_attr_e( 'Add Images to Product Slider', 'woocommerce' ); ?>" data-update="<?php esc_attr_e( 'Add to Slider gallery', 'woocommerce' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'woocommerce' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'woocommerce' ); ?>"><?php _e( 'Add product slider images', 'woocommerce' ); ?></a>
		</p>
		<?php
	}


	function save_product_slider_metabox($post_id) {

	    $attachment_ids = isset( $_POST['product_slider_gallery'] ) ? array_filter( explode( ',', wc_clean( $_POST['product_slider_gallery'] ) ) ) : array();

		update_post_meta( $post_id, '_product_slider_gallery', implode( ',', $attachment_ids ) );
	}


	// woocommerce product image customization
	function woocommerce_show_product_slider(){
		global $WCPc,$post, $woocommerce, $product;
		
		$product_slider = get_post_meta($post->ID,'_product_image_gallery',true);
		$attachments = explode( ',', $product_slider );

		$product_360_view = get_post_meta($post->ID,'_product_slider_gallery',true);
		$attachments_360_view = explode( ',', $product_360_view );
		
		if($this->general_settings['is_product_360'] == 'true' && count($attachments_360_view)>1){
			/*if(count($attachments_360_view)>1){*/
				add_filter('woocommerce_single_product_image_thumbnail_html',array($this, 'remove_product_thumbnails_html'));
				$html_360_list = '';
				foreach ( $attachments_360_view as $attachment_id ) {
					$html_360_list .= '<img alt="" src="'.wp_get_attachment_url($attachment_id).'" />';
				}
				echo '<div id="pro360images" style="display: none">'.$html_360_list.'</div>';
				echo '<div class="product_360_wrap"><span class="icon360"></span>
						<img id="product_360" src="" alt=""/>
					</div>';
			/*}*/
		}else if($this->general_settings['is_enable_gallery_slider'] == 'true'){
			if(count($attachments)>1){
				add_filter('woocommerce_single_product_image_thumbnail_html',array($this, 'remove_product_thumbnails_html'));
				$html_slider = '<div id="slider" class="flexslider">
	          				<ul class="slides">';
	          	$html_carousel = '<div id="carousel" class="flexslider">
	          				<ul class="slides">';
				foreach ( $attachments as $attachment_id ) {
					$attachment = wp_get_attachment_image( $attachment_id, array(600,600) );
					$html_slider .= '<li>'.$attachment.'</li>';
					$html_carousel .= '<li>'.$attachment.'</li>';
				}
				$html_carousel .= '</ul></div>';
				$html_slider .= '</ul></div>';
				echo $html_slider.$html_carousel;
			}
		}else{
			
			$image_caption = get_post( get_post_thumbnail_id($post->ID) )->post_excerpt;
			$image_link    = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
			$image         = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title'	=> get_the_title( get_post_thumbnail_id($post->ID) )
			) );
			$attachment_count = count( $product->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			}
			echo sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_caption, $image );
		}
	}

	function remove_product_thumbnails_html(){
		return '';
	}


	// product 360 view scripts
	function product_360_view_script($pro_360){ ?>
	<script>
		rotate([<?php echo $pro_360; ?>]);
	</script>
	<?php 
	}


	/**
	* Add Product Video tab in single product page 
	*
	* @return void
	*/
	function add_product_video_tab() { 
		global $WCPc;
		if($this->general_settings['is_enable_product_video'] == 'true'){
		?>
		<li class="product_vid_icon product_vid_icons"><a href="#product_video"><?php _e( 'Product Video', $WCPc->text_domain ); ?></a></li>
	<?php }
	}
		
	/**
	* Output of Product video tab in single product page 
	*
	* @return void
	*/
	function output_product_video_tab() {
		global $post, $WCPc, $woocommerce;
		$vid_html = '';
		$video_tab_data = get_post_meta( $post->ID, 'product_video_tab_data', true );
		if ( empty( $video_tab_data ) ) {
			$video_tab_data = array( 'vid_title' => '', 'emb_video_link' => '' );
		}  
		$vid_html .= '<div class="options_group" > <table class="form-field form-table">' ;
		$vid_html .= '<tbody>';
		$vid_html .= '<tr valign="top"><td scope="row" class="video_title">Video Title</td>';
		$vid_html .= '<td><input type="text" id="vid_title" name="vid_title" class="vid_title" value="'.$video_tab_data['vid_title'].'" placeholder="enter video title" />';
		$vid_html .= '</td></tr>'; 
		$vid_html .= '<tr valign="top"><td scope="row" class="video_link">Video Embbed Link</td>';
		$vid_html .= '<td><textarea id="emb_video_link" name="emb_video_link" class="emb_video_link">'.$video_tab_data['emb_video_link'].'</textarea>';
		$vid_html .= '</td></tr>'; 
		
		$vid_html = apply_filters( 'WCPc_additional_fields_product_video_tab', $vid_html );
		
		$vid_html .= '</tbody>' ;
		$vid_html .= '</table>';
		$vid_html .= '</div>' ;
		echo '<div id="product_video" class="panel woocommerce_options_panel">'.$vid_html.'</div>';
	}

	/**
	* Save Product video data
	*
	* @return void
	*/
	function process_product_video_data( $post_id ) {
		$post = get_post( $post_id );
		$vid_tab_data = array();
		if( $post->post_type == 'product' ) {
			if(isset($_POST['vid_title']) && isset($_POST['emb_video_link'])) {
				$vid_tab_data['vid_title'] = stripslashes( $_POST['vid_title'] );
				$vid_tab_data['emb_video_link'] = stripslashes( $_POST['emb_video_link'] );
				update_post_meta( $post_id, 'product_video_tab_data', $vid_tab_data );
			}
		}
	}

	/**
	* Add Product video tab on single product page
	*
	* @return void
	*/
	function product_video_tab( $tabs ) {
		global $product, $WCPc, $post;
		$vid_tab_data = get_post_meta( $post->ID, 'product_video_tab_data', true );
		//var_dump($vid_tab_data);
		if(!empty($vid_tab_data['vid_title']) || !empty($vid_tab_data['emb_video_link'] )) {
			$title = __( 'Product Video', $WCPc->text_domain );
			$tabs['product_video'] = array(
						'title' => $title,
						'priority' => 50,
						'callback' => array($this, 'woocommerce_product_video_tab')
					);
		}
		return $tabs;

	}
	
	/**
	* Add Product video tab html
	*
	* @return void
	*/
	function woocommerce_product_video_tab() {
		global $woocommerce, $WCPc;
		$WCPc->template->get_template('product_video_tab.php');
	}

	function add_make_my_pack_product_type( $types ){
		if($this->general_settings['is_make_my_pack'] == 'false'){ 
			unset( $types[ 'make_my_pack' ] );
		}else{ 
			// Key should be exactly the same as in the class product_type parameter
			$types[ 'make_my_pack' ] = __( 'Make My Pack' );
			
		}
		
		return $types;
	}


	/**
	 * Show pricing fields in general for make my pack product.
	 */
	function make_my_pack_custom_js() {
		global $product;
		if ( 'product' != get_post_type() ) :
			return;
		endif;
		?><script type='text/javascript'>
			jQuery( document ).ready( function() {
				jQuery( '.options_group.pricing' ).addClass( 'show_if_make_my_pack' ).show();
			});
		</script><?php
	}

	/**
	 * Add a make my pack product tab.
	 */
	function make_my_pack_product_tabs( $tabs) {
		global $WCPc;
		$tabs['make_my_pack'] = array(
			'label'		=> __( 'Make My Pack', $WCPc->text_domain),
			'target'	=> 'make_my_pack_options',
			'class'		=> array('show_if_make_my_pack','hide_if_simple', 'hide_if_grouped', 'hide_if_external', 'hide_if_virtual','hide_if_variable' ),
		);
		return $tabs;
	}

	/**
	 * Contents of the make my pack product tab.
	 */
	function make_my_pack_product_tab_content() {
		global $post,$WCPc;
		?>
		<div id='make_my_pack_options' class='panel woocommerce_options_panel'>
			<div class='options_group'>
				<p class="form-field">
					<label for="package_product_ids"><?php _e( 'Add Products', $WCPc->text_domain ); ?></label>
					<input type="hidden" class="wc-product-search" style="width: 90%;" id="package_product_ids" name="package_product_ids" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', $WCPc->text_domain ); ?>" data-action="woocommerce_json_search_products" data-multiple="true" data-exclude="<?php echo intval( $post->ID ); ?>" data-selected="<?php
						$product_ids = array_filter( array_map( 'absint', (array) get_post_meta( $post->ID, '_package_product_ids', true ) ) );
						$json_ids    = array();

						foreach ( $product_ids as $product_id ) {
							$product = wc_get_product( $product_id );
							if ( is_object( $product ) ) {
								$json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
							}
						}

						echo esc_attr( json_encode( $json_ids ) );
					?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" /> <?php echo wc_help_tip( __( 'Add products for your package.', $WCPc->text_domain ) ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the custom fields.
	 */
	function save_make_my_pack_field( $post_id ) {
		$package_product_ids = isset( $_POST['package_product_ids'] ) ? array_filter( array_map( 'intval', explode( ',', $_POST['package_product_ids'] ) ) ) : array();
		update_post_meta( $post_id, '_package_product_ids', $package_product_ids );
	}


	function WCPc_woocommerce_locate_template( $template, $template_name, $template_path ) {
	 	
	  	global $woocommerce,$WCPc;
     	$_template = $template;
	    if ( ! $template_path ) 
	        $template_path = $woocommerce->template_url;
	 
	    $plugin_path  = $WCPc->plugin_path  . 'templates/woocommerce/';
 
    	// Look within passed path within the theme - this is priority
	    $template = locate_template(
		    array(
		      $template_path . $template_name,
		      $template_name
		    )
   		);
 
   		if( ! $template && file_exists( $plugin_path . $template_name ) )
    		$template = $plugin_path . $template_name;
 
   		if ( ! $template )
    		$template = $_template;

   		return $template;
	}

	function woocommerce_template_single_add_to_cart() {
        global $product;
        do_action( 'woocommerce_' . $product->product_type . '_add_to_cart'  );
    }

    function woocommerce_make_my_pack_add_to_cart_func() {
    	global $woocommerce,$WCPc;
        $WCPc->template->get_template( 'woocommerce/single-product/add-to-cart/make_my_pack.php' );
    }


}

// Class declarations may not be nested, thats why declear it out of the class
/**
 * Register custom make my pack product type after init
 */
function register_make_my_pack_product_type() {
	/**
	 * extend with simple product type to get same option as Simple Product have.
	 */
	class WC_Product_Make_My_Pack extends WC_Product {

		public function __construct( $product ) {

			$this->product_type = 'make_my_pack';

			parent::__construct( $product );

		}

	}
}