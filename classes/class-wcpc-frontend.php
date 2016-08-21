<?php
class WCPc_Frontend {

	public function __construct() {
		global $WCPc;
		// enqueue frontend style
		add_action( 'wp_enqueue_scripts', array(&$this, 'frontend_styles'));
		//enqueue frontend scripts
		add_action('wp_enqueue_scripts', array(&$this, 'frontend_scripts'));
		add_action('wp_print_scripts',array(&$this, 'frontend_scripts'));
		//print_r($WCPc->product);
	}

	/**
	 * Add frontend style
	 * @return void
	 */
	public function frontend_styles() {
		global $WCPc;
		$frontend_style_path = $WCPc->plugin_url . 'assets/frontend/css/';
		// Register the style
		wp_register_style('wcpc_frontend_css', $frontend_style_path. 'frontend.css', $WCPc->version);
		wp_enqueue_style( 'wcpc_frontend_css' );
		wp_enqueue_style('bxslider_css',  $frontend_style_path .'jquery.bxslider.css', array(), $WCPc->version);
		wp_enqueue_style('flexslider_css',  $frontend_style_path .'flexslider.css', array(), $WCPc->version);
	}

	/**
	 * Add frontend scripts
	 * @return void
	 */
	public function frontend_scripts() {
		global $WCPc;
		$frontend_script_path = $WCPc->plugin_url . 'assets/frontend/js/';
		wp_enqueue_script('jquery_min_js', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
		wp_enqueue_script('wcpc_frontend_js', $frontend_script_path. 'frontend.js', array('jquery'), $WCPc->version, true);
		wp_enqueue_script('bxslider_js', $frontend_script_path. 'jquery.bxslider.js', array('jquery'), $WCPc->version, true);
		wp_enqueue_script('flexslider_js', $frontend_script_path. 'jquery.flexslider.js', array('jquery'), $WCPc->version, true);
		wp_enqueue_script('threesixty_js', $frontend_script_path. 'jquery.threesixty.js', array('jquery'));
		wp_enqueue_script( 'product_frontend_js', $frontend_script_path. 'product_frontend.js', array( 'jquery' ) );
     	wp_localize_script( 'product_frontend_js', 'admin_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'product_360_js', $frontend_script_path. 'product_360.js', array( 'jquery' ) );
	}


	
}
?>