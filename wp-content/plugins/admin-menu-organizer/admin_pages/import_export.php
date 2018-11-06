<br/>

<?php
if(isset($_GET['import_success']) && $_GET['import_success']) {
    echo "Import is ok.";
}
?>
<br/>

<h3>Export Your Settings</h3>

<a href="<?php admin_url("admin.php"); ?>?page=ams_import_export&ams_export=1&ams_export_nonce=<?php echo wp_create_nonce("ams_export_nonce"); ?>">Download Export File</a>

<br/>
<br/>
<br/>

<h3>Import From File</h3>

<form method="post" enctype="multipart/form-data">
  <input type='hidden' name='ams_import_nonce' value='<?php echo wp_create_nonce("ams_import_nonce"); ?>'/>    
  <input type="hidden" name='submit_ams_import_file' value='1'/>
  <input name="ams_import_file" type="file"/>
  
  <input type="submit"/>
  
</form>
