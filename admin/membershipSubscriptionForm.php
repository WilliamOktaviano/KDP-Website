<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('MembershipSubscription.class.php');
$membershipSubscription = createObjAndAddToCol( new MembershipSubscription()); 

$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod());
$customer = createObjAndAddToCol( new Customer()); 
$membershipLevel = new MembershipLevel();

$obj = $membershipSubscription;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'membershipSubscriptionList';

$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

//$useVoucherPoint = $obj->loadSetting('transactionVoucherPoint');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	
	$rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
      
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate']);
	
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']); 
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	
    //ini sementara gk diupdate, akan di unset
    // hanya utk validasi di validate form
    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 

	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal); 
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2); 
       
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
 }

$arrMembershipLevel = $membershipLevel->generateComboboxOpt(null,array('criteria' => ' and ('.$membershipLevel->tableName.'.statuskey = 1)'));  
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
//$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null,array('criteria' => ' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'));

$rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey',$termOfPayment->tableName.'.name'),
									  ' and ('.$termOfPayment->tableName.'.statuskey = 1 and '.$termOfPayment->tableName.'.duedays > 0 ' .$editTermOfPaymentInactiveCriteria.')'
									  );
$arrTOP = $termOfPayment->generateComboboxOpt(array('data' => $rsTOP )); 

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
        
        membershipSubscription = new MembershipSubscription(tabID,<?php echo json_encode($rs); ?>,cashTOP,<?php /*echo json_encode($rsVoucher);*/ ?> );
        prepareHandler(membershipSubscription); 
        
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
    
       <div class="div-table main-tab-table-2">
            <div class="div-table-row">
                    <div class="div-table-col" style="width: 40%"> 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array(  
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )  
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>  
								 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['membership']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selMembershipLevel', $arrMembershipLevel); ?>
                                        </div> 
                                    </div>   
                                 
                             </div>
                         
                    </div> 
					<div class="div-table-col">  
							<div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
							<div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['payment']); ?></div>


							 <div style="width:50%; float:right;  ">
									<div class="div-table" style="width:100%" >
										  <div class="div-table-row  form-group"> 
												<div class="div-table-col-3" style="text-align:right;">
													<?php echo ucwords($obj->lang['payment']); ?> 
												</div>  
												<div class="div-table-col-3" style="width:180px;"> 
													 <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
												</div>  
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
												</div> 

												<?php } ?> 

												<div class="div-table-row form-group ">
													<div class="div-table-col-3"></div>   
													<div class="div-table-col-3">
														<div class="text-link-01 mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
													</div> 
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
											</div>  
									  </div>   

							</div>   
							 <div class="div-table" style="width:48%;float:right;">
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
												 <?php echo ucwords($obj->lang['point']); ?>
											</div>  
											<div class="div-table-col-3"> 
												<div class="flex">          
													<div style="width:7.2em"> <?php echo $obj->inputNumber('point', array ('etc' => 'style="text-align:right;"')); ?> </div>
													<div> <?php echo $obj->inputNumber('pointValue', array ('add-class'=> $finalDiscDecimalType,'readonly' => true, 'etc' => 'style="text-align:right;"')); ?> </div>
												 </div> 
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

									 <div class="div-table-row  form-group"> 
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

								   <div class="div-table-row  form-group"> 
										<div class="div-table-col-3" style="text-align:right;"> 
											 <?php echo ucwords($obj->lang['total']); ?> 
										</div>  
										<div class="div-table-col-3"> 
											<?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
										</div> 
									</div>  
							  </div>   
							 <div style="clear:both"></div> 

						  </div>    
					</div>
           </div>
      </div>   
          
      
        
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
