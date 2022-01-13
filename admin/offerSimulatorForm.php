<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('OfferSimulator.class.php');
$offerSimulator = createObjAndAddToCol( new OfferSimulator()); 

$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$item = createObjAndAddToCol( new Item()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$city = createObjAndAddToCol( new City()); 
$customer = createObjAndAddToCol( new Customer()); 

$obj = $offerSimulator;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'offerSimulatorList';


$editWarehouseInactiveCriteria = ''; 
$editCityInactiveCriteria = ''; 
$editCustomCodeInactiveCriteria = '';
 
$rsSalesDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y H:i');

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$totalWeight = 0;
 
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    
	$totalWeight = $obj->formatNumber(ceil($rs[0]['totalweight']/1000));  // selalu KG
	 
    $_POST['selCustomCode'] = $rs[0]['customcodekey']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y H:i');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$_POST['name'] = $rs[0]['name']; 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['customerEmail'] = $rsCustomer[0]['email'] ;
	$_POST['customerPhone'] = $rsCustomer[0]['phone'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['description'] = $rs[0]['description'];

	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);

	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    $editCustomCodeInactiveCriteria = ' or  '.$customCode->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customcodekey']); 
}

$rsKey = $obj->getTableKeyAndObj($obj->tableName);

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrCustomCode =  $obj->convertForCombobox($customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and ('.$customCode->tableName.'.statuskey = 1 ' . $editCustomCodeInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;

        
        offerSimulator = new OfferSimulator(tabID,<?php echo json_encode($rs); ?>);
        prepareHandler(offerSimulator); 
        
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

                                   customerName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.customer[1]
                                            }
                                        } 
                                    },

                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
    });
    
</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" > 
    <?php prepareOnLoadDataForm($obj); ?>   
     
       <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                   <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
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
                                            <?php echo $obj->inputDateTime('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>   
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('customerName', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>  
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('customerPhone', array('readonly' => true)); ?> 
                                        </div> 
                                    </div> 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('customerEmail', array('readonly' => true)); ?> 
                                        </div> 
                                    </div> 
                                 
                             </div>
                         
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                            <div class="form-group">
                                        <div class="col-xs-12"> 
                                            <?php echo  $obj->inputTextArea('description', array('etc' => 'style="height:10em;"')); ?>                                         </div> 
                            </div>
                            
                         </div>
                    </div>
                    
           </div>
      </div> 
      
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsSalesDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $arrUnit = $arrDefaultUnit;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                            $unitname = 'Pcs';
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber';

                            if ($rsSalesDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $inputnumber = 'inputdecimal';
                            } 

                            
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']);  
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['priceinunit']); 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['total']);  
                            $_POST['selUnit[]'] =  $rsSalesDetail[$i]['unitkey']; 
                            $_POST['hidGramasi[]'] =  $rsSalesDetail[$i]['weight']; 
                            $_POST['hidGramasiSubtotal[]'] =  $rsSalesDetail[$i]['weight'] *  $rsSalesDetail[$i]['qtyinbaseunit']; 
                                     
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsSalesDetail[$i]['itemkey']),'conversionunitkey','unitname','',array('relconversionmultiplier' => 'conversionmultiplier')); 
                 
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidGramasi[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidGramasiSubtotal[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite,'value' => 1, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ','readonly' =>true, 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php // echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
          <div style="float:right"> 
         
                 <div class="div-table" style="width: 100%">

                       <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;"> 
                                 <?php echo ucwords($obj->lang['total']); ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:180px;"> 
                                <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                            </div> 
                            <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col" ></div>

                        </div> 

                  </div>   
                 <div style="clear:both"></div> 
          </div>
         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php // echo $obj->generateSaveButton();?>

        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
