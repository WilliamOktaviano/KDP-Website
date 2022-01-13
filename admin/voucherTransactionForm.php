<?php 
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array("VoucherTransaction.class.php"));
$voucherTransaction = new VoucherTransaction();
$voucher = new Voucher();
$customer = new Customer();
$warehouse = new Warehouse();

$obj= $voucherTransaction;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'voucherTransactionList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$_POST['startExp'] = date('d / m / Y');
$_POST['endExp'] = date('d / m / Y');
$_POST['trDate'] = date('d / m / Y');

$rsDetail = array(); 

$editWarehouseInactiveCriteria = ''; 


$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
        
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['usedOn'] = $obj->formatDBDate($rs[0]['useddate'],'d / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];

    
    $_POST['hidVoucherKey'] = $rs[0]['refkey'];
    $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
    $_POST['hidRefCustomerKey'] = $rs[0]['refcustomerkey']; 
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['selWarehouse'] = $rsVoucher[0]['warehousekey']; 
    
   
		$rsVoucher = $voucher->getDataRowById($rs[0]['refvoucherkey']);
		$_POST['voucherCode'] = $rsVoucher[0]['code'];
		$_POST['amount'] = $voucher->formatNumber( $rsVoucher[0]['value']);
	 
    
    if (!empty($rs[0]['customerkey'])){
		$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		$_POST['customerName'] = $rsCustomer[0]['name'];
	}
    
    if (!empty($rs[0]['refcustomerkey'])){
		$rsCustomer = $customer->getDataRowById($rs[0]['refcustomerkey']);
		$_POST['refCustomerName'] = $rsCustomer[0]['name'];
	}


    $editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  

	 
}
 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label'=>'status'));
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
        var tabID = selectedTab.newPanel[0].id;
        
        var voucherTransaction = new VoucherTransaction(tabID);
        prepareHandler(voucherTransaction);     

        var fieldValidation =  { 
                               code: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.code[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['voucher']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php    
                                     echo $obj->inputAutoComplete(array( 
                                                                         'objRefer' => $voucher,  
                                                                         'element' => array('value' => 'voucherCode',
                                                                                            'key' => 'hidVoucherKey'),
                                                                         'source' =>array(
                                                                                             'url' => 'ajax-voucher.php',
                                                                                             'data' => array(  'action' =>'searchData', 'statuskey'=>2 )
                                                                                                    ) 
                                                                       )
                                                                 );  
                                     ?> 
                                </div> 
                            </div>  
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputDate('trDate'); ?> 
                                </div> 
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['usedOn']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputDate('usedOn'); ?> 
                                </div> 
                            </div>
                              <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputNumber('amount'); ?> 
                                </div> 
                            </div> 
                             
                             <div class="form-group">
                                 <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                 <div class="col-xs-9">  
                                    <?php    
                                     echo $obj->inputAutoComplete(array( 
                                                                         'objRefer' => $customer,  
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
                                 <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                                 <div class="col-xs-9">  
                                    <?php    
                                     echo $obj->inputAutoComplete(array( 
                                                                         'objRefer' => $customer,  
                                                                         'element' => array('value' => 'refCustomerName',
                                                                                            'key' => 'hidRefCustomerKey'),
                                                                         'source' =>array(
                                                                                             'url' => 'ajax-customer.php',
                                                                                             'data' => array(  'action' =>'searchData' )
                                                                                                    ) 
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
       
        <div style="clear:both"></div>
       
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton();   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
