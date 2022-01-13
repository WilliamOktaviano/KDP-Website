<?php  
require_once '../_config.php';  
require_once '../_include-v2.php'; 

includeClass(array('GalleryCategory.class.php'));

$galleryCategory = createObjAndAddToCol( new GalleryCategory()); 

$obj= $galleryCategory;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'galleryCategoryList';
    
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rs = prepareOnLoadData($obj); 

$rsItemImage = array(); 

if (!empty($_GET['id'])){   
	$_POST['name'] = $rs[0]['name']; 
    $_POST['selCategory'] = $rs[0]['parentkey'];  
	$_POST['orderList'] = $obj->formatNumber($rs[0]['orderlist']);
    $_POST['trShortDesc'] = $rs[0]['shortdescription']; 
     
    
 	$arrChild  = $obj->getChildren($rs[0]['pkey']);
	array_push($arrChild, $rs[0]['pkey']);
	if (!empty($arrChild)) 
		$editCategoryCriteria = ' and '.$obj->tableName.'.pkey not in ('.implode(",",$arrChild).')'; 
    
    
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

$arrCategory = $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey = 1 ' .$editCategoryCriteria);
$temp = count($arrCategory);
$arrCategory[$temp]['name'] = 'ROOT';
$arrCategory[$temp]['pkey'] = 0;

$arrCategory = $obj->generateComboboxOpt(array('data' => $arrCategory));


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
          var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var galleryCategory = new GalleryCategory(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);
    
        prepareHandler(galleryCategory);   
        
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
                                                message: phpErrorMsg.category[1]
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
        
         <div class="div-table main-tab-table-2">
            <div class="div-table-row">
                <div class="div-table-col"> 
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']) ?></label> 
                            <div class="col-xs-9"> 
                                   <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  ucwords($obj->lang['code']) ?></label> 
                            <div class="col-xs-9">  
                                    <?php echo $obj->inputAutoCode('code'); ?>
                            </div> 
                        </div>    
                       <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['parent']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selCategory',$arrCategory); ?>
                                </div> 
                            </div>  
                        <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputText('name'); ?>
                                </div> 
                        </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['order']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputNumber('orderList'); ?>
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
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['shortDescription']); ?></div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                <?php echo  $obj->inputTextArea('trShortDesc',array('etc' => 'style="height:10em;"' )); ?>
                            </div> 
                        </div>   
                    </div>
                </div>
             </div>
             
             
        </div>     
               
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>  
      <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>