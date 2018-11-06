<?php
$user_id = get_current_user_id();


?>

<form id="sorter_additional_data">
<?php echo apply_filters("ams_sorter_additional_data_form",""); ?>
</form>


<div class="ams_message_bar">Message bar</div>

<div class="ams_toggler_on_off_container">
    <?php $ams_on = ams_is_on($user_id); ?>
    <input type="hidden" id="ams_on" name="ams_on" value="<?php echo ( $ams_on)?1:0; ?>" />
    <a href='#' class="ams_toggler_on_off <?php echo ( $ams_on)?"":"is_off"; ?>" onclick="return ams_toggle_on_off(this);"><?php echo ( $ams_on)?"Switch Off the menu sorter for this user.":"The Menu Sorter System is OFF. Click here to Switch On"; ?></a>
</div>

<div class='ams_intro'>
  
    <h3>Sorter</h3>
  <strong><?php echo __("Instructions"); ?>:</strong>: <?php echo __("Drag the expanders on the left into the right hand column to organize your menu. When you are finished,click the 'save' button."); ?>
  
</div>

<div  class="ams-sorter-save-button-container">
<input class="ams-sorter-save-button button-primary button-primary-disabled" type="button" onclick="ams_save(true)" value="Save Configuration"/>
</div>

<?php echo apply_filters("ams_sorter_before",""); ?>

<?php
  $menu_items = apply_filters("ams_filter_menu_global",$GLOBALS[ 'menu' ]);
  $form = "";

?>

<div id="ams_left_column">
  
  
  <?php if(false): ?>
<div id='ams_expanders_dnd_demo' class="ams_expanders_dnd_demo" style="position:relative;width:300px;">
  <span style='position:absolute;right:20px;top:20px;' onclick="jQuery('#ams_expanders_dnd_demo').hide()" class="dashicons dashicons-no"></span>
<img width="300" src="<?php echo plugin_dir_url(__FILE__); ?>/expander-drag-demo.gif"/>
</div>
  <?php endif; ?>
  
<div class="ams_expanders_menu">
    <div class="ams_expanders_menu_title">
      <h4><?php echo __("Expanders"); ?></h4>
    </div>
  
    <div id="ams_expanders_menu_droparea">  

      <?php for($i=1;$i<=10;$i++): ?>
      <div id='ex<?php echo $i; ?>' class="expander_dnd">

          <?php
          $user_id = get_current_user_id();
          $parameters = false;
          
          if(isset($_GET['ams_role'])) {
            $user_id = sanitize_key($_GET['ams_role']);
            $parameters = array("entity_type"=>'role');
          }
          
          $expander_data = AMS_Plugin::get_expander_data($i,$user_id,$parameters);
          
          if(is_array($expander_data)) {
          $expander_text = $expander_data['e_name'];
          $expander_mode = $expander_data['e_mode'];
          }
          else {
              $expander_text = "Expander";
              $expander_mode = 0;
          }
          
          if(!$expander_text)$expander_text = "Expander";

          $sel_0 = selected($expander_mode,0,false);
          $sel_1 = selected($expander_mode,1,false);
          ?>

          <span data-expander-id="<?php echo $i; ?>" class="dashicons dashicons-admin-generic expander-form-opener"></span>

          <!-- <span class="wp-menu-image dashicons-before dashicons-arrow-down-alt2"></span> -->
          <h4><span class="expander_text_<?php echo $i?>"><?php echo $expander_text; ?></span></h4>

          <?php 
          
          $additional_prepend_expander_form_fields = apply_filters("ams_prepend_additional_expander_form_fields","",$expander_data);
          $additional_append_expander_form_fields = apply_filters("ams_append_additional_expander_form_fields","",$expander_data);
          
  $form .= <<<EEE

          <div id="expander_form_{$i}" class="expander_form">

          <span class="button-link widget-control-close close-expander-form">Close</span>
          <div style='clear:both;height:20px;'></div>

          <form method='post' id='ams_settings_{$i}'>
          <input type='hidden' name='e_id' value='{$i}'>
          {$additional_prepend_expander_form_fields}
          
          <div class='expander_form_field'>
          Name:
          <input name='e_name' value='{$expander_text}'/>
          </div>
          
          <div class='expander_form_field'>
          Mode:

          <select name="e_mode">
              <option {$sel_0} value="0">Closed by Default</option>
              <option {$sel_1}  value="1">Open by Default</option>
          </select>
          
          </div>

          {$additional_append_expander_form_fields}
          <input type='button' class="save_expander_form button button-primary widget-control-save right" value='Save'/>
          </form>
          
          <div style='clear:both'></div>

          </div>
EEE;
              ?>              

      </div>
      <?php endfor; ?>
    
    </div>
</div>

<?php echo $form; ?>

<?php $ams_setting = get_option("ams_menu_setting"); ?>

  
</div>

<div id="ams_right_column">
    <div class="ams_expanders_menu_title">
      <h4><?php echo __("Your Menu"); ?></h4>
    </div>

<div id="dnd_target" >
  
  <?php



  foreach($menu_items as $key=>$val) {

      if($val[4]=='wp-menu-separator')continue;
      if(strpos($val[4],'wp-menu-separator')!==false)continue;

      $sanitize_5 = $val[5];
      $sanitize_5 = str_replace("?","-",$sanitize_5);
      $sanitize_5 = str_replace("=","-",$sanitize_5);
      $sanitize_5 = str_replace("/","-",$sanitize_5);

      echo "<div id='ams-sorter-{$sanitize_5}' class='admin_menu_item'>";
      //echo $val[4];
      //echo "<br/>";
      //var_dump($val);
      echo "<strong>".$val[0]."</strong>";
      echo "</div>";

  }

  ?>
</div>

</div>


<div style="clear:both"></div>

<?php echo apply_filters("ams_sorter_after",""); ?>


<input type="button" style='display:block' value="Show go box" onclick="jQuery('#go').show();" />
<input type="hidden" id="ams_onoff_ajax_nonce" value="<?php echo wp_create_nonce( 'ams_onoff_ajax' ); ?>" />
<input type="hidden" id="ams_sorter_ajax_nonce" value="<?php echo wp_create_nonce( 'ams_sorter_ajax' ); ?>" />
<input type="hidden" id="ams_expander_ajax_nonce" value="<?php echo wp_create_nonce( 'ams_expander_ajax' ); ?>" />

<textarea id='go' style='display:none;' cols='80' rows='20'><?php echo $ams_setting; ?></textarea>


