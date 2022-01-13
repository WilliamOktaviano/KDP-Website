<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Brand.class.php'));
$brand = createObjAndAddToCol(new Brand()); 
 
$isActiveMarketplace = $class->isActiveModule('marketplace');

if($isActiveMarketplace)
	$marketplace = createObjAndAddToCol(new Marketplace()); 

$obj= $brand;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'brandList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$rs = prepareOnLoadData($obj);  
$rsBrandDetail = array();
$rsMarketplace = ($isActiveMarketplace) ? $marketplace->searchData('','',true,' and '.$marketplace->tableName.'.statuskey = 1') : array();

$rsItemImage = array(); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    $rsBrandDetail = $obj->getMarketplaceBrand($id);  
    $rsBrandDetail = array_column($rsBrandDetail, null, 'marketplacekey'); 
    
	$_POST['name'] = $rs[0]['name'];  
    $_POST['trDesc'] = $rs[0]['trdesc'];  
    $_POST['chkIsPublish'] = $rs[0]['publish']; 
    
		
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

if($isActiveMarketplace){
	
	includeClass(array('Storefront.class.php'));
	$storefront =  new Storefront();  
	$arrStoreFront = $storefront->searchDataRow(array($storefront->tableName.'.pkey',$storefront->tableName.'.name',$storefront->tableName.'.marketplacekey'),
												' and '.$storefront->tableName.'.statuskey = 1'
												);
	$arrStoreFront = $obj->reindexDetailCollections($arrStoreFront,'marketplacekey');  
	 
	$arrMPStorefrontOpt = array(); 
	foreach($rsMarketplace as $key => $row) 
		$arrMPStorefrontOpt[$row['pkey']] = isset($arrStoreFront[$row['pkey']]) ? $obj->generateComboboxOpt(array('data' => $arrStoreFront[$row['pkey']])) : array();  
	 
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
              
        var brand = new Brand(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);
    
        prepareHandler(brand);   
        
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
                                                message: phpErrorMsg.brand[1]
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
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['brand']); ?></label> 
                        <div class="col-xs-9"> 
                              <?php echo $obj->inputText('name'); ?> 
                        </div> 
                    </div>                     
                     <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label> 
                        <div class="col-xs-9"> 
                            <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                        </div> 
                    </div> 

                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featured']); ?></label> 
                        <div class="col-xs-9"> 
                              <?php echo $obj->inputCheckBox('chkIsPublish'); ?>  
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
            <?php if ($isActiveMarketplace && !$isQuickAdd){ ?> 
            <div class="div-table-col">	
            <div class="div-tab-panel"> 
                <div class="div-table-caption border-purple"><?php echo $obj->lang['marketplace']; ?></div>
                <div class="div-table transaction-detail  no-odd-even-style" style="width:100%"> 

                      <?php  
                          $totalRows = count($rsMarketplace); 
                          if($totalRows == 0)
                              echo '<div class="asterix-label">'.$obj->lang['noActiveMarketplace'].'</div>';
                              
                          for ($i=0;$i<$totalRows; $i++){  

                                $class =  'transaction-detail-row marketplace-brand-row';
                                $style = '';
                                $overwrite = true; 
                                $disabled = false;

                                if ($i == $totalRows ){
                                    $class = 'brand-row-template';
                                    $style = 'style="display:none"';
                                    $overwrite = false;
                                    $disabled = true;  
                                } else {    
                                    $marketplacekey = $rsMarketplace[$i]['pkey']; 
                                    $_POST['hidMarketplaceKey[]'] = $marketplacekey;  

                                    $_POST['hidDetailKey[]'] = '';
                                    $_POST['marketplaceBrandName[]'] = '';
                                    $_POST['hidMarketplaceBrandKey[]'] = '';

                                    if (isset($rsBrandDetail[$marketplacekey])){ 
                                        $_POST['hidDetailKey[]'] = $rsBrandDetail[$marketplacekey]['pkey']; 
                                        $_POST['marketplaceBrandName[]'] = $rsBrandDetail[$marketplacekey]['marketplacebrandname'];  
                                        $_POST['hidMarketplaceBrandKey[]'] = $rsBrandDetail[$marketplacekey]['marketplacebrandkey'];   
                                    }
                                }


                        ?>

                        <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                            <div class="div-table-col detail-col-detail" style="width: 150px">
                                <?php echo $rsMarketplace[$i]['name']; ?>
                                <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                <?php echo $obj->inputHidden('hidMarketplaceKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                            </div> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidMarketplaceBrandKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                <?php echo $obj->inputText('marketplaceBrandName[]', array('overwritePost' => $overwrite,'disabled' => $disabled )); ?>
                            </div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"></div>
                        </div> 

                        <?php } ?>
                </div>  
                <div style="clear:both; height:1em;"></div>   
              </div> 
				
			<div class="div-tab-panel"> 
                <div class="div-table-caption border-green"><?php echo $obj->lang['storefront']; ?></div>
                         <div class="div-table transaction-detail no-odd-even-style" style="width:100%"> 
                           
                              <?php  
                                  $rsStorefrontDetail = $obj->getMarketplaceStorefront($id);  
                                  $rsStorefrontDetail = array_column($rsStorefrontDetail, null, 'marketplacekey'); 
											 
                                  $totalRows = count($rsMarketplace); 
                                  if($totalRows == 0)
                                      echo '<div class="asterix-label">'.$obj->lang['noActiveMarketplace'].'</div>';

								  // buat liat marketplacekey jggn double
							      $existingMPkey = array();
								  $marketplacekey == 0;
											
                                  for ($i=0;$i<$totalRows; $i++){  

                                        $class =  'transaction-detail-row marketplace-storefront-row';
                                        $style = '';
                                        $overwrite = true; 
                                        $disabled = false;
 
                                        if ($i == $totalRows ){
                                            $class = 'storefront-row-template';
                                            $style = 'style="display:none"';
                                            $overwrite = false;
                                            $disabled = true;  
                                        } else {    
                                            $marketplacekey = $rsMarketplace[$i]['pkey'];  
											$_POST['hidStoreFrontMarketplaceKey[]'] = '';
                                            $_POST['hidDetailStorefrontKey[]'] = '';
                                            $_POST['marketplaceStorefrontName[]'] = ''; 
											$_POST['hidStorefrontKey[]'] = '';
											$_POST['hidStoreFrontMarketplaceKey[]'] = $marketplacekey; 
                                            
											// gk bisa pake isset
											// lebih baik loop per marketplacekey 
                                            if (!in_array($marketplacekey,$existingMPkey) && isset($rsStorefrontDetail[$marketplacekey])){  
												
												$arrStorefrontKey = json_decode($rsStorefrontDetail[$marketplacekey]['refstorefrontkey']);
												if(!is_array($arrStorefrontKey)) $arrStorefrontKey = array($arrStorefrontKey);
												
												$_POST['hidDetailStorefrontKey[]'] = $rsStorefrontDetail[$marketplacekey]['pkey'];
                                                $_POST['hidStorefrontKey[]'] = $arrStorefrontKey;  
                                                $_POST['marketplaceStorefrontName[]'] = $rsStorefrontDetail[$marketplacekey]['name'];  
												
												array_push($existingMPkey,$marketplacekey);
                                            } 
                                        }

                                ?>
 
                                <div class="div-table-row <?php echo $class; ?>" <?php echo $style; ?> >
                                    <div class="div-table-col detail-col-detail" style="width: 150px; vertical-align:top; padding-top:7px">
                                        <?php echo $rsMarketplace[$i]['name']; ?>
                                        <?php echo $obj->inputHidden('hidDetailStorefrontKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
										<?php echo $obj->inputHidden('hidStoreFrontMarketplaceKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?> 
                                    </div> 
                                    <div class="div-table-col detail-col-detail">
										<?php echo $obj->inputSelect('hidStorefrontKey[]', $arrMPStorefrontOpt[$marketplacekey],array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                                        <?php //echo $obj->inputHidden('hidStorefrontKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        <?php //echo $obj->inputText('marketplaceStorefrontName[]', array('overwritePost' => $overwrite,'disabled' => $disabled )); ?>
                                    </div> 
                                </div> 

                                <?php } ?>
                        </div>  
                        <div style="clear:both; height:1em;"></div>
              </div> 
            </div>
            <?php } ?>  
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
