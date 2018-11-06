

<?php
$user_id = get_current_user_id();

$ams_preferences = apply_filters("ams_prepare_preferences_array",apply_filters("ams_preferences_form_load_values",AMS_Plugin::get_preferences()));

?>




<form class="form-horizontal" method="post">
  <input type='hidden' name='preferences_nonce' value='<?php echo wp_create_nonce("preferences_nonce"); ?>'/>
<input type="hidden" name="ams_preferences_submitted" value="1"/>
<fieldset class="ams_form">


  <?php ob_start(); ?>
  
<!-- Multiple Radios -->
<div class="form-group" id='ams_pref_menu_width'>
  <label class="col-md-4 control-label" for="radios">Menu Width</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="amsp_width_radios-0">
      <input type="radio" name="width_preset" id="amsp_width_radios-0" <?php checked($ams_preferences['width_preset'],1); ?> value="1" >
      Use AMO Default Width (280px)
    </label>
	</div>
  <div class="radio">
    <label for="amsp_width_radios-1">
      <input type="radio" name="width_preset" id="amsp_width_radios-1" <?php checked($ams_preferences['width_preset'],2); ?> value="2">
      Use Wordpress Width (180px)
    </label>
	</div>
  <div class="radio">
    <label for="amsp_width_radios-2">
      <input type="radio" name="width_preset" id="amsp_width_radios-2" <?php checked($ams_preferences['width_preset'],3); ?> value="3">
      User Custom Width
    </label>
	</div>
  </div>
</div>

<!-- Text input-->
<div class="form-group" id='ams_pref_custom_width_group' style='<?php if($ams_preferences['width_preset']!=3)echo "display:none;"; ?>'>
  <label class="col-md-4 control-label" for="menu_width">Custom Width</label>  
  <div class="col-md-4">
  <input id="menu_width" name="menu_width" min='50' step="10" type="number" placeholder="" value="<?php echo $ams_preferences['menu_width']; ?>" class="form-control input-md" size="4">px
  </div>
</div>




<div class="form-group" id='ams_pref_pad_radios'>
  <label class="col-md-4 control-label" for="radios">Pad Menu Items</label>
  <div class="col-md-4">
  <div class="radio">
    <label for="ams-pad-radios-0">
      <input type="radio" name="pad_menu_items" id="ams-pad-radios-0" <?php checked($ams_preferences['pad_menu_items'],1); ?> value="1" >
      Use AMO Default (10px)
    </label>
	</div>
  <div class="radio">
    <label for="ams-pad-radios-1">
      <input type="radio" name="pad_menu_items" id="ams-pad-radios-1" <?php checked($ams_preferences['pad_menu_items'],2); ?> value="2">
      No Additional Padding
    </label>
	</div>
  <div class="radio">
    <label for="ams-pad-radios-2">
      <input type="radio" name="pad_menu_items" id="ams-pad-radios-2" <?php checked($ams_preferences['pad_menu_items'],3); ?> value="3">
      User Custom Padding
    </label>
	</div>
  </div>
</div>

<!-- Text input-->
<div class="form-group" id='ams_pref_custom_padding_group' style='<?php if($ams_preferences['pad_menu_items']!=3)echo "display:none;"; ?>'>
  <label class="col-md-4 control-label" for="menu_width">Custom Padding</label>  
  <div class="col-md-2">
  <input id="menu_width" name="custom_padding" type="number" placeholder="" value="<?php echo $ams_preferences['custom_padding']; ?>" class="form-control input-md" size="4">px
  </div>
</div>





<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="disable_css">Disable CSS</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="disable_css-0">
      <input type="checkbox" name="disable_css"  <?php checked($ams_preferences['disable_css'],1); ?>  id="disable_css-0" value="1">
      &nbsp;
    </label>
  </div>
</div>

<!-- Text input-->
<div class="form-group" id='ams_pref_background_color'>
  <label class="col-md-4 control-label" for="expander_background">Custom Colour For Expander Background</label>  
  <div class="col-md-4">
  <input id="expander_background" name="expander_background" value="<?php echo $ams_preferences['expander_background']; ?>" type="text" placeholder="" class="form-control input-md color-field">
    <br/>(leave blank to use default color scheme)
  </div>
</div>

<!-- Text input-->
<div class="form-group" id='ams_pref_text_color'>
  <label class="col-md-4 control-label" for="expander_foreground">Text Colour for Expander</label>  
  <div class="col-md-4">
  <input id="expander_foreground" name="expander_foreground" type="text" placeholder="" class="form-control input-md color-field"  value="<?php echo $ams_preferences['expander_foreground']; ?>">
    <br/>(leave blank to use default color scheme)    
  </div>
</div>


<!-- Text input-->
<div class="form-group" id='ams_pref_background_color'>
  <label class="col-md-4 control-label" for="expander_hover_background">Custom Colour For Expander Background on Hover</label>  
  <div class="col-md-4">
  <input id="expander_hover_background" name="expander_hover_background" value="<?php echo $ams_preferences['expander_hover_background']; ?>" type="text" placeholder="" class="form-control input-md color-field">
    <br/>(leave blank to use default color scheme)
  </div>
</div>

<!-- Text input-->
<div class="form-group" id='ams_pref_text_color'>
  <label class="col-md-4 control-label" for="expander_hover_foreground">Text Hover Colour for Expander</label>  
  <div class="col-md-4">
  <input id="expander_hover_foreground" name="expander_hover_foreground" type="text" placeholder="" class="form-control input-md color-field"  value="<?php echo $ams_preferences['expander_hover_foreground']; ?>">
    <br/>(leave blank to use default color scheme)    
  </div>
</div>



<?php
echo apply_filters("ams_preferences_form", ob_get_clean());
?>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="save"></label>
  <div class="col-md-4">
    <input type="submit" name="save" class="button button-primary" value='Save'/>
  </div>
</div>
</fieldset>
</form>
