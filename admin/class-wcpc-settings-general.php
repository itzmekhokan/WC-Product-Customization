<?php
class WCPc_Settings_General {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  private $tab;
  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "wcpc_{$this->tab}_settings_name" );
    $this->settings_page_init();
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WCPc;
    
    $settings_tab_options = array("tab" => "{$this->tab}", "ref" => $this,
      "sections" => array(
          // Section one
          "default_settings_section" => array(
              "title" =>  __('', $WCPc->text_domain), 
              "fields" => array( 
                  /* Checkbox */
                  "is_make_my_pack" => array(
                                      'title' => __('Add Make My Pack Support', $WCPc->text_domain), 
                                      'type' => 'select', 
                                      'id' => 'is_make_my_pack', 
                                      'label_for' => 'is_make_my_pack', 
                                      'name' => 'is_make_my_pack', 
                                      'options' => array('true' => 'True', 'false' => 'False' ), 
                                      'hints' => __('Make custom product pack supports', $WCPc->text_domain),
                                      'desc' => __('Make custom product pack supports', $WCPc->text_domain)
                                      ),
                  "is_product_360" => array(
                                      'title' => __('Add Product 360 View Support', $WCPc->text_domain), 
                                      'type' => 'select', 
                                      'id' => 'is_product_360', 
                                      'label_for' => 'is_product_360', 
                                      'name' => 'is_product_360', 
                                      'options' => array('true' => 'True', 'false' => 'False' ), 
                                      'hints' => __('add metabox support in product page for woocommerce product 360 view', $WCPc->text_domain),
                                      'desc' => __('add metabox support in product page for woocommerce product 360 view', $WCPc->text_domain)
                                      ),
                  "is_enable_gallery_slider" => array(
                                      'title' => __('Enable Product Gallery Slider', $WCPc->text_domain), 
                                      'type' => 'select', 
                                      'id' => 'is_enable_gallery_slider', 
                                      'label_for' => 'is_enable_gallery_slider', 
                                      'name' => 'is_enable_gallery_slider', 
                                      'options' => array('true' => 'True', 'false' => 'False' ), 
                                      'hints' => __('enable gallery slider in woocommerce single product page', $WCPc->text_domain),
                                      'desc' => __('enable gallery slider in woocommerce single product page', $WCPc->text_domain)
                                      ),
                  "is_enable_product_video" => array(
                                      'title' => __('Enable Product Video Tab', $WCPc->text_domain), 
                                      'type' => 'select', 
                                      'id' => 'is_enable_product_video', 
                                      'label_for' => 'is_enable_product_video', 
                                      'name' => 'is_enable_product_video', 
                                      'options' => array('true' => 'True', 'false' => 'False' ), 
                                      'hints' => __('enable Product Video Tab in woocommerce single product page', $WCPc->text_domain),
                                      'desc' => __('enable Product Video Tab in woocommerce single product page', $WCPc->text_domain)
                                      )
                                ) 
              )
        )
    );

    $WCPc->admin->settings->wcpc_settings_field_init(apply_filters("wcpc_settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function wcpc_general_settings_sanitize( $input ) {
    global $WCPc;
    $new_input = array();
    
    $hasError = false;

    if( isset( $input['is_make_my_pack'] ) ){
      $new_input['is_make_my_pack'] = sanitize_text_field( $input['is_make_my_pack'] );
    }else{
      $new_input['is_make_my_pack'] = 'false';
    }

    if( isset( $input['is_product_360'] ) ){
      $new_input['is_product_360'] = sanitize_text_field( $input['is_product_360'] );
    }else{
      $new_input['is_product_360'] = 'false';
    }

    if( isset( $input['is_enable_gallery_slider'] ) ){
      $new_input['is_enable_gallery_slider'] = sanitize_text_field($input['is_enable_gallery_slider']);
    }else{
      $new_input['is_enable_gallery_slider'] = 'false';
    }

    if( isset( $input['is_enable_product_video'] ) ){
      $new_input['is_enable_product_video'] = sanitize_text_field($input['is_enable_product_video']);
    }else{
      $new_input['is_enable_product_video'] = 'false';
    }
    
    if($hasError == false) {
      add_settings_error(
        "wcpc_{$this->tab}_settings_name",
        esc_attr( "wcpc_{$this->tab}_settings_admin_updated" ),
        __('General settings updated', $WCPc->text_domain),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function default_settings_section_info() {
    global $WCPc;
    _e('Enter your default settings below', $WCPc->text_domain);

  }
  
}