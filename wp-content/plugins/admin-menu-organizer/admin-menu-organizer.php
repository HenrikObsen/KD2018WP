<?php


/**
 * Plugin Name:       Admin Menu Organizer
 * Plugin URI:        https://www.phpdevelopment.ca/admin-menu-organizer
 * Description:       Arrange your admin menu into expandable sections
 * Version:           1.0.1
 * Author:            Callum Richards
 * Author URI:        https://www.phpdevelopment.ca/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       admin-menu-organizer
 */





include_once(dirname(__FILE__) . "/ajax.php");
include_once(dirname(__FILE__) . "/classes/user.class.php");

define("AMS_NUM_EXPANDERS",10);

class AMS_Plugin
{
    
    function __construct()
    {
      
      
        if( !is_admin() ) {
        return;      
        }
      
        add_action('admin_menu', array(
            $this,
            'ams_admin_menu_custom'
        ));
        add_action('admin_enqueue_scripts', array(
            $this,
            'enqueue_admin_scripts'
        ));
        add_action('admin_menu', array(
            $this,
            'admin_menu'
        ));

        add_action('admin_head', array(
            $this,
            'css_in_head'
        ));
        
        if(true) {

            
            
          add_action('admin_head', array(
              $this,
              'main_generated_js'
          ));
        }

        
        add_action('admin_head', array(
            $this,
            'sorter_page_generated_js'
        ));
        
        add_action('admin_enqueue_scripts', array(
            $this,
            'add_color_picker'
        ));
        add_action('init', array(
            $this,
            'init'
        ));
        add_action('admin_footer', array(
            $this,
            'footer'
        ));
        
        add_action( 'wp_ajax_autocomplete', array(
            $this,
            'ajax_autocomplete'
        ));

        add_action( 'admin_notices', array(
            $this,
            'preferences_admin_notice__success'
        ));
        add_action( 'ams_prepare_preferences_array', array(
            $this,
            'ams_prepare_preferences_array'
        ));
        add_action( 'ams_page_header', array(
            $this,
            'ams_page_header'
        ));
        
        
        
    }
    
    
    function ams_prepare_preferences_array($ams_preferences) {
      
      if (!is_array($ams_preferences))
          $ams_preferences = array();
      if (!isset($ams_preferences['width_preset']))
          $ams_preferences['width_preset'] = "1";
      if (!isset($ams_preferences['menu_width']))
          $ams_preferences['menu_width'] = "280";
      if (!isset($ams_preferences['expander_foreground']))
          $ams_preferences['expander_foreground'] = "";
      if (!isset($ams_preferences['expander_background']))
          $ams_preferences['expander_background'] = "";
      if (!isset($ams_preferences['disable_css']))
          $ams_preferences['disable_css'] = "";
      if (!isset($ams_preferences['pad_menu_items']))
          $ams_preferences['pad_menu_items'] = "";
      if (!isset($ams_preferences['custom_padding']))
          $ams_preferences['custom_padding'] = "10";
      
      return $ams_preferences;
    }
    
    
    
    function preferences_admin_notice__success() {
        if(!defined("AMS_PREFERENCES_SUCCESS"))return false;
        
        
        switch(AMS_PREFERENCES_SUCCESS) {
         
        case 1:
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Preferences Saved!', 'ams' ); ?></p>
        </div>
        <?php
        break;
      
        default:
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e(apply_filters("ams_get_preferences_saved_message",AMS_PREFERENCES_SUCCESS),'ams'); ?></p>
        </div>
        <?php
          
        break;
        }
    }

     static function get_expander_data($expander_id,$user_id=0,$parameters=false) {
      if(!$user_id)$user_id = get_current_user_id ();
      
      $User = new AMS_User($user_id);
      
      $expander_data =  apply_filters("ams_get_expander_data",$User->get_expander_data($expander_id),$user_id,$expander_id,$parameters);
      
      return $expander_data;
    }
    
     static function is_on() {
      return (defined("AMS_ON") && AMS_ON)?true:false;
    }
    
    function css_in_head() {
        
        if(!self::is_on()) {
            return;
        }
      
      $ams_preferences = AMS_Plugin::get_preferences();

        ?>
<style type='text/css'>
#adminmenu {
  display:none;
}
<?php
switch($ams_preferences['width_preset']) {
    case 1: default:
      // AMS default
      $menu_width = 280;
      
    break;
    case 2:
      $menu_width = 0; // don't adjust this at all
      
    break;
    case 3:
      $menu_width = intval($ams_preferences['menu_width']);
      
      if($menu_width<40)$menu_width = 40;
      
    break;
}


if($menu_width>0): ?>

/* Width */
@media only screen and (min-width: 961px) {

#wpcontent, #wpfooter {
    margin-left: <?php echo $menu_width; ?>px;
}
#adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap {
    width: <?php echo $menu_width; ?>px;
}

#adminmenu .wp-submenu {
    left: <?php echo $menu_width; ?>px;  
}
#adminmenu .wp-has-current-submenu .wp-submenu {
    left:auto;
}
#adminmenu .wp-has-current-submenu .wp-submenu, #adminmenu .wp-has-current-submenu .wp-submenu.sub-open, #adminmenu .wp-has-current-submenu.opensub .wp-submenu, #adminmenu a.wp-has-current-submenu:focus+.wp-submenu, .no-js li.wp-has-current-submenu:hover .wp-submenu {
    left:auto;  
}

}

<?php endif; ?>

/* Width */


  
</style>
        <?php
    }
    
    function footer() {   
      
      if(!self::is_on())return;
      
      
      $ams_preferences = AMS_Plugin::get_preferences();
      
      
      ?>
  
<style type='text/css'>
  

.sticky-menu #adminmenuwrap {
  position:relative;
}
li#collapse-menu {
    border-top: 2px solid rgba(255,255,255,0.3);
}


<?php if(!isset($ams_preferences['disable_css']) OR !$ams_preferences['disable_css']): ?>  
/* Admin menu itself */
.ams-expander {
    border-top:1px solid rgba(255,255,255,0.1) !important;
}
.ams-expander .wp-menu-name {
    font-weight:bold;
}

#adminmenu li.ams-expander.wp-has-current-submenu a.wp-has-current-submenu, 
#adminmenu li.ams-expander a.wp-not-current-submenu, #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
  /*
  background-color: rgba(0,0,0,.3);
  */
}


.folded #adminmenu a.menu-top {
    padding-left: 0em;
    padding-right: 0em;
}



.folded #adminmenu, .folded #adminmenu li.menu-top, .folded #adminmenuback, .folded #adminmenuwrap {
    width: 36px;
}
.wp-menu-separator {
    display:none !important;
}

/*End  Admin menu itself */
<?php endif; ?>


<?php 

$ams_default = apply_filters("ams_css_default_menu_item_padding",6);

switch($ams_preferences['pad_menu_items']) {
  case 1: default:
  $padding_px = $ams_default;
  break;
  case 2:
  $padding_px = 0;
  break;
  case 3:
  $padding_px = (isset($ams_preferences['custom_padding']) && $ams_preferences['custom_padding']) ? intval($ams_preferences['custom_padding']) : $ams_default;
  break;
}

if($padding_px): 
  $sub_padding_px = max($padding_px,6); // minimum for sub menu is 6px
  $sub_padding_left_px = max($padding_px,12); // minimum for sub menu is 6px
  ?>
/* Padding */
@media only screen and (min-width: 961px) {

  
#adminmenu a.menu-top, #adminmenu .wp-submenu a{
    padding: <?php echo $padding_px; ?>px;
}
#adminmenu .wp-has-current-submenu .wp-submenu a {
    padding: <?php echo $sub_padding_px; ?>px;
    padding-left: <?php echo $sub_padding_left_px; ?>px;
  
}


}
/* Padding End */
<?php endif; ?>
  
  <?php if($ams_preferences['expander_background'] OR $ams_preferences['expander_foreground']): ?>
  .ams-expander {
    background-color:<?php echo $ams_preferences['expander_background']; ?>;
    color:<?php echo $ams_preferences['expander_foreground']; ?>;
  }
  
  <?php /* if($ams_preferences['disable_css']): */ ?>
#adminmenu li.ams-expander.wp-has-current-submenu a.wp-has-current-submenu, #adminmenu li.ams-expander a.wp-not-current-submenu {     
    background:transparent;                                                                                                                                                                                                
    background-color:rgba(0,0,0,0);
  }
  <?php  endif;  ?>


  <?php if($ams_preferences['expander_hover_background'] OR $ams_preferences['expander_hover_foreground']): ?>
  #adminmenu li.menu-top.ams-expander:hover {
    background-color:<?php echo $ams_preferences['expander_hover_background']; ?>;
    color:<?php echo $ams_preferences['expander_hover_foreground']; ?>;
  }
  #adminmenu li.menu-top.ams-expander:hover a div{
    color:<?php echo $ams_preferences['expander_hover_foreground']; ?>;  
  }
  #adminmenu li.menu-top.ams-expander:hover div.wp-menu-image:before {
    color:<?php echo $ams_preferences['expander_hover_foreground']; ?>;        
  }
  <?php /* if($ams_preferences['disable_css']): */ ?>
  <?php  endif;  ?>
  
  

  <?php if($ams_preferences['expander_foreground']): ?>
  .ams-expander a .wp-menu-name {
    color:<?php echo $ams_preferences['expander_foreground']; ?>;    
  }
  #adminmenu .ams-expander.wp-has-current-submenu div.wp-menu-image:before {
    color:<?php echo $ams_preferences['expander_foreground']; ?>;        
  }
  <?php endif; ?>
  
  <?php echo apply_filters("ams_footer_css",""); ?>
  
</style>
      <?php
    }
    
    


    function ajax_autocomplete() {

        global $config_arr;
        
        $args['s'] = $_REQUEST['q'];
        $wp_query = new WP_Query($args);

        if($wp_query->have_posts()) {

            while($wp_query->have_posts()) {
                $wp_query->the_post();


                $id = get_the_ID();
                $title = get_the_title();
                $arr[]=array("label"=>$id.". ".$title,'value'=>$title,'id'=>$id);

            }
        }

        echo json_encode($arr);
        
        wp_die();
    }


    
    function init()
    {
        
      
      
      
      
        $user_id = get_current_user_id();
        
        if(isset($_GET['ams_export'])) {
            
            if( !isset($_REQUEST['ams_export_nonce']) OR !wp_verify_nonce($_REQUEST['ams_export_nonce'],'ams_export_nonce')) {
                echo "Nonce failed.";
                die;
            }
          
          $User = new AMS_User($user_id);
          $str = json_encode($User->get_all_data());
          
          //echo $str;
          //die;
          
          $handle = fopen("admin_menu_configuration.dat", "w");
          fwrite($handle, $str);
          fclose($handle);

          header('Content-Type: application/octet-stream');
          header('Content-Disposition: attachment; filename='.basename('admin_menu_configuration.dat'));
          header('Expires: 0');
          header('Cache-Control: must-revalidate');
          header('Pragma: public');
          header('Content-Length: ' . strlen($str));
          echo $str;
          exit;
    
        }
        
        if(isset($_POST['submit_ams_import_file'])) {
          
            if( !isset($_REQUEST['ams_import_nonce']) OR  !wp_verify_nonce($_REQUEST['ams_import_nonce'],'ams_import_nonce')) {
                echo "Nonce failed.";
                die;
            }
            
            //#=var_dump($_FILES);
          
          $new_data = file_get_contents($_FILES['ams_import_file']['tmp_name']);

          $user_id = get_current_user_id();
          $User = new AMS_User($user_id);
          
          
          $data = json_decode($new_data,true);
          
          $User->save_sorter_data($data['sorter']);
          
          $User->save_expanders($data['expanders']);

          $User->save_preferences($data['preferences']);
          
          wp_redirect(admin_url('admin.php?page=ams_import_export&import_success=1'));
          wp_die();
          
        }
        
        $ams_on = ams_is_on($user_id);
        
        if($ams_on) {
            define("AMS_ON",true);
        }
        else {
            define("AMS_ON",false);
        }
        

        if( isset($_POST['ams_preferences_submitted']) && $_POST['ams_preferences_submitted'] ) {

            if(!wp_verify_nonce($_REQUEST['preferences_nonce'],'preferences_nonce')) {
                echo "Nonce failed.";
                die;
            }
            
          if(apply_filters("ams_preferences_continue_save",true)) {

          $User = new AMS_User($user_id);

          $User->save_preferences($_POST);



          define("AMS_PREFERENCES_SUCCESS",1);

          }
          else {
            // let filter handle the saving instead.
          }

        }
        
        if( isset($_POST['ams_global_settings_submitted']) && $_POST['ams_global_settings_submitted'] ) {

            if(!wp_verify_nonce($_REQUEST['ams_global_nonce'],'ams_global_nonce')) {
                echo "Nonce failed.";
                die;
            }
            update_option("ams_minimum_capability",$_REQUEST['cap']);
            define("AMS_PREFERENCES_SUCCESS",1);
        }
        

        
    }
    
    
    function add_color_picker()
    {
        wp_enqueue_style('wp-color-picker');
    }
    
    
    
    function admin_menu()
    {
        $current_cap = get_option("ams_minimum_capability");
        if(!$current_cap)$current_cap="read";
        
        if(!current_user_can("manage_options")):
          $user_can_view = apply_filters("amo_current_user_can_view",true);
        else:
          $user_can_view = true; // if they'
        endif;
        
        if(!$user_can_view)return false;
      
        add_menu_page('Admin Menu Organizer', 'Admin Menu Organizer', $current_cap, 'ams_settings', array(
            $this,
            'ams_sorter_main_page'
        ), 'dashicons-tickets', 60);
        
        

        add_submenu_page('ams_settings', 'Admin Menu Organizer User Settings', 'User Settings', $current_cap, 'ams_preferences_page', array(
            $this,
            'ams_preferences_page'
        ));
        add_submenu_page('ams_settings', 'Import / Export', 'Import / Export', $current_cap, 'ams_import_export', array(
            $this,
            'ams_import_export_page'
        ));
        
        add_submenu_page('ams_settings', 'Admin Menu Organizer Global Settings', 'Global Settings', 'manage_options', 'ams_global_settings_page', array(
            $this,
            'ams_global_settings_page'
        ));
        
    }
    
    function ams_page_header() {
      

    }
    
    
    function ams_sorter_main_page()
    {
        global $title;
        
        print '<div class="wrap">';
        
        do_action("ams_page_header");
        
        print "<h1 class='ams_title'>$title</h1>";
        
        $file = __DIR__ . "/admin_pages/sorter.php";
        
        if (file_exists($file))
            require $file;
        
        print '</div>';
        
    }
    
    function ams_custom_pages_page()
    {
        global $title;
        
        print '<div class="wrap">';
        do_action("ams_page_header");
        print "<h1 class='ams_title'>$title</h1>";
        
        $file = __DIR__ . "/admin_pages/custom_pages.php";
        
        if (file_exists($file))
            require $file;
        
        print '</div>';
        
    }
    
    function ams_preferences_page()
    {
        global $title;
        
        print '<div class="wrap">';
        do_action("ams_page_header");
        print "<h1 class='ams_title'>$title</h1>";
        
        $file = __DIR__ . "/admin_pages/preferences.php";
        
        if (file_exists($file))
            require $file;
        
        print '</div>';
        
    }
    
    function ams_global_settings_page()
    {
        if(!current_user_can("manage_options"))return;
      
        global $title;
        
        print '<div class="wrap">';
        do_action("ams_page_header");
        print "<h1 class='ams_title'>$title</h1>";
        
        $file = __DIR__ . "/admin_pages/global.php";
        
        if (file_exists($file))
            require $file;
        
        print '</div>';
        
    }    
    
    function ams_import_export_page() {
        global $title;
        
        print '<div class="wrap">';
        do_action("ams_page_header");
        print "<h1 class='ams_title'>$title</h1>";
        
        $file = __DIR__ . "/admin_pages/import_export.php";
        
        if (file_exists($file))
            require $file;
        
        print '</div>';
      
    }
    
    static function get_page_data($page_id) {
      
      $menu_items = apply_filters("ams_filter_menu_global",$GLOBALS[ 'menu' ]);      
      //echo $page_id;
      
    //  echo "<hr/>";
      $actual_page_id = substr($page_id,11);
     // echo $actual_page_id;
  //    echo "<hr/>";
      if(is_array($menu_items)) {
      foreach($menu_items as $mu) {
        if(!isset($mu[5]))continue;
//        echo "<li>".$mu[5];
        if($mu[5]==$actual_page_id) {
          return $mu;
        }
        
      }
      }

    }
    
    function enqueue_admin_scripts()
    {
        
        wp_enqueue_script('ams_js', plugin_dir_url(__FILE__) . 'admin-menu-sorter.js', array(
            'wp-color-picker',
            'jquery',
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-mouse',
            'jquery-ui-droppable',
            'jquery-ui-draggable',
            'jquery-ui-sortable',
            'jquery-ui-autocomplete',
        ), '1.0');
        wp_localize_script('ams_js', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
        
//        wp_enqueue_script('jquery');
//        wp_enqueue_script('jquery-ui-core');
//        wp_enqueue_script("jqueryui");
        
        
        wp_register_style('ams_admin_css', plugin_dir_url(__FILE__) . 'admin-style.css', false, '1.0.0');
        wp_enqueue_style('ams_admin_css');
        
    }
    
    function main_generated_js()
    {
            if(!self::is_on())return "";

?>
<script type='text/javascript'>


jQuery(function() {

var current_expander=0;



<?php
        
        $user_id = get_current_user_id();
        $orig = get_user_meta($user_id, "ams_menu_setting", true);
        $setting = apply_filters("ams_load_setting", $orig);
        
        if(!$setting) {
          ?>
        jQuery("#adminmenu").show();
        console.log("showed!");
              
            });


        </script>
              
              
          <?php
          
          return false;
        }
        
        $menu_items = explode(" ", $setting);
        $counter    = 0;
        
        if (isset($menu_items[0]) && substr($menu_items[0], 0, 2) === "ex"):
            $expander_id = substr($menu_items[0], 2); /* if the first expander comes before a menu item, we have to add it here */ 
        
                echo "current_expander = ";
                echo $expander_id;
                echo ";";
                $prepend_expander_id_to_menu = $expander_id;
        endif;
        
        
        
        
        foreach ($menu_items as $menu_item):
            if (strpos($menu_item, "ams-sorter-") !== false):
                /* Assign each menu item its sort position */
              $menu_item = str_replace("/","-",$menu_item);
?>    
    jQuery("#<?php
                echo substr($menu_item, 11);
?>").attr("data-sort-pos","<?php
                echo $counter;
?>");
    
    <?php
                /* Do same for admin page if applicable */
?>     
    jQuery("#ams-sorter-<?php
                echo substr($menu_item, 11);
?>").attr("data-sort-pos","<?php
                echo $counter;
?>");
    <?php
                $counter++;
?>
       <?php
                /* break in case too many */
                if ($counter > 2000) {
                    break;
                }
?>
   <?php
            endif;
?>
<?php
        endforeach;
?>

jQuery("#collapse-menu").attr("data-sort-pos","1000");

    
var $people = jQuery('#adminmenu'),
    $peopleli = $people.children('li');

$peopleli.sort(function(a,b){
    var an = parseInt(a.getAttribute('data-sort-pos')),
        bn = parseInt(b.getAttribute('data-sort-pos'));

    if(an > bn) {
        return 1;
    }
    if(an < bn) {
        return -1;
    }
    return 0;
});

$peopleli.appendTo($people);





<?php
        
        $last_menu_item = "";
        foreach ($menu_items as $menu_item):
            if (strpos($menu_item, "ams-sorter-") !== false):
                $last_menu_item = substr($menu_item, 11);
            endif;
            
            if (strpos($menu_item, "ex") !== false):
                $expander_id = substr($menu_item, 2);
                if (!is_numeric($expander_id))
                    continue;
                $user_id       = get_current_user_id();
                $expander_data = $this->get_expander_data($expander_id);
                //var_dump($expander_data);
                //die;
                if (is_array($expander_data)) {
                    $expander_text = $expander_data['e_name'];
                    $expander_mode = $expander_data['e_mode'];
                    
                } else {
                    $expander_text = "Unnamed Expander";
                    $expander_mode = 0;
                }
                
                if(!$expander_text)$expander_text = "Unnamed Expander";
                
                if ($last_menu_item !== ""):
?>

jQuery("#adminmenu > #<?php
                    echo $last_menu_item;
?>").after('<li id="data-expander-<?php
                    echo $expander_id;
?>" data-expander-id="<?php
                    echo $expander_id;
?>" class="wp-has-current-submenu ams-expander ams-default-state-<?php
                    echo $expander_mode;
?> data-expander-<?php
                    echo ($expander_mode) ? "open" : "closed";
?>  expander-<?php
                    echo $expander_id;
?> menu-top menu-icon-arrow-down-alt2"><a href="#" class="wp-has-current-submenu menu-top menu-icon-comments"><div class="wp-menu-arrow"><div></div></div><div class="wp-menu-image dashicons-before dashicons-arrow-<?php
                    echo ($expander_mode) ? "up" : "down";
?>-alt2"><br></div><div class="wp-menu-name"><?php
                    echo $expander_text;
?><span class="awaiting-mod count-0"><span class="pending-count">0</span></span></div></a></li><li class="ams_sub_group" id="ams_sub_group_<?php
                    echo $expander_id;
?>" style="<?php
                    echo ($expander_mode) ? "display:block" : "display:none;";
?>"></li>');    

<?php
    
                endif;
?>

    <?php
            endif;
?>
<?php
        endforeach;
?>


jQuery('#adminmenu').children('li').not("#collapse-menu").each(function () {
    if(jQuery(this).hasClass("ams-expander")) {
        current_expander = jQuery(this).attr("data-expander-id");
    }
    else {
        jQuery(this).appendTo(jQuery("#ams_sub_group_"+current_expander));
        jQuery(this).attr("data-expander-id",current_expander);
        if(jQuery("#data-expander-"+current_expander).hasClass("ams-default-state-0")) {
        }
    }
});



<?php
        if (isset($prepend_expander_id_to_menu) && $prepend_expander_id_to_menu > 0):
            $user_id       = get_current_user_id();
            $expander_data = get_user_meta($user_id, "ams_menu_expander_{$prepend_expander_id_to_menu}_settings", true);
            if (is_array($expander_data)) {
                $expander_text = $expander_data['e_name'];
                $expander_mode = $expander_data['e_mode'];
            } else {
                $expander_mode = 0;
                $expander_text = "Expander $prepend_expander_id_to_menu";
            }
?>
   

<?php
            /* prepend list with the first expander if the very first item is an expander */
?>
jQuery("#adminmenu").prepend('<li id="data-expander-<?php
            echo $prepend_expander_id_to_menu;
?>" data-expander-id="<?php
            echo $prepend_expander_id_to_menu;
?>" class="wp-has-current-submenu ams-expander ams-default-state-<?php
            echo $expander_mode;
?> data-expander-<?php
            echo ($expander_mode) ? "open" : "closed";
?>   expander-<?php
            echo $prepend_expander_id_to_menu;
?> menu-top menu-icon-arrow-<?php
            echo ($expander_mode) ? "up" : "down";
?>-alt2"><a href="#" class="wp-has-current-submenu menu-top menu-icon-comments"><div class="wp-menu-arrow"><div></div></div><div class="wp-menu-image dashicons-before dashicons-arrow-<?php
            echo ($expander_mode) ? "up" : "down";
?>-alt2"><br></div><div class="wp-menu-name"><?php
            echo $expander_text;
?><span class="awaiting-mod count-0"><span class="pending-count">0</span></span></div></a></li><li class="ams_sub_group" id="ams_sub_group_<?php
            echo $prepend_expander_id_to_menu;
?>" style="<?php
            echo ($expander_mode) ? "display:block" : "display:none;";
?>"></li>');    

jQuery("[data-expander-id=<?php
            echo $prepend_expander_id_to_menu;
?>]").not(".ams-expander").not(".expander-form-opener").each(function() {
        jQuery(this).appendTo(jQuery("#ams_sub_group_<?php
            echo $prepend_expander_id_to_menu;
?>"));        
});

// wp-not-current-submenu 
<?php
        endif;
?>

jQuery(".ams-expander a").click(function() {
   ams_side_menu_toggle_menu(jQuery(this).parent().attr("data-expander-id"));
   return false;
});

jQuery("#adminmenu").append('<li id="sort-menu-link" data-sort-pos="1001"  class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-page" style="display: list-item;"><a href="<?php
        echo admin_url('admin.php?page=ams_settings');
?>" class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-sort" aria-haspopup="false"><div class="wp-menu-arrow"><div></div></div><div class="wp-menu-image dashicons-before dashicons-sort"><br></div><div class="wp-menu-name">Sort</div></a></li>');
  
jQuery("#adminmenu").append('<li id="expand-all-link" data-sort-pos="1002"  class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-page" style="display: list-item;"><a onclick="ams_expand_all();return false;" href="#" class="wp-has-submenu wp-not-current-submenu menu-top menu-icon-sort" aria-haspopup="false"><div class="wp-menu-arrow"><div></div></div><div class="wp-menu-image dashicons-before dashicons-arrow-down-alt2"><br></div><div class="wp-menu-name">Expand All</div></a></li>');
   
   
   /* open the expander if menu item is open */
   jQuery(".wp-menu-open").each(function() {
        var expander_id = jQuery(this).attr("data-expander-id");
        console.log("We should expand"+expander_id+" (menu-open)");
        ams_side_menu_toggle_menu(expander_id,1,true);    
    });
    jQuery(".current.menu-top").each(function() {
        var expander_id = jQuery(this).attr("data-expander-id");
        console.log("We should expand"+expander_id+" (current-menu-top)");
        ams_side_menu_toggle_menu(expander_id,1,true);    
    });
   
    setTimeout(function() {
      //jQuery(".ams_menu_loader").hide();
      console.log("as menu loading...");
      jQuery("#adminmenu").fadeIn();
    },1);
  
    });
    

</script>

    <?php
    }
    
    function sorter_page_generated_js()
    {
?>
<script type='text/javascript'>


jQuery(function() {


<?php
        
        $user_id = get_current_user_id();
        
        $orig = get_user_meta($user_id, "ams_menu_setting", true);
        
        
        
        $setting = apply_filters("ams_load_setting_on_sorter_page", $orig);
        
        $menu_items = explode(" ", $setting);
        $counter    = 0;
        
        
        if (isset($menu_items[0]) && substr($menu_items[0], 0, 2) === "ex"):
            $expander_id = substr($menu_items[0], 2); /* if the first expander comes before a menu item, we have to add it here */ 
        
            if (true) {
?>
       
        current_expander = <?php
                echo $expander_id;
?>;





<?php
                
                $prepend_expander_id_to_menu = $expander_id;
                
            }
        endif;
        
        
        
        
        foreach ($menu_items as $menu_item):
            if (strpos($menu_item, "ams-sorter-") !== false):
              
              
              $menu_item = str_replace("/","-",$menu_item);

                /* Assign each menu item its sort position */
?>    

    
    <?php
                /* Do same for admin page if applicable */
?>     
    jQuery("#ams-sorter-<?php
                echo substr($menu_item, 11);
?>").attr("data-sort-pos","<?php
                echo $counter;
?>");
    <?php
                $counter++;

                /* break in case too many */
                if ($counter > 200) {
                    break;
                }

            endif;

        endforeach;
?>



var $dnd = jQuery('#dnd_target'),
    $dndli = $dnd.children('div');

$dndli.sort(function(a,b){
  
  console.log(a);
  console.log(b);
    var an = parseInt(a.getAttribute('data-sort-pos')),
        bn = parseInt(b.getAttribute('data-sort-pos'));
        
        console.log("a="+a.getAttribute("id"));
        console.log("b="+b.getAttribute("id"));

    if(an > bn) {
        console.log("--- >>>> ----");
        return 1;
    }
    if(an < bn) {
        console.log("--- <<<< ----");
        return -1;
    }
    console.log("---- UMmmmmmmmmm");
    return 0;
});

$dndli.appendTo($dnd);



<?php
        
        $last_menu_item = "";
        foreach ($menu_items as $menu_item):
            if (strpos($menu_item, "ams-sorter-") !== false):
                $last_menu_item = substr($menu_item, 11);
            endif;
            
            if (strpos($menu_item, "ex") !== false):
                $expander_id = substr($menu_item, 2);
                if (!is_numeric($expander_id))
                    continue;
                $user_id       = get_current_user_id();
                $expander_data = get_user_meta($user_id, "ams_menu_expander_{$expander_id}_settings", true);
                //var_dump($expander_data);
                //die;
                
                $expander_data = $this->populate_missing_keys($expander_data,array("e_name","e_mode"));
                
                if (is_array($expander_data)) {
                    $expander_text = $expander_data['e_name'];
                    $expander_mode = $expander_data['e_mode'];
                    
                } else {
                    $expander_text = "Expand $expander_id";
                    $expander_mode = 0;
                }
                
                if ($last_menu_item !== ""):
?>

<?php
                    /* insert expander after the previous item */
?>
jQuery("#ex<?php
                    echo $expander_id;
?>").insertAfter(jQuery( "#dnd_target > #ams-sorter-<?php
                    echo $last_menu_item;
?>"));
    <?php
                endif;
?>

    <?php
            endif;
?>
<?php
        endforeach;
?>




<?php
        if (isset($prepend_expander_id_to_menu) && $prepend_expander_id_to_menu > 0):
            $user_id       = get_current_user_id();
            $expander_data = get_user_meta($user_id, "ams_menu_expander_{$prepend_expander_id_to_menu}_settings", true);
            if (is_array($expander_data)) {
                $expander_text = $expander_data['e_name'];
                $expander_mode = $expander_data['e_mode'];
            } else {
                $expander_mode = 0;
                $expander_text = "Expander $prepend_expander_id_to_menu";
            }
?>
   

<?php
            /* prepend list with the first expander if the very first item is an expander */
?>
 
jQuery("#ex<?php
            echo $prepend_expander_id_to_menu;
?>").prependTo(jQuery( "#dnd_target"));

// @NEXT
jQuery("[data-expander-id=<?php
            echo $prepend_expander_id_to_menu;
?>]").not(".ams-expander").not(".expander-form-opener").each(function() {
        jQuery(this).appendTo(jQuery("#ams_sub_group_<?php
            echo $prepend_expander_id_to_menu;
?>"));        
});

// wp-not-current-submenu 
<?php
        endif;
?>


  
    });
    

</script>

    <?php
    }
    
    function ams_admin_menu_custom()
    {
        $obj = (object) array(
            'page_title' => "P Title",
            'menu_title' => "M Title",
            'capability' => 'manage_options'
        );
        
        $ams_custom_pages = get_option("ams_custom_pages");
        
        if (is_array($ams_custom_pages)) {
            foreach ($ams_custom_pages as $key => $menu_page) {
                if (!isset($menu_page['ams_cp_page_title']) OR $menu_page['ams_cp_page_title'] == '')
                    continue;
                
                $slug = "ams{$key}";
                
                
                $ams_type = (isset($menu_page['ams_cp_type']))?$menu_page['ams_cp_type']:1;
                
                switch($ams_type) {
                  case 11:
                    
                    if (isset($menu_page['ams_existing_admin_page']) && $menu_page['ams_existing_admin_page']) {
                        $slug = $menu_page['ams_existing_admin_page'];

                        add_menu_page($menu_page['ams_cp_page_title'], $menu_page['ams_cp_menu_title'], $menu_page['ams_cp_cap_required'], $slug, '', "dashicons-" . $menu_page['ams_cp_icon']);

                    }
                    
                  break;
                  case 1:
                  case 2:
                  case 3:
                  case 4:
                    add_menu_page($menu_page['ams_cp_page_title'],$menu_page['ams_cp_menu_title'], $menu_page['ams_cp_cap_required'], $slug, array(
                        $this,
                        "call_page_function_" . $key
                    ), "dashicons-" . $menu_page['ams_cp_icon']);
                    
                  break;
                    
                }
                
            }
        }
        
        
        
        
    }
    
    
    public function __call($name, $arguments = array())
    {
        
        $ams_custom_pages = get_option("ams_custom_pages");
        
        
        if (strpos($name, "call_page_function_") !== false) {
            $menu_item_id = substr($name, 19);
            
            switch($ams_custom_pages[$menu_item_id]['ams_cp_type']) {
              
              case 2:
                if (isset($ams_custom_pages[$menu_item_id]['ams_post_id'])) {


                    echo "<div class='ams_custom_page_header'>";
                    echo "<h2>{$ams_custom_pages[$menu_item_id]['ams_cp_page_title']}</h2>";
                    echo "</div>";

                    echo "<div class='ams_custom_page_content'>";
                    $content_post = get_post($ams_custom_pages[$menu_item_id]['ams_post_id']);
                    $content      = $content_post->post_content;
                    $content      = apply_filters('the_content', $content);
                    $content      = str_replace(']]>', ']]&gt;', $content);
                    echo $content;
                    echo "</div>";

                    return;

                }
              break;
              
              case 3:
                    $content = $ams_custom_pages[$menu_item_id]['ams_content'];
                    echo do_shortcode($content);
                    
                    return;
                
              break;  
            
              case 4:
                    $content = $ams_custom_pages[$menu_item_id]['ams_iframe_src'];
                ?>
                <iframe class='ams_iframe'  width="420" height="315" src="<?php echo $content; ?>" frameborder="0" allowfullscreen></iframe>
                <?php
                
                    return;
              break;
              
            }
            
            // ams_content
            
        }
        
        // if we get here, we've called an invalid function. Uh oh.
        trigger_error("Non-existent function called on AMS_Plugin (there is no function called '{$name}' here. Sorry.", E_USER_ERROR);
        
        
    }
    
    function get_all_user_caps()
    {
        
        $data = get_userdata(get_current_user_id());
        
        if (is_object($data)) {
            $current_user_caps = $data->allcaps;
            
        } else {
            return false;
        }
        
        
        
        return array_combine(array_keys($current_user_caps), array_keys($current_user_caps));
    }
    
    
    function write_select_options(array $arr, $current_val = 0)
    {
        
        foreach ($arr as $key => $val) {
?>    
    <option value="<?php
            echo $key;
?>" <?php
            selected($key, $current_val);
?> ><?php
            echo $val;
?></option>
    <?php
        }
        
        
    }

    
    function get_all_menu_items()
    {
        
        $menu_items = $GLOBALS['menu'];
        $submenu    = $GLOBALS['submenu'];
        
        $arr = array();
        
        foreach ($menu_items as $menu_item) {
            
            if (isset($menu_item[4]) && $menu_item[4] === 'wp-menu-separator')
                continue;
            
            $arr["tl_" . $menu_item[2]] = $menu_item[0]; // add top level item to array
            
            if (isset($submenu[$menu_item[2]]) && is_array($submenu[$menu_item[2]])) {
                foreach ($submenu[$menu_item[2]] as $sub) {
                    $arr[$sub[2]] = "......" . $sub[0];
                }
            }
            
        }
        
        return $arr;
        
    }
    function get_preferences()
    {
        $user_id         = get_current_user_id();
        
        $User = new AMS_User($user_id);
        
        return apply_filters("ams_prepare_preferences_array",apply_filters("ams_user_preferences",$User->get_preferences()));

        
    }
    
    static function populate_missing_keys($arr,$keys,$val="") {
        
        $empty_arr = array_fill_keys($keys,$val);
        
        if(!is_array($arr))$arr = array();
        
        $final_array = array_merge($empty_arr,$arr);
        
        return $final_array;
    }
    
}

new AMS_Plugin();


function ams_is_on($user_id) {
  
  return apply_filters("is_ams_on",get_user_meta( $user_id, 'ams_menu_on' , true ));
  
}