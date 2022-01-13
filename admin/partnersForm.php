<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Partners.class.php');
$partners = createObjAndAddToCol(new Partners()); 
$obj= $partners;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'partnersList';
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 
$rs = prepareOnLoadData($obj);  
  
$rsItemImage = array(); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
   
    $ltdlng = explode(',',$rs[0]['maplocation']); 
    if(count($ltdlng) == 2){
        $posX = trim($ltdlng[0]);
        $posY = trim($ltdlng[1]);

        $adj = 5;

        // 1000 adalah lebar peta  - fixed - 
        $posXRelative = (($posX - $adj) / 1000) * 100 ; 

        // 370 adalah lebar peta  - fixed - 
        $posYRelative = (($posY - $adj)  / 370) * 100 ; 
         
    }

        
	if( !empty($rs[0]['file'])){
		$rsItemImage[0]['file'] =  $rs[0]['file'];
        $rsItemImage[0]['phpthumbhash'] = getPHPThumbHash($rsItemImage[0]['file']);
       
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}  
    
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript"> 
	
    jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var partners = new Partners(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);
     
        prepareHandler(partners);   
        
         var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                    name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.name[1]
                                            }, 
                                        }
                                    },
                                } ; 
 
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
        
    });
     
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
     <?php prepareOnLoadDataForm($obj); ?> 
     <?php echo $obj->generateLangOptions(); ?>
      
     <div class="div-table main-tab-table-2">
         <div class="div-table-row">
            <div class="div-table-col">	 
                 <div class="div-tab-panel">   
                    <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                        <div class="col-xs-9"> 
                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                        </div> 
                    </div> 

                     <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                        <div class="col-xs-9">
                             <?php echo $obj->inputAutoCode('code'); ?>
                        </div> 
                     </div>

                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                        <div class="col-xs-9"> 
                              <?php echo $obj->inputText('name', array('multilang' => true)); ?> 
                        </div> 
                    </div>  
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                        <div class="col-xs-9">  
                             <?php echo $obj->inputText('phone'); ?>
                        </div> 
                    </div> 
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                        <div class="col-xs-9">  
                              <?php echo $obj->inputTextArea('address',array('etc' => 'style="height:8em;"')); ?>
                        </div> 
                    </div>  
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['url']); ?></label> 
                        <div class="col-xs-9"> 
                              <?php echo $obj->inputText('url', array('etc' => 'placeholder="http://"')); ?>  
                        </div> 
                     </div>
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?><div style="font-size:0.9em;" class="text-muted">(Lat, Lng)</div></label> 
                        <div class="col-xs-9"> 
                            <?php echo  $obj->inputText('location'); ?>
                        </div> 
                    </div>  

                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featured']); ?></label> 
                        <div class="col-xs-9"> 
                              <?php echo $obj->inputCheckBox('chkIsFeatured'); ?>  
                        </div> 
                    </div>  
                     
                   <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
                        <div class="col-xs-9"> 
                             <!-- image uploader --> 
                            <div class="item-image-uploader">
                                <ul class="image-list" ></ul>
                                <div style="clear:both; height:1em; "></div>
                                <div class="file-uploader">	
                                    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                </div>
                              </div>  
                            <!-- image uploader --> 
                        </div> 
                    </div>  
                </div> 
            </div>  
             <div class="div-table-col">	 
                 <div class="div-tab-panel">    
                     <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                     <div class="form-group"> 
                        <div class="col-xs-12"> 
                            <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:15em;"','multilang' => true )); ?>
                        </div> 
                    </div> 
 
                </div> 
            </div> 
        </div>
     </div>  
      
<!--

         <div class="div-tab-panel">    
             <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['location']); ?></div>
             <div style="width: 1000px; margin:auto; margin-top:2em; ">
                 <div style="width: 25em"><?php echo  $obj->inputText('txtMapLocation',array( 'readonly' => true , 'etc' => 'style="text-align:center"' )); ?></div>
             </div>
             <div class="map-panel" style="cursor:crosshair; position:relative;  height: 370px; width: 1000px; margin:auto; border:1px solid #dedede; margin-top:1em; margin-bottom: 2em; background-position: center; background-repeat: no-repeat; background-size: contain; background-image:url('/include/img/map-indo.jpg')">
                <div class="marker" style="position: absolute; left: <?php echo $posXRelative; ?>%; top:  <?php echo $posYRelative; ?>%;  border:1px solid #333; height: 10px; width: 10px; background-color: #f00; border-radius: 50em;"></div>
             </div>
        </div>
-->

                     
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
