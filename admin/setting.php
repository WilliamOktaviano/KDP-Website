<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Setting.class.php');
$setting = createObjAndAddToCol(new Setting());

$obj= $setting;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$_POST['action'] = 'edit';

$categorykey = '';
if (isset($_GET) && !empty($_GET['categorykey']))
   $categorykey= $_GET['categorykey'];
else
    die;
 
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
	var folder = "<?php echo $obj->uploadFolder; ?>";
	var fileUploaderTarget = "item-image-uploader";
	 
	jQuery(document).ready(function(){   
     
        var tabID = selectedTab.newPanel[0].id ;  
        setOnDocumentReady(tabID); 
           
		 $('#defaultForm-' + tabID )
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
				alert("Pengaturan berhasil disimpan.");
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
   <form id="defaultForm" method="post" class="form-horizontal" action="updateSetting.php">
           <?php echo $obj->inputHidden('action'); ?>
           <?php echo $obj->inputHidden('hidSettingCategoryKey', array('overwritePost' => false, 'value' => $categorykey)); ?>
           <?php   
			$rsSettingCategory = $obj->getSettingCategory($categorykey);
			$contenttab = '';
			$script = '';
       
			for ($i=0;$i<count($rsSettingCategory);$i++) {
				 
				    $contenttab .= ' <div class="div-tab-panel"> '; 
					
			 		$rsContent = $obj->getSettingList($rsSettingCategory[$i]['pkey']);
					for ($k=0;$k<count($rsContent) ; $k++){ 
											  
                        if ($rsContent[$k]['code'] == '-'){
                            
                            $contenttab .= ' <div class="form-group">
                                                 <div class="col-xs-12"></div>  
                                            </div> '; 

                            continue;
                        }
                            
					
				 		if ($rsContent[$k]['multivalue'] == 1){
                              
                            
									$rsSettingDetail = $obj->getDetailById($rsContent[$k]['pkey']);
									for ($j=0;$j<count($rsSettingDetail); $j++){ 
										if ($j==0){   
											$_POST[$rsContent[$k]['code'].'Label[]'] =  $rsSettingDetail[$j]['label']; 
											$_POST[$rsContent[$k]['code'].'[]'] =  $rsSettingDetail[$j]['value']; 
										}else{  
										    $arrPostValue = array();
											array_push($arrPostValue,array("selector" => $rsContent[$k]['code'].'Label', "value" =>  $rsSettingDetail[$j]['label'])) ;
											array_push($arrPostValue,array("selector" => $rsContent[$k]['code'], "value" =>    $rsSettingDetail[$j]['value'])) ; 
											$script .=  'addNewTemplateRow("'.$rsContent[$k]['code'].'-row-template",\''.str_replace("'","\'",json_encode($arrPostValue)).'\');'; 
										}
									}
									
								
								$inputType = $obj->getInput($rsContent[$k]);
						 
                            
								$contenttab .= ' <div class="form-group"> 
                                                      <div class="col-xs-12">
                                                        <strong>'.$rsContent[$k]['description'].'</strong>
                                                      </div>  
                                                </div> ';  
						
						
								$contenttab .= ' <div class="form-group">
                                                    <div class="col-xs-3">
                                                        '.$obj->inputText($rsContent[$k]['code'].'Label[]').' 
                                                    </div>  
                                                    <div class="col-xs-9">
                                                        '.$inputType.' 
                                                    </div>  
                                                </div> '; 
										
								$inputType = $obj->getInput($rsContent[$k],true);
						
						  
								$contenttab .= ' <div class="form-group '.$rsContent[$k]['code'].'-row-template" style="display:none;" > 
                                                    <div class="col-xs-3">
                                                         '.$obj->inputText($rsContent[$k]['code'].'Label[]', array('disabled' => true)).' 
                                                    </div>  
                                                    <div class="col-xs-8">
                                                         '.$inputType.' 
                                                    </div>  
                                                    <div class="col-xs-1">'. $obj->inputLinkButton('btnDeleteDetail', $obj->lang['delete'], array('class' => 'btn btn-link remove-button')) .'</div>
                                        		</div> '; 
										
								$contenttab .= ' <div class="form-group" >
                                                     <div class="col-xs-3"></div>
                                                     <div class="col-xs-9">'.$obj->inputButton('btnAddRows'.$rsContent[$k]['code'], $class->lang['add']).'</div> 
                                                </div>  
                                                <div class="form-group" >
                                                     <div class="col-xs-12"></div> 
                                                </div>  
										'; 
								
								$script .= '   $("#defaultForm-"+ selectedTab.newPanel[0].id +" [name=btnAddRows'.$rsContent[$k]['code'].']").on(\'click\', function() {  addNewTemplateRow("'.$rsContent[$k]['code'].'-row-template",null); }); ';		
						
						}else{
							
							$_POST [$rsContent[$k]['code']] =  $obj->loadSetting($rsContent[$k]['code']) ; 
							if ($rsContent[$k]['type'] == 2 ) 
								 	$_POST [$rsContent[$k]['code']] = $obj->formatNumber($_POST [$rsContent[$k]['code']],0);
									
							$inputType = $obj->getInput($rsContent[$k]);
							$contenttab .= ' <div class="form-group">
                                                <div class="col-xs-3">'.$rsContent[$k]['description'].'</div> 
                                                <div class="col-xs-9">'. $inputType.'</div>  
                                            </div> ';  
						}
						
					}
					 
					
					$contenttab .= '</div> 
                    ';
			} 
			
            $script = '<script  type="text/javascript"> jQuery(document).ready(function(){   '.$script.'  }); </script> ';
			echo $contenttab . $script;
			
			  
			?> 
        
        <div style="clear:both"></div>
        
        <div class="form-button-panel" > 
         <?php   echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>
</div>
</body> 

</html>