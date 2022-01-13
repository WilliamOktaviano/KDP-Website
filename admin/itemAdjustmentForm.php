<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ItemAdjustment.class.php');
$itemAdjustment = createObjAndAddToCol(new ItemAdjustment());
$itemCategory = createObjAndAddToCol(new ItemCategory());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$warehouse = createObjAndAddToCol(new Warehouse());
$obj= $itemAdjustment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'itemAdjustmentList'; 
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;  

$editWarehouseInactiveCriteria = ''; 
$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	   
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];
	   
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);   
} 

//$arrCategory = $obj->convertForCombobox($itemCategory->getLeafNodeWithPath(),'pkey','path');  
//$arrCategory[0] =  $obj->lang['allCategories']; 

$arrCategory = $obj->generateComboboxOpt(array('data' => $itemCategory->getLeafNodeWithPath(), 'label' => 'path'));  
$arrCategory[0] =  $obj->lang['allCategories'];

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrDefaultUnit = $itemUnit->generateComboboxOpt(null,array('criteria' => ' and ('.$itemUnit->tableName.'.statuskey = 1 )')); 
$arrUnit = $itemUnit->generateComboboxOpt(null,array('criteria' => ' and ('.$itemUnit->tableName.'.statuskey = 1 )')); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">   
      
	jQuery(document).ready(function(){  
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var itemAdjustment = new ItemAdjustment(tabID,<?php echo json_encode($rs); ?>);
    
         prepareHandler(itemAdjustment);   
        
         var fieldValidation =  {code: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.code[1] }, 
                                    }
                                 }
                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
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
                                <label class="col-xs-3 control-label">Status</label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                <div class="col-xs-9"> 
                                <?php echo $obj->inputAutoCode('code'); ?>
                                </div> 
                            </div>    
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                <div class="col-xs-9"> 
                                <?php echo $obj->inputDate('trDate'); ?>  
                                </div> 
                            </div>    
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                </div> 
                            </div>     
                     </div>
         			</div>
                    <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                                </div> 
                            </div>   
                        </div>
                    </div>
                </div>
                    
         </div>  
                        
      <div class="<?php echo $obj->hideOnDisabled(); ?>">
          <div style="float:left"><?php echo  $obj->inputSelect('selCategoryKey', $arrCategory); ?></div>
          <div style="float:left; margin-left:1em"><?php echo $obj->inputButton('btnImport',$obj->lang['import'],array('etc' => 'style="margin-top:0.2em"', 'class' => 'btn btn-primary btn-second-tone')); ?></div>
      </div>
        <div style="clear:both; height:2em;"></div> 
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['prevQty']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['newQty']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['adjustment']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:60px"></div>
					<div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['cogs']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:70px"></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>"  style="width:45px"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail); 
                    for ($i=0;$i<=$totalRows; $i++){   
                          
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"';
                            $baseunitname = 'Pcs';
                        } else { 
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname']; 
                            $_POST['qtyBefore[]'] =   $obj->formatNumber($rsDetail[$i]['qtybefore']); 
                            $_POST['qtyAfter[]'] =   $obj->formatNumber($rsDetail[$i]['qtyafter']); 
                            $_POST['qtyAdjust[]'] =   $obj->formatNumber($rsDetail[$i]['qtyadjust']);  
                            $_POST['COGS[]'] =   $obj->formatNumber($rsDetail[$i]['costinbaseunit']);  
                        }
                 ?>
                
                   <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyBefore[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div> <!--onChange="itemAdj.calculateTotal(this)" -->
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyAfter[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>  <!-- onChange="itemAdj.calculateTotal(this)"-->
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyAdjust[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><div class="text-muted"><span class="baseitemunit"><?php echo (!empty($rsDetail[$i]['baseunitname'])) ? $rsDetail[$i]['baseunitname'] : '';?></span></div></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('COGS[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><div class="text-muted"><span class="baseitemunit"><?php echo (!empty($rsDetail[$i]['baseunitname'])) ? $rsDetail[$i]['baseunitname'] : ''; ?></span></div></div>
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div> <!--onClick="itemAdj.calculateTotal()"-->
                   </div>
                     
                <?php }	  ?>
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
   
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	    <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>   
     <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
