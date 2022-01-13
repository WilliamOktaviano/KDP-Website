<?php 
include '../_config.php'; 
include '../_include-v2.php'; 


$obj= $voucher;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'voucherList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$_POST['startDate'] = date('d / m / Y');
$_POST['endDate'] = date('d / m / Y');
$_POST['customerType'] = CUSTOMER_TYPE['enduser'];

$rsDetail = array(); 
$rsItemCategoryDetail = array(); 
$rsCityDetail = array(); 
$rsCityCategoryDetail = array(); 
$rsItemDetail = array(); 
$rsBrandDetail = array(); 

$editWarehouseInactiveCriteria = ''; 

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    $rsItemCategoryDetail = $obj->getItemCategory($id); 
    $rsCityDetail = $obj->getCity($id);  
    $rsCityDetail = $obj->getCityCategory($id);  
    $rsItemDetail = $obj->getItem($id);  
    $rsBrandDetail = $obj->getBrand($id); 
	
    if ($rs[0]['discounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
      
    $editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
 
}


$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label'=>'status'));
$arrType = $obj->generateComboboxOpt(array('data' => $obj->getVoucherType()));
$arrCategory = $obj->generateComboboxOpt(array('data' => $obj->getVoucherCategory()));
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
 

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
        
        arrVoucher.rsCityDetail = <?php echo json_encode($rsCityDetail); ?>;
        arrVoucher.rsCityCategoryDetail = <?php echo json_encode($rsCityCategoryDetail); ?>;
        arrVoucher.rsItemCategoryDetail = <?php echo json_encode($rsItemCategoryDetail); ?>;
        arrVoucher.rsBrandDetail = <?php echo json_encode($rsBrandDetail); ?>;
        arrVoucher.rsItemDetail = <?php echo json_encode($rsItemDetail); ?>;
        
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
<!--                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?>
                                </div> 
                            </div>  -->  
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('name'); ?> 
                                </div> 
                            </div> 
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['alias']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('alias'); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selCategory', $arrCategory); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selType', $arrType); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label" style="padding-top:0"><?php echo ucwords($obj->lang['usedQty']); ?> /<br><?php echo ucwords($obj->lang['issuedQty']); ?> </label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputNumber('used',array('readonly' => true)); ?>
                                         <div class="asterix-label" style="font-size:0.9em; margin-top:0.5em">&nbsp;</div>
                                        </div>
                                        <div > / </div>
                                        <div class="consume">
                                            <?php echo $obj->inputNumber('qty'); ?>
                                            <div class="asterix-label" style="font-size:0.9em; margin-top:0.5em"><?php echo $obj->lang['leaveItBlankForUnlimited']; ?></div>
                                        </div>
                                    </div>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                    <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputDate('startDate',array('etc' => 'style="text-align:center;"')); ?></div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputDate('endDate',array('etc' => 'style="text-align:center;"')); ?> </div>
                                            </div>   
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['maxDiscount']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputNumber('maxDiscount'); ?> 
                                    <div class="asterix-label" style="font-size:0.9em; margin-top:0.5em"><?php echo $obj->lang['leaveItBlankForUnlimited']; ?></div>
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
       
        <div style="clear:both; height: 2em"></div>
        <div class="div-tab-panel">  
            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['criteria']); ?></div>  
            
            <div  style="width: 50%; float: left">
            <div class="form-group">
                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['minimumTransaction']); ?></label> 
                    <div class="col-xs-9"> 
                          <?php echo $obj->inputNumber('minAmount'); ?> 
                    </div> 
            </div> 
            <div class="form-group">
                    <label class="col-xs-3 control-label"><?php echo $obj->lang['customerType']; ?></label> 
                    <div class="col-xs-9"> 
                                <?php
                                    $optionsBusiness = array();
                                    array_push($optionsBusiness,array('label' => ucwords($obj->lang['reseller']), 'value' => '1' ));
                                    array_push($optionsBusiness,array('label' => ucwords($obj->lang['endUser']), 'value' => '2' )); 
                                    echo $obj->inputRadio('customerType', array('optionItems' => $optionsBusiness)); 
                                ?>                                  
                     </div> 
            </div> 
            </div>

            <div style="clear:both; height: 1em "></div> 
            <div class="div-table" style="width: 100%">
            <div class="div-table-row">
                <div class="div-table-col-5" style="vertical-align:top; width: 33.33%">  
                    <div class="div-table brand-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header" style="border-top:0;" ><?php echo ucwords($obj->lang['brand']); ?></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border-top:0;"></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border-top:0;"></div> 
                        </div>
                        
                         <?php  
                            $totalRows = count($rsBrandDetail); 

                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'brand-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailBrandKey[]'] =  $rsBrandDetail[$i]['pkey'];
                                    $_POST['hidBrandKey[]'] =  $rsBrandDetail[$i]['brandkey']; 
                                    $_POST['brandName[]'] =  $rsBrandDetail[$i]['brandname']; 
                                } 

                        ?>


                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailBrandKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidBrandKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('brandName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="brand-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                      </div>

                    <?php } ?>
                        
                    </div>  
                </div> 
                <div class="div-table-col-5" style="vertical-align:top;  width: 33.33%"> 
                    <div class="div-table item-category-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header" style=" border-top:0" ><?php echo ucwords($obj->lang['itemCategory']); ?></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style=" border-top:0" ></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style=" border-top:0" ></div> 
                        </div>
                        
                         <?php  
                            $totalRows = count($rsItemCategoryDetail); 

                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'category-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailCategoryKey[]'] =  $rsItemCategoryDetail[$i]['pkey'];
                                    $_POST['hidCategoryKey[]'] =  $rsItemCategoryDetail[$i]['categorykey']; 
                                    $_POST['categoryName[]'] =  $rsItemCategoryDetail[$i]['categoryname'];

                                } 

                        ?>


                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailCategoryKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidCategoryKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('categoryName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="category-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                      </div>

                    <?php } ?>
                        
                    </div>    
                </div> 
                <div class="div-table-col-5" style="vertical-align:top;  width: 33.33%"> 
                    <div class="div-table item-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header" style="border-top:0;" ><?php echo ucwords($obj->lang['item']); ?></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="border-top:0;"></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="border-top:0;"></div> 
                        </div>
                        
                         <?php  
                            $totalRows = count($rsItemDetail); 
 
                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'item-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailItemKey[]'] =  $rsItemDetail[$i]['pkey'];
                                    $_POST['hidItemKey[]'] =  $rsItemDetail[$i]['itemkey']; 
                                    $_POST['itemName[]'] =  $rsItemDetail[$i]['itemname'];

                                } 

                        ?>


                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailItemKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="item-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                       </div>

                    <?php } ?>
                        
                    </div>  
                </div>
            </div>
            <div  class="div-table-row"> 
                <div class="div-table-col-5" style="vertical-align:top; "> 
                    <div class="div-table city-category-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header" style="border-top:0;" ><?php echo ucwords($obj->lang['cityCategory']); ?></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border-top:0;"></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border-top:0;"></div> 
                        </div>
                        
                         <?php  
                            $totalRows = count($rsCityDetail); 

                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'city-category-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {   
                                    $_POST['hidDetailKey[]'] =  $rsCityDetail[$i]['pkey'];
                                    $_POST['hidCityCategoryKey[]'] =  $rsCityDetail[$i]['categorykey']; 
                                    $_POST['cityCategoryName[]'] =  $rsCityDetail[$i]['categoryname']; 
                                } 

                        ?>


                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidCityCategoryKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('cityCategoryName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="city-category-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                      </div>

                    <?php } ?>
                        
                    </div>  
                </div>
                <div class="div-table-col-5" style="vertical-align:top; ">
                
                  <!--  <div class="div-table city-criteria mnv-transaction transaction-detail" style="width:100%;">
                        <div class="div-table-row"> 
                            <div class="div-table-col detail-col-header" style="border-top:0;" ><?php echo ucwords($obj->lang['city']); ?></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border-top:0;"></div> 
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border-top:0;"></div> 
                        </div>
                        
                         <?php  
                            $totalRows = count($rsCityDetail); 

                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $readonly = false;

                                if ($i == $totalRows ){
                                    $class = 'city-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';
                                } else {  

                                    $_POST['hidDetailKey[]'] =  $rsCityDetail[$i]['pkey'];
                                    $_POST['hidCityKey[]'] =  $rsCityDetail[$i]['citykey']; 
                                    $_POST['cityName[]'] =  $rsCityDetail[$i]['cityname'];

                                } 

                        ?>


                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidCityKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('cityName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                            </div>   
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="city-row-template"')) : ''; ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                      </div>

                    <?php } ?>
                        
                    </div>  -->
                </div>
                <div class="div-table-col-5" style="vertical-align:top; ">  </div>
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
