<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('SalesOrder.class.php');
$salesOrder = createObjAndAddToCol( new SalesOrder()); 

$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$item = createObjAndAddToCol( new Item()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$shipment = createObjAndAddToCol( new Shipment()); 
$city = createObjAndAddToCol( new City()); 
$customer = createObjAndAddToCol( new Customer()); 

$obj = $salesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'salesOrderList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editSalesInactiveCriteria = ''; 
$editCityInactiveCriteria = ''; 
$editCustomCodeInactiveCriteria = '';
 
$rsSalesDetail = array();
$rsPaymentMethodDetail = array();
$rsVoucher = array();

$_POST['trDate'] = date('d / m / Y H:i');

$saleskey = base64_decode($_SESSION[$obj->loginAdminSession]['id']); 
$_POST['selSalesKey'] = $saleskey;
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$totalWeight = 0;
 
$rs = prepareOnLoadData($obj);  

$useVoucherPoint = $obj->loadSetting('transactionVoucherPoint');

$rsKey = $obj->getTableKeyAndObj($obj->tableName,array('key'));

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
    $rsVoucher = $obj->getVoucherDetail($id);
		
	$totalWeight = $obj->formatNumber(ceil($rs[0]['totalweight']/1000));  // selalu KG
	 
    $_POST['selCustomCode'] = $rs[0]['customcodekey']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y H:i');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  
    
    $_POST['marketplaceOrderId'] = $rs[0]['marketplaceorderid'];
    
    //ini sementara gk diupdate, akan di unset
    // hanya utk validasi di validate form
    $_POST['marketplaceKey'] = $rs[0]['marketplacekey'];
    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 

	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal);
	$_POST['pointValue'] = $obj->formatNumber($rs[0]['pointvalue']);
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
	$_POST['hidSalesKey'] = $rs[0]['saleskey'];
	if(!empty($rs[0]['saleskey'])){ 
   	    $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
	   $_POST['salesName'] = $rsSales[0]['name'] ; 
    }
    
	$_POST['recipientName'] = $rs[0]['recipientname'];
	$_POST['recipientPhone'] = $rs[0]['recipientphone'];
	$_POST['recipientEmail'] = $rs[0]['recipientemail'];
	$_POST['recipientAddress'] = $rs[0]['recipientaddress'];
	$_POST['recipientZipcode'] = $rs[0]['recipientzipcode'];
	$_POST['hidRecipientCityKey'] = $rs[0]['recipientcitykey'];
    if(!empty($rs[0]['recipientcitykey'])){ 
   	    $rsCity = $city->searchData($city->tableName.'.pkey',$rs[0]['recipientcitykey'],true);
	    $_POST['recipientCityName'] = $rsCity[0]['citycategoryname']; 
    }
    	
    $_POST['selShipmentService'] = $rs[0]['shipmentservicekey'];   
	$_POST['shipmentTracking'] = $rs[0]['shipmenttracking'];
	
	$_POST['useInsurance'] = $rs[0]['useinsurance'];
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
    $_POST['chkIsFullDeliver'] = $rs[0]['isfulldeliver'];
    $_POST['chkIsDropship'] = $rs[0]['isdropship'];
      
    $_POST['dropshiperName'] = $rs[0]['dropshipername'];
	$_POST['dropshiperPhone'] = $rs[0]['dropshiperphone'];
	$_POST['dropshiperAddress'] = $rs[0]['dropshiperaddress']; 
	$_POST['refCode'] = $rs[0]['refcode']; 
    //$_POST['hidTransVoucherKey'] = $rs[0]['voucherkey'];
	
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
	$_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);
	$_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;  
	$_POST['totalPayment'] =  $obj->formatNumber($rs[0]['totalpayment']) ;  
     
    $totalDP = 0;
	if($class->isActiveModule('CustomerDownpayment')){
		$customerDownpayment = createObjAndAddToCol( new CustomerDownpayment()); 
		$rsDP = $customerDownpayment->getDownpaymentList('',array('refkey' => $id, 'reftabletype' => $rsKey['key']));
		foreach($rsDP as $dp)
			$totalDP += $dp['amount']; 
	}
	    
    $_POST['downpayment'] = $obj->formatNumber($totalDP); 
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
	$editSalesInactiveCriteria = 'or '.$employee->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']);
    $editCustomCodeInactiveCriteria = ' or  '.$customCode->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customcodekey']); 
}


if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 )){ 
    $_POST['action'] = 'resendEmail';
}


$rsTOP = $termOfPayment->searchDataRow( array($termOfPayment->tableName.'.pkey', $termOfPayment->tableName.'.name', $termOfPayment->tableName.'.duedays')
									   , ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrTOP = $obj->generateComboboxOpt(array('data' => $rsTOP));
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status')); 
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null,array('criteria' => ' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')')); 
$arrShipment = $obj->generateComboboxOpt(array('data' =>$shipment->getAllShipment(),'value' => 'servicekey','label' => 'joinservicename'));
$arrDefaultUnit = $itemUnit->generateComboboxOpt(null,array('criteria' => ' and ('.$itemUnit->tableName.'.statuskey = 1 )')); 
$arrCustomCode = $customCode->generateComboboxOpt(null,array('criteria' =>' and ('.$customCode->tableName.'.reftabletype = '.$rsKey['key'].' and '.$customCode->tableName.'.statuskey = 1 ' . $editCustomCodeInactiveCriteria.')')); 

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
        
        salesOrder = new SalesOrder(tabID,<?php echo json_encode($rs); ?>,cashTOP,<?php echo json_encode($rsVoucher); ?> );
        prepareHandler(salesOrder); 
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
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
    <?php echo $obj->inputHidden('hidSendEmail'); ?>
    <?php echo $obj->inputHidden('hidCreditLimit'); ?>
    <?php echo $obj->inputHidden('marketplaceKey'); ?>
    <?php echo $obj->inputHidden('marketplaceOrderId'); ?> 
    <?php // echo $obj->inputHidden('hidTransVoucherKey'); ?> 
     
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['refCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refCode', array('readonly' => true)); ?>
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
                                    <?php if (!empty($arrCustomCode)) {  ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesType']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selCustomCode', $arrCustomCode); ?>
                                        </div> 
                                    </div>  
                                    <?php }  ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'searchField' => 'code,name' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'customerForm.php',
                                                                                                    'element' => array('value' => 'customerName',
                                                                                                           'key' => 'hidCustomerKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateSalesman(); getTabObj().updateRecipients()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php                
                                                    echo $obj->inputAutoComplete(array(  
                                                                                        'element' => array('value' => 'salesName',
                                                                                                           'key' => 'hidSalesKey'),
                                                                                        'source' =>array(
                                                                                            'url' => 'ajax-employee.php',
                                                                                            'data' => array(  'action' =>'searchData' , 
                                                                                                              'issales' => 1 )
                                                                                        )  
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div>  
                                    <div class="form-group" style="padding-bottom:7px">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['partialShipment']); ?></label> 
                                        <div class="col-xs-1"> 
                                            <?php echo  $obj->inputCheckBox('chkIsDropship'); ?> 
                                        </div> 
                                        <label class="col-xs-3 control-label" style="padding-left:0"><?php echo ucwords($obj->lang['dropship']); ?></label> 
                                        <div class="col-xs-1"> 
                                                   <?php 
                                                        $etc = (PARTIAL_SHIPMENT) ?  '' :  'onclick="return false"'; 
                                                        echo $obj->inputCheckBox('chkIsFullDeliver', array('value' => 1, 'etc' =>  $etc  )); 
                                                    ?>  
                                        </div> 
                                        <label class="col-xs-3 control-label" style="padding-left:0"><?php echo ucwords($obj->lang['fullDelivered']); ?></label> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>    
                                 
                             </div>
                         
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['shippingInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientName'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientPhone'); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientEmail'); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php  echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $city,
                                                                'revalidateField' => false, 
                                                                'element' => array('value' => 'recipientCityName',
                                                                                   'key' => 'hidRecipientCityKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-city.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                )                                                               
                                                                )
                                                        );  
                                    ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('recipientAddress', array('etc' => 'style="height:10em;"')); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['zipcode']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputText('recipientZipcode'); ?> 
                                </div> 
                            </div> 
                            
                             <?php if (isset($_POST) && !empty($_POST['hidId']) && ($_POST['selStatus'] == 2 || $_POST['selStatus'] == 3)){ ?>
                          
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shippingReceipt']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"> <?php echo $obj->inputText('shipmentTracking', array( 'add-class'=> ' btn-second-tone', 'allowedStatusForEdit' => array(1,2))); ?></div>
                                                <div><?php echo $obj->inputButton('btnUpdate', $obj->lang['update'], array(  'allowedStatusForEdit' => array(1,2))); ?></div>
                                            </div>
                                           
                                        </div> 
                                    </div>  
                                <?php } ?> 
                        </div>
                          <div class="div-tab-panel dropship-information" style="display:none;"> 
                            <div class="div-table-caption border-pink" ><?php echo ucwords($obj->lang['dropshiper']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9">   
                                    <?php echo $obj->inputText('dropshiperName'); ?> 
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('dropshiperPhone'); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('dropshiperAddress', array('etc' => 'style="height:10em;"')); ?> 
                                </div> 
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
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right; padding-right:0;"></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right; padding-left:0.2em;"><?php echo ucwords($obj->lang['discount']); ?> @</div>
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
                            $_POST['selDiscountType[]'] =  $rsSalesDetail[$i]['discounttype'] ; 
                            $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['discount'],$decimal); 
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
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('discountValueInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selDiscountType[]',$obj->arrDiscountType, array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ','readonly' =>true, 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
          <div> 
                <div style="width:350px; float:right; ">
                        <div class="div-table" style="width:100%" >
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['payment']); ?> 
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                         <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
                                    </div> 
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                </div> 
                        </div>    
                        
                    <div class="mnv-total-group mnv-payment-method cashTOP"  >  
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

                                <div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
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

							 <div class="div-table-row  form-group"> 
								<div class="div-table-col-3"></div>  
								<div class="div-table-col-3"> </div>
							</div>
							<div class="div-table-row  form-group"> 
								<div class="div-table-col-3" style="text-align:right;">
									<?php echo ucwords($obj->lang['downpayment']); ?> 
								</div>  
								<div class="div-table-col-3"> 
									<?php echo $obj->inputNumber('downpayment',array ('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>  
								</div>
							</div>
					  </div>   
                    
                </div>   
                 <div class="div-table" style="float:right; margin-right:4em">
                        <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['subtotal']); ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:200px;"> 
                                <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                            </div>

                        </div>
                        <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3"  style="text-align:right;">
                                     <?php echo ucwords($obj->lang['discount']); ?>
                                </div>  
                                <div class="div-table-col-3"> 
                                    <div class="flex">          
                                        <div><?php echo $obj->inputSelect('selFinalDiscountType',$obj->arrDiscountType); ?> </div>
                                        <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')); ?> </div>
                                     </div> 
                                </div> 
                        </div>
					  	<?php if ($useVoucherPoint == 1) { ?>
                        	<?php  
                                $totalRows = count($rsVoucher);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'voucher-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailVoucherKey[]'] = $rsVoucher[$i]['pkey'];
                                            $_POST['hidVoucherKey[]'] = $rsVoucher[$i]['voucherkey'];
                                            $_POST['voucherCode[]'] = $rsVoucher[$i]['code']; 
                                            $_POST['voucherAmount[]'] = $obj->formatNumber($rsVoucher[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group voucher-row <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailVoucherKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                                        <?php echo $obj->inputHidden('hidVoucherKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                                        <?php echo $obj->inputText('voucherCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'etc'=>'placeholder="'.$obj->lang['voucherCode'].'"')); ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px"> 
                                       <?php echo $obj->inputNumber('voucherAmount[]', array('overwritePost' => $overwrite, 'class'=>'form-control inputnumber mnv-detail-field', 'disabled' => $disabled,'readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                                </div>  
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                    <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                </div>
                            </div>  
                            <?php } ?> 
					 		<div style="clear:both; height: 1em"></div>
                        <?php } ?> 
					 	
					  	<?php if ($useVoucherPoint == 2) { ?> 
                       <div class="div-table-row  form-group form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right;">
                                 <?php echo ucwords($obj->lang['point']); ?>
                            </div>  
                            <div class="div-table-col-3"> 
                                 <?php echo $obj->inputText('pointValue', array( 'etc' => 'style="text-align:right;" ')); ?>
                            </div> 
                        </div>
					 	<?php } ?>
					 
                         <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3" style="text-align:right; padding-top:2em;">
                               <?php echo ucwords($obj->lang['beforeTax']); ?>
                            </div>  
                            <div class="div-table-col-3" style="padding-top:2em;"> 
                                 <?php echo $obj->inputNumber('beforeTaxTotal', array( 'disabled' => true, 'etc' => 'style="text-align:right;"')); ?>
                            </div>

                        </div>

                           <div class="div-table-row  form-group"> 
                                  <div class="div-table-col-3"  style="text-align:right;">
                                    <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                                 </div>   
                                 <div class="div-table-col-3"> 
                                     <div class="flex">    
                                        <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                                        <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                                        <div>%</div>
                                        <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                      </div> 
                                </div> 
                             </div>   

                         <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right;padding-top:2em;">
                                 <?php echo ucwords($obj->lang['shippingCourier']); ?> 
                            </div>  
                            <div class="div-table-col-3" style=" padding-top:2em;" > 
                                  <?php echo  $obj->inputSelect('selShipmentService', $arrShipment) ?>
                            </div> 
                        </div>
                           <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right;">
                                 <?php echo ucwords($obj->lang['insurance']); ?> 
                            </div>  
                            <div class="div-table-col-3" > 
                                  <?php echo  $obj->inputCheckBox('useInsurance', array('etc' => 'style="margin-top:0;"')) ?> 
                            </div> 
                        </div> 

                        <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3"  style="text-align:right; vertical-align:top; padding-top:0.4em">
                                <?php echo ucwords($obj->lang['shippingFee']); ?> 
                                <div class="asterix-label" ><?php echo $obj->lang['weight']. ' <span class="total-weight">'.$obj->formatNumber($totalWeight).'</span> Kg'; ?></div>
                            </div>  
                            <div class="div-table-col-3" > 
                                <?php echo $obj->inputNumber('shipmentFee', array('etc' => 'style="text-align:right;" ')); ?> 
                            </div> 
                        </div>

                         <div class="div-table-row  form-group   form-detail-field"> 
                            <div class="div-table-col-3" style="text-align:right;"> 
                                 <?php echo ucwords($obj->lang['others']); ?> 
                            </div>      
                            <div class="div-table-col-3"> 
                                <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?> 
                            </div> 
                        </div>
                       <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;"> 
                                 <?php echo ucwords($obj->lang['total']); ?> 
                            </div>  
                            <div class="div-table-col-3"> 
                                <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                            </div> 
                        </div> 
                         <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;"> </div>  
                            <div class="div-table-col-3"> 
                                   <div class="form-detail-button" style="float:right; text-align:right;" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div>
                            </div> 
                        </div> 
                  </div>   
                 <div style="clear:both"></div> 
          </div>
         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
         <?php 
				/*if($security->isAdminLogin($obj->securityObject,11,false)) {
					$totalSent =  (isset($rs[0]['invoicesent'])) ? $rs[0]['invoicesent'] : 0; 
					echo $obj->inputSubmit('btnSaveEmail',ucwords($obj->lang['save']) . ' & '. ucwords($obj->lang['invoice']). '  ('.$totalSent.')', array('etc' => 'style="margin-left:2em"' ));
				}*/
		?> 
        <?php  
             /*if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 ))
                echo  $obj->inputSubmit('btnEmail',ucwords($obj->lang['resend']) . ' '.ucwords($obj->lang['invoice']).' ('.$rs[0]['invoicesent'].')', array ('allowedStatusForEdit' => array(2,3)));  */
         ?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
