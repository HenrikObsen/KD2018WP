<?php

class AMS_User {
  
  var $user_id;
  
  function __construct($user_id) {
    
    $this->user_id = $user_id;
    
  }
  
  function get_all_data() {
    
    $final_array['sorter'] = $this->get_sorter_data();
    $final_array['expanders'] = $this->get_all_expander_data();
    $final_array['preferences'] = $this->get_preferences();
    
    return $final_array;
    
  }
  
  function import_all_data($json,$is_json=true) {
    
    if($is_json) {
      $data_arr = json_decode($json);
    }
    else {
      $data_arr = $json; // in case we need to pass the array directly
    }
    
    if( !isset($data_arr['sorter']) && !isset($data_arr['preferences']) && !isset($data_arr['expanders']) ) {
      return -1;
    }
    
    $this->save_sorter_data($data_arr['sorter']);
    $this->save_preferences($data_arr['preferences']);
    $this->save_expanders($data_arr['expanders']);
    
  }
  
  function get_sorter_data() {
      $settings = get_user_meta($this->user_id, "ams_menu_setting", true);
      return $settings;
  }
  
  function save_sorter_data($setting) {
      update_user_meta( $this->user_id, 'ams_menu_setting', apply_filters("ams_save_setting",$setting) );    
  }
  
  function save_preferences($arr) {    
    update_user_meta($this->user_id,"ams_preferences",$arr);        
  }
  
  function save_expanders($arr) {    
    
    $expander_id = 1;
    
    foreach($arr as $expander_data) {
       
       $this->save_single_expander($expander_id,$expander_data);
       
       $expander_id++;
    }
    
    delete_user_meta($this->user_id,"ams_menu_expander_{$expander_id}_settings");
    
  }
  
  function save_single_expander($expander_id,$expander_data) {
      
      
      $data_to_write = array();
      
       if(isset($expander_data['e_name'])) {
            $data_to_write['e_name']=$expander_data['e_name'];  
       }
       if(isset($expander_data['e_mode'])) {
            $data_to_write['e_mode']=$expander_data['e_mode'];           
       }
      $data_to_write['e_id'] = $expander_id;
       
       $data_to_write = apply_filters("clean_expander_data",$data_to_write,$expander_data,$expander_id);
              
       if(!count($data_to_write))return;
       
       update_user_meta($this->user_id,"ams_menu_expander_{$expander_id}_settings",$data_to_write);              
       
  }
  
  
  function get_expander_data($expander_id) {
    
    $expander_data =  get_user_meta($this->user_id, "ams_menu_expander_{$expander_id}_settings", true);
    
    $expander_data = AMS_Plugin::populate_missing_keys($expander_data, array("e_id","e_name","e_mode"));
    
    return $expander_data;
  }
  
  function get_all_expander_data() {
    
    $expander_id = 1;
    
    $arr = array();
    
    while(get_user_meta($this->user_id, "ams_menu_expander_{$expander_id}_settings", true) OR $expander_id<=10) {
      $arr[$expander_id] = get_user_meta($this->user_id, "ams_menu_expander_{$expander_id}_settings", true);
      $expander_id++;
    }
    
    return $arr;
    
    
  }
  
  function get_preferences()
  {
      $ams_preferences = get_user_meta($this->user_id, "ams_preferences", true);

      return $ams_preferences;

  }  
  
}