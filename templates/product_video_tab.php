<?php
/**
 * The template for displaying single product page video tab 
 */
 
global $WCPc, $product,$post;
	$vid_tab_data = get_post_meta( $post->ID, 'product_video_tab_data', true );
	$html = '';
	$html .= '<div class="product-video">';
	$html .= apply_filters('wcpc_before_product_video_info_tab', ''); 
	$html .= '<h2>'.$vid_tab_data['vid_title'].'</h2>';
	$html .= '<div><iframe width="100%" height="315" src="'.$vid_tab_data['emb_video_link'].'" frameborder="0" allowfullscreen></iframe></div>';
	$html .= apply_filters('wcpc_after_product_video_info_tab', ''); 
	$html .= '</div>';
	echo $html;
	
?>