<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Gallery.class.php','GalleryCategory.class.php','Customer.class.php')); 
$gallery = createObjAndAddToCol( new Gallery()); 
$customer = createObjAndAddToCol( new Customer()); 
$galleryCategory = createObjAndAddToCol( new GalleryCategory());  

$obj= $gallery;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'galleryList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$editCategoryInactiveCriteria = '';

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
	 
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['name'] = $rs[0]['name'];
	$_POST['isFeatured'] = $rs[0]['featured'];
    
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	if (!empty($rsCustomer)){
		$_POST['customerName'] = $rsCustomer[0]['name'] ;
		$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	}
    
    $editCategoryInactiveCriteria = ' or '.$galleryCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);

	 
	//update image 
	
	$rsItemImage = $obj->getGalleryImage($id);
	
	if(count($rsItemImage) > 0){
        
        for($i=0;$i<count($rsItemImage);$i++){
            $rsItemImage[$i]['phpthumbhash'] = getPHPThumbHash($rsItemImage[$i]['file']);
        }
        
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath);  
	}
    

	 
} 


$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  

$rsCat = $galleryCategory->searchData('','',true, ' and ('.$galleryCategory->tableName.'.statuskey = 1'. $editCategoryInactiveCriteria.')');
$arrCategory = $galleryCategory->generateComboboxOpt(array('data' => $rsCat )); 
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 

	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var gallery = new Gallery(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);
    
        prepareHandler(gallery);   
        
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
                                                message: phpErrorMsg.title[1]
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
                                    <div class="div-table-caption border-orange">Informasi Umum</div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Status</label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Kode</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Judul</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo  $obj->inputSelect('selCategory',$arrCategory); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Pelanggan</label> 
                                        <div class="col-xs-9"> 
                                              <?php    
                                                echo $obj->inputAutoComplete(array(     
                                                                                    'objRefer' => $customer,
                                                                                    'element' => array('value' => 'customerName',
                                                                                                       'key' => 'hidCustomerKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-customer.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'customerForm.php',
                                                                                                            'element' => array('value' => 'customerName',
                                                                                                                   'key' => 'hidCustomerKey'),
                                                                                                            'width' => '1000px',
                                                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['customer']
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                         <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featured']); ?></label> 
                                         <div class="col-xs-9"> 
                                               <?php echo $obj->inputCheckBox('isFeatured'); ?>   
                                         </div> 
                                    </div> 
                            </div>
                    </div>
             
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">  
                                    <div class="div-table-caption border-green">Deskripsi</div> 
                                    <div class="form-group"> 
                                        <div class="col-xs-12"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>  
                                        </div> 
                                    </div>   
                            </div>
                    </div>
                </div>
        </div> 
         
        <div class="div-table main-tab-table-1" style="width:100%;">
              <div class="div-table-row">
                    <div class="div-table-col">  
                         <div class="div-tab-panel">  
                             <div class="div-table-caption border-blue">Gambar</div>
                            <div class="form-group">
                                 <div class="col-xs-12"> 
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
            </div>
        </div>      
   
      
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
