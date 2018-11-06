<?php

add_action( 'wp_ajax_ams_save', 'ajax_ams_save' );

function ajax_ams_save() {
    
    $user_id = get_current_user_id();
    
    if(!isset($_REQUEST['ams_sorter_ajax_nonce'])) {
      
      var_dump($_REQUEST);
        echo "Nonce failed! (not present)";
        die;
    }
    else {
        $ver = wp_verify_nonce($_REQUEST['ams_sorter_ajax_nonce'],'ams_sorter_ajax');
    }
    
    if(!$ver) {
        echo "Nonce failed!";
        die;
    }
    
    if(apply_filters("ams_continue_save_setting",true,$user_id)) {
      update_user_meta( $user_id, 'ams_menu_setting', apply_filters("ams_save_setting",$_REQUEST['new_value']) );
    }
    
    wp_die();
}
add_action( 'wp_ajax_ams_expander_save', 'ajax_ams_expander_save' );

function ajax_ams_expander_save() {

    if(!isset($_REQUEST['ams_expander_ajax_nonce'])) {
        echo "No nonce";
        return false;        
    }
    else {
        $ver = wp_verify_nonce($_REQUEST['ams_expander_ajax_nonce'],'ams_expander_ajax');
    }
    
    if(!$ver) {
        echo "Nonce failed!";
        die;
    }
    else {
        //echo "Nonce is fine";
    }
    
    parse_str($_REQUEST['new_value'],$vars);
    
    
    
    $user_id = get_current_user_id();
    if(apply_filters("ams_continue_save_expander",true,$vars,$user_id)) {
    
    $User = new AMS_User($user_id);
    $User->save_single_expander($vars['e_id'], $vars);
    
    }
    
    wp_die();
    
    
}

add_action( 'wp_ajax_ams_toggle_on_off', 'ajax_ams_toggle_on_off' );

function ajax_ams_toggle_on_off() {
  
    if(!isset($_REQUEST['ams_onoff_ajax_nonce'])) {
        echo "No nonce";
        return false;        
    }
    else {
        $ver = wp_verify_nonce($_REQUEST['ams_onoff_ajax_nonce'],'ams_onoff_ajax');
    }
    
    if(!$ver) {
        echo "Nonce failed!";
        die;
    }
    else {
       // echo "Nonce is fine";
    }
    
    $ams_change_to = ( isset($_REQUEST['ams_on']) && $_REQUEST['ams_on'] ) ? 0 : 1;
    $user_id = get_current_user_id();
    echo $ams_change_to;
    update_user_meta( $user_id, 'ams_menu_on', $ams_change_to );
    wp_die();    
}

