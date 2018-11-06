<form method="post">
    <input type='hidden' name='ams_global_nonce' value='<?php echo wp_create_nonce("ams_global_nonce"); ?>'/>
<input type="hidden" name="ams_global_settings_submitted" value="1"/>

 <!-- Text input-->
<div class="form-group" id='ams_pref_custom_width_group'>
  <label class="col-md-4 control-label" for="menu_width">Minimum Capability Required to View the Organizer</label>  
  <div class="col-md-4">
    
    
  <?php
     global $wp_roles; 
     $roles = $wp_roles->roles; 
     
     $caps = array();
     
     foreach($roles as $role) {
         $new_caps = $role['capabilities'];
         $caps = array_merge($caps,$new_caps);
     }
     
     ksort($caps);
     
     
  ?>
     <select name="cap">      
  <?php
  
    $current_cap = get_option("ams_minimum_capability");
    if(!$current_cap)$current_cap="read";
  
    foreach($caps as $cap=>$on) {
      ?>
     <option <?php selected($cap,$current_cap); ?> value="<?php echo $cap; ?>"><?php echo $cap; ?></option>
       <?php
    }


  ?>
    </select>

  </div>
</div> 
 <!--
 <p style="padding:40px;">
     Upgrade to Pro to manage settings for different roles and users.
 </p>
 -->
  
<div class="form-group">
  <label class="col-md-4 control-label" for="save"></label>
  <div class="col-md-4">
    <input type="submit" name="save" class="button button-primary" value='Save'/>
  </div>
</div>
 
</form>