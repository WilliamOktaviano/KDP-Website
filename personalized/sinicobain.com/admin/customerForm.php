<?php 
require_once '../../../_config.php'; 
require_once '../../../_include-v2.php'; 

includeClass(array('Customer.class.php','MembershipLevel.class.php'));

$customer = createObjAndAddToCol(new Customer());
$ar = createObjAndAddToCol(new AR());
$paymentMethod =   createObjAndAddToCol(new PaymentMethod());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$customerCategory = createObjAndAddToCol(new CustomerCategory());
$businessCategory = createObjAndAddToCol(new BusinessCategory());
$city = createObjAndAddToCol(new City());
$currency = createObjAndAddToCol(new Currency());
$customerFeatures = createObjAndAddToCol(new CustomerFeatures());  
$membershipLevel = new MembershipLevel(); 

$obj= $customer;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$hasARAccess = $security->isAdminLogin($ar->securityObject,10);  
  
$formAction = 'customerList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editCategoryInactiveCriteria = '';
$editBusinessInactiveCriteria = '';

$rs = prepareOnLoadData($obj); 
$_POST['dob'] = '01 / 01 / 2000';
 

if (!empty($_GET['id'])){ 
           
    $id = $_GET['id'];
	
	$_POST['name'] = $rs[0]['name']; 
	$_POST['alias'] = $rs[0]['alias']; 
	$_POST['address'] = $rs[0]['address'];  
	$_POST['zipCode'] = $rs[0]['zipcode']; 
	$_POST['phone'] = $rs[0]['phone']; 
	$_POST['mobile'] = $rs[0]['mobile']; 
	$_POST['email'] = $rs[0]['email']; 
	$_POST['description'] = $rs[0]['description']; 
	$_POST['selCategory'] = $rs[0]['categorykey']; 
	$_POST['hidCityKey'] = $rs[0]['citykey']; 
	$_POST['selBusiness'] = $rs[0]['businesskey'];  
	$_POST['userName'] = $rs[0]['username'];  
	$_POST['selMembership'] = $rs[0]['membershiplevel'];  
	$_POST['mapAddress'] = $rs[0]['mapaddress'];  
    $_POST['hidLatLng'] = $rs[0]['latlng'];
    
    $_POST['sex'] = $rs[0]['sexkey']; 
	$_POST['businessName'] = $rs[0]['businessname']; 
    $_POST['dob'] = $obj->formatDBDate($rs[0]['dateofbirth'],'d / m / Y'); 
	
	$arrBlankDate = array('0000-00-00','0000-00-00 00:00:00','1970-01-01', '1970-01-01 00:00:00');
	
	$_POST['txtActivationDate'] = (empty($rs[0]['activationdate']) || in_array( $rs[0]['activationdate'],$arrBlankDate)) ? '' : $obj->formatDBDate($rs[0]['activationdate'],'d / m / Y'); 
	$_POST['txtActivePeriod'] = (empty($rs[0]['expdate']) || in_array( $rs[0]['expdate'],$arrBlankDate)) ? '' :  $obj->formatDBDate($rs[0]['expdate'],'d / m / Y'); 

	if (!empty($rs[0]['citykey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
    $_POST['hidSalesKey'] = $rs[0]['saleskey'];
	if (!empty($rs[0]['saleskey'])){
		$rsSales = $employee->getDataRowById($rs[0]['saleskey']);
		$_POST['salesName'] = $rsSales[0]['name'];
	}
    

    $_POST['hidPlaceOfBirthKey'] = $rs[0]['placeofbirth'];

    if (!empty($_POST['hidPlaceOfBirthKey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['placeofbirth'],true);
		$_POST['placeOfBirth'] = $rsCity[0]['name'] ;
	}   

		
	$editCategoryInactiveCriteria = ' or '.$customerCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
	$editBusinessInactiveCriteria = ' or '.$businessCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['businesskey']);
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status')); 
$arrCategory = $customerCategory->generateComboboxOpt(null,array('criteria' =>' and ('.$customerCategory->tableName.'.statuskey'. $editCategoryInactiveCriteria.')')); 
$arrSex = $obj->generateComboboxOpt(array('data' => $obj->getSex()));
$arrMembershipLevel = $membershipLevel->generateComboboxOpt(null,array('criteria' => ' and ('.$membershipLevel->tableName.'.statuskey = 1)'));  
$arrBusiness = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey',$businessCategory->tableName.'.name'),
								   	' and '.$businessCategory->tableName.'.statuskey = 1 ' .$editBusinessInactiveCriteria 
								  );
 
$arrRoot = array();
$arrRoot[0]['name'] = '-----';
$arrRoot[0]['pkey'] = 0;
$arrBusiness = array_merge($arrRoot,$arrBusiness);

$arrBusiness = $obj->generateComboboxOpt(array('data' => $arrBusiness));

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
		 var opt = {};
		 opt.showItemImage = false;  <?php  //echo ($showItemImage) ? 'true' : 'false';?>;
         var customer = new Customer(tabID,opt); 
         prepareHandler(customer);   
         
         var fieldValidation =  { code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },  
                                    name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.customer[1]
                                            }, 
                                        }
                                    },
                                    email: { 
                                        validators: { 
                                            emailAddress: {
                                                message:  phpErrorMsg.email[3]
                                            }
                                        }
                                    },

                                } ; 
        
        
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
	});
			
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?> 
	  	<?php echo $obj->inputHidden('hidLatLng'); ?>
     
       <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col"> 
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                <div class="col-xs-9"> 
                                        <?php echo  $obj->inputSelect('selStatus', $arrStatus,array('value' => 2)); ?>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['alias']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('alias'); ?>
                                </div> 
                            </div>    
<!--
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selCategory', $arrCategory); ?> 
                                </div> 
                            </div> 
-->
                           
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputTextArea('mapAddress',array('readonly' => true, 'etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div> 
<!--
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php  
                                            $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'cityForm.php',
                                                                'element' => array('value' => 'cityName',
                                                                       'key' => 'hidCityKey'),
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' .  $obj->lang['city'])
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
-->
<!--
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['zipcode']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputText('zipCode'); ?>
                                </div> 
                            </div>  
-->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                <div class="col-xs-4" style="padding-right:0"> 
                                    <?php echo $obj->inputText('phone'); ?>
                                </div>  
                                <div class="col-xs-5" style="padding-left:5px"> 
                                    <?php echo $obj->inputText('mobile'); ?>
                                </div> 
                            </div>     
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('email'); ?>
                                </div> 
                            </div> 
                        
                        </div>
    
                        
                    </div>
                  
                    <div class="div-table-col">  
                     
                      <div class="div-tab-panel"> 
                                  <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['websiteAccount']); ?></div>
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['username']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputText('userName', array('etc' => 'readonly="readonly"')); ?>  
                                        </div> 
                                   </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['password']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputPassword('password'); ?>   
                                        </div> 
                                   </div>   
            
<!--
                                  <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['placeAndDateOfBirth']); ?></label> 
                                    <div class="col-xs-6" style="padding-right:0"> 
                                            <?php  echo $obj->inputAutoComplete(array(
                                                            'objRefer' => $city,
                                                            'revalidateField' => false, 
                                                            'element' => array('value' => 'placeOfBirth',
                                                                               'key' => 'hidPlaceOfBirthKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ) ,
                                                          )
                                                    );  
                                        ?>
                                    </div> 
                                     <div class="col-xs-3" style="padding-left:5px"> 
                                          <?php echo $obj->inputDate('dob', array('etc' => 'style="text-align:center"')); ?>
                                    </div> 
                                </div>   
                                
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sex']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputSelect('sex',$arrSex); ?>
                                    </div> 
                                </div>  
-->
						  
							   <div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['membership']); ?></label> 
									<div class="col-xs-9"> 
										   <?php echo  $obj->inputSelect('selMembership', $arrMembershipLevel); ?> 
									</div> 
								</div> 
							    <div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['activationDate']); ?></label> 
									<div class="col-xs-9"> 
										   <?php echo  $obj->inputText('txtActivationDate', array('readonly' => true)); ?> 
									</div> 
								</div> 
							    <div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['activePeriod']); ?></label> 
									<div class="col-xs-9"> 
										   <?php echo  $obj->inputText('txtActivePeriod', array('readonly' => true)); ?> 
									</div> 
								</div> 
	                                       
                       </div> 
                    </div>
             </div>
      </div>  
        <div class="form-button-panel" > <?php echo $obj->generateSaveButton(); ?>  </div>
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
