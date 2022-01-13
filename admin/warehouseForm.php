<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array("Warehouse.class.php"));
$warehouse = createObjAndAddToCol(new Warehouse());  
$paymentMethod = createObjAndAddToCol(new PaymentMethod());   
$city = createObjAndAddToCol(new City());   
$company = createObjAndAddToCol(new Company());   
$location = createObjAndAddToCol(new Location());   
$coaLink = createObjAndAddToCol(new COALink());   
    
$isActiveCOA = $class->isActiveModule('ChartOfAccount'); 
if($isActiveCOA & USE_GL) $chartOfAccount = createObjAndAddToCol(new ChartOfAccount());  

$obj= $warehouse;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'warehouseList';
  
$employeekey = base64_decode($_SESSION[$security->loginAdminSession]['id']);
$editCompanyInactiveCriteria = '';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
    $warehousekey = $rs[0]['pkey'];
                                
	$_POST['name']=$rs[0]['name'];
	$_POST['address']=$rs[0]['address']; 
	$_POST['country']=$rs[0]['country'];
	$_POST['zip']=$rs[0]['zip'];
	$_POST['trDesc']=$rs[0]['trdesc'];
	$_POST['qohcount']=$rs[0]['isqohcount'];
	$_POST['webqoh']=$rs[0]['iswebqoh'];
	$_POST['isrma']=$rs[0]['isrma'];
	$_POST['isbus']=$rs[0]['isbus'];
	$_POST['isvendor']=$rs[0]['isvendor'];
	$_POST['hidCityKey'] = $rs[0]['citykey'];
	$_POST['selCompany'] = $rs[0]['companykey'];
	$_POST['orderlist'] = $rs[0]['orderlist'];
	$_POST['location'] = $rs[0]['location'];
	$_POST['phone'] = $rs[0]['phone'];
	
	if (!empty($rs[0]['citykey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
     
	$editCompanyInactiveCriteria = ' or '.$company->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['companykey']);
}else{  
    $warehousekey = 1;  
}  

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrPaymentMethod = ($class->isActiveModule('PaymentMethod')) ? $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1)') : array(); 
$arrCompany = $class->convertForCombobox($company->searchData('','',true,' and ('.$company->tableName.'.employeekey = '.$obj->oDbCon->paramString($employeekey).' and ' .$company->tableName.'.statuskey = 1' .$editCompanyInactiveCriteria.')'),'pkey','name');   

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
			 
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.warehouse['1'],
                        }, 
                    }
                }, 
                code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code['1'],
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputText('name'); ?>
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['stock']); ?></label> 
                            <div class="col-xs-9" style="padding-top:0.5em; padding-bottom:0.5em">
                                <div class="flex">
                                    <div> <?php echo $obj->inputCheckBox('qohcount', array('value' => 1)); ?> </div>
                                    <div> <?php echo ucwords($obj->lang['qoh']); ?></div>
                                    <div style="margin-left:1.5em"> <?php echo $obj->inputCheckBox('webqoh', array('value' => 1)); ?> </div>
                                    <div> <?php echo ucwords($obj->lang['webstore']); ?></div>
                                    <div style="margin-left:1.5em"> <?php echo $obj->inputCheckBox('isrma'); ?> </div>
                                    <div> <?php echo $obj->lang['rma']; ?></div>
                                    <div style="margin-left:1.5em"> <?php echo $obj->inputCheckBox('isbus'); ?> </div>
                                    <div>BUS</div>
                                    <div style="margin-left:1.5em"> <?php echo $obj->inputCheckBox('isvendor'); ?> </div>
                                    <div>Vendor</div>
                                </div>     
                            </div>
                        </div>     
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['orderList']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputText('orderlist'); ?>
                            </div> 
                        </div>     
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                            </div> 
                        </div>         
                         
                     </div>  
                </div> 
                <div class="div-table-col">
                    
                    <div class="div-tab-panel"> 
                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['location']); ?></div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo  $obj->inputTextArea('address',array('etc' => 'style="height:10em;"' )); ?>
                                </div> 
                            </div>      
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                        <?php  
                                                $popupOpt = (!$isQuickAdd) ? array(
                                                                    'url' => 'cityForm.php',
                                                                    'element' => array('value' => 'cityName','valueDBField' => 'citycategoryname',
                                                                           'key' => 'hidCityKey'),
                                                                    'width' => '600px',
                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['city'])
                                                                )  : '';

                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $city,
                                                                                    'revalidateField' => false, 
                                                                                    'element' => array('value' => 'cityName',
                                                                                                       'key' => 'hidCityKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-city.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) ,
                                                                                    'popupForm' => $popupOpt
                                                                                  )
                                                                            );  
                                        ?> 
                                </div> 
                            </div>      
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['zipcode']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $class->inputText('zip'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?><div style="font-size:0.9em;" class="text-muted">(Lat, Lng)</div></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputText('location'); ?>
                                </div> 
                            </div>  
                          <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $class->inputText('phone'); ?>
                                </div> 
                            </div>  
                          

                    </div>
                </div> 
                
            </div> 
          	
        </div>
        <?php if ($isActiveCOA && USE_GL) {  
                $popupOpt =  (!$isQuickAdd) ? array(
                                    'url' => 'chartOfAccountForm.php',
                                    'element' => array('value' => 'coalink[]',
                                           'key' => 'hidcoakey[]'),
                                    'width' => '600px',
                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount'])
                                )  : ''; 

                $arrAutoComplete = array(
                                        'objRefer' => $chartOfAccount,
                                        'revalidateField' => false, 
                                        'element' => array('value' => 'coalink[]',
                                                           'key' => 'hidcoakey[]'),
                                        'source' =>array(
                                                            'url' => 'ajax-coa.php',
                                                            'data' => array(  'action' =>'searchData' )
                                                        ) ,
                                        'popupForm' => $popupOpt
                            );  
      ?>
         <div class="div-table main-tab-table-2"> 

            <div class="div-table-row">
                <div class="div-table-col">
                        
                    <div class="div-tab-panel"> 
                    <div class="div-table-caption border-green"><?php echo strtoupper($obj->lang['coalink']); ?></div> 

                    <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['cashBank']); ?></div>  

                     <?php for ($i=0;$i<count($arrPaymentMethod);$i++){

                        $rsPaymentCOA =  $coaLink->getCOALink ('payment', $obj->tableName, $warehousekey,$arrPaymentMethod[$i]['pkey']);

                        $_POST['hidcoakey[]'] = '';
                        $_POST['coalink[]'] = '';

                        if (!empty($rsPaymentCOA)){ 
                            $_POST['hidcoakey[]'] = $rsPaymentCOA[0]['coakey']; 
                            $_POST['coalink[]'] = $rsPaymentCOA[0]['value']; 
                        } 

                       ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $arrPaymentMethod[$i]['name']; ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false, 'value' => $arrPaymentMethod[$i]['pkey'])); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'payment')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>  
                            </div> 
                        </div>   

                    <?php }  ?>
                        
                    <?php   
                            $rsCOALink = $coaLink->getCOALink ('cashbankops', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?>
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['opsCashBank']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'cashbankops')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div>       
 
                             
                    <?php   
                            $rsCOALink = $coaLink->getCOALink ('cashbankdriver', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?>
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['driverCashBank']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'cashbankdriver')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div>       
 
                        
                    <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['inventory']); ?></div>  

                    <?php
                        // persediaan  per gudang
                        if ($obj->loadSetting('inventoryCOAType') == 1){  

                            $rsCOALink = $coaLink->getCOALink ('inventory', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?>
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['inventory']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'inventory')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>        
                            </div> 
                        </div>   
                     <?php } ?>
                    <?php
                        // persediaan  per gudang
                        if ($obj->loadSetting('inventoryCOAType') == 1){  

                            $rsCOALink = $coaLink->getCOALink ('inventorytemp', $obj->tableName, $warehousekey,0);  

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?>
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tempInventory']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'inventorytemp')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>   
                            </div> 
                        </div>    
                     <?php } ?>
                    <?php 

                            $rsCOALink = $coaLink->getCOALink ('hpp', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  

                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo strtoupper($obj->lang['cogs']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'hpp')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>        
                            </div> 
                        </div>  
                          
                        <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['purchasing']); ?></div>      

                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('purchaseretaildiscount', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['purchaseRetailDiscount']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'purchaseretaildiscount')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div>  
                        
                       <!-- 
                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('purchaseservicediscount', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['purchaseServiceDiscount']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'purchaseservicediscount')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div>  -->
                         
                        <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['sales']) .' &amp; ' . strtoupper($obj->lang['revenue']); ?></div>      

                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('salesservice', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesService']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'salesservice')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div>  
                        
                        <?php  

                        $rsCOALink = $coaLink->getCOALink ('salesservicediscount', $obj->tableName, $warehousekey,0); 

                        $_POST['hidcoakey[]'] = '';
                        $_POST['coalink[]'] = '';

                        if (!empty($rsCOALink)){ 
                            $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                            $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                        }  

                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesServiceDiscount']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'salesservicediscount')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div> 
                        
                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('salesretail', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesRetail']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'salesretail')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div>  

                        <?php  

                        $rsCOALink = $coaLink->getCOALink ('salesretaildiscount', $obj->tableName, $warehousekey,0); 

                        $_POST['hidcoakey[]'] = '';
                        $_POST['coalink[]'] = '';

                        if (!empty($rsCOALink)){ 
                            $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                            $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                        }  

                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesRetailDiscount']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'salesretaildiscount')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div> 

                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('otherrevenue', $obj->tableName, $warehousekey,0);   

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['otherRevenue']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'otherrevenue')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
                            </div> 
                        </div>   

                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('lossprofitrate', $obj->tableName, $warehousekey,0);   

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['profitLossRate']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'lossprofitrate')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
                            </div> 
                        </div>                        
                       

						<?php if (PLAN_TYPE['categorykey'] == 7) { ?>
							<?php 

								$rsCOALink = $coaLink->getCOALink ('orlead', $obj->tableName, $warehousekey,0);   

								$_POST['hidcoakey[]'] = '';
								$_POST['coalink[]'] = '';

								if (!empty($rsCOALink)){ 
									$_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
									$_POST['coalink[]'] = $rsCOALink[0]['value']; 
								}   
							?>

							<div class="form-group">
								<label class="col-xs-3 control-label">OR Lead</label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
									<?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'orlead')); ?>  
									<?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
								</div> 
							</div>    
						
						
							<?php 

								$rsCOALink = $coaLink->getCOALink ('bankprovision', $obj->tableName, $warehousekey,0);   

								$_POST['hidcoakey[]'] = '';
								$_POST['coalink[]'] = '';

								if (!empty($rsCOALink)){ 
									$_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
									$_POST['coalink[]'] = $rsCOALink[0]['value']; 
								}   
							?>

							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankProvision']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
									<?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'bankprovision')); ?>  
									<?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
								</div> 
							</div>    
						
						
							<?php 

								$rsCOALink = $coaLink->getCOALink ('adminrevenue', $obj->tableName, $warehousekey,0);   

								$_POST['hidcoakey[]'] = '';
								$_POST['coalink[]'] = '';

								if (!empty($rsCOALink)){ 
									$_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
									$_POST['coalink[]'] = $rsCOALink[0]['value']; 
								}   
							?>

							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['adminRevenue']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
									<?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'adminrevenue')); ?>  
									<?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
								</div> 
							</div>    
						
						<?php } ?>
						 
						<div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['downpayment']); ?></div>      
						<?php 

                            $rsCOALink = $coaLink->getCOALink ('customerdownpayment', $obj->tableName, $warehousekey,0);   

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customerDownpayment']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'customerdownpayment')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
                            </div> 
                        </div>
                        
                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('supplierdownpayment', $obj->tableName, $warehousekey,0);   

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplierDownpayment']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'supplierdownpayment')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
                            </div> 
                        </div>   
						
                    </div> 
                </div>
                <div class="div-table-col">
                     
                    <div class="div-tab-panel"> 
                    <div class="div-table-caption border-green" style="border-color:#fff !important">&nbsp;</div> 
                        
                        <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['ap/ar']); ?></div>    

                        <?php
                        // persediaan digabung per gudang 
                            $rsCOALink = $coaLink->getCOALink ('ar', $obj->tableName, $warehousekey,0); 
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  

                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['accountsReceivable']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'ar')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>   
                            </div> 
                        </div>   

                        
                         <?php  
                            $rsCOALink = $coaLink->getCOALink ('employeear', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['employeeAR']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'employeear')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>
                        
                        <?php 

                            $rsCOALink = $coaLink->getCOALink ('arwriteoff', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['writeOffAccountsReceivable']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'arwriteoff')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>          
                            </div> 
                        </div>  

                        
                   <!--   <?php   
                            $rsCOALink = $coaLink->getCOALink ('operationalar', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  
                        ?>
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['operationalAR']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'operationalar')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div>       
-->
                       <?php
                            // persediaan digabung per gudang  
                            $rsCOALink = $coaLink->getCOALink ('ap', $obj->tableName, $warehousekey,0);    
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['accountsPayable']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'ap')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>           
                            </div> 
                        </div>  
                        
                         <?php  
	
							$rsCOALink = $coaLink->getCOALink ('employeeap', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['employeeAP']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'employeeap')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>
                        
                         <?php  
                            $rsCOALink = $coaLink->getCOALink ('commissionap', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commissionAP']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'commissionap')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>
						
			            <!--<?php  
                           /* $rsCOALink = $coaLink->getCOALink ('suppliercommission', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   */
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplierCommission']); ?></label> 
                            <div class="col-xs-9">  
                                <?php //echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php //echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'suppliercommission')); ?>  
                                <?php //echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>-->


                        <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['cost']); ?></div>    

                      
                          <?php 

                            $rsCOALink = $coaLink->getCOALink ('operationalcost', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['operationalCost']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'operationalcost')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>
                          <?php 

                            $rsCOALink = $coaLink->getCOALink ('outsourcecost', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['outsourceCost']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'outsourcecost')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>
                        
                         <?php 

                            $rsCOALink = $coaLink->getCOALink ('maintenancecost', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['maintenanceCost']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'maintenancecost')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>
                        
                       <?php  
                            $rsCOALink = $coaLink->getCOALink ('commissioncost', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commissionCost']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'commissioncost')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>

                       <?php 
                            $rsCOALink = $coaLink->getCOALink ('shippingcost', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shippingCost']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'shippingcost')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>

                       <?php 

                            $rsCOALink = $coaLink->getCOALink ('lostgaininventory', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['inventoryAdjustment']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'lostgaininventory')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>         
                            </div> 
                        </div> 
                        
                         <?php 

                            $rsCOALink = $coaLink->getCOALink ('othercost', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['otherCost']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'othercost')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>
			
						<?php if ($obj->loadSetting('usePrepaidExpense') == 1) { ?> 
						<?php 

                            $rsCOALink = $coaLink->getCOALink ('prepaidexpense', $obj->tableName, $warehousekey,0);  
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }   
                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['prepaidExpense']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'prepaidexpense')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>    
                            </div> 
                        </div>  
                        <?php } ?>
						
                        <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['tax']); ?></div>    

                        <?php  
                            $rsCOALink = $coaLink->getCOALink ('taxout', $obj->tableName, $warehousekey,0); 

                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  

                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxOut']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'taxout')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>      
                            </div> 
                        </div>


                        <?php
                        // pajak masukan 
                            $rsCOALink = $coaLink->getCOALink ('taxin', $obj->tableName, $warehousekey,0); 
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  

                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxIn']); ?></label> 
                            <div class="col-xs-9">   
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'taxin')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>   
                            </div> 
                        </div>
                        
                         <?php
                        // hutang pajak pph 23
                            $rsCOALink = $coaLink->getCOALink ('payabletax23', $obj->tableName, $warehousekey,0); 
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  

                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['payableTax23']); ?></label> 
                            <div class="col-xs-9">   
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'payabletax23')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>   
                            </div> 
                        </div>
                        
                         <?php
                            // pajak pph 23 prepaid
                            $rsCOALink = $coaLink->getCOALink ('prepaidtax23', $obj->tableName, $warehousekey,0); 
                            $_POST['hidcoakey[]'] = '';
                            $_POST['coalink[]'] = '';

                            if (!empty($rsCOALink)){ 
                                $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                            }  

                        ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['prepaidTax23']); ?></label> 
                            <div class="col-xs-9">   
                                <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'prepaidtax23')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>   
                            </div> 
                        </div>
                        
                        <?php if($obj->loadSetting('tax23GLInInvoice') == 1) { ?>
                          
                             <?php
                                // pajak pph 23 prepaid
                                $rsCOALink = $coaLink->getCOALink ('prepaidtax23Counter', $obj->tableName, $warehousekey,0); 
                                $_POST['hidcoakey[]'] = '';
                                $_POST['coalink[]'] = '';

                                if (!empty($rsCOALink)){ 
                                    $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                    $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                                }  

                            ?>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['prepaidTax23']); ?> (<?php echo ucwords($obj->lang['counterAccount']); ?>)</label> 
                                <div class="col-xs-9">   
                                    <?php echo $obj->inputHidden('hidrefkey[]',array('overwritePost' => false)); ?> 
                                    <?php echo $obj->inputHidden('hidcategorykey[]',array('overwritePost' => false, 'value' => 'prepaidtax23Counter')); ?>  
                                    <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>   
                                </div> 
                            </div>

                        <?php } ?>
                        
                </div>
                </div>    
            </div> 
         </div>
        <?php } ?> 
        <div class="form-button-panel" > 
        <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>   
  
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
