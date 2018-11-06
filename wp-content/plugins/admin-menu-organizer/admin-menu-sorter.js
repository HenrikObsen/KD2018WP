var amo_sorter_unsaved = false;

jQuery(window).on("beforeunload", function () {
    if(amo_sorter_unsaved) {
      return "You have unsaved changes, please hit save before leaving.";
    }
      
    return;
    
});
    
jQuery(function() {
  
  

  //jQuery("#adminmenu").hide();
  
   jQuery('.expander_dnd').draggable({
    revert:"invalid",
    connectToSortable: "#dnd_target",
  });
   
  jQuery('#dnd_target').droppable( {
    drop: handleDropEvent2,
    greedy:true,
    accept:".expander_dnd",
  } ); 
  
  jQuery('#ams_expanders_menu_droparea').droppable( {
    drop: handleDropEvent_back_in_box,
    greedy:true,
    accept:".expander_dnd",
  } ); 
  
  jQuery("#dnd_target").sortable( {
      revert:true,
      update:updateSort,
  });
  
  jQuery("#ams_expanders_menu_droparea").sortable( {
      revert:true,
      update:updateSort,
  });

  
  jQuery(".save_expander_form").on("click",function() {
     ams_expander_save(jQuery(this)); 
  });
  jQuery('.expander-form-opener').on('mousedown', function(e) {
    e.stopPropagation();
  });  
  jQuery('.close-expander-form').on('mousedown', function(e) {
    e.stopPropagation();
  });    
  jQuery('.save_expander_form').on('mousedown', function(e) {
    e.stopPropagation();
  });  
  
  jQuery(".expander-form-opener").click(function(event) {
//     jQuery(this).siblings(".expander_form").toggle(); 
    event.stopPropagation();
     console.log("opening form");
     
    var id = jQuery(this).attr("data-expander-id");
     
     jQuery("#ex"+id).append(jQuery("#expander_form_"+id));
     /*
    jQuery(".expander_form").not("#expander_form_"+id).hide(function() {
        console.log("does this get executed?");
        jQuery("#expander_form_"+id).fadeIn();
    }); 
    
    jQuery("#expander_form_"+id).fadeIn();
    */
    //jQuery("#glass_screen").show();
        jQuery("#expander_form_"+id).slideToggle("fast",function() {
        jQuery("#expander_form_"+id).parent().css("height","auto");          
        });
    

     //jQuery(this).siblings(".expander_form").css("background-color","red"); 
  });
  jQuery(".close-expander-form").click(function(event) {
     event.stopPropagation();
    jQuery("#glass_screen").hide();
     jQuery(this).parent().slideUp();
  });
  
  

    jQuery( ".autocomplete_search_box" ).autocomplete({
      source: function( request, response  ) {
          
          
        jQuery.ajax( {
          url: ajaxurl,
          data: {
            action : 'autocomplete',
            q: request.term,
          },
          success: function( data ) {
              
              console.log("Returned from autocomplete: "+data);
              
              var arrExtracted = JSON.parse(data);
               
            // Handle 'no match' indicated by [ "" ] response
            //response( data.length === 1 && data[ 0 ].length === 0 ? [] : data );
            response(arrExtracted);
          }
        } );
      },      
      minLength: 2,
      search: function( event, ui ) {
          //current_page_id = jQuery(this).prev(".autocomplete_search_id").attr('rel');
          //current_filter_id = jQuery(this).prev(".autocomplete_search_id").attr('rev');
          
          
      },
 
      select: function( event, ui ) {  
          jQuery(this).siblings(".autocomplete_search_id").val(ui.item.id);
          console.log("id selected = "+ui.item.id);
          
          jQuery(this).siblings(".autocomplete_selected_label").html("Selected: "+ui.item.label+" <a href='#' class='autocomplete_search_button' onclick='show_autocomplete();'>Search</a>");
          jQuery(".autocomplete_search_box").fadeOut();
          jQuery('#ams_cp_type_2').attr('checked', true);
          
      }
    });
    
    jQuery("#ams_existing_admin_page").on("focus",function() {
      jQuery('#ams_cp_type_1').prop('checked', true);
    });
    jQuery(".ams_content").on("focus",function() {
      jQuery('#ams_cp_type_3').prop('checked', true);
    });
    jQuery("#ams_iframe_src").on("focus",function() {
      jQuery('#ams_cp_type_4').prop('checked', true);
    });
  
    jQuery("input[name='pad_menu_items']").click(function() {
       console.log("pad menu changed");
       if(jQuery("#ams-pad-radios-2").is(":checked")) {
         jQuery("#ams_pref_custom_padding_group").slideDown();
         console.log("pad menu slide down");
       }
       else {
         jQuery("#ams_pref_custom_padding_group").slideUp();
         console.log("pad menu slide up");
         
       }
    });
    jQuery("input[name='width_preset']").click(function() {
       console.log("width menu changed");
       if(jQuery("#amsp_width_radios-2").is(":checked")) {
         jQuery("#ams_pref_custom_width_group").slideDown();
       console.log("width menu changed - s down");
       }
       else {
         jQuery("#ams_pref_custom_width_group").slideUp();
       console.log("width menu changed - s up");
         
       }
    });
    
    
    jQuery(function() {

            jQuery('.color-field').wpColorPicker();

    });    
  
  
});

function stopDraggingTool( event, ui) {
    registerListeners();    
}

function handleDropEvent2( event, ui ) {
  
  jQuery("#dnd_target").sortable('serialize');

}

function handleDropEvent_back_in_box( event, ui) {
  var draggable = ui.draggable;
  jQuery("#ams_expanders_menu_droparea").append(draggable);
  
  draggable.css('top',0);
  draggable.css('left',0);
  draggable.css('display','block');
  draggable.css('position','relative');
  draggable.css('max-width','267px');
  
  updateSort();
  
  
}

function setSorterDirty() {
  amo_sorter_unsaved = true;  
  jQuery(".ams-sorter-save-button").removeClass("button-primary-disabled");
}
function setSorterClean() {
  amo_sorter_unsaved = false;  
  jQuery(".ams-sorter-save-button").addClass("button-primary-disabled");
}


function updateSort() {
    var orders = [];
    jQuery.each(jQuery("#dnd_target").children(), function(i, item) {
        orders.push(jQuery(item).attr("id"));
    });

    jQuery("#go").val(orders.join(" "));
    
    setSorterDirty();
    
            
}


function ams_save(show_message) {

     var new_value = jQuery("#go").val();
     
     var sorter_additional_data = jQuery("#sorter_additional_data").serialize();
     
    jQuery.ajax({
        url: ajax_object.ajax_url,
        data: {
            'action':'ams_save',
            'new_value' : new_value,
            'sorter_additional_data' : sorter_additional_data,
            'ams_sorter_ajax_nonce' : jQuery("#ams_sorter_ajax_nonce").val(),
        },
        success:function(data) {
            if(show_message) {
            ams_display_message("Menu configuration saved.");
            setSorterClean();
            } 
            
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });  
              
}

function ams_expander_save(me) {

     var new_value = jQuery(me).parent().serialize();
     
    jQuery.ajax({
        url: ajax_object.ajax_url,
        data: {
            'action':'ams_expander_save',
            'new_value' : new_value,
            'ams_expander_ajax_nonce' : jQuery("#ams_expander_ajax_nonce").val(),
        },
        success:function(data) {
            // This outputs the result of the ajax request
            console.log(data);
            jQuery(".expander_form").slideUp();
            
            var the_id = jQuery(me).parent().children("[name=e_id]").val();
            var the_val = jQuery(me).parent().children(".expander_form_field").children("[name=e_name]").val();
            console.log("Val="+the_val);
            console.log("ID="+the_id);
            jQuery(".expander_text_"+the_id).html(the_val);
            ams_display_message("The expander has been saved.");
            
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });  
              
}

function ams_display_message(str) {
      jQuery(".ams_message_bar").html('<span class="dashicons dashicons-yes"></span> '+str+'');
      jQuery(".ams_message_bar").slideDown("slow",function() {



        setTimeout(function() {
          jQuery(".ams_message_bar").slideUp();

        },5000);

      });

}

function ams_side_menu_toggle_menu(expander_id,force,instant) {
      var the_expander = jQuery(".expander-"+expander_id);
  
  switch(force) {
    
    default:
      // just toggle
      
      
      if(the_expander.hasClass("data-expander-open")) {
         the_expander.removeClass("data-expander-open").addClass("data-expander-closed");
      }
      else {
         the_expander.addClass("data-expander-open").removeClass("data-expander-closed");        
      }
      
      jQuery(".data-expander-child-of-"+expander_id).fadeToggle(); 
         jQuery("#ams_sub_group_"+expander_id).slideToggle();
      
      
      
    break;
    case 1:
    // force open
    
      if(!instant) {
      jQuery("#ams_sub_group_"+expander_id).slideDown();
      }
      else {
      jQuery("#ams_sub_group_"+expander_id).show();
      }

      jQuery(".data-expander-child-of-"+expander_id).fadeIn(); 
         the_expander.addClass("data-expander-open").removeClass("data-expander-closed"); 
    break;
    case 2:
    // force close
      jQuery("#ams_sub_group_"+expander_id).slideUp();
         jQuery("#ams_sub_group_"+expander_id).fadeOut();
         the_expander.removeClass("data-expander-open").addClass("data-expander-closed");
    break;
    
  }
  
  
  if(the_expander.hasClass("data-expander-open")) {
  the_expander.find(".dashicons-before").addClass("dashicons-arrow-up-alt2").removeClass("dashicons-arrow-down-alt2");
    
  }
  else {
  the_expander.find(".dashicons-before").addClass("dashicons-arrow-down-alt2").removeClass("dashicons-arrow-up-alt2");
    
  }
  
  
  
  
}

function ams_expand_all() {
    
    var num_closed = 0;
  for(i=1;i<=10;i++) {
     var the_expander = jQuery(".expander-"+i);
     
     if(the_expander.hasClass("data-expander-open")) {
         
     }
     else {
         if(the_expander.length) { /* make sure it exists */
         num_closed++;         
             
         }
     }
     
  }
  
  if(num_closed == 0) {
  for(i=1;i<=10;i++) {
    ams_side_menu_toggle_menu(i,0); // force close
  }
      
  }
  else {
  for(i=1;i<=10;i++) {
    ams_side_menu_toggle_menu(i,1); // force open
  }
  }
  
}



function ams_toggle_on_off(me) {

     var ams_on = (jQuery("#ams_on").val()=='1')?1:0;
     
     jQuery(".ams_message_bar").hide();
     
    jQuery.ajax({
        url: ajax_object.ajax_url,
        data: {
            'action':'ams_toggle_on_off',
            'ams_on' : ams_on,
            'ams_onoff_ajax_nonce' : jQuery("#ams_onoff_ajax_nonce").val(),
        },
        success:function(data) {
          
            var expander_on = "";
            if(data==1) {
                expander_on = "on";
                jQuery("#ams_on").val(1);
                jQuery(".ams_toggler_on_off").html("The Menu Sorter System is On for this user. Click here to Switch Off.");
                jQuery(".ams_toggler_on_off").removeClass("is_off");
            }
            else {
                expander_on = "off";
                jQuery("#ams_on").val(0);
                jQuery(".ams_toggler_on_off").html("The Menu Sorter System is OFF for this user. Click here to Switch On.");
                jQuery(".ams_toggler_on_off").addClass("is_off");
            }
          
            ams_display_message("The AMS system is now switched "+expander_on+" on for this user");
            
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });  
    
    return false;
    
}

function show_autocomplete() {
   jQuery(".autocomplete_search_box").slideDown();
}