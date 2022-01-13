<?php 

require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Shipment.class.php','Marketplace.class.php'));
$shipment = new Shipment();
$marketplace = new Marketplace();

$obj= $shipment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'shipmentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$rsDetail = array();

$isActiveMarketplace = $obj->isActiveModule('marketplace');

$rsMarketplace = ($isActiveMarketplace) ? $marketplace->searchData('','',true,' and '.$marketplace->tableName.'.statuskey = 1') : array();
$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	$rs = $obj->getDataRowById($id); 
    $rsDetail =$obj->getServices($id);
	 
	$_POST['name'] = $rs[0]['name'];  
	$_POST['insurance'] = $obj->formatNumber($rs[0]['insurance'],2);  
	$_POST['adminFee'] = $obj->formatNumber($rs[0]['adminfee']);  
	$_POST['extCost'] = $obj->formatNumber($rs[0]['extcost']);  
	$_POST['maxWeight'] = $obj->formatNumber($rs[0]['maxweight']);  
	$_POST['minWeight'] = $obj->formatNumber($rs[0]['minweight']);  
	$_POST['url'] = $rs[0]['url'];  
	$_POST['username'] = $rs[0]['username'];  
	$_POST['apiKey'] = $rs[0]['apikey'];  
	$_POST['chkDropOffLocation'] = $rs[0]['needlocation'];
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
        
        var shipment = new Shipment(tabID);
        prepareHandler(shipment);
        
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
                                                message:  phpErrorMsg.name[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dropOffLocation']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputCheckBox('chkDropOffLocation'); ?> 
                                        </div> 
                                     </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['insurance'].' (%)'); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDecimal('insurance'); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['adminFee']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('adminFee'); ?> 
                                        </div> 
                                    </div> 
                                  <!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['extCost']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('extCost'); ?> 
                                        </div> 
                                    </div> 
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['maxWeight']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('maxWeight'); ?> 
                                        </div> 
                                    </div>
                                      <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['minWeight']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('minWeight'); ?> 
                                        </div> 
                                    </div> -->
                            </div>   
                  </div> 
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">
                                    <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['API']); ?></div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['url']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('url'); ?> 
                                        </div> 
                                     </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['username']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('username'); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">API Key</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('apiKey'); ?> 
                                        </div> 
                                    </div> 
                            </div>   
                  </div> 
             </div>
        </div>      
               <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333;" attr-level="0"  >
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width: 15em"><?php echo ucwords($obj->lang['serviceCode']); ?></div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['serviceName']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  

                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['serviceCode[]'] =  $rsDetail[$i]['servicecode']; 
                            $_POST['serviceName[]'] =  $rsDetail[$i]['servicename']; 

                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail" style="vertical-align:top"> 
                        <?php echo $obj->inputText('serviceCode[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('serviceName[]', array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                    
                        <!-- marketplace link -->
                          <?php  
                                  $totalMarketplace = count($rsMarketplace);  
                                  if($totalMarketplace > 0){
                                       
                                        echo '<div class="div-table transaction-detail" style="width:100%" attr-level="1" attr-group="hidLogisticDetailKey">';
 
                                        $rsMarketplaceDetail = $obj->getMarketplaceLogistics($id,' and '.$obj->tableNameMarketplaceLogistics.'.refkey = ' .  $obj->oDbCon->paramString($rsDetail[$i]['pkey']) );  
                                        $rsMarketplaceDetail = array_column($rsMarketplaceDetail, null, 'marketplacekey'); 
                                       
                                        for ($j=0;$j<=$totalMarketplace; $j++){  

                                        $class =  'transaction-detail-row marketplace-logistic-row';
                                        $style = '';
                                        $overwrite = true; 
                                        $disabled = false;
 
                                        if ($j == $totalMarketplace ){
                                            $class = 'logistic-row-template';
                                            $style = 'style="display:none"';
                                            $overwrite = false;
                                            $disabled = true;  
                                        } else {    
                                            $marketplacekey = $rsMarketplace[$j]['pkey']; 
                                            $_POST['hidMarketplaceKey[]'] = $marketplacekey;  
                                            
                                            $_POST['hidLogisticDetailKey[]'] = '';
                                            $_POST['marketplaceLogisticName[]'] = '';
                                            $_POST['hidMarketplaceLogisticKey[]'] = '';
                                             

                                            if (isset($rsMarketplaceDetail[$marketplacekey])){  
                                                $_POST['hidLogisticDetailKey[]'] = $rsMarketplaceDetail[$marketplacekey]['pkey']; 
                                                $_POST['hidMarketplaceLogisticKey[]'] = $rsMarketplaceDetail[$marketplacekey]['marketplacelogisticid'];    
                                                $_POST['marketplaceLogisticName[]'] =  $rsMarketplaceDetail[$marketplacekey]['marketplacelogisticname'];     
                                            }
                                        }


                                ?>

                                <div class="div-table-row <?php echo $class; ?>" <?php echo $style; ?> >
                                    <div class="div-table-col detail-col-detail" style="width: 150px">
                                        <?php echo $rsMarketplace[$j]['name']; ?>
                                        <?php echo $obj->inputHidden('hidLogisticDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        <?php echo $obj->inputHidden('hidMarketplaceKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputHidden('hidMarketplaceLogisticKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        <?php echo $obj->inputText('marketplaceLogisticName[]', array('overwritePost' => $overwrite,'disabled' => $disabled )); ?>
                                    </div> 
                                </div> 

                                <?php }
                         
                                        echo '</div>';
                                  }
                        ?>
                         
                    </div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"  style="vertical-align:top"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div>
                   
                   
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
        
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>