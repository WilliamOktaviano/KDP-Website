<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('SalesDelivery.class.php'));   
$salesDelivery = createObjAndAddToCol( new SalesDelivery()); 
$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$item = createObjAndAddToCol( new Item()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod());   
$customer = createObjAndAddToCol( new Customer()); 
$salesOrder =  createObjAndAddToCol( new SalesOrder()); 
$supplier =  createObjAndAddToCol( new Supplier()); 
	
$obj= $salesDelivery;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'salesDeliveryList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title']; 

$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rsSalesDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
	$_POST['trDesc'] = $rs[0]['trdesc'];
    
    if (!empty($rs[0]['supplierkey'])){
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['supplierName'] = $rsSupplier[0]['name'] ;
        $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ; 
    }
    
    $rsSalesOder = $salesOrder->getDataRowById($rs[0]['refkey']);
    $_POST['hidSalesOrderKey'] = $rsSalesOder[0]['pkey']; 
    $rsCustomer = $customer->getDataRowById($rsSalesOder[0]['customerkey']);
    $_POST['salesOrderCode'] = $rsSalesOder[0]['code'] . ' - ' . $rsCustomer[0]['name'] ;
	$_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);  
    $_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'];
    $_POST['balance'] = $obj->formatNumber($rs[0]['balance']); 
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']); 
    
    $editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
	 
}
 

$rsTOP = $termOfPayment->searchDataRow( array($termOfPayment->tableName.'.pkey', $termOfPayment->tableName.'.name', $termOfPayment->tableName.'.duedays')
									   , ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrTOP = $obj->generateComboboxOpt(array('data' => $rsTOP));
 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status')); 
$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null,array('criteria' => ' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')')); 
$arrUnit = $itemUnit->generateComboboxOpt(null,array('criteria' => ' and ('.$itemUnit->tableName.'.statuskey = 1 )'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">   
	  
     	jQuery(document).ready(function(){   
        var tabID = selectedTab.newPanel[0].id;
        var cashTOP = Array();
        <?php 
        for ($i=0;$i<count($rsTOP);$i++){
            if ($rsTOP[$i]['duedays'] <> 0)
                echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
        }
        ?>  

        salesDelivery = new SalesDelivery(tabID,<?php echo json_encode($rs); ?>,cashTOP);
        prepareHandler(salesDelivery); 
           
        var fieldValidation =  {
                                   code: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.code[1]
                                                }, 
                                            }
                                        }, 

                                         salesOrderCode: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.salesOrder[1]
                                                }, 
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
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['soCode']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php  echo $obj->inputAutoComplete(array( 
                                                                        'objRefer' => $salesOrder,
                                                                        'revalidateField' => true,
                                                                        'element' => array('value' => 'salesOrderCode',
                                                                                           'key' => 'hidSalesOrderKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-sales-order.php',
                                                                                            'data' => array(  'action' =>'searchData', 'isfulldeliver' => 0 )
                                                                                        ) ,
                                                                        'callbackFunction' => 'getTabObj().importData()'
                                                                      )
                                                                );  
                                    ?>
                                    </div> 
                                </div>    
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shippingCourier']); ?></label> 
                                    <div class="col-xs-9"> 
                                       <?php  echo $obj->inputAutoComplete(array( 
                                                                        'objRefer' => $supplier, 
                                                                        'element' => array('value' => 'supplierName',
                                                                                           'key' => 'hidSupplierKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-supplier.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ) ,
                                                                        'popupForm' => array(
                                                                                            'url' => 'supplierForm.php',
                                                                                            'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                            'width' => '1000px',
                                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                        ),
                                                                        'callbackFunction' => 'getTabObj().updateTOP()'
                                                                      )
                                                                );  
                                    ?>
                                    </div> 
                                </div>    
                                 
                            </div>
                    </div>
                    
                    <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div> 
                            </div>   
                        </div>
                    </div>


                </div>       
            </div>    
                  
        
        <div class="div-table mnv-transaction transaction-detail sales-delivery-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['orderedQty']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['deliveredQty']); ?></div>  
                    <div class="div-table-col detail-col-header"  style="width:80px"></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>  icon-col" ></div> 
                </div>
                
				<?php 
                            
                    $totalRows = count($rsSalesDetail); 
                    for ($i=0;$i<=$totalRows; $i++){  
					
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = ''; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $unitname = '';
                        } else { 
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
                            $unitname = $rsSalesDetail[$i]['baseunitname'];
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                            $_POST['hidSODetailKey[]'] = $rsSalesDetail[$i]['refsodetailkey']; 
                            $_POST['hidItemKey[]'] = $rsSalesDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] = $rsSalesDetail[$i]['itemname']; 
                            $_POST['orderedQtyInBaseUnit[]'] = $obj->formatNumber($rsSalesDetail[$i]['orderedqtyinbaseunit']);
                            $_POST['qtyMinusInBaseUnit[]'] = $obj->formatNumber($rsSalesDetail[$i]['qtyminusinbaseunit']); 
                            $_POST['deliveredQtyInBaseUnit[]'] = $obj->formatNumber($rsSalesDetail[$i]['deliveredqtyinbaseunit']);  
                        }
                        
                   ?>    
           
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'readonly' => true, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidSODetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('orderedQtyInBaseUnit[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyMinusInBaseUnit[]', array('overwritePost' => $overwrite,'readonly' => true,  'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('deliveredQtyInBaseUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><div class="text-muted"><span class="baseitemunit"><?php echo $unitname;?></span></div></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                </div>
               
                <?php } ?> 
                   
         </div>                            
         <div style="clear:both; height:1em;"></div>  
      
         <div style="width:350px; float:right; ">
                    
                        <div class="div-table" style="width:100%" >
                             <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo ucwords($obj->lang['shippingFee']); ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
                                     <?php echo $obj->inputNumber('shipmentFee', array('etc' => 'style="text-align:right;" ')); ?>
                                </div> 
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div> 
                      </div> 
                      <div class="div-table" style="width:100%; margin-top:1em" > 
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo ucwords($obj->lang['payment']); ?> 
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
                                     <?php echo  $obj->inputSelect('selTermOfPaymentKey',$arrTOP); ?>
                                </div>  
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>
                      </div>

                    <div class="mnv-total-group mnv-payment-method cashTOP "  >  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>

                        <div class="mnv-total-group-detail">
                            <div class="div-table  transaction-detail" style="width: 100%">
                                <?php 

                                    $totalRows = count($rsPaymentMethodDetail); 

                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'payment-method-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
                                                $_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
                                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>  
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"  attrhandler="getTabObj().calculateTotal()"', 'class' =>'btn btn-link remove-button' )); ?>
                                    </div>
                                </div> 

                                <?php } ?> 

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>   
                                    <div class="div-table-col-3">
                                        <div class="text-link-01 mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>  
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                </div>  

                           </div>   
                        </div>
                    </div>  
                 <div class="div-table" style="width:100%; margin-top:1em"> 
                        <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['balance']); ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:180px;"> 
                                <?php echo $obj->inputNumber('balance', array ( 'readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>  
                            </div> 
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div> 
                  </div>    
              </div> 
         
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
