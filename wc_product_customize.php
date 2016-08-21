<?php
/*
Plugin Name: WC Product Customize
Plugin URI: http://dualcube.com
Description: WC Products Customization
Author: Dualcube
Version: 1.0.0
Author URI: http://dualcube.com
*/
if ( ! class_exists( 'WC_Dependencies' ) )
	require_once 'includes/class-wcpc-dependencies.php';
require_once 'includes/wcpc-core-functions.php';
require_once 'config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('WCPC_TOKEN')) exit;
if(!defined('WCPC_TEXT_DOMAIN')) exit;

// checked woocommerce plugin installed or not
if(in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins')))) {
	if(!class_exists('WCPc')) {
		require_once( 'classes/class-wcpc.php' );
		global $WCPc;
		$WCPc = new WCPc( __FILE__ );
		$GLOBALS['WCPc'] = $WCPc;
	}
}else{
	add_action( 'plugins_loaded', 'woo_required', 1 );
	function woo_required(){
		$html = '<div class="error">';
		$html .= '<p>';
		$html .= __( 'You have to Install woocommerce plugin first.!' );
		$html .= '</p>';
		$html .= '</div>';
		echo $html;
	}
}
?>