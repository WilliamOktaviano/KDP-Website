<?php 
require_once '../_config.php';  
require_once '../_include-v2.php'; 

includeClass('Item.class.php');

$item = createObjAndAddToCol(new Item());

$isActiveMarketplace = $class->isActiveModule('marketplace');
$isActiveCOA = $class->isActiveModule('chartOfAccount'); 

if($isActiveMarketplace)
	$marketplace = createObjAndAddToCol(new Marketplace());

$warehouse = createObjAndAddToCol(new Warehouse());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$itemCondition = createObjAndAddToCol(new ItemCondition());
$itemCategory = createObjAndAddToCol(new ItemCategory());
$itemMovement = createObjAndAddToCol(new ItemMovement());
$brand = createObjAndAddToCol(new Brand());

if($isActiveCOA && USE_GL)
	$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());


$obj= $item;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$showContentOfPackage = $item->loadSetting('showItemContentOfPackage');
$showItemDescription = $item->loadSetting('showItemDescription');
$showItemFilter = $item->loadSetting('showItemFilter'); 
$showItemImage = $item->loadSetting('showItemImage');
$showMultiUnit = $item->loadSetting('showMultiUnit');
//$showVendorPartNumber = $item->loadSetting('showVendorPartNumber'); 
$useVariant = $class->loadSetting('useVariant'); 

$formAction = 'itemList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];
 
$editUnitInactiveCriteria = ''; 
$editConditionInactiveCriteria = '';
 
$rsItemDescription = array();
$rsItemUnitConversion = array();  
$rsItemFilter = array();
$rsItemPackageOfContentDetail = array();
$rsVendorPartNumber = array();
$rsSN = array();
$arrCondition = array();
$rsItemImage = array();
$rsItemFile = array();
$rsItemVariant = array();
$rsDetailVideo = array();
$arrPHPThumbHash = array();
$hasVariant = 0;
  
$allowChangeUnit = '';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj); 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber'; 

$_POST['chkNeedSN'] = 1;
$_POST['chkSyncAllMarketplace'] = 1; 
$_POST['chkSyncToMarketplace[]'] = 1;

$marketplaceObjs = ($isActiveMarketplace) ? $marketplace->getMarketplaceObj() : array();
                     
$rsWarehouseQOHCount = $warehouse->searchData($warehouse->tableName.'.isqohcount',1,true);
$rsWarehouseQOHCount = array_column($rsWarehouseQOHCount,'pkey');

if (!empty($_GET['id'])){ 
	 
    $id = $_GET['id'];
    //$rsVendorPartNumber = $obj->getVendorPartNumber($id);
    $rsDetailVideo = $obj->getDetailVideo($id);
    $rsSyncMarketplace = $obj->getItemSyncMarketplace($id);
    $rsSyncMarketplace = array_column($rsSyncMarketplace,null,'marketplacekey');
     
     foreach($marketplaceObjs as  $marketplaceRow){ 
        $marketplaceKey = $marketplaceRow['key'];  
        if(!isset($rsSyncMarketplace[$marketplaceKey]) || $rsSyncMarketplace[$marketplaceKey]['issync'] == 0){
            $_POST['chkSyncAllMarketplace'] = 0;
            break;
        } 
     }

    
    if (USE_SN)
        $rsSN = $obj->searchSerialNumber($id,'','', '', 'and warehousekey <> 0');
        
    $obj->lockUsedUnit($id);
	  
	$_POST['name'] = $rs[0]['name'];
	$_POST['tag'] = $rs[0]['tag']; 
	 
	$_POST['sellingPrice'] = $obj->formatNumber($rs[0]['sellingprice']); 
	
	$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
    $_POST['cogs'] = ($hasCOGSAccess) ?  $obj->formatNumber($rs[0]['cogs']) : 0 ;
      
	$_POST['selWeightUnit'] = $rs[0]['weightunitkey'];
    
	$_POST['minStockQty'] = $obj->formatNumber($rs[0]['minstockqty']);
	$_POST['maxStockQty'] = $obj->formatNumber($rs[0]['maxstockqty']); 
	$_POST['qtyOnHand'] = $obj->formatNumber($itemMovement->sumItemMovement($rs[0]['pkey'],$rsWarehouseQOHCount)); 
	$_POST['shortdescription'] = $rs[0]['shortdescription']; 
	$_POST['selBaseUnitKey'] = $rs[0]['baseunitkey']; 
	$_POST['selDefaultTransUnitKey'] = $rs[0]['deftransunitkey']; 
    $_POST['chkIsPublish'] = $rs[0]['publish'];

    $_POST['chkNeedSN'] = $rs[0]['needsn'];
    //$_POST['chkSyncAllMarketplace'] = $rs[0]['syncallmarketplace'];
    
    $_POST['length'] = $obj->formatNumber($rs[0]['length'],2);
	$_POST['width'] = $obj->formatNumber($rs[0]['width'],2);
	$_POST['height'] = $obj->formatNumber($rs[0]['height'],2);

    if ($rs[0]['commissiontype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
    
    $_POST['selCommissionType'] = $rs[0]['commissiontype'] ;
	$_POST['commissionValue'] = $obj->formatNumber($rs[0]['commission'],$finalDiscDecimal);
	$_POST['pointValue'] = $obj->formatNumber($rs[0]['pointvalue']);
    
    
	$_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $itemCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $itemCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
    
	$_POST['hidBrandKey'] = $rs[0]['brandkey']; 
    if (!empty($rs[0]['brandkey'])){
		$rsBrand = $brand->getDataRowById($rs[0]['brandkey']);
		$_POST['brandName'] = $rsBrand[0]['name'];
	}
    
	if($isActiveCOA && USE_GL){
		$_POST['hidInventoryCOAKey'] = $rs[0]['inventorycoakey']; 
		if (!empty($rs[0]['inventorycoakey'])){
			$rsCoa = $chartOfAccount->getDataRowById($rs[0]['inventorycoakey']);
			$_POST['InventoryCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
		}

		$_POST['hidInventoryTempCOAKey'] = $rs[0]['inventorytempcoakey']; 
		if (!empty($rs[0]['inventorytempcoakey'])){
			$rsCoa = $chartOfAccount->getDataRowById($rs[0]['inventorytempcoakey']);
			$_POST['InventoryTempCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
		} 

		$_POST['hidRevenueCOAKey'] = $rs[0]['revenuecoakey']; 
		if (!empty($rs[0]['revenuecoakey'])){
			$rsCoa = $chartOfAccount->getDataRowById($rs[0]['revenuecoakey']);
			$_POST['revenueCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
		}

		$_POST['hidCostCOAKey'] = $rs[0]['costcoakey']; 
		if (!empty($rs[0]['costcoakey'])){
			$rsCoa = $chartOfAccount->getDataRowById($rs[0]['costcoakey']);
			$_POST['costCOALink'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
		}
	}
    
    
    
    if($showContentOfPackage){ 
        $_POST['hidContentOfPackageKey'] = $rs[0]['contentofpackagekey']; 
        if (!empty($rs[0]['contentofpackagekey'])){
            $rsItemPackage = $itemChecklistGroup->getDataRowById($rs[0]['contentofpackagekey']);
            $_POST['contentOfPackageName'] = $rsItemPackage[0]['name'];
        }    
        $rsItemPackageOfContentDetail = $obj->getItemPackageOfContent($id);    
    }

    if($useVariant){ 

		$rsItemVariant = $obj->searchData($obj->tableName.'.isvariant',1,true,' and '.$obj->tableName.'.statuskey = 1 and '.$obj->tableName.'.parentkey ='.$rs[0]['pkey']);
		$hasVariant = (!empty($rsItemVariant)) ? 1 : 0;
		$_POST['hidBeforeParentItemKey'] = $rs[0]['parentkey'];
		$_POST['hidParentItemKey'] = $rs[0]['parentkey'];
        $_POST['chkIsVariant'] = $rs[0]['isvariant']; 
        $_POST['chkIsPrimary'] = $rs[0]['isprimary']; 
        if (!empty($rs[0]['parentkey']) && $rs[0]['isvariant']){
            $rsItemParent = $item->getDataRowById($rs[0]['parentkey']);
            $_POST['parentItemName'] = $rsItemParent[0]['name'];
        }      
    }
	      
    $_POST['gramasi'] = $obj->formatNumber($rs[0]['gramasi'],2);   
    $_POST['selCondition'] = $rs[0]['conditionkey'];
     
	$editUnitInactiveCriteria = ' or '.$itemUnit->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['baseunitkey']) .' or '.$itemUnit->tableName.'.pkey = '  . $obj->oDbCon->paramString($rs[0]['deftransunitkey']); 
	$editConditionInactiveCriteria = ' or '.$itemCondition->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['conditionkey']); 
  
    if ($showItemDescription) 
	   $rsItemDescription = $obj->getItemDescription($id);	
      
	 
/*    if ($showItemFilter) { 
	   $rsItemFilter = array_column($itemFilter->getDetailByColumn('itemkey',$id, true),'refkey');   
       $_POST['selFilter[]'] =  $rsItemFilter;
    } */
    
    if ($showMultiUnit) { 
        $rsItemUnitConversion = $obj->getAvailableUnit($id, ' and conversionunitkey <> baseunitkey', ' order by conversionmultiplier asc');	

        // lock by conversion 
        $lockedByConversion = false;
        for ($i=0;$i<count($rsItemUnitConversion); $i++){   
                if ($rsItemUnitConversion[$i]['islocked'] == 1){
                    $lockedByConversion = true;
                    break;
                } 
        }

        // coba cari yg aktif saja
        $rsMovement = $itemMovement->searchData('itemkey', $id, true,' and '. $itemMovement->tableName.'.statuskey = 1', 'limit 1');
        $allowChangeUnit = (empty($rsMovement) && !$lockedByConversion) ? '' : 'disabled="disabled"'; 
    }
    
    if ($showItemImage){
        //update image 
        $rsItemImage = $obj->getItemImage($id);

        if(count($rsItemImage) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
        }
        
        foreach($rsItemImage as $key=>$row) 
            array_push($arrPHPThumbHash,getPHPThumbHash($row['file']));
        
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
    }
	
			 
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWeight = $obj->generateComboboxOpt(array('data' => $obj->getSystemWeight()));  
$arrUnit = $itemUnit->generateComboboxOpt(null,array('criteria' =>' and ('.$itemUnit->tableName.'.statuskey = 1 ' . $editUnitInactiveCriteria. ')')); 
$arrCondition = $itemCondition->generateComboboxOpt(null,array('criteria' =>' and ('.$itemCondition->tableName.'.statuskey = 1 ' . $editConditionInactiveCriteria. ')')); 


/*if ($showItemFilter) { 
    $arrFilter = array();
    $rsItemFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey','1',true);

    for($i=0;$i<count($rsItemFilterCategory);$i++){
         $rsFilter = $itemFilter->searchData('categorykey',$rsItemFilterCategory[$i]['pkey'],true, ' and '.$itemFilter->tableName.'.statuskey = 1'); 
         $arrFilter[$rsItemFilterCategory[$i]['name']] = $class->convertForCombobox($rsFilter,'pkey','name') ;  
    }  
}*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
 
<style>
.item-filter  {list-style:none; padding:0; margin:0}
.item-filter li{float:left; margin: 0.2em 0.2em 0em 0.2em; display:inline-block; }    
.item-filter li label{width:150px; cursor:pointer;background-color:#dedede; padding:0.7em 1em 1em 1em;}
.item-filter li input {font-size:1.5em;margin-right:0.2em; }
    
.marketplace-attributes {list-style: none; margin: 0; padding: 0}
.marketplace-attributes li {width: 50%; float: left; display: inline-block; padding: 0.2em 1em}
</style>  

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript">   
 
    
  jQuery(document).ready(function(){  
        
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
            
        var opt = {};

        opt.usevariant = <?php echo ($useVariant) ? 'true' : 'false';?>;
        opt.showItemDescription = <?php echo ($showItemDescription) ? 'true' : 'false';?>;
        opt.rsItemDescription = <?php echo json_encode($rsItemDescription); ?>;
         
        opt.showMultiUnit = <?php echo ($showMultiUnit) ? 'true' : 'false';?>;
        opt.rsItemUnitConversion = <?php echo json_encode($rsItemUnitConversion); ?>;
        
        opt.showItemImage = <?php echo ($showItemImage) ? 'true' : 'false';?>;
        
        opt.uploadImageFolder = "<?php echo $obj->uploadFolder; ?>";
        opt.imageUploaderTarget = "item-image-uploader"; 
        opt.arrImage = <?php echo json_encode(array_column($rsItemImage,'file')); ?>;  
        opt.arrPHPThumbHash = <?php echo json_encode($arrPHPThumbHash); ?>;  
      
        opt.uploadFileFolder = "<?php echo $obj->uploadFileFolder; ?>"; 
        opt.fileUploaderTarget = "item-file-uploader"; 
        opt.arrFile =  <?php echo json_encode(array_column($rsItemFile,'file')); ?>;  
        opt.hasActiveMarketplace =  <?php echo ($isActiveMarketplace && $obj->hasActiveMarketplace()) ? 'true' : 'false'; ?>;  
        
        var cons = {};
        cons.MARKETPLACE = <?php echo json_encode(MARKETPLACE); ?>;  
            
        var item = new Item(tabID, opt, cons);
    
         prepareHandler(item);   
    
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

                                    gramasi: {
                                        validators: { 
                                            greaterThan: {
                                                value: -1,
                                                inclusive: false,
                                                separator: ',', 
                                                message: phpErrorMsg.gramasi[2]
                                            }
                                        }
                                    },

                                    sellingPrice: {
                                        validators: { 
                                            greaterThan: {
                                                value: -1,
                                                inclusive: false,
                                                separator: ',', 
                                                message: phpErrorMsg.sellingPrice[2]
                                            }, 
                                        }
                                    },

                                    maxStockQty: {
                                        validators: { 
                                            greaterThan: {
                                                value: -1,
                                                inclusive: false,
                                                separator: ',', 
                                                message: phpErrorMsg.maxStockQty[2]
                                            }, 
                                        }
                                    },

                                    minStockQty: { 
                                        validators: { 
                                            greaterThan: {
                                                value: -1,
                                                inclusive: false,
                                                separator: ',', 
                                                message: phpErrorMsg.minStockQty[2]
                                            }
                                        }
                                    }, 
                                } ; 
         
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
        
}); 
	  
</script>

<style>  
    .tag-list li {background-color: #ccc}  
</style>
</head> 

<body> 

<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
    <?php prepareOnLoadDataForm($obj); ?>  
    <?php echo $obj->inputHidden('hidParentKey'); ?>
    <?php echo $obj->inputHidden('hidBeforeParentItemKey'); ?>
      
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemCondition']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo  $obj->inputSelect('selCondition', $arrCondition); ?>
                                        </div> 
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                         <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $itemCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-item-category.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                        'url' => 'itemCategoryForm.php',
                                                                                                        'element' => array('value' => 'categoryName',
                                                                                                               'key' => 'hidCategoryKey'),
                                                                                                        'width' => '600px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['itemCategory'])
                                                                                                    ),
                                                                                'callbackFunction' => 'getTabObj().updateMarketplaceAttributes()' 
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['brand']); ?></label> 
                                        <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $brand,  
                                                                                'element' => array('value' => 'brandName',
                                                                                                   'key' => 'hidBrandKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-brand.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                        'url' => 'brandForm.php',
                                                                                                        'element' => array('value' => 'brandName',
                                                                                                               'key' => 'hidBrandKey'),
                                                                                                        'width' => '600px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['brand'])
                                                                                                    ),
                                                                                'callbackFunction' => 'getTabObj().updateMPBrandAttribute()'  
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['weight']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">          
                                                <div><?php echo  $obj->inputSelect('selWeightUnit', $arrWeight); ?></div>
                                                <div class="consume"><?php echo $obj->inputDecimal('gramasi'); ?></div>
                                             </div> 
                                        </div> 
                                    </div>   
                                      
                                        <div class="form-group">
                                        <label class="col-xs-3 control-label" style="margin-top:15px"><?php echo ucwords($obj->lang['size']); ?></label> 
                                        <div class="col-xs-3" style="padding-right:5px;">     
                                            <div class="text-muted"><?php echo ucwords($obj->lang['length']); ?></div>       
                                           <div class="flex">
                                               <div><?php echo $obj->inputDecimal('length'); ?></div>
                                               <div class="text-muted">CM</div>
                                            </div>   
                                        </div> 
                                        <div class="col-xs-3"  style="padding-right:10px; padding-left:10px">   
                                            <div class="text-muted"><?php echo ucwords($obj->lang['width']); ?></div>       
                                           <div class="flex">            
                                               <div><?php echo $obj->inputDecimal('width'); ?></div>
                                               <div class="text-muted">CM</div>
                                            </div>   
                                        </div> 
                                        <div class="col-xs-3"  style="padding-left:5px;">         
                                            <div class="text-muted"><?php echo ucwords($obj->lang['height']); ?></div>     
                                           <div class="flex">        
                                               <div><?php echo $obj->inputDecimal('height'); ?></div>
                                               <div class="text-muted">CM</div>
                                            </div>   
                                        </div> 
                                    </div> 
                                      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputTextArea('shortdescription', array( 'etc' => 'style="height:8em;"')); ?>
                                        </div> 
                                    </div> 
                                      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tag']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('tag'); ?>
                                        </div>
                                    </div> 
                                    
									<?php if ($useVariant) { ?>  
									<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['variantProduct']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputCheckBox('chkIsVariant'); ?>
                                        </div> 
                                    </div>  
								
									<div class="form-group isvariant">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['parent']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                            echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $item,  
                                                                                'element' => array('value' => 'parentItemName',
                                                                                                   'key' => 'hidParentItemKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-item.php',
                                                                                                    'data' => array(  'action' =>'searchData', 
                                                                                                                              'isparent' => 1 )
                                                                                                ),  
                                                                              'callbackFunction' => 'getTabObj().updateItemParent()'  
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>  
	                                <div class="form-group isvariant">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['primary']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputCheckBox('chkIsPrimary'); ?>
                                        </div> 
                                    </div>  
									<?php } ?>

                                    
                                    <div class="form-group">
                                        <?php if (PLAN_TYPE['usefrontend']) { ?>   
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featuredItem']); ?></label> 
                                            <div class="col-xs-3"> 
                                                 <?php echo $obj->inputCheckBox('chkIsPublish'); ?> 
                                            </div> 
                                        <?php } ?>
                                        
                                        <?php if (USE_SN) { ?>   
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['serialNumber']); ?></label> 
                                        <div class="col-xs-3" > 
                                             <?php echo $obj->inputCheckBox('chkNeedSN'); ?> 
                                        </div> 
                                        <?php } ?>
                                    </div>  
                                
                           </div> 
                         
                        <div class="div-tab-panel"> 
                          	<div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['transactionInformation']); ?></div>
                            
                                    <div class="col-xs-6"> 
                                        <div class="form-group">
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['cogs']); ?></label> 
                                            <div class="col-xs-12"> 
                                                <?php echo $obj->inputNumber('cogs', array('readonly' => true)); ?>
                                            </div> 
                                        </div>  
                                    </div>
                                    <div class="col-xs-6"> 
                                        <div class="form-group">
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['sellingPrice']); ?> / <span class="text-muted baseitemunit"></span></label> 
                                            <div class="col-xs-12"> 
                                                 <?php echo $obj->inputNumber('sellingPrice'); ?>
                                            </div> 
                                        </div>  
                                    </div> 
                                    <div class="col-xs-6"> 
                                        <div class="form-group">
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['commission']); ?></label> 
                                            <div class="col-xs-12"> 
                                                <div class="flex">
                                                    <div><?php echo  $obj->inputSelect('selCommissionType', $obj->arrDiscountType); ?></div>
                                                    <div class="consume"><?php echo $obj->inputNumber('commissionValue', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?></div>
                                                </div> 
                                            </div>  
                                        </div>  
                                    </div>  
                            
                                    <div class="col-xs-6"> 
                                        <div class="form-group">
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['point']); ?></label> 
                                            <div class="col-xs-12">  
                                                 <?php echo $obj->inputNumber('pointValue', array ('class'=> 'form-control ')); ?> 
                                            </div>  
                                        </div>  
                                    </div>  
                            
                                    <div style="clear:both; height:1em"></div>
                                    <div class="col-xs-6"> 
                                        <div class="form-group">
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['baseunit']); ?></label> 
                                            <div class="col-xs-12"> 
                                                <?php echo  $obj->inputSelect('selBaseUnitKey', $arrUnit, array( 'etc' => $allowChangeUnit)); ?>
                                            </div> 
                                        </div>  
                                    </div> 
                                    <div class="col-xs-6"> 
                                        <div class="form-group">
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['defaultTansactionUnit']); ?></label> 
                                            <div class="col-xs-12"> 
                                              <?php echo  $obj->inputSelect('selDefaultTransUnitKey', $arrUnit); ?>
                                            </div> 
                                        </div>  
                                    </div>
 
                                    <?php if ($showMultiUnit) { ?>
                                    <div class="div-table transaction-detail" style="width:100%; padding: 0 10px"> 
                                        
                                            <div class="div-table-row odd-style-adjustment"> 
                                                <div class="div-table-col" style="width: 10em; font-weight:bold"><?php echo $obj->lang['conversionUnit']; ?></div>
                                                <div class="div-table-col" ></div>
                                                <div class="div-table-col" style="width: 8em; text-align:right; font-weight:bold"><?php echo $obj->lang['conversion']; ?></div>
                                                <div class="div-table-col"></div>
                                                <div class="div-table-col" style="text-align:right; font-weight:bold"><?php echo $obj->lang['sellingPrice']; ?></div>
                                                <div class="div-table-col" style="width:25px;"></div> 
                                            </div>   
                                         
                                        <?php 
                                            $totalRows = count($rsItemUnitConversion);
                                            for ($i=0;$i<=$totalRows; $i++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false; 

                                                if ($i == $totalRows ){
                                                    $class = 'unit-conversion-row-template row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false;
                                                } else{ 
                                                    $_POST['hidConversionDetailKey[]'] =  $rsItemUnitConversion[$i]['pkey'];
                                                    $_POST['selConversionUnitKey[]'] =  $rsItemUnitConversion[$i]['conversionunitkey'];
                                                    $_POST['txtConversionMultiplier[]'] =  $obj->formatNumber($rsItemUnitConversion[$i]['conversionmultiplier']);
                                                    $_POST['unitSellingPrice[]'] =  $obj->formatNumber($rsItemUnitConversion[$i]['sellingprice']);

                                                    $isLocked = ($rsItemUnitConversion[$i]['islocked'] == 1) ? true : false;
                                                    $readonly = ($isLocked) ? ' readonly="readonly" ' : ''; 
                                                }

                                                   /* if ($rsItemUnitConversion[$i]['baseunitkey'] == $rsItemUnitConversion[$i]['conversionunitkey'])
                                                        continue;*/
                                                   
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?> odd-style-adjustment"> 
                                                <div class="div-table-col">
                                                        <?php echo $obj->inputHidden('hidConversionDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                        <?php echo $obj->inputSelect('selConversionUnitKey[]', $arrUnit, array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                </div>
                                                <div class="div-table-col"  style="line-height:3em">=</div>
                                                <div class="div-table-col"><?php echo $obj->inputNumber('txtConversionMultiplier[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled, 'etc' => 'style="text-align:right"')); ?></div>
                                                <div class="div-table-col baseitemunit" style="line-height:3em"></div>
                                                <div class="div-table-col" ><?php echo $obj->inputNumber('unitSellingPrice[]', array('overwritePost' => $overwrite , 'readonly' => $readonly, 'disabled' => $disabled , 'etc' => 'style="text-align:right"')); ?></div>
                                                <div class="div-table-col" style="text-align:center; line-height:3em">
                                                    <?php 
                                                    if ($isLocked)
                                                        echo '<div class="fas fa-lock"></div>';
                                                    else
                                                        echo $obj->inputLinkButton('btnDeleteUnitConversion','<i class="fas fa-times"></i>', array( 'class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0;"')); 
                                                   ?>  
                                                </div> 
                                            </div>   
                                        <?php }	 ?>   
                                    </div>  
                                    <div class="col-xs-12" style="text-align:center; margin-top:15px">  <?php echo $obj->inputButton('btnAddUnitConversion',$obj->lang['add'], array('class' => 'btn btn-primary btn-second-tone')); ?>  </div>

                                    <?php } ?>
                             
                         </div>  
                        
                      <?php if ($showItemImage) { ?>
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
                         <div class="div-tab-panel">  
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
                         <div class="div-tab-panel">
                             
                        <div class="div-table-caption border-black"><?php echo ucwords($obj->lang['video']); ?></div> 
                                <div class="form-group"> 
                                    <div class="col-xs-12">  
                                        <div class="div-table mnv-transaction transaction-detail" style="width: 100%">
                                        <?php 
                                            $totalRows = count($rsDetailVideo);
                                            for ($i=0;$i<=$totalRows; $i++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false; 
                                                //$style = '';

                                                if ($i == $totalRows ){
                                                    $class = 'detail-row-template row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false;
                                                    //$style = 'style="display:none"';
                                                } else{ 
                                                    $_POST['hidDetailVideoKey[]'] =  $rsDetailVideo[$i]['pkey'];
                                                    $_POST['url[]'] =  $rsDetailVideo[$i]['url'];
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row  odd-style-adjustment <?php echo $class; ?> "> 
                                                <div class="div-table-col"> 
                                                    <div class="flex">     
                                                        <div class="consume">
                                                            <?php echo $obj->inputHidden('hidDetailVideoKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled, 'etc' => 'placeholder="http://"')); ?>
                                                            <?php echo $obj->inputText('url[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled , 'etc' => 'placeholder="http://"')); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>
 
                                                    </div> 
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
                                    </div>
                                    </div> 
                                </div>
                                    
                        </div>
                      <?php } ?>
                        
                    </div>  
                    <div class="div-table-col"> 
                        
      <!--                  <?php if ($showVendorPartNumber) { ?> 
  				          <div class="div-tab-panel"> 
                                <div class="div-table-caption border-blue"><?php echo $obj->lang['vendorPartNumber']; ?></div>
                                    <div class="div-table transaction-detail vendor-part-number" style="width:100%">  
                                          <?php  
                                              $totalRows = count($rsVendorPartNumber); 

                                              for ($i=0;$i<=$totalRows; $i++){  

                                                    $class =  'transaction-detail-row';
                                                    $style = '';
                                                    $overwrite = true;
                                                    $etc = ''; 
 
                                                    if ($i == $totalRows ){
                                                        $class = 'vendor-part-number-row-template';
                                                        $style = 'style="display:none"';
                                                        $overwrite = false;
                                                        $etc = 'disabled="disabled"';  
                                                    } else {    
                                                        $_POST['hidDetailKey[]'] = $rsVendorPartNumber[$i]['pkey'];
                                                        $_POST['partNumber[]'] = $rsVendorPartNumber[$i]['partnumber'];     
                                                    }
                                                   
                                                   $readonlypartnumber = false;
                                                   $hideDeleteIcon = '';
                                                   if (isset($rsVendorPartNumber[$i]) && $rsVendorPartNumber[$i]['islock']){
                                                       $readonlypartnumber = true;
                                                       $hideDeleteIcon = 'display:none;';
                                                   }
                                          
                                            ?>

                                            <div class="div-table-row  odd-style-adjustment <?php echo $class; ?>" <?php echo $style; ?> >
                                                <div class="div-table-col detail-col-detail" style="width:300px;border:0"><?php echo $obj->inputText('partNumber[]' ,array('overwritePost' => $overwrite, 'readonly' => $readonlypartnumber, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>" style="width:25px; text-align:center"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>
                                            </div> 

                                            <?php } ?>
                                    </div>  
                         
                                    <div style="clear:both; height:1em;"></div>  
                                    <div class="div-table transaction-detail" style="width:100%;">
                                        <div class="div-table-row">
                                            <div class="div-table-col detail-col-detail">
                                                <?php echo $obj->inputButton('btnAddPartNumberRows',ucwords($obj->lang['addRows']), array('class' =>'btn btn-primary btn-second-tone')); ?>
                                            </div> 
                                        </div>     
                                    </div> 

                          </div> 
                         <?php } ?>-->
                        
                            <?php if ($showItemDescription) { ?> 
                                <div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
                          		<div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                                  <?php
								    $totalRows = count($rsItemDescription); 
                  
                                    for ($i=0;$i<=$totalRows; $i++){   
                                        $class =  'transaction-detail-row'; 
                                        $overwrite = true;
                                        $etc = ''; 
                                        $style = '';
                                        $editor = '';

                                        if ($i == $totalRows ){
                                            $class = 'item-description-row-template'; 
                                            $overwrite = false;
                                            $etc = 'disabled="disabled"'; 
                                            $style  = 'style="display:none"';
                                            $editor =  $obj->inputTextArea('txtDescription[]', array('overwritePost' => $overwrite, 'class' => 'ckeditor'));
                                        } else {  
                                            $_POST['txtDescriptionLabel[]'] =  $rsItemDescription[$i]['label'];
                                            $_POST['txtDescription[]'] =  $rsItemDescription[$i]['value']; 
                                            $editor =  $obj->inputEditor('txtDescription[]', array('overwritePost' => $overwrite));
                                        }
                                    ?>
 
                                        <div class="form-group <?php echo $class; ?>" <?php echo $style; ?>>
                                            <div class="col-xs-12"> 
                                                <?php echo $obj->inputText('txtDescriptionLabel[]',array('value' => 'Deskripsi Produk', 'overwritePost' => $overwrite, 'etc' => $etc)); ?> 
                                            </div>  
                                            <div class="col-xs-12"  style="margin-top:1em">  
                                                <?php echo  $editor; ?>  
                                            </div> 
                                            <div class="col-xs-12" style="text-align:right;margin-top:0.5em">
                                                 <?php echo $obj->inputLinkButton('btnDeleteDescription', $obj->lang['delete'],array('class' => 'btn btn-link remove-button')); ?> 
                                            </div>
                                        </div>   

 
								<?php } ?> 
                                 
                                <div class="col-xs-12" style="text-align:center">  <?php echo $obj->inputButton('btnAddDescription',$obj->lang['add'], array('class' => 'btn btn-primary btn-second-tone')); ?>   </div>
                                     
                              </div> 
                            <?php }  ?>  
                         
                   			<div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['stockInformation']); ?></div>  
                                    <div class="col-xs-4">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['qoh']); ?> <span class="text-muted">(<span class="baseitemunit"></span>)</span></label>    
                                            <div class="col-xs-12"><?php echo $obj->inputNumber('qtyOnHand', array('readonly' => true)); ?></div>  
                                        </div>   
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['minStock']); ?> <span class="text-muted">(<span class="baseitemunit"></span>)</span></label>   
                                             <div class="col-xs-12"><?php echo $obj->inputNumber('minStockQty'); ?></div>    
                                        </div>   
                                    </div>  
                                    <div class="col-xs-4">
                                        <div class="form-group">   
                                            <label class="col-xs-12 control-label"><?php echo ucwords($obj->lang['maxStock']); ?> <span class="text-muted">(<span class="baseitemunit"></span>)</span></label>    
                                             <div class="col-xs-12"><?php echo $obj->inputNumber('maxStockQty'); ?></div>    
                                        </div>   
                                    </div>
                                    <div style="clear:both; height: 1em"></div>
                                 
                                        <?php if ($hasVariant) { ?>
                                            <div class="div-table" style="margin:auto;  width:95%; "> 
                                                     <div class="div-table-row"> 
                                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;" ><strong><?php echo ucwords($obj->lang['code']); ?></strong> </div> 
                                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;" > <strong><?php echo ucwords($obj->lang['variant']); ?></strong>  </div> 
                                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" > <strong><?php echo ucwords($obj->lang['qty']); ?></strong></div> 
                                                     </div> 
                                                     <?php
                                                     for($i=0;$i<count($rsItemVariant);$i++){

                                                         $qoh = $obj->formatNumber($itemMovement->sumItemMovement($rsItemVariant[$i]['pkey'],$rsWarehouseQOHCount)); 
                                                         $colorClass= ($qoh == 0) ? "text-red-cardinal" : '';

                                                         echo '
                                                         <div class="div-table-row"> 
                                                             <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede;" > '.$rsItemVariant[$i]['code'].'</div> 
                                                             <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede;" > '.$rsItemVariant[$i]['name'].'</div>
                                                             <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede; text-align:right;" > '.$qoh.'</div> 
                                                         </div> 
                                                         ';
                                                     }
                                                    ?>
                                            </div>
                                        <?php } else {  
    
                                          echo '<div class="div-table" style="margin:auto;  width:95%; "> 
                                                     <div class="div-table-row"> 
                                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;" >   <strong>'.$obj->lang['warehouse'].'</strong> </div> 
                                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" >  <strong>'.ucwords($obj->lang['qty']).'</strong>  </div> 
                                                     </div>';

                                                     // yg normal
                                                     $rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true);
                                                     for($i=0;$i<count($rsWarehouse);$i++){

                                                         $qoh = 0;
                                                         $colorClass ="";

                                                         if (!empty($id)) 
                                                            $qoh = $obj->formatNumber($itemMovement->getItemQOH($id, $rsWarehouse[$i]['pkey']));

                                                         $colorClass= ($qoh == 0) ? "text-red-cardinal" : '';

                                                         echo '
                                                         <div class="div-table-row"> 
                                                             <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede;" >'.$rsWarehouse[$i]['name'].'</div> 
                                                             <div class="div-table-col-5 '.$colorClass.'" style="border-bottom:1px solid #dedede; text-align:right;" > '.$qoh.'</div> 
                                                         </div> 
                                                         ';
                                                      }
                                            echo '</div>';          
                                        }
                                    ?>
                            </div>
                        
                            <?php if ($isActiveCOA && USE_GL) { ?>  
                            <div class="div-tab-panel"> 
                                    <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['financialInformation']); ?></div>  
                                     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['inventoryAccount']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php 
                                                       echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $chartOfAccount,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'InventoryCOALink',
                                                                                               'key' => 'hidInventoryCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            )  
                                                                            )); 
                                             ?>    
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['inventoryTempAccount']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php 
                                                       echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $chartOfAccount,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'InventoryTempCOALink',
                                                                                               'key' => 'hidInventoryTempCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            )  
                                                                            )); 
                                             ?>    
                                        </div> 
                                    </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['revenueAccount']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php 
                                                       echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $chartOfAccount,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'revenueCOALink',
                                                                                               'key' => 'hidRevenueCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            )  
                                                                            )); 
                                             ?>    
                                        </div> 
                                    </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['expenseAccount']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php 
                                                       echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $chartOfAccount,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'costCOALink',
                                                                                               'key' => 'hidCostCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            )  
                                                                            )); 
                                             ?>    
                                        </div> 
                                    </div>
                                
                            </div>
                            <?php } ?>
                        </div>
             </div>   
            </div> 
  
       <div style="clear:both; height:2em;"></div>    
       
      
      <!-- MARKETPLACE -->
    <?php if ($isActiveMarketplace){ ?>  
    <div class="section-panel">  
       <div class="title"><?php echo $obj->lang['marketplace']; ?></div>
       <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                <div style="margin-left:0.8em"> <?php echo $obj->inputCheckBox('chkSyncAllMarketplace'); ?>  <?php echo $obj->lang['syncToAllMarketplaces']; ?></div>
                
                <div style="clear:both; height:0.5em"></div> 
                <div class="flex">
                    <div style="padding-left:1em"><?php echo $obj->lang['importAttributes']; ?></div>
                    <div style="width: 40em; padding-left: 1em">
                    <?php 
                            echo $obj->inputAutoComplete(array(  
                                    'element' => array('value' => 'itemImportName',
                                                       'key' => 'hidItemImportKey'),
                                    'source' => array(
                                                        'url' => 'ajax-item.php',
                                                        'data' => array(  'action' =>'searchData' )
                                                    ), 
                                    )); 
                     ?>    
                    </div>
                    <div><?php echo $obj->inputButton('btnImportMarketplaceAttribute', $obj->lang['import'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
                </div>
                <div style="clear:both; height:1em"></div> 
                <div style="margin-left:0.8em">
                <?php   
                     
                  foreach($marketplaceObjs as  $marketplaceRow){ 
                        $marketplaceKey = $marketplaceRow['key']; 
                        $marketplaceObj = $marketplaceRow['obj'];
                        
					  
                        $rsMarketplaceCategory = (isset($rs[0]['categorykey'])) ? $itemCategory->getMarketplaceCategory( $rs[0]['categorykey'],$marketplaceKey) : array(); 
                        $marketplacecategorykey = (!empty($rsMarketplaceCategory)) ?  $rsMarketplaceCategory[0]['marketplacecategorykey'] : 0 ;
                                
                        $_POST['hidSyncMarketplaceKey[]'] = $marketplaceKey;
                       
                        // data lama harus add manual didatabase
                        if(isset($rsSyncMarketplace))  
                            $_POST['chkSyncToMarketplace[]'] = $rsSyncMarketplace[$marketplaceKey]['issync'];
                       
                        echo '<div class="section-title"> '.$obj->inputHidden('hidSyncMarketplaceKey[]').' '.$obj->inputCheckBox('chkSyncToMarketplace[]').'    '.strtoupper($marketplaceRow['name']).'</div>';
                      
                       if($useVariant){
                           
                         $hidVariantDetailKeyName = 'hidVariantDetailKey[]';
                         $selVariantName = 'selVariant'.$marketplaceKey.'[]';
                         $selOptionName = 'selOption'.$marketplaceKey.'[]';
                             
                         //  reset  
                         $selectedVariantKey = 0;
                         $_POST[$hidVariantDetailKeyName] = '';
                         $_POST[$selOptionName] = '';
                           
                         if($marketplaceKey == MARKETPLACE['tokopedia']){//sementara
                              
							$rsVariantDetail = array();
                            $arrVariantOption = array();
                              
                            $parentkey = (isset($rs[0]['parentkey'])) ? $rs[0]['parentkey'] : 0;
							$marketplaceVariant = $marketplaceObj->getMarketplaceCategoryVariant($marketplacecategorykey, $parentkey); 
                            
                            // load unit option
							$arrVariant = $obj->convertForCombobox($marketplaceVariant,'key','label');
 
							if(!empty($rs[0]['pkey'])){    
								$rsVariantDetail = $obj->getMarketplaceVariantDetail($rs[0]['pkey'], $marketplaceKey);  
                                $selectedVariantKey = (!empty($rsVariantDetail[0]['variantkey'])) ? $rsVariantDetail[0]['variantkey'] : 0;
                                
								$_POST[$hidVariantDetailKeyName] = $rsVariantDetail[0]['pkey']; 
                                $_POST[$selVariantName] = $selectedVariantKey; 
                                 
                                $optkey = 0;
                                if(!empty($rsVariantDetail[0]['optionvalue'])){  
                                    $optvalue = $rsVariantDetail[0]['optionvalue']; 
                                    $optkey = json_decode(html_entity_decode($optvalue),true);    
                                    $optkey = array_keys($optkey)[0];
                                } 
                                $_POST[$selOptionName] = $optkey; 
							 }
                             
                            //load available options
                            $selectedVariantKey = (!empty($selectedVariantKey)) ? $selectedVariantKey : array_column($marketplaceVariant,'key')[0];
                            $rsVariantOption = array_column($marketplaceVariant,'rsVariant','key'); 
                            if (isset($rsVariantOption[$selectedVariantKey])) 
                                $arrVariantOption = $obj->convertForCombobox($rsVariantOption[$selectedVariantKey],'value_id','value');     
                           
                        ?>
					 
                            <div class="flex" style="padding: 0.2em 1em">
                                <div class="attribute-label" style="width:150px;vertical-align:top"> <?php echo $obj->lang['variantProduct']; ?></div>
                                <div class="consume">
                                    <div class="variant-row flex">
                                        <div style="min-width: 200px; vertical-align:top"><?php echo $obj->inputHidden($hidVariantDetailKeyName); ?><?php echo  $obj->inputSelect($selVariantName, $arrVariant, array('add-class' => 'select-variant', 'etc' => 'attr-marketplacekey="'.$marketplaceKey.'"')); ?></div>
                                        <div style="min-width: 200px" class="variant-option-col"><?php echo  $obj->inputSelect($selOptionName,$arrVariantOption); ?></div> 
                                    </div>
                                </div>    
                            </div>  
							
						 <?php 
						}else if ($marketplaceKey == MARKETPLACE['shopee']){ 
                             if(!empty($rs[0]['pkey'])){    
                                    $rsVariantDetail = $obj->getMarketplaceVariantDetail($rs[0]['pkey'], $marketplaceKey);   

                                    $_POST[$hidVariantDetailKeyName] = $rsVariantDetail[0]['pkey']; 
                                    $_POST[$selVariantName] = $rsVariantDetail[0]['variantkey'];  
                                    $_POST[$selOptionName] = $rsVariantDetail[0]['optionvalue']; 
							 }
                              

                            ?>
                             <div class="flex" style="padding: 0.2em 1em">
                                <div class="attribute-label" style="width:150px;vertical-align:top"> <?php echo $obj->lang['variantProduct']; ?></div>
                                <div class="consume">
                                    <div class="variant-row flex">
                                        <div style="min-width: 200px; vertical-align:top"><?php echo $obj->inputHidden($hidVariantDetailKeyName); ?><?php echo  $obj->inputText($selVariantName, array('etc' => 'placeholder="'.$obj->lang['variant'].'" attr-marketplacekey="'.$marketplaceKey.'"')); ?></div>
                                        <div style="min-width: 200px" class="variant-option-col"><?php echo $obj->inputText($selOptionName,array('placeholder="'.$obj->lang['option'].'"')); ?></div> 
                                    </div>
                                </div>    
                            </div>  
                       <?php  }  
                       }
					  	

                        echo '<ul class="marketplace-attributes">
                                <div class="attributes-'.$marketplaceKey.'"></div>';

					  	// logistics
	    				$rsAvailableLogistics = $marketplaceObj->getAvailableMarketplaceLogisticsForItem();

                        $arrCriteria = array();
                        array_push($arrCriteria, 'ismandatory = 1');

                        $criteria = implode(' and ', $arrCriteria);
                        $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   
            
                        // category dr marketplace_category_attributes 
                        $marketplaceAttributes = $marketplaceObj->getMarketplaceCategoryAttributes($marketplacecategorykey,$criteria);
                       
                        // ambil nilai yg sudah disimpan
                        // category dr item_marketplace_category_attributes 
                        $rsAttributes = array();
                          
                        if(!empty($rs[0]['pkey'])){  
                            $rsAttributes = $obj->getMarketplaceCategoryAttributes($rs[0]['pkey'], $marketplaceKey); 
                            $rsAttributes = array_column($rsAttributes,null,'attributekey');
							
							$rsLogisticsDetail = $obj->getMarketplaceLogistics($rs[0]['pkey'], $marketplaceKey); 
							$arrActiveShipment = array_column($rsLogisticsDetail,'reflogistickey'); 
                        }
                       
                  
                        foreach($marketplaceAttributes as $row){
                                $attributeKey = $row['attributekey'];
                             
                                $options = array();
                                if ($row['inputtype'] == INPUT_TYPE['select']) 
                                    $options = $marketplaceObj->getSelectOptions($row['value']);
                                else if ( $row['inputtype'] == INPUT_TYPE['autocompletejs'] )
                                    $options = array_keys($marketplaceObj->getSelectOptions($row['value']));
                           
                                $_POST['hidMarketplaceKey[]'] = $marketplaceKey; 
                                $_POST['hidCategoryAttributeKey[]'] = $attributeKey; 
                            
                                if (isset($rsAttributes[$attributeKey])){ 
                                    $_POST['hidAttributeDetailKey[]'] =  $rsAttributes[$attributeKey]['pkey'];  
                                    $_POST['attributeValue[]'] =  $rsAttributes[$attributeKey]['value'];   
                                }else{
                                    $_POST['hidAttributeDetailKey[]'] = 0;  
                                    $_POST['attributeValue[]'] = '';  
                                }
                                  
                                $label = $row['label'];
                            
                                $_POST['hidAttributeLabel[]'] = $label;
                                 
                                // attr-label sementara baru dibuat utk autocomplete
                                $input = $setting->getInput(array('multivalue' => 0, 'type' => $row['inputtype'], 'code' => 'attributeValue[]', 'options' => $options, 'etc' => 'attr-label="'.strtolower($label).'"' ));
                                $desc = (isset($row['description']) && !empty($row['description'])) ? '<div class="tag">'.$row['description'].'</div>' : '';
                                $class = (isset($row['class']) && !empty($row['class'])) ? $row['class'] : '';
                             
                        ?>
                           <li class="attributes-row category-attribute-row-template <?php echo $class; ?>" >
                            <div class="flex">
                                <div style="width:150px">
                                    <div class="attribute-label"><?php echo $label; ?> </div>
                                    <?php echo $obj->inputHidden('hidAttributeLabel[]'); ?>
                                    <?php echo $obj->inputHidden('hidAttributeDetailKey[]'); ?>
                                    <?php echo $obj->inputHidden('hidMarketplaceKey[]'); ?> 
                                    <?php echo $obj->inputHidden('hidCategoryAttributeKey[]'); ?>
                                </div>
                                <div class="consume attribute-value"><?php echo $input.$desc; ?></div>
                            </div>
                            </li>
            
                          
                     <?php   }
                          echo '</ul>';   
                      
                          echo '<div style="clear:both;"></div>';
					  
					  	 if(!empty($rsAvailableLogistics)){
						   echo '<div style="padding:1em">';	 
						   echo '<div class="attribute-label" style="margin-bottom:0.5em">'.$obj->lang['shipment'].'</div>';
						   echo '<div class="flex" style="align-items: flex-start; align-content:flex-start; flex-wrap: wrap">'; 
							   
						   foreach($rsAvailableLogistics as $logisticsRow){ 
									$_POST['chkLogisticsMarketplace[]'] = (empty($rs[0]['pkey']) || in_array( $logisticsRow['pkey'],$arrActiveShipment)) ? 1 : 0;
							?>
									<div >
									    <div  style="width: 200px">
											<?php echo $obj->inputCheckBox('chkLogisticsMarketplace[]'); ?> <?php echo $logisticsRow['name']; ?>
										</div> 
										<?php echo $obj->inputHidden('hidLogisticsMarketplaceDetailKey[]'); ?>
										<?php echo $obj->inputHidden('hidLogisticsMarketplaceKey[]',array('value' => $marketplaceKey)); ?>  
										<?php echo $obj->inputHidden('hidLogisticsRefKey[]',array('value' => $logisticsRow['pkey'])); ?>  
									</div> 
						<?php  } 
						   echo '</div>';
						   echo '</div>';
						 }  
					  
                          echo '<div style="clear:both; height: 1em"></div>';
                      }
                    ?> 
                    
                    
                       <li class="attributes-row category-attribute-row-template row-template">
                            <div class="flex">
                                <div style="width:150px">
                                    <div class="attribute-label"></div>
                                    <?php echo $obj->inputHidden('hidAttributeLabel[]', array('disabled' => true )); ?>
                                    <?php echo $obj->inputHidden('hidAttributeDetailKey[]', array('disabled' => true )); ?>
                                    <?php echo $obj->inputHidden('hidMarketplaceKey[]', array('disabled' => true )); ?> 
                                    <?php echo $obj->inputHidden('hidCategoryAttributeKey[]', array('disabled' => true )); ?>
                                </div>
                                <div class="consume attribute-value"></div>
                            </div>
                        </li>

                </div>  
       </div>   
       <div style="clear:both"></div>    
		
    </div>
    <?php } ?>
      
    <?php if ($showContentOfPackage) { ?>
    <div class="section-panel pale-purple-scheme">  
       <div class="title"><?php echo ucwords($obj->lang['contentOfPackage']); ?></div>
       <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
            <div style="clear:both; height:0.5em"></div>
            <div class="form-group"> 
                <div class="col-xs-6"> 
                <?php    
                    echo $obj->inputAutoComplete(array(
                                                        'revalidateField' => false, 
                                                        'objRefer' => $itemChecklistGroup,
                                                        'element' => array('value' => 'contentOfPackageName',
                                                                           'key' => 'hidContentOfPackageKey'),
                                                                           'source' =>array(
                                                                                            'url' => 'ajax-item-checklist-group.php',
                                                                                            'data' => array(  'action' =>'searchData', 'statuskey' => 1)
                                                                                            ),
                                                                                            'callbackFunction' =>  'getTabObj().importContentOfPackage()'
                                                                                        )
                                                                                );  
                ?> 
                </div>  
            </div>
            <div class="div-table blue-scheme mnv-transaction transaction-detail package-list" style="width:100%;">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="border:0"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:90px; border:0"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>"  style="border:0"></div>
                </div>

                <?php 
                    $totalRows = count($rsItemPackageOfContentDetail); 

                    for ($i=0;$i<=$totalRows; $i++){  

                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = ''; 
                        $style = '';

                        if ($i == $totalRows ){
                            $class = 'detail-content-of-package-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $style = 'style="display:none"';
                        } else {
                            $_POST['hidItemPackageKey[]'] =  $rsItemPackageOfContentDetail[$i]['itemkey'];
                            $_POST['itemPackageName[]'] =  $rsItemPackageOfContentDetail[$i]['itemchecklistname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsItemPackageOfContentDetail[$i]['qty']); 
                        } 

                ?>


                <div class="div-table-row <?php echo $class; ?> "   <?php echo $style; ?>>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemPackageName[]',array('overwritePost' => $overwrite, 'etc' =>$etc)); ?><?php echo $obj->inputHidden('hidItemPackageKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?></div>
                </div>

                <?php } ?> 

            </div>      
            <div style="clear:both; height:1em;"></div> 
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddPackageRows', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
        </div>   
        <div style="clear:both;"></div>       
    </div>        
    <?php } ?>
      
      <?php if (USE_SN) { ?>
        <div class="section-panel green-scheme ">  
           <div class="title"><?php echo ucwords($obj->lang['serialNumber']); ?></div>
           <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                <div style="clear:both; height:0.5em"></div>
                <ul class="tag-list">
                <?php for ($i=0;$i<count($rsSN);$i++){ 
                        echo '<li>'.$rsSN[$i]['serialnumber'].'</li>';
                } ?>
                </ul>
            </div>   
            <div style="clear:both;"></div>       
        </div>   
      <?php } ?> 
      
      <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>   
    <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
