<?php
class WCPc_Admin {

	public $settings;
	public function __construct() {
		// Admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
		add_action('wcpc_admin_footer', array(&$this, 'dualcube_admin_footer_for_wcpc'));

		$this->load_class('settings');
		$this->settings = new WCPc_Settings();

	}

	function load_class($class_name = '') {
	  global $WCPc;
		if ('' != $class_name) {
			require_once ($WCPc->plugin_path . '/admin/class-' . esc_attr($WCPc->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()
	
	function dualcube_admin_footer_for_wcpc() {
    global $WCPc;
    ?>
    <div style="clear: both"></div>
    <div id="dc_admin_footer">
      <?php _e('Powered by', $WCPc->text_domain); ?> <a href="http://dualcube.com" target="_blank"><img src="<?php echo $WCPc->plugin_url.'/assets/images/dualcube.png'; ?>"></a><?php _e('Dualcube', $WCPc->text_domain); ?> &copy; <?php echo date('Y');?>
    </div>
    <?php
	}

	
	public function enqueue_admin_script() {
		global $WCPc, $woocommerce;
		$screen = get_current_screen();
		wp_enqueue_style('pro_slider_meta_css',  $WCPc->plugin_url.'assets/admin/css/pro_slider_meta.css', array(), $WCPc->version);
		// Register the script
		wp_register_script('wcpc_admin_js', $WCPc->plugin_url.'assets/admin/js/admin.js', array('jquery'), $WCPc->version, false);

		if (in_array( $screen->id, array( 'product', 'edit-product' ))) :
			wp_enqueue_script('wcpc_product_js', $WCPc->plugin_url.'assets/admin/js/product.js', array('jquery'), $WCPc->version, true);
		endif;
		
		
		$WCPc->library->load_qtip_lib();
		$WCPc->library->load_upload_lib();
		$WCPc->library->load_colorpicker_lib();
		$WCPc->library->load_datepicker_lib();
		wp_enqueue_style('wcpc_admin_css',  $WCPc->plugin_url.'assets/admin/css/admin.css', array(), $WCPc->version);
		wp_enqueue_script('wcpc_settings_js', $WCPc->plugin_url.'assets/admin/js/wcpc_settings.js', array('jquery'), $WCPc->version, true);
		wp_enqueue_script( 'thickbox' );
 
		wp_enqueue_style( 'thickbox' );

	}
}
?>