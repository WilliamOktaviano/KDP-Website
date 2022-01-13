<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('PaymentMethod.class.php');
$paymentMethod = createObjAndAddToCol(new PaymentMethod()); 

$obj= $paymentMethod;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 
    
$formAction = 'paymentMethodList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	$rs = $obj->getDataRowById($id);
    
	$_POST['name'] = $rs[0]['name'];   
	$_POST['bankName'] = $rs[0]['bankname'];   
	$_POST['bankAccountNumber'] = $rs[0]['bankaccountnumber'];   
	$_POST['bankAccountName'] = $rs[0]['bankaccountname'];   
	$_POST['branch'] = $rs[0]['branch']; 
	$_POST['swiftCode'] = $rs[0]['swiftcode'];     
	$_POST['bankCode'] = $rs[0]['bankcode'];     
	$_POST['bankAddress'] = $rs[0]['bankaddress']; 
    $_POST['chkVA'] = $rs[0]['isvirtualaccount'];
	$_POST['useInPaymentConfirmation'] = $rs[0]['useInPaymentConfirmation']; 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
         
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID); 
			 
		 $('#defaultForm-' + tabID)
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
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
                            message: phpErrorMsg.paymentMethod[1]
                        }, 
                    }
                },  				
            }
        })
        .on('success.form.bv', function(e) {
              <?php echo $obj->submitFormScript(); ?>
        });
	});
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?>      
       <div class="div-table main-tab-table-1">
              <div class="div-table-row">
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">   
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentMethod']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankName']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('bankName'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('bankAccountNumber'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountName']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('bankAccountName'); ?>
                                        </div> 
                                     </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['branch']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('branch'); ?>
                                        </div> 
                                     </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputTextArea('bankAddress', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
  				                          <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('bankCode'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
  				                          <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['swift']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('swiftCode'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['showInPaymentConfirmation']); ?></label> 
                                        <div class="col-xs-3"> 
                                           <?php echo $obj->inputCheckBox('useInPaymentConfirmation'); ?> 
                                        </div> 
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['virtualAccount']); ?></label> 
                                        <div class="col-xs-3"> 
                                           <?php echo $obj->inputCheckBox('chkVA'); ?> 
                                        </div> 
                                     </div>
                            </div>   
                  </div>
             </div>
        </div>     
        
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
