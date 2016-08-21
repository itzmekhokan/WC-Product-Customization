<?php
/**
 * WCPc Main Class
 */
final class WCPc {

	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	public $file;
	public $ajax;
	public $admin;
	public $product;
	public $template;
	public $library;
	public $wcpc_wp_fields;
	public $frontend;
	public $product_360;

	public function __construct($file) {

		$this->file = $file;
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCPC_TOKEN;
		$this->text_domain = WCPC_TEXT_DOMAIN;
		$this->version = WCPC_PLUGIN_VERSION;

        add_action('init', array($this, 'init'));

	}	


	/**
	 * initilize plugin on WP init
	 */
	function init() {
		// Init Text Domain
		$this->text_domain;

		// Init library
		$this->load_class('library');
		$this->library = new WCPc_Library();

		// Init ajax
		if(defined('DOING_AJAX')) {
	      	$this->load_class('ajax');
	      	$this->ajax = new WCPc_Ajax();
	    }
		// Init admin
		if (is_admin()) {
			$this->load_class('admin');
			$this->admin = new WCPc_Admin();
		}

		// Init templates
		$this->load_class( 'template' );
		$this->template = new WCPc_Template();

		// Init product action class 
		$this->load_class('product');
		$this->product = new WCPc_Product();
		
		// Init main frontend action class
		if (!is_admin()) {
			$this->load_class( 'frontend' );
			$this->frontend = new WCPc_Frontend();
		}

		// Wp Fields
		$this->wcpc_wp_fields = $this->library->load_wp_fields();
	}


	public function load_class($class_name = '') {
		if ( '' != $class_name && '' != $this->token ) {
			require_once ( 'class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php' );
		} // End If Statement
	}// End load_class()


	/** Cache Helpers *********************************************************/
	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
		if (!defined('DONOTCACHEPAGE'))
			define("DONOTCACHEPAGE", "true");
		// WP Super Cache constant
	}
}
?>