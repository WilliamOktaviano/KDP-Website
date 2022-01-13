<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('WarehouseTransfer.class.php'));
$warehouseTransfer = new WarehouseTransfer();
$itemUnit = new ItemUnit();
$item = new Item();
$warehouse = new Warehouse();

$obj= $warehouseTransfer;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'warehouseTransferList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 
$destinationConversion = $obj->loadSetting('warehouseTransferConversion');

$editWarehouseInactiveCriteria = '';

$rsDetail = array();  
$rsDetailDestination = array();
$_POST['trDate'] = date('d / m / Y');
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsDetailDestination = $obj->getDetailWithRelatedInformationDestination($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseFromKey'] =$rs[0]['fromwarehousekey']; 
	$_POST['selWarehouseToKey'] =$rs[0]['towarehousekey']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];
	  
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['fromwarehousekey']);  
	$editWarehouseInactiveCriteria .= ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['towarehousekey']);   
} 
 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrDefaultUnit = $itemUnit->generateComboboxOpt(null,array('criteria' => ' and ('.$itemUnit->tableName.'.statuskey = 1 )')); 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style> 
    .total-sn-label {font-size: 0.9em; color:#999; font-style: italic}
    .tag-list li {height: 2em; text-align: center; }
    .transaction-detail>.div-table-row:nth-child(2n+3) .tag-list li {background-color: #dedede !important}
    .options-row .form-panel-result {max-height: 10em; overflow: auto}
</style>
<title></title> 
 
<script type="text/javascript">  
 
	  
	jQuery(document).ready(function(){  
        
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var warehouseTransfer = new WarehouseTransfer(tabID); 
         prepareHandler(warehouseTransfer);   
        
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['origin']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseFromKey', $arrWarehouse ); ?>   
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['destination']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selWarehouseToKey', $arrWarehouse); ?>
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
                        
        <div class="div-table transaction-detail" style="width:100%;">  
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
               		<div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div> 
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
        </div> 
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                
				<?php
                  	   
                    $totalRows = count($rsDetail); 
                    for ($i=0;$i<=$totalRows; $i++){  

                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        $txtSN = '';
                        $arrUnit = $arrDefaultUnit;                        
                        $showOptions = false;

                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {  
//                            $rsSN = $obj->getSerialNumber($rsDetail[$i]['pkey']);
//                            $arrSN = array_column($rsSN, 'serialnumber');
//                            
//                            if (!empty($rsSN)){ 
//                                $txtSN = "<ul  class=\"tag-list\">";
//                                
//                                for($j=0;$j<count($rsSN);$j++) 
//                                    $txtSN .= '<li>'.$rsSN[$j]['serialnumber'].'</li>';
//                           
//                                $txtSN .= "</ul>";
//                            }

                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['selUnit[]'] =  $rsDetail[$i]['unitkey']; 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);  

                            $_POST['snList[]'] =  implode(chr(13),$arrSN);
                            $_POST['hidNeedSN[]'] = $rsDetail[$i]['needsn']; 
                            $_POST['hidTempItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsDetail[$i]['itemkey']),'conversionunitkey','unitname'); 
        
				            $showOptions = (USE_SN && $rsDetail[$i]['needsn'] == 1) ? true : false;
                        }
                   ?> 
            
                   <div class="div-table-row  odd-style-adjustment  <?php echo $class; ?>">    
                       
                        <div class="div-table-col" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width: 100%">
                                         <div class="div-table-row"> 
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidNeedSN[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top; width:80px; "><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top; width:100px;"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"  style="width:35px;"><?php echo $obj->inputLinkButton('btnMoreOptions' , '<i class="fas fa-ellipsis-h"></i>', array('class' => 'btn btn-link btn-more-options','disabled' => true)); ?></div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                                </div> 
                            </div> 
                            <div class="options-row" style="<?php if (!$showOptions) { echo 'display:none;'; } ?>">
                                <div style="<?php if (!$showOptions) { echo 'display:none;'; } ?>" class="total-sn-label need-sn">Selisih SN : <span class="total-sn-remaining">0</span></div>
                                <div class="panel form-panel-result" <?php if(!empty($txtSN)) echo 'style="display:block"'; ?> >
                                    <?php echo $txtSN; ?>
                                    <div style="clear:both"></div>
                                </div>
                                <div class="panel form-panel" style="display:none; width: 100%">
                                    <div style="font-weight:bold"><?php echo $obj->lang['serialNumber']; ?></div>
                                    <?php echo  $obj->inputTextArea('snList[]', array('overwritePost' => $overwrite, 'etc' => 'attr-label="mnv-opt-sn"  style="height:10em;"')); ?>  
                                </div>
                               <div class="panel summary-panel" style="width:200px; float:left"></div>
                               <div style="clear:both"></div>  
                             </div>   
                        </div>
                   </div>
            
                <?php  }  ?> 
                     
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
