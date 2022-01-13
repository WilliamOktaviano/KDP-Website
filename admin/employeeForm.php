<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Employee.class.php');


$isActiveCOA = $class->isActiveModule('ChartOfAccount');
$isActiveCustomer = $class->isActiveModule('Customer');

$employee = createObjAndAddToCol(new Employee());

	
if($isActiveCOA) $chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
if($isActiveCustomer) $customer = createObjAndAddToCol(new Customer());

$city = createObjAndAddToCol(new City());
$employeeCategory = createObjAndAddToCol(new EmployeeCategory());
$roleTemplate = createObjAndAddToCol(new RoleTemplate());
$warehouse = createObjAndAddToCol(new Warehouse());

$obj = $employee;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$hasSecurityPrivileges = $obj->hasSecurityPrivileges();   

$formAction = 'employeeList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editCategoryInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCustomerInactiveCriteria = '';
$editCOAInactiveCriteria = '';
 
$rs = prepareOnLoadData($obj);  
$rsContactPerson = array();

$_POST['dateOfBirth'] = '01 / 01 / 2000';
$_POST['drivingLicenseExpDate'] = date('d / m / Y');
$_POST['chkAllCustomerAccess'] = 1;

$rsCompanyAccess = $obj->getAccessCompany($obj->userkey); 
$rsCompanyOwned = array();

$isFranchisee = false;

if (!empty($_GET['id'])){ 
	 
    $id = $_GET['id'];
    
    $rsContactPerson = $obj->getContactPerson($id);  
    $isFranchisee = $obj->checkIsUserFranchisee($id);       
    
    $rsCompanyOwned = $obj->getAccessCompany($id); 
    
	$_POST['memberUserName'] = $rs[0]['username']; 
	$_POST['selCategory'] = $rs[0]['categorykey']; 
	$_POST['selWarehouse'] = $rs[0]['warehousekey']; 
	$_POST['memberName'] = $rs[0]['name']; 
	$_POST['memberAddress1'] = $rs[0]['address1']; 
	$_POST['memberAddress2'] = $rs[0]['address2']; 
	$_POST['livingAddress1'] = $rs[0]['livingaddress1']; 
	$_POST['livingAddress2'] = $rs[0]['livingaddress2'];  
	$_POST['memberZipCode'] = $rs[0]['zipcode']; 
	$_POST['memberPhone'] = $rs[0]['phone']; 
	$_POST['memberMobile'] = $rs[0]['mobile']; 
	$_POST['memberEmail'] = $rs[0]['email']; 
	$_POST['hidCityKey'] = $rs[0]['citykey']; 
	$_POST['hidPlaceOfBirthKey'] = $rs[0]['placeofbirth']; 
	$_POST['dateOfBirth'] = $obj->formatDBDate($rs[0]['dateofbirth'],'d / m / Y'); 
	$_POST['memberEmail'] = $rs[0]['email']; 
	$_POST['drivingLicense'] = $rs[0]['drivinglicense']; 
	$_POST['drivingLicenseExpDate'] = $obj->formatDBDate($rs[0]['drivinglicenseexpdate'],'d / m / Y'); 
	$_POST['emergencyName'] = $rs[0]['emergencyname']; 
	$_POST['emergencyPhone'] = $rs[0]['emergencyphone']; 
	$_POST['maritalStatus'] = $rs[0]['maritalstatuskey']; 
	$_POST['religion'] = $rs[0]['religionkey']; 
	$_POST['nationality'] = $rs[0]['nationality']; 
	$_POST['sex'] = $rs[0]['sexkey']; 
	$_POST['IDNumber'] = $rs[0]['idnumber']; 
	$_POST['taxid'] = $rs[0]['taxid']; 
    $_POST['chkAllWarehouseAccess'] = $rs[0]['allwarehouseaccess'];
    $_POST['chkAllCustomerAccess'] = $rs[0]['allcustomeraccess'];
    $_POST['chkAllSalesAccess'] = $rs[0]['allsalesaccess'];
    $_POST['chkAllCOAAccess'] = $rs[0]['allcoaaccess'];
    $_POST['bankName'] = $rs[0]['bankname'];
    $_POST['bankAccountName'] = $rs[0]['bankaccountname'];
    $_POST['bankAccountNumber'] = $rs[0]['bankaccountnumber'];
    $_POST['position'] = $rs[0]['position'];
    $_POST['aroutstanding'] = $class->formatNumber($rs[0]['aroutstanding']);
    $_POST['commissionPercentage'] = $class->formatNumber($rs[0]['commissionpercentage'],2);
    $_POST['chkNeedRealization'] = $rs[0]['needrealization'];
      
    $rsCompanyPrivileges = array_column( $obj->getAccessCompany($id),'companykey');     
	$_POST['selCompany[]'] = $rsCompanyPrivileges; 
    
    if($rs[0]['allwarehouseaccess'] <> 1){
        $rsWarehousePrivileges =  $obj->getWarehouseAccess($id);     
        $_POST['selWarehouseAccess[]'] = $rsWarehousePrivileges;  
    }
         
    if($isActiveCustomer && $rs[0]['allcustomeraccess'] <> 1){
        $rsCustomerPrivileges =  $obj->getCustomerAccess($id);     
        $_POST['selCustomerAccess[]'] = $rsCustomerPrivileges;  
    }
     
    if($rs[0]['allsalesaccess'] <> 1){
        $rsSalesPrivileges =  $obj->getSalesAccess($id);     
        $_POST['selSalesAccess[]'] = $rsSalesPrivileges;  
    }
    
    if($rs[0]['allcoaaccess'] <> 1){
        $rsCOAAccess =  $obj->getCOAAccess($id);     
        $_POST['selCOAAccess[]'] = $rsCOAAccess;  
    } 
    
	if (!empty($_POST['hidPlaceOfBirthKey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['placeofbirth'],true);
		$_POST['placeOfBirth'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
    if (!empty($_POST['hidCityKey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
	if($isActiveCOA && USE_GL){
		$_POST['hidCashBankCOAKey'] = $rs[0]['cashbankcoakey'];  
		if (!empty($rs[0]['cashbankcoakey'])){
			$rsCOA = $chartOfAccount->getDataRowById($rs[0]['cashbankcoakey']); 
			$_POST['cashbankCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
		}

		$_POST['hidCommissionAPCOAKey'] = $rs[0]['commissionapcoakey'];  
		if (!empty($rs[0]['commissionapcoakey'])){
			$rsCOA = $chartOfAccount->getDataRowById($rs[0]['commissionapcoakey']); 
			$_POST['commissionAPCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
		}


		$_POST['hidAPCOAKey'] = $rs[0]['apcoakey'];  
		if (!empty($rs[0]['apcoakey'])){
			$rsCOA = $chartOfAccount->getDataRowById($rs[0]['apcoakey']); 
			$_POST['APCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
		}

		$_POST['hidARCOAKey'] = $rs[0]['arcoakey'];  
		if (!empty($rs[0]['arcoakey'])){
			$rsCOA = $chartOfAccount->getDataRowById($rs[0]['arcoakey']); 
			$_POST['ARCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
		}
	}
    
    $_POST['chkIsDriver'] = $rs[0]['isdriver'];
    $_POST['chkIsSales'] = $rs[0]['issales'];
    
    
    //update image photo
    $rsPhotoImage = array(); 
    if( !empty($rs[0]['photofile'])){
		$rsPhotoImage[0]['file'] =  $rs[0]['photofile'];
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadPhotoFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadPhotoFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	} 
	  
    
    
	 //update image 
	$rsItemImage = $obj->getItemImages($id,$obj->tableImageID); 
	if(count($rsItemImage) > 0){
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath);  
	} 
    
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    //$editCustomerInactiveCriteria = ' or '.$customer->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customerkey']);
    $editCategoryInactiveCriteria = ' or '.$employeeCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
	//$editCOAInactiveCriteria = ' or '.$chartOfAccount->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
	 
} 
   
$rsSecurityObjectCategory = $security->getSecurityObjectCategory(); 
//$arrCompany = $obj->convertForCombobox($rsCompanyAccess,'companykey','companyname');   

$arrCompany = $obj->generateComboboxOpt(array('data' =>$rsCompanyAccess,'value' => 'companykey' ,'label' => 'companyname')); 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status')); 
//$arrReligion = $obj->generateComboboxOpt(array('data' => $obj->getReligion()));
$arrMarital = $obj->generateComboboxOpt(array('data' => $obj->getMaritalStatus()));
$arrSex = $obj->generateComboboxOpt(array('data' => $obj->getSex()));	
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1 ' .$editWarehouseInactiveCriteria.')'));
$arrSales = $employee->generateComboboxOpt(null,array('criteria' => ' and '.$employee->tableName.'.issales = 1 and ('.$employee->tableName.'.statuskey = 2)'));// jgn pake inactive criteria agar tetep bisa lihat transaksi sales laama
$arrCategory =  $employeeCategory->generateComboboxOpt(null,array('criteria' => ' and ('.$employeeCategory->tableName.'.statuskey'. $editCategoryInactiveCriteria.')'));
$arrTemplateRole = $roleTemplate->generateComboboxOpt(null,array('criteria' => ' and '.$roleTemplate->tableName.'.statuskey = 1'));

$arrCOA = ($isActiveCOA) ? $chartOfAccount->generateComboboxOpt(array('label'=>'coaname','value' =>'pkey', 'queryLabel' => 'concat('.$chartOfAccount->tableName. '.code,\' - \','.$chartOfAccount->tableName. '.name) as coaname' ),array('criteria' =>' and ('.$chartOfAccount->tableName.'.statuskey = 1 and '.$chartOfAccount->tableName .'.isleaf = 1 ' .$editCOAInactiveCriteria.')')) : array(); 
$arrCustomer = ($isActiveCustomer) ? $customer->generateComboboxOpt(null,array('criteria' => ' and ('.$customer->tableName.'.statuskey = 2 ' .$editCustomerInactiveCriteria.')')) : array();

$arrImportFrom = array( '1' => $obj->lang['roleTemplate'], '2' => $obj->lang['employee']);
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
#security-module {padding:0; margin:0; list-style:none;} 
#security-module li{padding:0; margin:0;}
#security-module li .chkSecurityList, #security-module li .chkModuleName{font-size:1.5em;margin-right:0.2em;} 
.status-item{display:inline-block;  background-color:#dedede; padding:0.7em 1em 1em 1em; margin-left:0.2em; cursor:pointer;}
.module-item{display:inline-block; background-color:#3399CC; white-space: nowrap; padding:0.7em 1em 1em 1em; color:#FFF; width:20em; cursor:pointer;}

</style> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
<script type="text/javascript">  
 
     function Employee(tabID) { 
           this.showDriverInformation = function showDriverInformation(obj){  
              
             if ($(obj).val() == 1){ 
                 $("#" + tabID + " .driver-information").show(); 
             }else{  
                 $("#" + tabID + " .driver-information").hide();
             }
              
           }

            this.showWarehouseAccess = function showWarehouseAccess(obj){  
              
             if ($(obj).val() == 1){ 
                 $("#" + tabID + " .warehouse-access-list").hide(); 
             }else{  
                 $("#" + tabID + " .warehouse-access-list").show();
             }
              
           }
            
            this.showCustomerAccess = function showCustomerAccess(obj){  
              
             if ($(obj).val() == 1){ 
                 $("#" + tabID + " .customer-access-list").hide(); 
             }else{  
                 $("#" + tabID + " .customer-access-list").show();
             }
              
           }

            this.showCOAAccess = function showCOAAccess(obj){  
              
             if ($(obj).val() == 1){ 
                 $("#" + tabID + " .coa-access-list").hide(); 
             }else{  
                 $("#" + tabID + " .coa-access-list").show();
             }
              
           }
                        
            this.showSalesAccess = function showSalesAccess(obj){  
              
             if ($(obj).val() == 1){ 
                 $("#" + tabID + " .sales-access-list").hide(); 
             }else{  
                 $("#" + tabID + " .sales-access-list").show();
             }
              
           }
            
           
         
             this.checkSecurityModule = function checkSecurityModule(obj){  
                                         var objName = obj.prop("name");
                                         var len = obj.closest("li").find(".chkSecurityList:not(:checked)").length;

                                         if (len == 0)
                                            obj.closest("li").find(".chkModuleName").first().prop("checked",true);
                                         else
                                            obj.closest("li").find(".chkModuleName").first().prop("checked",false);

                                    } 

            this.importData = function importData(){  

                                    $body = $("body"); 
                                    $body.addClass("loading");   
                                    var activeAjaxConnections = 0;  
                                    
                                    var importType = $( "#" + tabID + " [name=selImportFrom]" ).val();
                                    var importKey = (importType == 2) ?  $( "#" + tabID + " [name=hidEmployeeTemplateKey]" ).val() : $( "#" + tabID + " [name=selRoleKey]" ).val();
                      
                                    $.ajax({
                                        type: "GET",
                                        url:  'ajax-role-template.php',
                                        beforeSend:function (xhr){
                                            $('#' + tabID  +' [name^=chkSecurityModuleAccess]').prop("checked",true).click();
                                            activeAjaxConnections++; 
                                        },
                                        data: "action=searchData&importType="+importType+"&pkey=" + importKey,  
                                        success: function(data){ 
                                                var data = JSON.parse(data);  
                                                var i;
                                                var chklist;
                                                var value;

                                                for(i=0;i<data.length;i++){ 
                                                  chklist = 'chkList' + data[i].objectkey;
                                                  value = data[i].statuskey;

                                                  $( '#' + tabID + ' [name^=\"' + chklist + '[]\"][value=' + value + ']').prop("checked",false).click();                  
                                                }

                                            activeAjaxConnections--; 
                                            if (0 == activeAjaxConnections) 
                                                $body.removeClass("loading");   

                                        } ,
                                         error: function(xhr, errDesc, exception) {
                                            activeAjaxConnections--; 
                                            if (0 == activeAjaxConnections) 
                                                $body.removeClass("loading");   

                                            }
                                    });
                                }

         }  
     
	
	jQuery(document).ready(function(){  
         /// FILE UPLOADER
        var folder = "<?php echo $obj->uploadFolder; ?>";
        var imageUploaderTarget = "id-image-uploader";
        var arrImage = Array(); 
        var arrPHPThumbHash = Array();
        
        var photoFolder = "<?php echo $obj->uploadPhotoFolder; ?>";
        var photoImageUploaderTarget = "photo-image-uploader";
        var arrPhotoImage = Array(); 
        var arrPhotoPHPThumbHash = Array();
     
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID);  
           
         <?php   
			if (isset($id) && !empty($id)){ 
			
				for($i=0;$i<count($rsItemImage);$i++) {
					echo 'arrImage.push("'.$rsItemImage[$i]['file'].'"); '; 
				    echo 'arrPHPThumbHash.push("'.getPHPThumbHash($rsItemImage[$i]['file']).'"); '; 
                }     
                echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":'.$id.', "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},true);';  
  
                for($i=0;$i<count($rsPhotoImage);$i++) {
					echo 'arrPhotoImage.push("'.$rsPhotoImage[$i]['file'].'"); '; 
				    echo 'arrPhotoPHPThumbHash.push("'.getPHPThumbHash($rsPhotoImage[$i]['file']).'"); '; 
                }
                echo 'createImageUploader({"tabID":tabID, "name":photoImageUploaderTarget},{"folder":photoFolder, "token":'.$id.', "arrImage":arrPhotoImage,"phpThumbHash":arrPhotoPHPThumbHash},false);';  
  
           
			}else{
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget}, {"folder":folder} ,true);';
				echo 'createImageUploader({"tabID":tabID, "name":photoImageUploaderTarget}, {"folder":photoFolder} ,false);';
			}
		?>  
        
        $( "." + imageUploaderTarget + " .image-list ").sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemImageArray({"tabID":tabID, "name":imageUploaderTarget}); }});
		$( "." + imageUploaderTarget + " .image-list"  ).disableSelection();
        $( "." + photoImageUploaderTarget + " .image-list"  ).disableSelection();
        
        
        employee = new Employee(tabID);    
          
        $("#" + tabID + " #security-module li .chkSecurityList").click(function() {  
             
		      if ($(this).prop("checked")) 
			 	 $(this).closest(".status-item").addClass("bg-green-avocado text-white");
			  else
			 	 $(this).closest(".status-item").removeClass("bg-green-avocado text-white");
				 
			  employee.checkSecurityModule($(this));	 
		 });
	
 		 $("#" + tabID + " .chkModuleName").click(function() {    
		       var checked = $(this).prop("checked"); 
		       $(this).closest("li").find(".chkSecurityList").each(function() {   
					 if ($(this).prop("checked") != checked)
					   $(this).click();
				});
		 }); 
 
    	$("#" + tabID + " .multi-selectbox").searchableOptionList({  maxHeight: '250px',  showSelectAll: true, showSelectionBelowList: true  }); 
		
        $( "#" + tabID + " .section-panel .title" ).click(function() {  
            $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
        });
        
        /*$( "#" + tabID + " [name=selWarehouseAccess]" ).change(function() {  
            var totalAccess = $(this).val().length ;
            
            if (totalAccess == 0)
                $(this).closest("div").find(".all-warehouse").show();
            else
                $(this).closest("div").find(".all-warehouse").hide();
        });*/
         
        
        $("#" + tabID + " [name=btnImport]" ).on('click', function() { 
           
           var checked = $(this).prop("checked");
           var hasChecked = false;
           var chkSecurityRows = $("#" + tabID + " .chkSecurityList").each(function() {   
           if ($(this).prop("checked") != checked)
                 hasChecked = true;
           });

            var importButton = $(this);                
            importButton.prop('disabled', true) ;     

              if (hasChecked == true){

                  $( "#dialog-message" ).html("Import data akan mereset hak akses.");
                  $( "#dialog-message" ).dialog({ 
                        width: 300,
                        modal: true,
                        title:"Konfirmasi Import Data", 
                        open: function() { 
                            $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                        },  
                        close:function() {}, 
                        buttons : {
                            OK : function (){    
                                  employee.importData();
                                  $( this ).dialog( "close" );
                            },
                            Cancel : function (){    
                                  $( this ).dialog( "close" );
                            }
                        },
                   });   

              }else{
                  employee.importData();
              }          
               
             importButton.prop('disabled', false) ;

        });
		 
        $("#" + tabID + " [name=btnSelectAll]").click(function() {      
             $("#" + tabID + " [name^=chkSecurityModuleAccess]").prop("checked",false).click();
        }); 
        $("#" + tabID + " [name=btnDeselectAll]").click(function() {      
             $("#" + tabID + " [name^=chkSecurityModuleAccess]").prop("checked",true).click();
        }); 
        $("#" + tabID + " [name=selImportFrom]").change(function() {       
                $("#" + tabID + " .role-template").toggle(); 
                $("#" + tabID + " .employee-template").toggle();
        }); 
        
        
		 $('#defaultForm-' + tabID ).bootstrapValidator({ 
				feedbackIcons: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                            code: { 
                                validators: {
                                    notEmpty: {
                                        message: phpErrorMsg.code['1'],
                                    }, 
                                }
                            },  
 
                            memberName: { 
                                validators: {
                                    notEmpty: {
                                        message: phpErrorMsg.employee['1']
                                    },  
                                }
                            },    

                            memberEmail: { 
                                validators: { 
                                    emailAddress: {
                                        message:  phpErrorMsg.email['3']
                                    }
                                }
                            },   

                        }
            })
            .on('success.form.bv', function(e) { 
                     <?php echo $obj->submitFormScript(); ?>
            });
        
        
            employee.showDriverInformation("#" + tabID + " [name=chkIsDriver]");
        
            <?php   
                if (isset($_GET) && !empty($_GET['showPrivileges'])){ 
                    echo 'var panelTitle = $( "#"+tabID+" .section-panel .title" );';
                    echo 'panelTitle.click();';
 
                    if (!empty($rs)) 
                        echo '$("html, body").stop().animate({scrollTop: panelTitle.offset().top - 50 }, 1000, \'swing\');'; 
               } 
           ?> 
      
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
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('value' => 2)); ?>
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
                                          <?php echo  $obj->inputSelect('selWarehouse',$arrWarehouse); ?> 
                                    </div> 
                                </div>  
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('memberName'); ?>
                                        </div> 
                                </div>     
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['division']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo  $obj->inputSelect('selCategory',$arrCategory); ?>
                                    </div> 
                                </div>   

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['livingAddress']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('livingAddress1'); ?>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('livingAddress2'); ?>
                                    </div> 
                                </div>  

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                    <div class="col-xs-4" style="padding-right:0"> 
                                        <?php echo $obj->inputText('memberPhone'); ?>
                                    </div>  
                                    <div class="col-xs-5" style="padding-left:5px"> 
                                        <?php echo $obj->inputText('memberMobile'); ?>
                                    </div> 
                                </div>  
  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo $obj->inputText('memberEmail'); ?>
                                    </div> 
                                </div>   
                        
                            <div class="form-group"> 
                                <label class="col-xs-3 control-label"> 
                                   <?php echo ucwords($obj->lang['position']); ?>
                                </label> 
                                <div class="col-xs-9"> 
                                       <?php echo $obj->inputText('position'); ?>
                                </div>  
                            </div>  
                        
                            <div class="form-group"> 
                                <label class="col-xs-3 control-label"> </label> 
                                <div class="col-xs-1"> 
                                      <?php echo  $obj->inputCheckBox('chkIsDriver', array('etc' => 'onChange="employee.showDriverInformation(this)"')); ?>  
                                </div> 
                                <label class="col-xs-2  control-label" style="padding-left:0"> 
                                     <?php echo ucwords($obj->lang['driver']); ?>
                                </label> 
                                <div class="col-xs-1"> 
                                      <?php echo  $obj->inputCheckBox('chkIsSales'); ?>   
                                </div> 
                                <label class="col-xs-2 control-label"  style="padding-left:0"> 
                                      <?php echo ucwords($obj->lang['salesman']); ?>
                                </label> 
                            </div>  
                        
                            <div class="form-group driver-information" style="display:none">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['drivingLicense']); ?>, <?php echo ucwords($obj->lang['expirationDate']); ?></label> 
                                <div class="col-xs-6" style="padding-right:0"> 
                                      <?php echo  $obj->inputText('drivingLicense'); ?> 
                                </div>  
                                 <div class="col-xs-3" style="padding-left:5px"> 
                                      <?php echo  $obj->inputDate('drivingLicenseExpDate', array('etc' => 'style="text-align:center"')); ?> 
                                </div> 
                            </div>   
                        
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['photo']; ?></label> 
                                <div class="col-xs-9"> 
                                     <!-- image uploader --> 
                                    <div class="item-image-uploader photo-image-uploader">
                                        <ul class="image-list" ></ul>
                                        <div style="clear:both; height:1em; "></div>
                                        <div class="file-uploader">	
                                            <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                        </div>
                                      </div>  
                                    <!-- image uploader --> 
                                </div> 
                            </div>  
                        
                           </div>
                    
                        
                        <div class="div-tab-panel">  
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['security']); ?></div>
                             <?php
                                // jika blm ad salt
                                if (empty($rs[0]['secretAuth'])){  
                                    echo '<div class="text-muted">'.$obj->lang['2FDisabled'].'</div>';
                                }else{ 
                                    echo '<div class="text-green-avocado">'.$obj->lang['2FEnabled'].'</div>';
                                } 
                             ?>  
                            
                            <?php if ($hasSecurityPrivileges) { ?>
                            <div style="clear:both; height:1em"></div>
                             <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['username']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('memberUserName'); ?>
                                    </div> 
                              </div>  

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['password']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputPassword('memberPassword'); ?>
                                </div> 
                            </div>  

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['passwordConfirmation']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputPassword('memberPasswordConfirmation'); ?>
                                </div> 
                            </div>   
                            <?php } ?>
                            
                        </div>
                    
                    
                        <div class="div-tab-panel" style="margin-bottom:0"> 
                               
                                <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['financialInformation']); ?></div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxIdentificationNumber']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('taxid'); ?>
                                    </div> 
                                </div>        
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankName']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('bankName'); ?>
                                    </div> 
                                </div>   
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountName']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('bankAccountName'); ?>
                                    </div> 
                                </div>   
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountNumber']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('bankAccountNumber'); ?>
                                    </div> 
                                </div>   
                                           
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesCommission']); ?> (%)</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDecimal('commissionPercentage'); ?> 
                                        </div> 
                               </div>               
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['AROutstanding']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputNumber('aroutstanding', array('readonly'=>true)); ?>
                                    </div> 
                                </div>   
                                               
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"></label> 
                                    <div class="col-xs-9"></div> 
                                </div>   
                            
                                <?php if ( $isActiveCOA && USE_GL) { ?>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cashBankAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $chartOfAccount,
                                                                                    'element' => array('value' => 'cashbankCOA',
                                                                                                       'key' => 'hidCashBankCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'chartOfAccountForm.php',
                                                                                                            'element' => array('value' => 'cashbankCOA',
                                                                                                                   'key' => 'hidCashBankCOAKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount']
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                            
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commissionAPAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $chartOfAccount,
                                                                                    'element' => array('value' => 'commissionAPCOA',
                                                                                                       'key' => 'hidCommissionAPCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'chartOfAccountForm.php',
                                                                                                            'element' => array('value' => 'commissionAPCOA',
                                                                                                                   'key' => 'hidCommissionAPCOAKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount']
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                            
                                   					<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['apAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $chartOfAccount,
                                                                                    'element' => array('value' => 'APCOA',
                                                                                                       'key' => 'hidAPCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'chartOfAccountForm.php',
                                                                                                            'element' => array('value' => 'APCOA',
                                                                                                                   'key' => 'hidAPCOAKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount']
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                            
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['ARAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $chartOfAccount,
                                                                                    'element' => array('value' => 'ARCOA',
                                                                                                       'key' => 'hidARCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'chartOfAccountForm.php',
                                                                                                            'element' => array('value' => 'ARCOA',
                                                                                                                   'key' => 'hidARCOAKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount']
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                            <?php } ?>
                            <?php if($obj->useRealization()) { ?> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['needRealization']; ?></label> 
                                    <div class="col-xs-9"><?php echo $obj->inputCheckBox('chkNeedRealization',array("value" => 1)); ?></div> 
                                </div>    
                            <?php } ?>

                        </div>
                </div>
                <div class="div-table-col">
                        <div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['IDInformation']); ?></div>
                            
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['IDNumber']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('IDNumber'); ?>
                                    </div> 
                                </div>  
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['placeAndDateOfBirth']); ?></label> 
                                    <div class="col-xs-6" style="padding-right:0"> 
                                            <?php   
                                                    $popupOpt = (!$isQuickAdd) ? array(
                                                            'url' => 'cityForm.php',
                                                            'element' => array('value' => 'placeOfBirth','valueDBField' => 'citycategoryname',
                                                                   'key' => 'hidPlaceOfBirthKey'),
                                                            'width' => '600px',
                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['city'])
                                                        )  : '';

                                                    echo $obj->inputAutoComplete(array(
                                                            'objRefer' => $city,
                                                            'revalidateField' => false, 
                                                            'element' => array('value' => 'placeOfBirth',
                                                                               'key' => 'hidPlaceOfBirthKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ) ,
                                                            'popupForm' => $popupOpt
                                                          )
                                                    );  
                                        ?>
                                    </div> 
                                     <div class="col-xs-3" style="padding-left:5px"> 
                                          <?php echo $obj->inputDate('dateOfBirth', array('etc' => 'style="text-align:center"')); ?>
                                    </div> 
                                </div>     
                           
                               <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sex']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputSelect('sex',$arrSex); ?>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('memberAddress1'); ?>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('memberAddress2'); ?>
                                    </div> 
                                </div>  
 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?>, <?php echo ucwords($obj->lang['zipcode']); ?></label> 
                                    <div class="col-xs-6"  style="padding-right:0"> 
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
                                     <div class="col-xs-3"  style="padding-left:5px"> 
                                          <?php echo $obj->inputText('memberZipCode', array('etc' => 'style="text-align:center"')); ?>
                                     </div> 
                                </div>    
<!--
                               <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['religion']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputSelect('religion',$arrReligion); ?>
                                    </div> 
                                </div>  
-->
                               <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['maritalStatus']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputSelect('maritalStatus',$arrMarital); ?>
                                    </div> 
                                </div>  
                            
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['nationality']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('nationality'); ?>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['photoID']; ?></label> 
                                    <div class="col-xs-9"> 
                                         <!-- image uploader --> 
                                        <div class="item-image-uploader id-image-uploader">
                                            <ul class="image-list" ></ul>
                                            <div style="clear:both; height:1em; "></div>
                                            <div class="file-uploader">	
                                                <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                            </div>
                                          </div>  
                                        <!-- image uploader --> 
                                    </div> 
                                </div>   
                       </div>
                       <div class="div-tab-panel"> 
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['emergencyContact']); ?></div>
                            <?php 
                           
                                $arrOptions = array();
                                array_push($arrOptions, array('title' =>  ucwords($obj->lang['name']) ,'selector' => 'cpName[]', 'data' => 'name' ));
                                array_push($arrOptions, array('title' =>  ucwords($obj->lang['relation']) ,'selector' => 'cpPosition[]', 'data' => 'position', 'style' => 'width:100px;' ));
                                array_push($arrOptions, array('title' =>  ucwords($obj->lang['phone']) ,'selector' => 'cpPhone[]', 'data' => 'phone',  'style' => 'width:130px;' ));
                           
                                echo $obj->contactPersonPlugin($rsContactPerson,array('field' =>$arrOptions)); 
                           
                            ?>     
                       </div>
                </div>
            </div>
      </div>    
         
   <?php if ($hasSecurityPrivileges) { ?>
      
    <div style="clear:both; height:2em;"></div>   
      
     <?php if(MULTI_COMPANY) { ?> 
       <div class="section-panel gray-scheme">  
       <div class="title"><?php echo ucwords($obj->lang['warehousePrivileges']); ?></div>
       <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                    <div style="clear:both; height:0.5em"></div>  
                    <div class="div-table" style="width: 100%">
                        <div class="div-table-row"> 
                            <div class="div-table-col">  
                                  <?php   
                                   // kalo bukan, dan user punya akses utk kasi perusahaan, bisa dipilih
                                    if ($isFranchisee){
                                        echo '<ul class="tag-list" style="margin-top:0.5em">';
                                        for($i=0;$i<count($rsCompanyOwned);$i++) {  
                                            echo '<li>'.$rsCompanyOwned[$i]['companyname'] .'</li>';
                                        }
                                        echo '</ul>';
                                    }else{
                                          echo $obj->inputSelect('selCompany[]',$arrCompany, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
                                    }  
                                ?> 
                            </div>
                        </div>
                    </div>  
       </div>   
       <div style="clear:both"></div>    
       </div>
     <?php } ?>   
     <?php if($obj->useWarehousePrivileges()) { ?> 
       <div class="section-panel green-scheme">  
       <div class="title"><?php echo ucwords($obj->lang['warehousePrivileges']); ?></div>
       <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                    <div style="clear:both; height:0.5em"></div>  
                    <div class="div-table" style="width: 100%">
                        <div class="div-table-row"> 
                            <div class="div-table-col">  
                                 <div style="margin-bottom:0.5em">
                                     <?php echo  $obj->inputCheckBox('chkAllWarehouseAccess', array('etc' => 'onChange="employee.showWarehouseAccess(this)"')); ?>
                                     <?php echo  $obj->lang['allLocation']; ?>   
                                 </div>  
                                 <div class="warehouse-access-list" style="<?php echo (empty($rs) || $rs[0]['allwarehouseaccess'] == 0) ? '' : 'display:none'; ?>">
                                 <?php echo $obj->inputSelect('selWarehouseAccess[]',$arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>  
                                 </div>
                            </div>
                        </div>
                    </div>  
       </div>   
       <div style="clear:both"></div>    
       </div>
     <?php } ?>    
      <?php if($obj->useSalesPrivileges()) { ?> 
       <div class="section-panel pale-tosca-scheme">  
       <div class="title"><?php echo ucwords($obj->lang['salesmanPrivileges']); ?></div>
       <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                    <div style="clear:both; height:0.5em"></div>  
                    <div class="div-table" style="width: 100%">
                        <div class="div-table-row"> 
                            <div class="div-table-col">  
                                 <div style="margin-bottom:0.5em">
                                     <?php echo  $obj->inputCheckBox('chkAllSalesAccess', array('etc' => 'onChange="employee.showSalesAccess(this)"')); ?>
                                     <?php echo  $obj->lang['allSalesman']; ?>   
                                 </div>  
                                 <div class="sales-access-list" style="<?php echo (isset($rs[0]['pkey']) && $rs[0]['allsalesaccess'] == 0) ? '' : 'display:none'; ?>">
                                 <?php echo $obj->inputSelect('selSalesAccess[]', $arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>  
                                 </div>
                            </div>
                        </div>
                    </div>  
       </div>   
       <div style="clear:both"></div>    
       </div>
     <?php } ?>   
      <?php if($obj->useCustomerPrivileges()) { ?> 
       <div class="section-panel pale-purple-scheme">  
       <div class="title"><?php echo ucwords($obj->lang['customerPrivileges']); ?></div>
       <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                    <div style="clear:both; height:0.5em"></div>  
                    <div class="div-table" style="width: 100%">
                        <div class="div-table-row"> 
                            <div class="div-table-col">  
                                 <div style="margin-bottom:0.5em">
                                     <?php echo  $obj->inputCheckBox('chkAllCustomerAccess', array('etc' => 'onChange="employee.showCustomerAccess(this)"')); ?>
                                     <?php echo  $obj->lang['allCustomer']; ?>   
                                 </div>  
                                 <div class="customer-access-list" style="<?php echo (isset($rs[0]['pkey']) && $rs[0]['allcustomeraccess'] == 0) ? '' : 'display:none'; ?>">
                                 <?php echo $obj->inputSelect('selCustomerAccess[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>  
                                 </div>
                            </div>
                        </div>
                    </div>  
       </div>   
       <div style="clear:both"></div>    
       </div>
     <?php } ?>  
     <?php if (USE_GL) { ?>   
       <div class="section-panel pale-orange-scheme">  
       <div class="title"><?php echo $obj->lang['coaPrivileges']; ?></div>
       <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                    <div style="clear:both; height:0.5em"></div> 
                    <div class="div-table" style="width: 100%">
                        <div class="div-table-row"> 
                            <div class="div-table-col">  
                                 <div style="margin-bottom:0.5em">
                                     <?php echo  $obj->inputCheckBox('chkAllCOAAccess', array('etc' => 'onChange="employee.showCOAAccess(this)"')); ?>   
                                     <?php echo  $obj->lang['allCOA']; ?>   
                                 </div>  
                                 <div class="coa-access-list" style="<?php echo (empty($rs) || $rs[0]['allcoaaccess'] == 0) ? '' : 'display:none'; ?>">
                                 <?php echo $obj->inputSelect('selCOAAccess[]',$arrCOA, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>  
                                 </div>
                            </div>
                        </div>
                    </div>  
       </div>   
       <div style="clear:both"></div>    
       </div>
      <?php } ?>  

       <div class="section-panel">  
           <div class="title"><?php echo ucwords($obj->lang['modulePrivileges']); ?></div>
           <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
                        <div style="clear:both; height:0.5em"></div>  
                        <div>
                            <div style="float:left; padding-top:8px"><?php echo $obj->lang['importFrom']?></div>
                            <div style="float:left; margin-left:0.5em; "><?php echo  $obj->inputSelect('selImportFrom', $arrImportFrom); ?></div>
                            <div style="float:left; margin-left:0.5em; width: 25em; display:none" class="employee-template">
                                <?php    
                                    echo $obj->inputAutoComplete(array(  
                                                                        'element' => array('value' => 'employeeTemplateName',
                                                                                           'key' => 'hidEmployeeTemplateKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-employee.php',
                                                                                            'data' => array(  'action' =>'searchData')
                                                                                        )  
                                                                      )
                                                                );  
                                    ?> 
                            </div>
                            <div style="float:left; margin-left:0.5em; " class="role-template"><?php echo  $obj->inputSelect('selRoleKey', $arrTemplateRole); ?></div>
                            <div style="float:left; margin-left:1em; margin-top:0.2em"><?php echo $obj->inputButton('btnImport', $obj->lang['import'],  array('class' => 'btn btn-primary btn-second-tone')); ?></div>
                            <div style="clear:both; height:2em"></div> 
                            <div>
                                  <?php echo $obj->inputButton('btnSelectAll', $obj->lang['selectAll'], array('class' => 'btn btn-primary btn-second-tone')); ?>
                                  <?php echo $obj->inputButton('btnDeselectAll', $obj->lang['deselectAll'], array('class' => 'btn btn-primary btn-second-tone')); ?> 
                           </div>

                        </div> 


                        <ul id="security-module" >
                        <?php 
                            $listSecurityAccess = '';

                              for ($catCtr=0;$catCtr<count($rsSecurityObjectCategory);$catCtr++){ 

                                $rsSecurityObject  = $security->generateSecurityObject($rsSecurityObjectCategory[$catCtr]['pkey']);    

                                if (!empty($rsSecurityObject)) 
                                    $listSecurityAccess .= '<div style="clear:both; height: 1em"></div><div class="section-title">'.strtoupper($rsSecurityObjectCategory[$catCtr]['name']).'</div>';


                                $colStyle = ($rsSecurityObjectCategory[$catCtr]['pkey'] == 10) ? ' style="display:inline-block; float:left; margin-right:2em; " ' : '';

                                for ($i=0;$i<count($rsSecurityObject);$i++){ 
                                $listAccessItem = '';  
                                $unChecked = false;

                                $listSecurityAccess .= '<li '.$colStyle.'>';

                                $arrStatusName[10] = $obj->lang['showAll'];
                                $arrStatusName[11] = $obj->lang['add'];
                                $arrStatusName[12] = $obj->lang['delete'];

                                if ($rsSecurityObject[$i]['modulestatus'] ==  'view_only'){
                                        $selectedClass = '';
                                        $checked = '';

                                        if (!empty($id) && $security->hasSecurityAccess($id,$rsSecurityObject[$i]['pkey'],10)){ 
                                            $checked = ' checked="checked"';
                                            $selectedClass = 'bg-green-avocado text-white';	
                                        } else{
                                            $unChecked = true;	
                                        }


                                        $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="10" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/>'.$obj->lang['showAll'].'</label>';
                                }else if ($rsSecurityObject[$i]['modulestatus'] ==  'view_and_update'){

                                        $selectedClass = '';
                                        $checked = '';

                                        if (!empty($id) && $security->hasSecurityAccess($id,$rsSecurityObject[$i]['pkey'],10)){ 
                                            $checked = ' checked="checked"';
                                            $selectedClass = 'bg-green-avocado text-white';	
                                        } else{
                                            $unChecked = true;	
                                        }


                                        $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="10" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/>'.$obj->lang['showAll'].'</label>';


                                        $selectedClass = '';
                                        $checked = '';

                                        if (!empty($id) && $security->hasSecurityAccess($id,$rsSecurityObject[$i]['pkey'],11)){ 
                                            $checked = ' checked="checked"';
                                            $selectedClass = 'bg-green-avocado text-white';	
                                        } else{
                                            $unChecked = true;	
                                        }


                                        $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="11" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/>Update</label>';
                                }else{
                                    for ($k=10;$k<=12;$k++){ 
                                        $selectedClass = '';
                                        $checked = '';

                                        if (!empty($id) && $security->hasSecurityAccess($id,$rsSecurityObject[$i]['pkey'],$k)){ 
                                            $checked = ' checked="checked"';
                                            $selectedClass = 'bg-green-avocado text-white';	
                                        } else{
                                            $unChecked = true;	
                                        }


                                        $listAccessItem .= '<label  class="status-item '.$selectedClass.'" ><input type="checkbox" class="chkSecurityList" value="'. $k .'" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/> '. $arrStatusName[$k] .'</label>';
                                     }

                                   $rsStatus = $security->getAllStatus($rsSecurityObject[$i]['modulestatus']);  
                                    for ($j=0;$j< count($rsStatus);$j++) { 
                                        $checked = '';
                                        $selectedClass = '';
                                        if (!empty($id) &&  $security->hasSecurityAccess($id,$rsSecurityObject[$i]['pkey'],$rsStatus[$j]['pkey'])){
                                           $checked = ' checked="checked"';
                                           $selectedClass = 'bg-green-avocado text-white';
                                        }else{
                                            $unChecked = true;	
                                        }

                                        $listAccessItem .= '<label class="status-item '.$selectedClass.'" ><input type="checkbox"  class="chkSecurityList"  value="'. $rsStatus[$j]['pkey'].'" name="chkList'.$rsSecurityObject[$i]['pkey'].'[]" '.$checked.'/> '.$rsStatus[$j]['status'].'</label>';

                                    } 
                                }


                                 $checked = '';
                                 $selectedClass = '';
                                 if ($unChecked == false ){
                                      $checked = ' checked="checked"';
                                      $selectedClass = 'bg-green-avocado text-white';
                                 }

                                 $listSecurityAccess .= '<label class="module-item"><input  type="checkbox"  class="chkModuleName '.$selectedClass.'" name="chkSecurityModuleAccess '. $rsSecurityObject[$i]['pkey'].'" '.$checked.'> '.strtoupper($rsSecurityObject[$i]['modulename']).'</label>';
                                 $listSecurityAccess .= $listAccessItem;
                                 $listSecurityAccess .= '</li>';

                            }

                         }

                            echo $listSecurityAccess;
                         ?>  

                      </ul>

           </div>   
           <div style="clear:both"></div>    
       </div>
   
 
   <?php } ?>
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
