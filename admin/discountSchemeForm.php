<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('DiscountScheme.class.php');
$discountScheme = createObjAndAddToCol(new DiscountScheme());
$itemCategory = createObjAndAddToCol(new ItemCategory());

$obj= $discountScheme;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'discountSchemeList'; 
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;  

$rsDetail = array();

$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y');
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	   
	$_POST['name'] = $rs[0]['name'];
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['trdatestart'],'d / m / Y');
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['trdateend'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];
	   
} 


$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label'=>'status')); 
$arrCategory = $itemCategory->generateComboboxOpt(array('data' => $itemCategory->getLeafNodeWithPath(),'label'=>'path')); 
$arrCategory[0] =  $obj->lang['allCategories'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">   
      
	jQuery(document).ready(function(){  
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var discountScheme = new DiscountScheme(tabID,<?php echo json_encode($rs); ?>);
    
         prepareHandler(discountScheme);   
        
         var fieldValidation =  {
                                    code: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                    },
                                    name: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.name[1] }, 
                                        }
                                    }
                                }; 
        
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                <div class="col-xs-9"> 
                                     <div class="flex">
                                                <div class="consume"><?php echo $obj->inputDate('trStartDate', array('etc'=>'style="text-align:center"')); ?></div>
                                                <div >-</div>
                                                <div class="consume"><?php echo $obj->inputDate('trEndDate', array('etc'=>'style="text-align:center"')); ?></div>
                                    </div> 
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
                        
      <div class="<?php echo $obj->hideOnDisabled(); ?>">
          <div style="float:left"><?php echo  $obj->inputSelect('selCategoryKey', $arrCategory); ?></div>
          <div style="float:left; margin-left:1em"><?php echo $obj->inputButton('btnImport',$obj->lang['import'],array('etc' => 'style="margin-top:0.2em"', 'class' => 'btn btn-primary btn-second-tone')); ?></div>
      </div>
        <div style="clear:both; height:2em;"></div> 
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div> 
					<div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['discount']); ?></div> 
					<div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['value']); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>"  style="width:45px"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail); 
                    for ($i=0;$i<=$totalRows; $i++){   
                          
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"';
                        } else { 
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
                            
                            if ($rsDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $inputnumber = 'inputdecimal';
                            } 

                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['sellingPrice[]'] = $obj->formatNumber($rsDetail[$i]['sellingprice']); 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname']; 
                            $_POST['selDiscountType[]'] =  $rsDetail[$i]['discounttype'] ; 
                            $_POST['discountValue[]'] =   $obj->formatNumber($rsDetail[$i]['discount'],$decimal);  
                        }
                 ?>
                
                   <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('sellingPrice[]',array('overwritePost' => $overwrite,'readonly' => true,'disabled' => $disabled, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selDiscountType[]',$obj->arrDiscountType, array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('discountValue[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div> <!--onClick="itemAdj.calculateTotal()"-->
                   </div>
                     
                <?php }	  ?>
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
   
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	    <?php  echo $obj->generateSaveButton();?>
        </div> 
        
    </form>   
     <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
