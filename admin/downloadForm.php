<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('DownloadCategory.class.php','Download.class.php'));
$download = createObjAndAddToCol(new Download()); 
$downloadCategory = createObjAndAddToCol(new DownloadCategory()); 

$obj= $download;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'downloadList';

$rsItemFile = array();
$rsItemImage = array();
$arrPHPThumbHash = array();

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    
	$_POST['name'] = $rs[0]['name'];  
 
	$_POST['hidCategoryKey'] = $rs[0]['categorykey'];   
	if( !empty($rs[0]['categorykey'])){
        $rsCat = $downloadCategory->getDataRowById($rs[0]['categorykey']);
        $_POST['categoryName'] = $rsCat[0]['name'];  
    }
    
	$_POST['shortDesc'] = $rs[0]['shortdesc'];  
    
	$_POST['chkExternal'] = $rs[0]['useexternallink'];  
	$_POST['externalLink'] = $rs[0]['externallink'];  
    
    //update file   
	$rsItemFile = $obj->getItemFile($id);

	if(count($rsItemFile) > 0){
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
		$obj->deleteAll($destinationPath); 

		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);

		$obj->fullCopy($sourcePath,$destinationPath);  
	}
	
	$rsItemImage = array();  
	if( !empty($rs[0]['file'])){ 
        $rsItemImage[0]['phpthumbhash'] = getPHPThumbHash($rs[0]['file']);
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadImageFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadImageFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}

    
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
<script type="text/javascript"> 
      var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var opt = {};
    
      	opt.uploadFileFolder = "<?php echo $obj->uploadFileFolder; ?>"; 
        opt.fileUploaderTarget = "item-file-uploader"; 
        opt.arrFile =  <?php echo json_encode(array_column($rsItemFile,'file')); ?>;  
		
		opt.uploadImageFolder = "<?php echo $obj->uploadImageFolder; ?>";
        opt.imageUploaderTarget = "item-image-uploader"; 
        opt.arrImage = <?php echo json_encode(array($rs[0]['file'])); ?>;  
        opt.arrPHPThumbHash = <?php echo json_encode(array($rsItemImage[0]['phpthumbhash'])); ?>;  
		
        var download = new Download(tabID,opt);
    
        prepareHandler(download);   
        
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

                                    categoryName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.category[1]
                                            },  
                                        }
                                    }, 

                                } ; 
 
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  	 
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
                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                <div class="col-xs-9"> 
                      <?php echo $obj->inputText('name'); ?> 
                </div> 
            </div>  
           <!--  <div class="form-group">
                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                <div class="col-xs-9"> 
                     <?php  
                                $popupOpt = (!$isQuickAdd) ? array(
                                        'url' => 'downloadCategoryForm.php',
                                        'element' => array('value' => 'categoryName',
                                               'key' => 'hidCategoryKey'),
                                        'width' => '600px',
                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['downloadCategory'])
                                    )  : ''; 

                                echo $obj->inputAutoComplete(array(  
                                                        'objRefer' => $downloadCategory,
                                                        'element' => array('value' => 'categoryName',
                                                                           'key' => 'hidCategoryKey'),
                                                        'source' =>array(
                                                                            'url' => 'ajax-download-category.php',
                                                                            'data' => array(  'action' =>'searchData' , 'isleaf' => 1)
                                                                        ) ,
                                                        'popupForm' => $popupOpt
                                                      )
                                                );  
                    ?>
                </div> 
             </div>  -->
            <div class="form-group">
                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label> 
                <div class="col-xs-9"> 
                      <?php echo $obj->inputTextArea('shortDesc',array('etc' => 'style="height:10em;"' )); ?> 
                </div> 
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['externalLink']); ?></label> 
                <div class="col-xs-9"> 
                      <?php echo $obj->inputCheckBox('chkExternal'); ?> 
                </div> 
            </div> 
			
			<div class="form-group isexternal">
                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['link']); ?></label> 
                <div class="col-xs-9"> 
                      <?php echo $obj->inputText('externalLink'); ?>
                </div> 
            </div>

        </div> 
		  </div>
		  <div class="div-table-col">
			  <div class="div-tab-panel"> 
				 <div class="div-table" style="width:100%">
					<div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['image']); ?></div> 
					 <div class="div-table-row"> 
						<div class="div-table-col-5">
						  <!-- image uploader --> 
							<div class="item-image-uploader">
								<ul class="image-list"></ul>
								<div style="clear:both; height:1em;"></div>
								<div class="file-uploader">	
									<noscript>			
									<p>Please enable JavaScript to use file uploader.</p> 
									</noscript> 
								</div>
							  </div>  
							<!-- image uploader --> 
						</div> 
				   </div> 
				 </div>
			 </div>
			  <div class="div-tab-panel isinternal">  
				 <div class="div-table" style="width:100%"> 
					<div class="div-table-caption border-black"><?php echo ucwords($obj->lang['file']); ?></div> 
					 <div class="div-table-row"> 
						<div class="div-table-col-5">
						  <!-- file uploader --> 
							<div class="item-file-uploader">
								<ul class="file-list"></ul>
								<div style="clear:both; height:1em;"></div>
								<div class="file-uploader">	
									<noscript>			
									<p>Please enable JavaScript to use file uploader.</p> 
									</noscript> 
								</div>
							  </div>  
							<!-- file uploader --> 
						</div> 
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
