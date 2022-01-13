<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('EmployeeCategory.class.php','Employee.class.php','Warehouse.class.php'));
$employee = createObjAndAddToCol(new Employee());
$employeeCategory = createObjAndAddToCol(new EmployeeCategory());
$warehouse = createObjAndAddToCol(new Warehouse());


require_once '../assets/vendor/autoload.php';   
$g = new \Google\Authenticator\GoogleAuthenticator();

$obj = $employee;

if (empty($_SESSION[$class->loginAdminSession]['id']))  
    die;
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editCategoryInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';

$secret = $g->generateSecret(); // $obj->generateStrongPassword(16,'','ud');
$_SESSION['2fAuth'] = $secret;
    
$id = base64_decode($_SESSION[$class->loginAdminSession]['id']);
$rs = $obj->getDataRowById($id);
$_POST['hidId'] = $rs[0]['pkey'];
$_POST['memberCode'] = $rs[0]['code'];
$_POST['memberUserName'] = $rs[0]['username'];
$_POST['selStatus'] = $rs[0]['statuskey']; 
$_POST['selCategory'] = $rs[0]['categorykey']; 
$_POST['selWarehouse'] = $rs[0]['warehousekey']; 
$_POST['memberName'] = $rs[0]['name'];  
$_POST['livingAddress1'] = $rs[0]['livingaddress1']; 
$_POST['livingAddress2'] = $rs[0]['livingaddress2'];   
$_POST['memberPhone'] = $rs[0]['phone']; 
$_POST['memberMobile'] = $rs[0]['mobile']; 
$_POST['memberEmail'] = $rs[0]['email'];  
 
$_POST['hidModifiedOn'] = $rs[0]['modifiedon']; 
$_POST['action'] = 'edit';


$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
$editCategoryInactiveCriteria = ' or '.$employeeCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');   
$arrCategory = $class->convertForCombobox($employeeCategory->searchData('','',true, ' and ('.$employeeCategory->tableName.'.statuskey'. $editCategoryInactiveCriteria.')'),'pkey','name');  


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 

<script type="text/javascript">  

	jQuery(document).ready(function(){  
	 	
        var tabID =  selectedTab.newPanel[0].id;
        setOnDocumentReady(tabID);   
		  
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
			fields: {
				memberName: { 
					validators: {
						notEmpty: {
							message: phpErrorMsg.name['1']
						}, 
					}
				}, 
             
				currentPassword: { 
					validators: {
						 notEmpty: {
							message: phpErrorMsg.password['1'],
						}, 
						stringLength: {
							min: 5,
							max: 30,
							message: phpErrorMsg.password['2']
						},
						remote: {
							message: phpErrorMsg.username['5'],
							url: 'updateProfile.php',
							data: {
								type: 'check',
								fieldtype: 'checkPassword'
							},
						type: 'POST'
						}
					}
				},
			 
				memberPassword: { 
						validators: {
						   
							stringLength: {
								min: 5,
								max: 30,
								message: phpErrorMsg.password['2']
							}, 
							identical: {
								field: 'passwordConfirmation',
								message: phpErrorMsg.password['3']
							}
						}
					},  
					
					passwordConfirmation: { 
						validators: {
						   
							stringLength: {
								min: 5,
								max: 30,
								message: phpErrorMsg.password['2']
							}, 
							identical: {
								field: 'memberPassword',
								message: phpErrorMsg.password['3']
							}
						}
					},
					
					memberEmail: { 
						 validators: {
							notEmpty: {
								message:  phpErrorMsg.email['1']
							},  
							emailAddress: {
								message:  phpErrorMsg.email['3']
							},
						}
					},
			}
			
        })
        .on('success.form.bv', function(e) {
             
            // Prevent form submission
            e.preventDefault();
 
             
			 // Get the form instance
             var $form = $(e.target);

             var btnSave = $form.find("[name=btnSave]");
     
             btnSave.prop('disabled', true);
             btnSave.find(".loading-icon").show();


            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data 
			 $.post($form.attr('action'), $form.serialize(), function(result) {   
				alert(phpLang.dataHasBeenSuccessfullyUpdated);
				selectedTab.newTab[0].remove();
				$tabs.tabs("refresh");   
            }, 'json');
        });
		
        
        $( "#" + tabID + " [name=verifyButton]" ).on('click', function() { 
              $.ajax({  
                        type: "GET",
                        async : false,
                        url:  'ajax-2fauth.php', 
                        data: "action=verify&code="+ $( "#" +  tabID + " [name=authcode]" ).val() ,  
                        success: function(data){   
                            var data = JSON.parse(data); 
                           
                            if (data.status){ 
                                $( "#" +  tabID + " .verification-panel").html('<div class="text-green-avocado">'+phpLang.verificationSuccessful+'</div>');
                                $( "#" +  tabID + " [name=secretAuth]").val("<?php echo $secret; ?>");
                            }else{ 
                                $( "#" +  tabID + " [name=secretAuth]").val("");
                                alert(phpLang.verificationFailed);
                            }
                        }  
                    }) ; 
        });
        
        $( "#" + tabID + " [name=remove2FButton]" ).on('click', function() { 
            $( "#" + tabID + " .barcode" ).show();
            $( "#" +  tabID + " [name=secretAuth]").val("");
            $( "#" + tabID + " .disable-button" ).remove(); 
        });
         
        
});
	
	 
	  
</script>
</head> 

<body>
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
   <form id="defaultForm" method="post" class="form-horizontal" action="updateProfile.php">
        <?php prepareOnLoadDataForm($obj); ?> 
     
     <div class="div-table  main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                     		<div class="div-tab-panel"> 
                                      <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['accountInformation']); ?></div>
                                     
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                            <div class="col-xs-9">  
                                                  <?php echo  $obj->inputText('memberCode', array('readonly' => true)) ?>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['username']); ?></label> 
                                            <div class="col-xs-9"> 
                                                    <?php echo $obj->inputText('memberUserName', array('readonly' => true)) ?>
                                            </div> 
                                        </div> 

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['oldPassword']); ?></label> 
                                            <div class="col-xs-9"> 
                                                    <?php echo $obj->inputPassword('currentPassword'); ?>
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
                                                 <?php echo $obj->inputPassword('passwordConfirmation'); ?>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['division']); ?></label> 
                                            <div class="col-xs-9"> 
                                                 <?php echo  $obj->inputSelect('selCategory', $arrCategory, array('disabled'=>true)); ?>
                                            </div> 
                                        </div> 
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                            <div class="col-xs-9"> 
                                                 <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse, array('disabled'=>true)); ?>
                                            </div> 
                                        </div>  
                                    
                           </div>
                        
                        <div class="div-tab-panel">  
                            <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['2FAuthentication']); ?></div>
                             <?php
                                // jika blm ad salt
                                $showBarcode = '';
                            
                                if (!empty($rs[0]['secretAuth'])){   
                                    $_POST['secretAuth']  = $rs[0]['secretAuth'];
                                    $showBarcode = 'display: none';
                                    echo '<div class="disable-button">
                                          <div class="text-green-avocado">'.$obj->lang['2FEnabled'] .'</div>
                                          <div style="clear:both; margin-top: 1em">'. $obj->inputButton('remove2FButton', ucwords($obj->lang['disable2fAuthentication'])).'</div>
                                          </div>
                                          ';
                                }
                            
                                    echo '<div class="barcode" style="'.$showBarcode.'">
                                          <div style="text-align:center; margin-top:1em; ">
                                                <img src="'.$g->getURL($rs[0]['username'],DOMAIN_NAME, $secret).'" width="100" height="100" />
                                                <div style="line-height:2em; font-size:1.5em">'.$obj->addSeparator($secret).'</div>
                                          </div>
                                          <div class="verification-panel" style="text-align:center; width: 80%; margin:auto;margin-top:1em; ">
                                          <div style="width:60%; float:left;">'.$obj->inputText('authcode', array('etc'=>'style="text-align:center" placeholder="'.ucwords($obj->lang['authenticationCode']).'"')).'</div>
                                          <div style="width:40%; float:left; margin-top:0.2em">'.$obj->inputButton('verifyButton', ucwords($obj->lang['verify']), array('class' => 'btn btn-primary btn-second-tone')).'</div>
                                          </div>
                                          </div>
                                          ';
                            
                                echo $obj->inputHidden('secretAuth');
                            ?>  
                        </div>
                    </div>  
                    <div class="div-table-col"  > 
                        <div class="div-tab-panel"> 
                                  <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['personalInformation']); ?></div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('memberName'); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo $obj->inputText('memberPhone'); ?>
                                        </div> 
                                    </div>  
                            
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('memberMobile'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('memberEmail'); ?>
                                        </div> 
                                    </div>   
                       </div>
                    </div> 
                </div>
        </div>
       
            <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->inputSubmit('btnSave', $obj->lang['save']); ?> 
        </div> 
        
    </form>
</div>
</body>

</html>