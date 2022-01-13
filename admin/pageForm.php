<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
includeClass('Page.class.php');
$page = createObjAndAddToCol(new Page());

$obj= $page;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'pageList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editCategoryCriteria= '';
$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 

 	$arrChild  = $obj->getChildren($rs[0]['pkey']);
	//array_push($arrChild, $rs[0]['pkey']);
	if (!empty($arrChild)) 
		$editCategoryCriteria = ' and '.$obj->tableName.'.pkey not in ('.implode(",",$arrChild).')'; 
	$rsItemImage = array();  
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

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrCategory = $obj->searchData($obj->tableName.'.statuskey',1,true,$editCategoryCriteria );
$temp = count($arrCategory);
$arrCategory[$temp]['pagename'] = 'ROOT';
$arrCategory[$temp]['pkey'] = 0;


$arrCategory = $obj->convertForCombobox($arrCategory,'pkey','pagename');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript"> 

	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var page = new Page(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);
    
        prepareHandler(page);   
        
         var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                    pagename: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.page[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['pageName']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('pagename'); ?>  
                                        </div> 
                                     </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['parent']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selCategory',$arrCategory); ?>
                                        </div> 
                                    </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('title',array('multilang' => true )); ?>  
                                        </div> 
                                     </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['orderList']); ?></label> 
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
                                             <?php echo  $obj->inputTextArea('txtShortDescription', array('etc' => 'style="height:10em;"', 'multilang' => true)); ?> 
                                        </div> 
                                    </div>    
                            </div>
                    </div>    
             </div>
        </div>        
       
        <div class="div-table main-tab-table-1" style="width:100%">
                <div class="div-table-col">  
                        <div class="div-tab-panel">    
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['content']); ?></div>
                                <div class="form-group"> 
                                    <div class="col-xs-12"> 
                                            <?php echo  $obj->inputEditor('txtDetail',array('multilang' => true )); ?> 
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
