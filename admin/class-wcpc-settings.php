<?php
class WCPc_Settings {
  
  private $tabs = array();
  private $options;
  /**
   * Start up
   */
  public function __construct() {
    // Admin menu
    add_action( 'admin_menu', array( $this, 'add_settings_page_for_wcpc' ), 100 );
    add_action( 'admin_init', array( $this, 'settings_page_init' ) );
    
    // Settings tabs
    add_action('wcpc_settings_page_general_tab_init', array(&$this, 'wcpc_general_tab_init'), 10, 1);
  }
  
  /**
   * Add options page
   */
  public function add_settings_page_for_wcpc() {
    global $WCPc;
    
    add_menu_page(
        __('WC Product Customizer', $WCPc->text_domain), 
        __('WC Product Customizer', $WCPc->text_domain), 
        'manage_options', 
        'wcpc-settings-admin', 
        array( $this, 'create_WCPc_settings' ),
        $WCPc->plugin_url . 'assets/images/dualcube.png'
    );
    
    $this->tabs = $this->get_wcpc_settings_tabs();
  }
  
  function get_wcpc_settings_tabs() {
    global $WCPc;
    $tabs = apply_filters('WCPc_tabs', array(
      'general' => __('General', $WCPc->text_domain)
    ));
    return $tabs;
  }
  
  function wcpc_settings_tabs( $current = 'general' ) {
    if ( isset ( $_GET['tab'] ) ) :
      $current = $_GET['tab'];
    else:
      $current = 'general';
    endif;
    
    $links = array();
    foreach( $this->tabs as $tab => $name ) :
      if ( $tab == $current ) :
        $links[] = "<a class='nav-tab nav-tab-active' href='?page=wcpc-settings-admin&tab=$tab'>$name</a>";
      else :
        $links[] = "<a class='nav-tab' href='?page=wcpc-settings-admin&tab=$tab'>$name</a>";
      endif;
    endforeach;
    echo '<div class="icon32" id="dualcube_menu_ico"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
      echo $link;
    echo '</h2>';
    
    foreach( $this->tabs as $tab => $name ) :
      if ( $tab == $current ) :
        echo "<h2>$name Settings</h2>";
      endif;
    endforeach;
  }

  /**
   * Options page callback
   */
  public function create_WCPc_settings() {
    global $WCPc;
    ?>
    <div class="wrap">
      <?php $this->wcpc_settings_tabs(); ?>
      <?php
      $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'general' );
      $this->options = get_option( "wcpc_{$tab}_settings_name" );
      //print_r($this->options);
      
      // This prints out all hidden setting errors
      settings_errors("wcpc_{$tab}_settings_name");
      ?>
      <form method="post" action="options.php">
      <?php
        // This prints out all hidden setting fields
        settings_fields( "wcpc_{$tab}_settings_group" );   
        do_settings_sections( "wcpc-{$tab}-settings-admin" );
        submit_button(); 
      ?>
      </form>
    </div>
    <?php
    do_action('wcpc_admin_footer');
  }

  /**
   * Register and add settings
   */
  public function settings_page_init() { 
    do_action('wcpc_befor_settings_page_init');

    // Register each tab settings
    foreach( $this->tabs as $tab => $name ) :
      do_action("wcpc_settings_page_{$tab}_tab_init", $tab);
    endforeach;
    
    do_action('wcpc_after_settings_page_init');
  }
  
  /**
   * Register and add settings fields
   */
  public function wcpc_settings_field_init($tab_options) {
    global $WCPc;

    if(!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections'])) {
      // Register tab options
      register_setting(
        "wcpc_{$tab_options['tab']}_settings_group", // Option group
        "wcpc_{$tab_options['tab']}_settings_name", // Option name
        array( $tab_options['ref'], "wcpc_{$tab_options['tab']}_settings_sanitize" ) // Sanitize
      );
      
      foreach($tab_options['sections'] as $sectionID => $section) { 
        // Register section
        add_settings_section(
          $sectionID, // ID
          $section['title'], // Title
          array( $tab_options['ref'], "{$sectionID}_info" ), // Callback
          "wcpc-{$tab_options['tab']}-settings-admin" // Page
        );
        
        // Register fields
        if(isset($section['fields'])) {
          foreach($section['fields'] as $fieldID => $field) {
            if(isset($field['type'])) {
              $field = $WCPc->wcpc_wp_fields->check_field_id_name($fieldID, $field);
              $field['tab'] = $tab_options['tab'];
              $callbak = $this->get_field_callback_type($field['type']);
              if(!empty($callbak)) { 
                add_settings_field(
                  $fieldID,
                  $field['title'],
                  array( $this, $callbak ),
                  "wcpc-{$tab_options['tab']}-settings-admin",
                  $sectionID,
                  $field
                );
              }
            }
          }
        }
      }
    }
  }
  
  function wcpc_general_tab_init($tab) {
    global $WCPc;
    $WCPc->admin->load_class("settings-{$tab}", $WCPc->plugin_path, $WCPc->token);
    new WCPc_Settings_General($tab);
  }
  
  function get_field_callback_type($fieldType) {
    $callBack = '';
    switch($fieldType) {
      case 'input':
      case 'text':
      case 'email':
      case 'number':
      case 'file':
      case 'url':
        $callBack = 'text_field_callback';
        break;
        
      case 'hidden':
        $callBack = 'hidden_field_callback';
        break;
        
      case 'textarea':
        $callBack = 'textarea_field_callback';
        break;
        
      case 'wpeditor':
        $callBack = 'wpeditor_field_callback';
        break;
        
      case 'checkbox':
        $callBack = 'checkbox_field_callback';
        break;
        
      case 'radio':
        $callBack = 'radio_field_callback';
        break;
        
      case 'select':
        $callBack = 'select_field_callback';
        break;
        
      case 'upload':
        $callBack = 'upload_field_callback';
        break;
        
      case 'colorpicker':
        $callBack = 'colorpicker_field_callback';
        break;
        
      case 'datepicker':
        $callBack = 'datepicker_field_callback';
        break;
        
      case 'multiinput':
        $callBack = 'multiinput_callback';
        break;
        
      default:
        $callBack = '';
        break;
    }
    
    return $callBack;
  }
  
  /** 
   * Get the hidden field display
   */
  public function hidden_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->hidden_input($field);
  }
  
  /** 
   * Get the text field display
   */
  public function text_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->text_input($field);
  }
  
  /** 
   * Get the text area display
   */
  public function textarea_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->textarea_input($field);
  }
  
  /** 
   * Get the wpeditor display
   */
  public function wpeditor_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? ( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? ( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->wpeditor_input($field);
  }
  
  /** 
   * Get the checkbox field display
   */
  public function checkbox_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['dfvalue'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : '';
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->checkbox_input($field);
  }
  
  /** 
   * Get the checkbox field display
   */
  public function radio_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->radio_input($field);
  }
  
  /** 
   * Get the select field display
   */
  public function select_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->select_input($field);
  }
  
  /** 
   * Get the upload field display
   */
  public function upload_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->upload_input($field);
  }
  
  /** 
   * Get the multiinput field display
   */
  public function multiinput_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? $field['value'] : array();
    $field['value'] = isset( $this->options[$field['name']] ) ? $this->options[$field['name']] : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->multi_input($field);
  }
  
  /** 
   * Get the colorpicker field display
   */
  public function colorpicker_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->colorpicker_input($field);
  }
  
  /** 
   * Get the datepicker field display
   */
  public function datepicker_field_callback($field) {
    global $WCPc;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "wcpc_{$field['tab']}_settings_name[{$field['name']}]";
    $WCPc->wcpc_wp_fields->datepicker_input($field);
  }
  
}