<?php
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $autoCode;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class


if(!$security->isAdminLogin($securityObject,10,true));
$_POST['action'] = 'edit';

$formAction = 'autoCodeList';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 

<script type="text/javascript">  

	jQuery(document).ready(function(){  
	 	
	 
		$("#" + selectedTab.newPanel[0].id + " #defaultForm").attr("id","defaultForm-"+selectedTab.newPanel[0].id);   
		 
		 $('#defaultForm-' + selectedTab.newPanel[0].id )
			.bootstrapValidator().on('success.form.bv', function(e) {
			
            // Get the form instance
             var $form = $(e.target);

             var btnSave = $form.find("[name=btnSave]");
  
             btnSave.prop('disabled', true);
             btnSave.find(".loading-icon").show();

            // Prevent form submission
            e.preventDefault();
 
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data 
			 $.post($form.attr('action'), $form.serialize(), function(result) { 
              
				alert("Pengaturan kode berhasil disimpan.");
				selectedTab.newTab[0].remove();
				$tabs.tabs("refresh");   
            }, 'json');
        });
		
});
	
	 
	  
</script>
</head>

<body>
<div style="width:100%; margin:auto; " class="tab-panel-form">
	<div class="notification-msg"></div>
	<form id="defaultForm" method="post" class="form-horizontal"   action="<?php echo $formAction; ?>">
		<?php echo $obj->input('hidden','action'); ?>
        
        <div class="div-table-tab-form" style="margin:auto; width:600px;">
         
         <div class="div-table-row form-group">
            <div class="div-table-col-5 div-table-col-header">
                <label class="col-lg-1 control-label"><strong>Modul</strong></label>
            </div> 
            <div class="div-table-col-5">
                <div class="col-lg-12"> 
                     <div style="float:left; padding:0 5px; width:40px; text-align:center"><strong>Auto</strong></div>
                     <div style="float:left; padding:0 5px; width:80px;"><strong>Prefix</strong></div>
                     <div style="float:left; padding:0 5px; width:80px;"><strong>Jml. Digit</strong></div>
                     <div style="float:left; padding:0 5px; width:80px;"><strong>Konter</strong></div>
                </div>
            </div> 
         </div>
         
		<?php 
		$rsAutoCode = $obj->searchData('','',true,'',' order by label asc'); 
		
		
		for ($i=0;$i<count($rsAutoCode);$i++) {
			 $autoChecked = '';
			 if (!empty($rsAutoCode[$i]['useautocode']))
			 	 $autoChecked = 'checked="checked"';
			
			 $_POST['hidId[]'] = $rsAutoCode[$i]['pkey'];	
			 $_POST['prefix[]'] = $rsAutoCode[$i]['prefix'];	 
			 $_POST['digit[]'] = $rsAutoCode[$i]['digit'];	  
			 $_POST['counter[]'] = $rsAutoCode[$i]['counter'];	 
		?>
		 	 
         <div class="div-table-row form-group">
            <div class="div-table-col-5">
                <label class="col-lg-1 control-label"><?php echo $rsAutoCode[$i]['label'];  echo $obj->input('hidden','hidId[]');?></label>
            </div> 
            <div class="div-table-col-5">
                <div class="col-lg-12"> 
                     <div style="float:left; padding:0 5px; width:40px; text-align:center"><input type="checkbox" value="1" <?php echo $autoChecked; ?> name="useautocode<?php echo $rsAutoCode[$i]['pkey']  ?>" /></div>
                     <div style="float:left; padding:0 5px; width:80px;"> <?php echo $obj->input('text','prefix[]'); ?></div>
                     <div style="float:left; padding:0 5px; width:80px;"> <?php echo $obj->input('text','digit[]'); ?></div>
                     <div style="float:left; padding:0 5px; width:80px;"> <?php echo $obj->input('text','counter[]'); ?></div>
                </div>
            </div> 
         </div>
			 
		
        <?php } ?>
			 
    </div>    
	<div style="clear:both"></div>
	
	<div class="form-button-panel" > 
	 <?php   echo $obj->generateSaveButton(); ?> 
	</div>
	
	</form>
</div>
</body>
</html>