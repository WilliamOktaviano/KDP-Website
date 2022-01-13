<?php 
include '../../../_config.php'; 
include '../../../_include-v2.php'; 

includeClass('Voucher.class.php');
$voucher = createObjAndAddToCol( new Voucher()); 

$obj= $voucher;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'voucherList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$rsDetail = array(); 

$editWarehouseInactiveCriteria = ''; 

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];
	  
    //$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
//$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
		var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var arrVoucher = {};
         
        var voucher = new Voucher(tabID,arrVoucher);
        prepareHandler(voucher);     

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
                                value: {
                                    validators: { 
                                        greaterThan: {
                                            value: 0,
                                            inclusive: false,
                                            separator: ',', 
                                            message: phpErrorMsg.amount[2]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('name'); ?> 
                                </div> 
                            </div>  
                          
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['voucherAmount']); ?></label> 
                                <div class="col-xs-9"> 
                                      <div class="flex">          
                                        <div><?php echo $obj->inputSelect('selDiscountType',$obj->arrDiscountType); ?> </div>
                                        <div class="consume"> <?php echo $obj->inputNumber('value', array ('class'=> 'form-control ' . $finalDiscDecimalType)); ?> </div> 
                                     </div>   
                                </div> 
                            </div> 
							<div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['minimumTransaction']); ?></label> 
									<div class="col-xs-9"> 
										  <?php echo $obj->inputNumber('minAmount'); ?> 
									</div> 
							</div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['maxDiscount']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputNumber('maxDiscount'); ?> 
                                    <div class="asterix-label" style="font-size:0.9em; margin-top:0.5em"><?php echo $obj->lang['leaveItBlankForUnlimited']; ?></div>
                                </div> 
                            </div>      
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['pointNeeded']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputNumber('pointNeeded'); ?> 
                                </div> 
                            </div> 
                             
                        </div>
                       
                </div>
                <div class="div-table-col"> 
                    <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                            <div class="form-group">
                                <div class="col-xs-12"> <?php echo  $obj->inputTextArea('shortDesc', array('etc' => 'style="height:10em;"')); ?></div>
                            </div>  
                            <div class="form-group">
                                <div class="col-xs-12">  <?php echo  $obj->inputEditor('trDesc' ); ?> </div>
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
